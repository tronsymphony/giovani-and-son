<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\Cart\Structures\CartSetCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartItemsCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartSet;
use ADP\BaseVersion\Includes\Rule\Structures\PackageItem;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule;
use ADP\BaseVersion\Includes\Rule\Structures\Range;
use ADP\Factory;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class RuleSetCollector {
	/**
	 * @var PackageRule
	 */
	protected $rule;

	/**
	 * @var CartItemsCollection
	 */
	protected $mutable_items_collection;

	protected $check_execution_time_callback;

	protected $packages;

	/**
	 * @param $rule PackageRule
	 */
	public function __construct( $rule ) {
		$this->rule                     = $rule;
		$this->mutable_items_collection = new CartItemsCollection( $rule->getId() );
		$this->packages                 = array();
	}

	public function register_check_execution_time_function( $callable, $context ) {
		$this->check_execution_time_callback = array(
			'callable' => $callable,
			'context'  => $context,
		);
	}

	private function check_execution_time() {
		if ( ! isset( $this->check_execution_time_callback['callable'] ) && $this->check_execution_time_callback['context'] ) {
			return;
		}

		$callable = $this->check_execution_time_callback['callable'];
		$context  = $this->check_execution_time_callback['context'];

		call_user_func( $callable, $context );
	}

	/**
	 * @param $mutable_items CartItem[]
	 */
	public function add_items( $mutable_items ) {
		foreach ( $mutable_items as $index => $cart_item ) {
			$this->mutable_items_collection->add( $cart_item );
		}
	}

	/**
	 * @param $cart Cart
	 *
	 * @throws Exception
	 */
	public function apply_filters( $cart ) {
		$packages           = array();

		// hashes with highest priority
		$type_products_hashes = array();

		foreach ( $this->rule->getPackages() as $package ) {
			$packages[] = $this->preparePackage( $cart, $package, $type_products_hashes );
		}

		if ( count( $packages ) === count( $this->rule->getPackages() ) ) {
			$this->packages = $packages;
		}

		foreach ( $this->packages as &$filter ) {
			$is_product_filter = $filter['is_product_filter'];
			unset( $filter['is_product_filter'] );

			/** Do not reorder 'exact products' filter hashes */
			if ( $is_product_filter ) {
				continue;
			}

			foreach ( array_reverse( $type_products_hashes ) as $hash ) {
				foreach ( $filter['valid_hashes'] as $index => $valid_hash ) {
					if ( $hash === $valid_hash ) {
						unset( $filter['valid_hashes'][ $index ] );
						$filter['valid_hashes'][] = $hash;
						$filter['valid_hashes']   = array_values( $filter['valid_hashes'] );
						break;
					}
				}
			}
		}
	}

	/**
	 * @param Cart        $cart
	 * @param PackageItem $package
	 * @param array       $type_products_hashes
	 *
	 * @return array
	 */
	protected function preparePackage( $cart, $package, &$type_products_hashes ) {
		$filters = $package->getFilters();
//		$excludes = $package->getExcludes();

		/**
		 * @var $product_filtering ProductFiltering
		 * @var $productExcluding ProductFiltering
		 */
		$product_filtering = Factory::get( "Rule_ProductFiltering", $cart->get_context()->getGlobalContext() );
		$productExcluding  = Factory::get( "Rule_ProductFiltering", $cart->get_context()->getGlobalContext() );

		$productExcludingEnabled = $cart->get_context()->get_option( 'allow_to_exclude_products' );
		$limitation      = $package->getLimitation();


		$valid_hashes = array();

		foreach ( $this->mutable_items_collection->get_items() as $cartItem ) {
			/**
			 * @var $cartItem CartItem
			 */
			$wcCartItemFacade = $cartItem->getWcItem();
			$product          = $wcCartItemFacade->getProduct();

//				if ( $productExcludingEnabled ) {
//					$isExclude = false;
//
//					foreach ( $excludes as $exclude ) {
//						$productExcluding->prepare( $exclude->getType(), $exclude->getValue(), $exclude->getMethod() );
//
//						if ( $productExcluding->check_product_suitability( $product, $wcCartItemFacade->getData() ) ) {
//							$isExclude = true;
//							break;
//						}
//					}
//
//					if ( $isExclude ) {
//						continue;
//					}
//				}

			/**
			 * Item must match all filters
			 */
			$match = true;
			foreach ( $filters as $filter ) {
				$product_filtering->prepare( $filter->getType(), $filter->getValue(), $filter->getMethod() );

				if ( $productExcludingEnabled ) {
					$productExcluding->prepare( $filter::TYPE_PRODUCT, $filter->getExcludeProductIds(),
						$filter::METHOD_IN_LIST );

					if ( $productExcluding->check_product_suitability( $product, $wcCartItemFacade->getData() ) ) {
						$match = false;
						break;
					}

					if ( $filter->isExcludeWcOnSale() && $product->is_on_sale( '' ) ) {
						$match = false;
						break;
					}

					if ( $filter->isExcludeAlreadyAffected() && $cartItem->areRuleApplied() ) {
						$match = false;
						break;
					}
				}

				if ( ! $product_filtering->check_product_suitability( $product, $wcCartItemFacade->getData() ) ) {
					$match = false;
					break;
				}
			}

			if ( $match ) {
				$valid_hashes[] = $cartItem->getHash();
				if ( $product_filtering->is_type( 'products' ) ) {
					$type_products_hashes[] = $cartItem->getHash();
				}
			}
		}

		return array(
			'valid_hashes'         => $valid_hashes,
			'is_product_filter'    => $product_filtering->is_type( 'products' ),
			'package'              => $package,
			'limitation' 		   => $limitation,
		);
	}

	/**
	 * @param $cart Cart
	 * @param $mode string
	 *
	 * @return CartSetCollection|false
	 * @throws Exception
	 */
	public function collect_sets( &$cart, $mode = 'legacy' ) {
		if ( 'legacy' === $mode ) {
			$collection = $this->collect_sets_legacy( $cart );
		} else {
			$collection = false;
		}

		return $collection;
	}

	/**
	 * @param $cart Cart
	 *
	 * @return CartSetCollection|false
	 * @throws Exception
	 */
	public function collect_sets_legacy( &$cart ) {
		$collection = new CartSetCollection();
		$applied    = true;

		while ( $applied && $collection->get_total_sets_qty() !== $this->rule->getPackagesCountLimit() ) {
			$set_items = array();

			foreach ( $this->packages as $filter_key => &$filter ) {
				$valid_hashes       = ! empty( $filter['valid_hashes'] ) ? $filter['valid_hashes'] : array();
				$limitation = ! empty( $filter['limitation'] ) ? $filter['limitation'] : PackageItem::LIMITATION_NONE;
				$package            = $filter['package'];
				/** @var $package PackageItem */
				$range = new Range( $package->getQty(), $package->getQtyEnd(), $valid_hashes );

				$valid_hashes_grouped = array();
				if ( $limitation === PackageItem::LIMITATION_VARIATION ) {
					foreach ( $valid_hashes as $index => $valid_cart_item_hash ) {
						$cartItem = $this->mutable_items_collection->get_not_empty_item_with_reference_by_hash( $valid_cart_item_hash );

						if ( ! $cartItem ) {
							continue;
						}
						$product = $cartItem->getWcItem()->getProduct();

						if ( ! isset( $valid_hashes_grouped[ $product->get_id() ] ) ) {
							$valid_hashes_grouped[ $product->get_id() ] = array();
						}

						$valid_hashes_grouped[ $product->get_id() ][] = $valid_cart_item_hash;
					}
				} elseif ( $limitation === PackageItem::LIMITATION_PRODUCT ) {
					foreach ( $valid_hashes as $index => $valid_cart_item_hash ) {
						$cartItem = $this->mutable_items_collection->get_not_empty_item_with_reference_by_hash( $valid_cart_item_hash );

						if ( ! $cartItem ) {
							continue;
						}
						$product = $cartItem->getWcItem()->getProduct();
						$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

						if ( ! isset( $valid_hashes_grouped[ $product_id ] ) ) {
							$valid_hashes_grouped[ $product_id ] = array();
						}

						$valid_hashes_grouped[ $product_id ][] = $valid_cart_item_hash;
					}
				} elseif ( $limitation === PackageItem::LIMITATION_UNIQUE ) {
					foreach ( $valid_hashes as $index => $valid_cart_item_hash ) {
						$valid_hashes_grouped[] = array( $valid_cart_item_hash );
					}
				} else {
					$valid_hashes_grouped[] = $valid_hashes;
				}

				$filter_applied = false;

				foreach ( $valid_hashes_grouped as $valid_hashes ) {
					$filter_applied = false;

					$filter_set_items = array();

					foreach ( $valid_hashes as $index => $valid_cart_item_hash ) {
						$cart_item = $this->mutable_items_collection->get_not_empty_item_with_reference_by_hash( $valid_cart_item_hash );

						if ( is_null( $cart_item ) ) {
							unset( $valid_hashes[ $index ] );
							continue;
						}

						$collected_qty = 0;
						foreach ( $filter_set_items as $filter_set_item ) {
							/**
							 * @var $filter_set_item CartItem
							 */
							$collected_qty += $filter_set_item->getQty();
						}

						$collected_qty += $cart_item->getQty();

						if ( ! $range->isValid() ) {
							continue;
						}

						if ( $range->isLess( $collected_qty ) ) {
							$set_cart_item = clone $cart_item;
							$cart_item->setQty( 0 );
							$filter_set_items[] = $set_cart_item;
						} elseif ( $range->isIn( $collected_qty ) ) {
							$set_cart_item = clone $cart_item;
							$cart_item->setQty( 0 );
							$filter_set_items[] = $set_cart_item;
							$filter_applied     = true;
							break;
						} elseif ( $range->isGreater( $collected_qty ) ) {
							$mode_value_to = $range->getTo();
							if ( is_infinite( $mode_value_to ) ) {
								continue;
							}

							$require_qty = $mode_value_to + $cart_item->getQty() - $collected_qty;

							$set_cart_item = clone $cart_item;
							$set_cart_item->setQty( $set_cart_item->getQty() - ( $cart_item->getQty() - $require_qty ) );
							$cart_item->setQty( $cart_item->getQty() - $require_qty );

							$filter_set_items[] = $set_cart_item;
							$filter_applied     = true;
							break;
						}
					}

					if ( $filter_set_items ) {
						if ( $filter_applied ) {
							$set_items[] = $filter_set_items;
						} else {
							/**
							 * For range filters, try to put remaining items in set
							 *
							 * If range 'to' equals infinity or 'to' not equal 'from'
							 */
							if ( $range->getQty() === false || $range->getQty() ) {
								$collected_qty = 0;
								foreach ( $filter_set_items as $filter_set_item ) {
									/**
									 * @var $filter_set_item CartItem
									 */
									$collected_qty += $filter_set_item->getQty();
								}

								if ( $range->isIn( $collected_qty ) ) {
									$set_items[]      = $filter_set_items;
									$filter_set_items = array();
									$filter_applied   = true;
								}
							}

							foreach ( $filter_set_items as $item ) {
								/**
								 * @var $item CartItem
								 */
								$this->mutable_items_collection->add( $item );
							}
						}

						$filter_set_items = array();
					}

					if ( $filter_applied ) {
						break;
					}
				}

				$applied = $applied && $filter_applied;
			}

			if ( $set_items && $applied ) {
				$collection->add( new CartSet( $this->rule->getId(), $set_items ) );
				$set_items = array();
			}

			$this->check_execution_time();
		}

		if ( ! empty( $set_items ) ) {
			foreach ( $set_items as $tmp_filter_set_items ) {
				foreach ( $tmp_filter_set_items as $item ) {
					$cart->addToCart( $item );
				}
			}
		}

		if ( ! empty( $filter_set_items ) ) {
			foreach ( $filter_set_items as $item ) {
				$cart->addToCart( $item );
			}
		}

		foreach ( $this->mutable_items_collection->get_items() as $item ) {
			/**
			 * @var $item CartItem
			 */
			$cart->addToCart( $item );
		}

		return $collection;
	}

}

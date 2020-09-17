<?php

namespace ADP\BaseVersion\Includes\Product;

use ADP\BaseVersion\Includes\Cart\CartCalculator;
use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\External\WC\DataStores\ProductVariationDataStoreCpt;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\Reporter\ProductCalculatorListener;
use ADP\Factory;
use Exception;
use ReflectionClass;
use ReflectionException;
use WC_Product;
use WC_Product_Grouped;
use WC_Product_Variable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Processor {
	const ERR_PRODUCT_WITH_NO_PRICE = 101;
	const ERR_TMP_ITEM_MISSING = 102;
	const ERR_PRODUCT_DOES_NOT_EXISTS = 103;
	const ERR_CART_DOES_NOT_EXISTS = 104;

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var CartCalculator
	 */
	protected $calc;

	/**
	 * @var Cart
	 */
	protected $cart;

	/**
	 * @var ProductCalculatorListener
	 */
	protected $listener;

	/**
	 * @param Context        $context
	 * @param CartCalculator $calc
	 */
	public function __construct( $context, $calc = null ) {
		$this->context  = $context;
		$this->listener = new ProductCalculatorListener( $context );

		if ( $calc instanceof CartCalculator ) {
			$this->calc = $calc;
		} else {
			$this->calc = Factory::callStaticMethod( "Cart_CartCalculator", 'make', $context, $this->listener );
			/** @see CartCalculator::make() */
		}
	}

	/**
	 * @param Cart $cart
	 */
	public function withCart( $cart ) {
		$this->cart = $cart;
	}

	protected function isCartExists() {
		return isset( $this->cart );
	}

	/**
	 * @param WC_Product|int $theProduct
	 * @param float           $qty
	 *
	 * @return ProcessedProductSimple|ProcessedVariableProduct|null
	 */
	public function calculateProduct( $theProduct, $qty = 1.0 ) {
		if ( is_numeric( $theProduct ) ) {
			$product = CacheHelper::getWcProduct( $theProduct );
		} elseif ( $theProduct instanceof WC_Product ) {
			$product = clone $theProduct;
		} else {
			$this->context->handle_error( new Exception( "Product does not exists",
				self::ERR_PRODUCT_DOES_NOT_EXISTS ) );

			return null;
		}

		if ( $product instanceof WC_Product_Variable || $product instanceof WC_Product_Grouped ) {
			$processed = Factory::get( "Product_ProcessedVariableProduct", $this->context, $product, $qty );
			/** @var $processed ProcessedVariableProduct */

			if ( $product instanceof WC_Product_Variable ) {
				$children = $product->get_visible_children();
			} elseif ( $product instanceof WC_Product_Grouped ) {
				$children = $product->get_children();
			} else {
				return null;
			}

			foreach ( $children as $childId ) {
				$processedChild = $this->calculate( $childId, $qty, array(), $product );

				if ( is_null( $processedChild ) ) {
					continue;
				}

				$processed->useChild( $processedChild );
			}
		} else {
			$processed = $this->calculate( $product, $qty );
		}

		return $processed;
	}

	/**
	 * @param WC_Product|int $theProduct
	 * @param float           $qty
	 * @param array           $cartItemData
	 * @param WC_Product     $theParentProduct
	 *
	 * @return ProcessedProductSimple|null
	 */
	protected function calculate( $theProduct, $qty = 1.0, $cartItemData = array(), $theParentProduct = null ) {
		if ( ! $this->isCartExists() ) {
			$this->context->handle_error( new Exception( "Cart does not exists", self::ERR_CART_DOES_NOT_EXISTS ) );

			return null;
		}

		if ( is_numeric( $theProduct ) ) {
			$prodID = $theProduct;
		} elseif ( $theProduct instanceof WC_Product ) {
			$prodID = $theProduct->get_id();
		} else {
			$prodID = null;
		}

		if ( $prodID && $processedProduct = CacheHelper::maybeGetProcessedProductToDisplay( $prodID, $qty,
				$cartItemData, $this->cart, $this->calc ) ) {
			return $processedProduct;
		}

		if ( is_numeric( $theParentProduct ) ) {
			$parent = CacheHelper::getWcProduct( $theParentProduct );
		} elseif ( $theParentProduct instanceof WC_Product ) {
			$parent = clone $theParentProduct;
			CacheHelper::loadVariationsPostMeta( $parent->get_id() );
		} else {
			$parent = null;
		}

		if ( is_numeric( $theProduct ) ) {
			if ( $parent && $parent->is_type( 'variable' ) ) {

				// We do not need to get product type if the parent product is known
				$overrideProductTypeQuery = function () {
					return 'variation';
				};

				$applyDataStore = function () use ( $parent ) {
					$data_store = new ProductVariationDataStoreCpt();
					if ( ! is_null( $parent ) ) {
						$data_store->add_parent( $parent );
					}

					return $data_store;
				};

				add_filter( 'woocommerce_product-variation_data_store', $applyDataStore, 10 );
				add_filter( 'woocommerce_product_type_query', $overrideProductTypeQuery, 10 );
				$product = CacheHelper::getWcProduct( $theProduct );
				remove_filter( 'woocommerce_product_type_query', $overrideProductTypeQuery, 10 );
				remove_filter( 'woocommerce_product-variation_data_store', $applyDataStore, 10 );
			} else {
				$product = CacheHelper::getWcProduct( $theProduct );
			}
		} elseif ( $theProduct instanceof WC_Product ) {
			$product = clone $theProduct;

			try {
				$reflection = new ReflectionClass( $product );
				$property   = $reflection->getProperty( 'changes' );
				$property->setAccessible( true );
				$property->setValue( $product, array() );
			} catch ( ReflectionException $exception ) {
				$property = null;
			}
		} else {
			$product = null;
		}

		if ( ! $product ) {
			$this->context->handle_error( new Exception( "Product does not exists",
				self::ERR_PRODUCT_DOES_NOT_EXISTS ) );

			return null;
		}

		if ( $product->get_price( 'edit' ) === '' ) {
			$this->context->handle_error( new Exception( "Empty price", self::ERR_PRODUCT_WITH_NO_PRICE ) );

			return null;
		}

		$cartItemData = apply_filters( 'adp_calculate_product_price_data', $cartItemData, $product, $this->context );
		$cartItem      = WcCartItemFacade::createFromProduct( $this->context, $product, $cartItemData );
		$cartItem->setQty( $qty );

		$cart = clone $this->cart;

		$item = $cartItem->createItem();
		$item->addAttr( $item::ATTR_TEMP );

		$cart->addToCart( $item );
		$this->listener->startCartProcessProduct( $product );
		$this->calc->processCart( $cart );
		$this->listener->finishCartProcessProduct( $product );

		$tmpItems = array();
		$qtyAlreadyInCart = floatval( 0 );
		foreach ( $cart->getItems() as $loopCartItem ) {
			if ( $loopCartItem->getWcItem()->getKey() === $item->getWcItem()->getKey() ) {
				if ( $loopCartItem->hasAttr( $loopCartItem::ATTR_TEMP ) ) {
					$tmpItems[] = $loopCartItem;
				}
			}

			if ( $loopCartItem->getWcItem()->getProduct()->get_id() === $item->getWcItem()->getProduct()->get_id() ) {
				$qtyAlreadyInCart += $loopCartItem->getQty();
			}
		}

		/**
		 * Cheap items at the bottom
		 *
		 * @var CartItem[] $tmpItems
		 */
		usort( $tmpItems, function ( $a, $b ) {
			/** @var CartItem $a */
			/** @var CartItem $b */

			return $a->getPrice() < $b->getPrice();
		} );

		$qtyAlreadyInCart = $qtyAlreadyInCart - count( $tmpItems );

		if ( count( $tmpItems ) === 0 ) {
			$this->context->handle_error( new Exception( "Temporary item is missing", self::ERR_TMP_ITEM_MISSING ) );

			return null;
		}

		$processedProduct = new ProcessedProductSimple( $this->context, $product, $tmpItems );
		$processedProduct->setQtyAlreadyInCart( $qtyAlreadyInCart );
		CacheHelper::addProcessedProductToDisplay( $cartItem, $processedProduct, $this->cart, $this->calc );
		$this->listener->processedProduct( $processedProduct );

		return $processedProduct;
	}

	/**
	 * @return ProductCalculatorListener
	 */
	public function getListener() {
		return $this->listener;
	}

	/**
	 * @return Cart
	 */
	public function getCart() {
		return $this->cart;
	}
}

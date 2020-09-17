<?php

namespace ADP\BaseVersion\Includes\External;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Cart\RulesCollection;
use ADP\BaseVersion\Includes\External\Cmp\WoocsCmp;
use ADP\BaseVersion\Includes\Rule\CartAdjustmentsLoader;
use ADP\BaseVersion\Includes\Rule\ConditionsLoader;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\LimitsLoader;
use ADP\BaseVersion\Includes\Rule\OptionsConverter;
use ADP\BaseVersion\Includes\Rule\Structures\Discount;
use ADP\BaseVersion\Includes\Rule\Structures\Filter;
use ADP\BaseVersion\Includes\Rule\Structures\Gift;
use ADP\BaseVersion\Includes\Rule\Structures\NoItemRule;
use ADP\BaseVersion\Includes\Rule\Structures\PackageItem;
use ADP\BaseVersion\Includes\Rule\Structures\PackageItemFilter;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule;
use ADP\BaseVersion\Includes\Rule\Structures\RangeDiscount;
use ADP\BaseVersion\Includes\Rule\Structures\RoleDiscount;
use ADP\BaseVersion\Includes\Rule\Structures\SetDiscount;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\BaseVersion\Includes\Translators\RuleTranslator;
use ADP\Factory;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class RuleStorage {
	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var ConditionsLoader
	 */
	protected $conditionsLoader;

	/**
	 * @var LimitsLoader
	 */
	protected $limitsLoader;

	/**
	 * @var CartAdjustmentsLoader
	 */
	protected $cartAdjLoader;

	/**
	 * Temporary object to convert rules to new scheme
	 * todo remove after upgrade DB to version 3.0
	 *
	 * @var OptionsConverter
	 */
	protected $optionsConverter;

	/**
	 * @var WoocsCmp
	 */
	protected $woocsCmp;

	/**
	 * @param Context $context
	 */
	public function __construct( $context ) {
		$this->context          = $context;
		$this->conditionsLoader = Factory::get( "Rule_ConditionsLoader" );
		$this->limitsLoader     = Factory::get( "Rule_LimitsLoader" );
		$this->cartAdjLoader    = Factory::get( 'Rule_CartAdjustmentsLoader' );
		$this->optionsConverter = new OptionsConverter();
		$this->woocsCmp         = new WoocsCmp( $context );
	}

	/**
	 * @param array $rows
	 *
	 * @return RulesCollection
	 */
	public function buildRules( $rows ) {
		$rules = array();

		foreach ( $rows as $row ) {
			$filters = isset( $row['filters'] ) ? $row['filters'] : array();
			$filter  = reset( $filters );

			if ( count( $filters ) === 1 && $filter !== false && floatval( $filter['qty'] ) === floatval( 1 ) ) {
				$rule = $this->buildSingleItemRule( $row );
			} elseif ( count( $filters ) > 1 || ( count( $filters ) === 1 && $filter !== false && floatval( $filter['qty'] ) > floatval( 1 ) ) ) {
				$rule = $this->buildPackageRule( $row );
			} elseif ( ( ! isset( $row['product_adjustments']['total']['value'] ) || $row['product_adjustments']['total']['value'] === "" ) ||
			           ( ! isset( $row['bulk_adjustments']['ranges'] ) || count( $row['bulk_adjustments']['ranges'] ) == 0 ) ||
			           ( ! isset( $row['role_discounts']['rows'] ) || count( $row['role_discounts']['rows'] ) == 0 )
			) {
				$rule   = $this->buildSingleItemRule( $row );
				$filter = new Filter();
				$filter->setType( $filter::TYPE_ANY );
				$filter->setMethod( $filter::METHOD_IN_LIST );
				$rule->addFilter( $filter );
			} else {
				$rule = $this->buildNoItemRule( $row );
			}

			if ( isset( $GLOBALS['sitepress'] ) && $sitepress = $GLOBALS['sitepress'] ) {
				if ( isset( $GLOBALS['woocommerce_wpml'] ) && $woocommerce_wpml = $GLOBALS['woocommerce_wpml'] && isset( $woocommerce_wpml->multi_currency ) ) {
					$currency = $woocommerce_wpml->multi_currency->get_client_currency();
					$rate     = $woocommerce_wpml->multi_currency->exchange_rate_services->get_currency_rate( $currency );
					if ( $rate ) {
						$rule = RuleTranslator::setCurrency( $rule, $rate );
					}
				}
				$rule = RuleTranslator::translate( $rule, $sitepress->get_current_language() );
			}

			if ( $this->woocsCmp->isActive() ) {
				$rule = RuleTranslator::setCurrency( $rule, $this->woocsCmp->getRate() );
			}

			$rules[] = apply_filters( 'adp_rule_loaded', $rule, $row );
		}

		return new RulesCollection( $rules );
	}

	protected function buildNoItemRule( $ruleData ) {
		/** @var NoItemRule $rule */
		$rule = Factory::get( "Rule_Structures_NoItemRule" );

		if ( isset( $ruleData['id'] ) ) {
			$rule->setId( $ruleData['id'] );
		}

		$rule->setTitle( $ruleData['title'] );
		$rule->setEnabled( $ruleData['enabled'] );
		$rule->setPriority( $ruleData['priority'] );
		if ( isset( $ruleData['additional']['conditions_relationship'] ) ) {
			$rule->setConditionsRelationship( $ruleData['additional']['conditions_relationship'] );
		}

		$this->installCartAdjustments( $rule, $ruleData );
		$this->installConditions( $rule, $ruleData );
		$this->installLimits( $rule, $ruleData );

		return $rule;
	}

	protected function buildPackageRule( $ruleData ) {
		$context = $this->context;
		/** @var PackageRule $rule */
		$rule = Factory::get( "Rule_Structures_PackageRule" );

		if ( isset( $ruleData['id'] ) ) {
			$rule->setId( $ruleData['id'] );
		}

		$rule->setTitle( $ruleData['title'] );
		$rule->setEnabled( $ruleData['enabled'] );
		$rule->setPriority( $ruleData['priority'] );
		if ( isset( $ruleData['additional']['conditions_relationship'] ) ) {
			$rule->setConditionsRelationship( $ruleData['additional']['conditions_relationship'] );
		}

		if ( isset( $ruleData['options']['repeat'] ) ) {
			$rule->setPackagesCountLimit( $ruleData['options']['repeat'] );
		}

		if ( isset( $ruleData['options']['apply_to'] ) ) {
			$rule->setApplyFirstTo( $ruleData['options']['apply_to'] );
		}

		foreach ( $ruleData['filters'] as $filterData ) {
			$item = $this->createRulePackage( $filterData );
			$rule->addPackage( $item );
		}

		$this->installProductAdjustment( $rule, $ruleData );
		$this->installRoleDiscounts( $rule, $ruleData );
		$this->installSortableProperties( $rule, $ruleData );

		$this->installFreeItems( $rule, $ruleData );

		$this->installCartAdjustments( $rule, $ruleData );
		$this->installConditions( $rule, $ruleData );
		$this->installLimits( $rule, $ruleData );

		return $rule;
	}

	/**
	 * @param array $filterData
	 *
	 * @return PackageItem
	 */
	protected function createRulePackage( $filterData ) {
		$context = $this->context;

		$type   = $filterData['type'];
		$method = $filterData['method'];
		$value  = $filterData['value'];
		$qty    = $filterData['qty'];

		$item = new PackageItem();
		$item->setQty( $qty );
		$item->setQtyEnd( $qty );

		$filter = new PackageItemFilter();
		$filter->setType( $type );
		$filter->setMethod( $method );
		$filter->setValue( $value );

		if ( isset( $filterData['product_exclude']['values'] ) ) {
			$filter->setExcludeProductIds( $filterData['product_exclude']['values'] );
		}

		if ( isset( $filterData['product_exclude']['on_wc_sale'] ) ) {
			$filter->setExcludeWcOnSale( $filterData['product_exclude']['on_wc_sale'] === "1" );
		}

		if ( isset( $filterData['product_exclude']['already_affected'] ) ) {
			$filter->setExcludeAlreadyAffected( $filterData['product_exclude']['already_affected'] === "1" );
		}

		if ( isset( $filterData['limitation'] ) ) {
			$item->setLimitation( $filterData['limitation'] );
		}

		$item->addFilter( $filter );

		return $item;
	}

	protected function buildSingleItemRule( $ruleData ) {
		$context = $this->context;
		/** @var SingleItemRule $rule */
		$rule = Factory::get( "Rule_Structures_SingleItemRule" );


		if ( isset( $ruleData['id'] ) ) {
			$rule->setId( $ruleData['id'] );
		}

		$rule->setTitle( $ruleData['title'] );
		$rule->setEnabled( $ruleData['enabled'] );
		$rule->setPriority( $ruleData['priority'] );

		if ( isset( $ruleData['additional']['conditions_relationship'] ) ) {
			$rule->setConditionsRelationship( $ruleData['additional']['conditions_relationship'] );
		}

		if ( isset( $ruleData['options']['repeat'] ) ) {
			$rule->setItemsCountLimit( $ruleData['options']['repeat'] );
		}

		if ( isset( $ruleData['options']['apply_to'] ) ) {
			$rule->setApplyFirstTo( $ruleData['options']['apply_to'] );
		}

		foreach ( $ruleData['filters'] as $filterData ) {
			$type   = $filterData['type'];
			$method = $filterData['method'];
			$value  = $filterData['value'];

			$filter = new Filter();
			$filter->setType( $type );
			$filter->setMethod( $method );
			$filter->setValue( $value );

			if ( isset( $filterData['product_exclude']['values'] ) ) {
				$filter->setExcludeProductIds( $filterData['product_exclude']['values'] );
			}

			if ( isset( $filterData['product_exclude']['on_wc_sale'] ) ) {
				$filter->setExcludeWcOnSale( $filterData['product_exclude']['on_wc_sale'] === "1" );
			}

			if ( isset( $filterData['product_exclude']['already_affected'] ) ) {
				$filter->setExcludeAlreadyAffected( $filterData['product_exclude']['already_affected'] === "1" );
			}

			$rule->addFilter( $filter );
		}

		$this->installProductAdjustment( $rule, $ruleData );
		$this->installRoleDiscounts( $rule, $ruleData );
		$this->installSortableProperties( $rule, $ruleData );

		$this->installFreeItems( $rule, $ruleData );

		$this->installCartAdjustments( $rule, $ruleData );
		$this->installConditions( $rule, $ruleData );
		$this->installLimits( $rule, $ruleData );

		return $rule;
	}

	/**
	 * @param Rule  $rule
	 * @param array $ruleData
	 *
	 * @throws Exception
	 */
	protected function installProductAdjustment( &$rule, $ruleData ) {
		$replaceDiscount     = isset( $ruleData['additional']['is_replace'] ) ? $ruleData['additional']['is_replace'] === 'on' : false;
		$replaceDiscountName = isset( $ruleData['additional']['replace_name'] ) ? $ruleData['additional']['replace_name'] : "";

		if ( isset( $ruleData['bulk_adjustments']['ranges'] ) ) {//check rule for having bulk adj
			$bulkData = $ruleData['bulk_adjustments'];

			$qty_based = $bulkData['qty_based'];

			if ( $rule instanceof SingleItemRule ) {
				if ( $qty_based === 'all' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_DEFAULT;
				} elseif ( $qty_based === 'total_qty_in_cart' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_ALL_ITEMS_IN_CART;
				} elseif ( $qty_based === 'product_categories' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_PRODUCT_CATEGORIES;
				} elseif ( $qty_based === 'product_selected_categories' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_PRODUCT_SELECTED_CATEGORIES;
				} elseif ( $qty_based === 'selected_products' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_PRODUCT_SELECTED_PRODUCTS;
				} elseif ( $qty_based === 'sets' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_DEFAULT;
				} elseif ( $qty_based === 'product' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_PRODUCT;
				} elseif ( $qty_based === 'variation' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_VARIATION;
				} elseif ( $qty_based === 'cart_position' ) {
					$qty_based = SingleItemRule\ProductsRangeAdjustments::GROUP_BY_CART_POSITIONS;
				}

				$productAdjustment = new SingleItemRule\ProductsRangeAdjustments( $this->context, $bulkData['type'], $qty_based );
			} elseif ( $rule instanceof PackageRule ) {
				if ( $qty_based === 'all' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_DEFAULT;
				} elseif ( $qty_based === 'total_qty_in_cart' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_ALL_ITEMS_IN_CART;
				} elseif ( $qty_based === 'product_categories' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_PRODUCT_CATEGORIES;
				} elseif ( $qty_based === 'product_selected_categories' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_PRODUCT_SELECTED_CATEGORIES;
				} elseif ( $qty_based === 'selected_products' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_PRODUCT_SELECTED_PRODUCTS;
				} elseif ( $qty_based === 'sets' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_SETS;
				} elseif ( $qty_based === 'product' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_PRODUCT;
				} elseif ( $qty_based === 'variation' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_VARIATION;
				} elseif ( $qty_based === 'cart_position' ) {
					$qty_based = PackageRule\PackageRangeAdjustments::GROUP_BY_CART_POSITIONS;
				}

				$productAdjustment = new PackageRule\PackageRangeAdjustments( $this->context, $bulkData['type'], $qty_based );
			} else {
				return;
			}

			if ( isset( $bulkData['selected_products'] ) && is_array( $bulkData['selected_products'] ) ) {
				$productAdjustment->setSelectedProductIds( $bulkData['selected_products'] );
			}

			if ( isset( $bulkData['selected_categories'] ) && is_array( $bulkData['selected_categories'] ) ) {
				$productAdjustment->setSelectedCategoryIds( $bulkData['selected_categories'] );
			}

			$rangeDiscounts    = array();
			foreach ( $bulkData['ranges'] as $range ) {
				if ( $productAdjustment instanceof SingleItemRule\ProductsRangeAdjustments ) {
					$bulkData['discount_type'] = str_replace( 'set_', '', $bulkData['discount_type'] );
					$type                      = 'percentage';
					if ( $bulkData['discount_type'] === 'price__fixed' ) {
						$type = Discount::TYPE_FIXED_VALUE;
					} elseif ( $bulkData['discount_type'] === 'discount__amount' ) {
						$type = Discount::TYPE_AMOUNT;
					}

					$discount = new Discount( $this->context, $type, $range['value'] );
				} elseif ( $productAdjustment instanceof PackageRule\PackageRangeAdjustments ) {
					if ( strpos( $bulkData['discount_type'], 'set' ) === false ) {
						$type = 'percentage';
						if ( $bulkData['discount_type'] === 'price__fixed' ) {
							$type = Discount::TYPE_FIXED_VALUE;
						} elseif ( $bulkData['discount_type'] === 'discount__amount' ) {
							$type = Discount::TYPE_AMOUNT;
						}

						$discount = new Discount( $this->context, $type, $range['value'] );
					} else {
						$type = 'percentage';
						if ( $bulkData['discount_type'] === 'set_price__fixed' ) {
							$type = SetDiscount::TYPE_FIXED_VALUE;
						} elseif ( $bulkData['discount_type'] === 'set_discount__amount' ) {
							$type = SetDiscount::TYPE_AMOUNT;
						}

						$discount = new SetDiscount( $this->context, $type, $range['value'] );
					}
				} else {
					return;
				}

				$rangeDiscounts[] = new RangeDiscount( $range['from'], $range['to'], $discount );
			}
			$productAdjustment->setRanges( $rangeDiscounts );

			$productAdjustment->setReplaceAsCartAdjustment( $replaceDiscount );
			$productAdjustment->setReplaceCartAdjustmentCode( $replaceDiscountName );

			if ( isset( $bulkData['table_message'] ) ) {
				$productAdjustment->setPromotionalMessage( $bulkData['table_message'] );
			}

			$rule->installProductRangeAdjustmentHandler( $productAdjustment );
		}

		if ( isset( $ruleData['product_adjustments'], $ruleData['product_adjustments']['type'] ) ) {
			$prodAdjData = $ruleData['product_adjustments'];
			$type        = $prodAdjData['type'];

			if ( $type === 'total' AND isset( $prodAdjData['total']['type'] ) ) {//check rule for having total adj
				$value  = $prodAdjData['total']['value'];
				$discountType = $prodAdjData[ 'total' ]['type'];

				if ( $discountType === 'price__fixed' ) {
					$discount = new Discount( $this->context, Discount::TYPE_FIXED_VALUE, $value );
				} elseif ( $discountType === 'discount__percentage' ) {
					$discount = new Discount( $this->context, Discount::TYPE_PERCENTAGE, $value );
				} elseif ( $discountType === 'discount__amount' ) {
					$discount = new Discount( $this->context, Discount::TYPE_AMOUNT, $value );
				} else {
					return;
				}

				if ( $rule instanceof SingleItemRule ) {
					$productAdjustment = new SingleItemRule\ProductsAdjustment( $discount );
				} elseif ( $rule instanceof PackageRule ) {
					$productAdjustment = new PackageRule\ProductsAdjustmentTotal( $discount );
				} else {
					return;
				}

				$productAdjustment->setReplaceAsCartAdjustment( $replaceDiscount );
				$productAdjustment->setReplaceCartAdjustmentCode( $replaceDiscountName );

				$rule->installProductAdjustmentHandler( $productAdjustment );
			} elseif ( $type === 'split' AND isset( $prodAdjData[ 'split' ][0]['type'] ) ) {//check rule for having split adj
				if ( $rule instanceof SingleItemRule ) {
					return;
				}

				$discounts = array();
				foreach ( $prodAdjData[ $type ] as $split_discount ) {
					$discount = new Discount( $this->context, Discount::TYPE_AMOUNT, $split_discount['value'] );
					if ( $split_discount['type'] === 'price__fixed' ) {
						$discount->setType( Discount::TYPE_FIXED_VALUE );
					} elseif ( $split_discount['type'] === 'discount__percentage' ) {
						$discount->settype( Discount::TYPE_PERCENTAGE );
					}
					$discounts[] = $discount;
				}

				$productAdjustment = new PackageRule\ProductsAdjustmentSplit( $discounts );
				$productAdjustment->setReplaceAsCartAdjustment( $replaceDiscount );
				$productAdjustment->setReplaceCartAdjustmentCode( $replaceDiscountName );

				$rule->installProductAdjustmentHandler( $productAdjustment );
			}
		}
	}

	/**
	 * @param SingleItemRule|PackageRule $rule
	 * @param array                      $ruleData
	 */
	protected function installRoleDiscounts( &$rule, $ruleData ) {
		if ( ! isset( $ruleData['role_discounts']['rows'] ) ) {
			return;
		}

		$roleDiscounts = array();
		foreach ( $ruleData['role_discounts']['rows'] as $row ) {
			$type  = isset( $row['discount_type'] ) ? $row['discount_type'] : null;
			$value = isset( $row['discount_value'] ) ? $row['discount_value'] : null;
			$roles = isset( $row['roles'] ) ? $row['roles'] : array();

			if ( ! isset( $type, $value ) ) {
				continue;
			}

			if ( $type === 'discount__percentage' ) {
				$type = Discount::TYPE_PERCENTAGE;
			} elseif ( $type === 'discount__amount' ) {
				$type = Discount::TYPE_AMOUNT;
			} elseif ( $type === 'price__fixed' ) {
				$type = Discount::TYPE_FIXED_VALUE;
			}

			$roleDiscount = new RoleDiscount( new Discount( $this->context, $type, $value ) );
			$roleDiscount->setRoles( $roles );
			$roleDiscounts[] = $roleDiscount;
		}

		$rule->setRoleDiscounts( $roleDiscounts );
	}

	/**
	 * @param Rule  $rule
	 * @param array $ruleData
	 */
	protected function installConditions( &$rule, $ruleData ) {
		$converter        = $this->optionsConverter;
		$conditionsLoader = $this->conditionsLoader;

		if ( ! empty( $ruleData[ $conditionsLoader::KEY ] ) ) {
			foreach ( $ruleData[ $conditionsLoader::KEY ] as $conditionData ) {
				try {
					$conditionData = $converter::convertCondition( $conditionData );
					$rule->addCondition( $conditionsLoader->build( $conditionData ) );
				} catch ( Exception $exception ) {
					$this->context->handle_error( $exception );
				}
			}
		}
	}

	/**
	 * @param Rule  $rule
	 * @param array $ruleData
	 */
	protected function installLimits( &$rule, $ruleData ) {
		$converter    = $this->optionsConverter;
		$limitsLoader = $this->limitsLoader;

		if ( ! empty( $ruleData[ $limitsLoader::KEY ] ) ) {
			foreach ( $ruleData[ $limitsLoader::KEY ] as $limitData ) {
				try {
					$limitData = $converter::convertLimit( $limitData );
					$rule->addLimit( $limitsLoader->build( $limitData ) );
				} catch ( Exception $exception ) {
					$this->context->handle_error( $exception );
				}
			}
		}
	}

	/**
	 * @param Rule  $rule
	 * @param array $ruleData
	 */
	protected function installCartAdjustments( &$rule, $ruleData ) {
		$converter     = $this->optionsConverter;
		$cartAdjLoader = $this->cartAdjLoader;

		if ( ! empty( $ruleData[ $cartAdjLoader::KEY ] ) ) {
			foreach ( $ruleData[ $cartAdjLoader::KEY ] as $cartAdjData ) {
				try {
					$cartAdjData = $converter::convertCartAdj( $cartAdjData );
					$rule->addCartAdjustment( $cartAdjLoader->build( $cartAdjData ) );
				} catch ( Exception $exception ) {
					$this->context->handle_error( $exception );
				}
			}
		}
	}

	/**
	 * @param SingleItemRule|PackageRule  $rule
	 * @param array $ruleData
	 */
	protected function installFreeItems( &$rule, $ruleData ) {
		$context = $this->context;

		$replaceFreeProducts = isset( $ruleData['additional']['is_replace_free_products_with_discount'] ) ? $ruleData['additional']['is_replace_free_products_with_discount'] === 'on' : false;
		$rule->setReplaceItemGifts( $replaceFreeProducts );
		$replaceFreeProductsName = isset( $ruleData['additional']['free_products_replace_name'] ) ? $ruleData['additional']['free_products_replace_name'] : "";
		$rule->setReplaceItemGiftsCode( $replaceFreeProductsName );

		if ( isset( $ruleData['get_products']['repeat'] ) ) {
			$repeat = $ruleData['get_products']['repeat'];

			if ( $repeat === 'based_on_subtotal' ) {
				$rule->setItemGiftStrategy( $rule::BASED_ON_SUBTOTAL_ITEM_GIFT_STRATEGY );
				if ( isset( $ruleData['get_products']['repeat_subtotal'] ) ) {
					$rule->setItemGiftSubtotalDivider( $ruleData['get_products']['repeat_subtotal'] );
				}
			} elseif ( is_numeric( $repeat ) ) {
				$rule->setItemGiftStrategy( $rule::BASED_ON_LIMIT_ITEM_GIFT_STRATEGY );
				$attemptCount = (int) $ruleData['get_products']['repeat'];
				$attemptCount = $attemptCount !== - 1 ? $attemptCount : INF;
				$rule->setItemGiftLimit( $attemptCount );
			}
		}

		$values = isset( $ruleData['get_products']['value'] ) ? $ruleData['get_products']['value'] : array();
		$gifts  = array();
		foreach ( $values as $value ) {
			$qty        = floatval( $value['qty'] );
			$productIds = isset( $value['value'] ) ? $value['value'] : array();

			$useProductFromFilter = isset( $value['use_product_from_filter'] ) ? $value['use_product_from_filter'] === 'on' : false;
			$type                 = $value['type']; // giftable_products

			if ( $useProductFromFilter ) {
				$gift = new Gift( $context, Gift::TYPE_CLONE_ADJUSTED );
			} else {
				$gift = new Gift( $context, Gift::TYPE_PRODUCT );
			}

			$gift->setQty( $qty );
			$gift->setValues( $productIds );
			$gifts[] = $gift;
		}
		$rule->setItemGifts( $gifts );
	}

	/**
	 * @param SingleItemRule|PackageRule $rule
	 * @param array                      $ruleData
	 */
	protected function installSortableProperties( &$rule, $ruleData ) {
		if ( isset( $ruleData['sortable_blocks_priority'] ) ) {
			$rule->setSortableBlocksPriority( $ruleData['sortable_blocks_priority'] );
		}

		if ( isset( $ruleData['additional']['sortable_apply_mode'] ) ) {
			$rule->setSortableApplyMode( $ruleData['additional']['sortable_apply_mode'] );
		}

		if ( isset( $ruleData['role_discounts']['dont_apply_bulk_if_roles_matched'] ) ) {
			$rule->setDontApplyBulkIfRolesMatched( $ruleData['role_discounts']['dont_apply_bulk_if_roles_matched'] === "1" );
		}
	}
}

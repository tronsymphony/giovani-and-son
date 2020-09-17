<?php

namespace ADP\BaseVersion\Includes\Cart;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartContext;
use ADP\BaseVersion\Includes\Cart\Structures\Coupon;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\WcTotalsFacade;
use WC_Cart;
use WC_Coupon;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class CartCouponsProcessor {
	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var Coupon[][]
	 */
	protected $groupedCoupons;

	/**
	 * @var Coupon[]
	 */
	protected $singleCoupons;

	/**
	 * @var array
	 */
	protected $readyCouponData;

	/**
	 * @var CartContext
	 */
	protected $cartContext;

	public function __construct( $context ) {
		$this->context = $context;
		$this->purge();
	}

	/**
	 * @param Cart $cart
	 */
	public function refreshCoupons( $cart ) {
		$context = $cart->get_context();
		$this->purge();

		foreach ( $cart->getCoupons() as $coupon ) {
			$coupon = clone $coupon;

			if ( empty( $coupon->getValue() ) || empty( $coupon->getCode() ) ) {
				continue;
			}

			if ( $coupon->isType( Coupon::TYPE_FIXED_VALUE ) ) {
				if ( $context->is_combine_multiple_discounts() ) {
					$coupon->setCode( $context->get_option( 'default_discount_name' ) );
				}
				$this->addGroupCoupon( $coupon );
			} elseif ( $coupon->isType( Coupon::TYPE_PERCENTAGE ) ) {
				$this->addSingleCoupon( $coupon );
			} elseif ( $coupon->isType( Coupon::TYPE_FREE_ITEM_PRICE ) || $coupon->isType( Coupon::TYPE_ITEM_DISCOUNT ) ) {
				$this->addGroupCoupon( $coupon );
			}
		}

		// remove postfix for single %% discount
		if ( count( $this->singleCoupons ) == 1 ) {
			$coupon = reset( $this->singleCoupons );
			$coupon->setCode( str_replace( ' #1', '', $coupon->getCode() ) );
			$this->singleCoupons = array( $coupon->getCode() => $coupon );
		}

		$this->cartContext = $cart->get_context();
	}

	/**
	 * @param WC_Cart $wcCart
	 */
	public function applyCoupons( &$wcCart ) {
		$couponCodesToApply = array_merge( array_keys( $this->groupedCoupons ), array_keys( $this->singleCoupons ) );

		$appliedCoupons = $wcCart->applied_coupons;

		foreach ( $couponCodesToApply as $couponCode ) {
			if ( ! in_array( $couponCode, $appliedCoupons ) ) {
				$appliedCoupons[] = $couponCode;
			}
		}

		$wcCart->applied_coupons = $appliedCoupons;
		$this->prepareCouponsData();
	}

	/**
	 * @param WC_Cart $wcCart
	 */
	public function sanitize( &$wcCart ) {
		$appliedCoupons = $wcCart->applied_coupons;

		foreach ( $appliedCoupons as $index => $couponCode ) {
			if ( isset( $this->readyCouponData[ $couponCode ] ) ) {
				unset( $appliedCoupons[ $index ] );
			}
		}

		$wcCart->applied_coupons = array_values( $appliedCoupons );
		$this->purge();
	}

	public function setFilterToInstallCouponsData() {
		add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'getCouponData' ), 10, 3 );
	}

	public function unsetFilterToInstallCouponsData() {
		remove_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'getCouponData' ), 10 );
	}

	public function setFiltersToSupportPercentLimitCoupon() {
		add_filter( 'woocommerce_coupon_discount_types', array( $this, 'addPercentLimitCouponDiscountType'), 10, 1 );
		add_filter( 'woocommerce_product_coupon_types', array( $this, 'addPercentLimitCouponProductType' ), 10, 1 );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'getPercentLimitCouponDiscountAmount' ), 10, 5 );
		add_filter( 'woocommerce_coupon_custom_discounts_array', array( $this, 'processPercentLimitCoupon' ), 10, 2 );
	}

	public function unsetFiltersToSupportPercentLimitCoupon() {
		remove_filter( 'woocommerce_coupon_discount_types', array( $this, 'addPercentLimitCouponDiscountType'), 10 );
		remove_filter( 'woocommerce_product_coupon_types', array( $this, 'addPercentLimitCouponProductType' ), 10 );
		remove_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'getPercentLimitCouponDiscountAmount' ), 10 );
		remove_filter( 'woocommerce_coupon_custom_discounts_array', array( $this, 'processPercentLimitCoupon' ), 10 );
	}

	/**
	 * This filter allows custom coupon objects to be created on the fly.
	 *
	 * @param false      $couponData
	 * @param mixed      $couponCode Coupon code
	 * @param WC_Coupon $coupon
	 *
	 * @return array|mixed
	 */
	public function getCouponData( $couponData, $couponCode, $coupon ) {
		if ( isset( $this->readyCouponData[ $couponCode ] ) ) {
			$couponData = $this->readyCouponData[ $couponCode ];
			$meta = $couponData['meta'];

			/**
			 * key 'meta'
			 * @see self::prepareCouponsData()
			 */
			foreach ( $meta as $key => $value ) {
				$coupon->add_meta_data( $key, $value, true );
			}

			//support max discount for percentage coupon
			$coupon = isset( $meta['adp']['parts'][0] ) ? $meta['adp']['parts'][0] : null;

			if ( $coupon ) {
				/** @var Coupon $coupon */
				if ( $couponData['discount_type'] === 'percent' && $coupon->isMaxDiscountDefined() ) {
					$couponData['discount_type'] = 'wdp_percent_limit_coupon';
				}
			}

			unset( $couponData['meta'] );
		}

		return $couponData;
	}

	public function addPercentLimitCouponDiscountType ( $discount_types ) {
		$discount_types['wdp_percent_limit_coupon'] = __( 'WDP Coupon', 'advanced-dynamic-pricing-for-woocommerce' );

		return $discount_types;
	}

	public function addPercentLimitCouponProductType ( $discount_types ) {
		$discount_types[] = 'wdp_percent_limit_coupon';

		return $discount_types;
	}

	public function getPercentLimitCouponDiscountAmount ( $discount_amount, $discounting_amount, $cart_item, $single, $coupon ) {
		/**
		 * @var WC_Coupon $coupon
		 */
		if ( $coupon->get_discount_type() === 'wdp_percent_limit_coupon' ) {
			$discount_amount = (float) $coupon->get_amount() * ( $discounting_amount / 100 );
		}

		return $discount_amount;
	}

	public function processPercentLimitCoupon ( $coupon_discounts, $coupon ) {
		/**
		 * @var WC_Coupon $coupon
		 */
		if ( $coupon->get_discount_type() === 'wdp_percent_limit_coupon' ) {
			$coupon_code     = $coupon->get_code();
			$wdp_coupon      = $this->singleCoupons[ $coupon_code ];
			$discount_amount = array_sum( $coupon_discounts );
			
			$max_discount    = $wdp_coupon->getMaxDiscount() * pow(10, wc_get_price_decimals());
			if ( $discount_amount > $max_discount ) {
				$item_discount = round( (float) $max_discount / count( $coupon_discounts ) );
				$k = 0;
				foreach ( $coupon_discounts as $key => $discount ) {
					if( $k >= count( $coupon_discounts ) - 1 ) {
						$coupon_discounts[ $key ] = $max_discount - $item_discount * $k;
						break;
					}
					$coupon_discounts[ $key ] = $item_discount;
					$k++;
				}
			}
		}

		return $coupon_discounts;
	}

	/**
	 * @param WC_Cart $wcCart
	 */
	public function updateTotals( $wcCart ) {
		$globalContext = $this->cartContext->getGlobalContext();
		$totalsWrapper = new WcTotalsFacade( $globalContext, $wcCart );
		$totalsWrapper->insertCouponsData( $this->groupedCoupons, $this->singleCoupons );
	}

	protected function prepareCouponsData() {
		$groupedCoupons = $this->groupedCoupons;
		$singleCoupons  = $this->singleCoupons;

		foreach ( $groupedCoupons as $couponCode => $coupons ) {
			$amount = floatval( 0 );

			$appliedCoupons = array();
			foreach ( $coupons as $coupon ) {
				if ( $coupon->isType( $coupon::TYPE_FIXED_VALUE ) || $coupon->isType( $coupon::TYPE_ITEM_DISCOUNT ) || $coupon->isType( $coupon::TYPE_FREE_ITEM_PRICE ) ) {
					$amount           += $coupon->getValue();
					$appliedCoupons[] = $coupon;
				}
			}

			if ( $amount > 0 ) {
				$this->addReadyCouponData( $couponCode, 'fixed_cart', $amount, $appliedCoupons );
			}
		}

		foreach ( $singleCoupons as $coupon ) {
			$coupon_type = $coupon->isType( $coupon::TYPE_PERCENTAGE ) ? 'percent' : 'fixed_cart';

			$this->addReadyCouponData( $coupon->getCode(), $coupon_type, $coupon->getValue(), array( $coupon ) );
		}
	}

	protected function addReadyCouponData( $code, $type, $amount, $parts ) {
		if ( isset( $this->readyCouponData[ $code ] ) ) {
			return;
		}

		$args = array(
			'discount_type' => $type,
			'amount'        => $amount,
			'meta'          => array(
				'adp' => array(
					'parts' => $parts,
				)
			),
		);

		/**
		 * using key 'meta' at
		 * @see self::getCouponData()
		 */

		$this->readyCouponData[ $code ] = $args;
	}

	/**
	 * @param Coupon $coupon
	 */
	protected function addGroupCoupon( $coupon ) {
		if ( ! isset( $this->groupedCoupons[ $coupon->getCode() ] ) ) {
			$this->groupedCoupons[ $coupon->getCode() ] = array();
		}

		$this->groupedCoupons[ $coupon->getCode() ][] = $coupon;
	}

	/**
	 * @param Coupon $coupon
	 */
	protected function addSingleCoupon( $coupon ) {
		$template = $coupon->getCode();

		$count = 1;
		do {
			$couponCode = "{$template} #{$count}";
			$count ++;
		} while ( isset( $this->singleCoupons[ $couponCode ] ) );

		$coupon->setCode( $couponCode );
		$this->singleCoupons[ $coupon->getCode() ] = $coupon;
	}

	protected function purge() {
		$this->groupedCoupons  = array();
		$this->singleCoupons   = array();
		$this->readyCouponData = array();
	}
}

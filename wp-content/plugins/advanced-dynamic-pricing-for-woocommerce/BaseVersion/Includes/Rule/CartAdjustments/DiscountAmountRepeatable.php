<?php

namespace ADP\BaseVersion\Includes\Rule\CartAdjustments;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItemsCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartSetCollection;
use ADP\BaseVersion\Includes\Cart\Structures\Coupon;
use ADP\BaseVersion\Includes\Rule\CartAdjustmentsLoader;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustment;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments\CartAdjUsingCollection;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments\CouponCartAdj;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class DiscountAmountRepeatable extends AbstractCartAdjustment implements CouponCartAdj, CartAdjustment, CartAdjUsingCollection {
	/**
	 * @var float
	 */
	protected $coupon_value;

	/**
	 * @var string
	 */
	protected $coupon_code;

	public static function getType() {
		return 'discount_repeatable__amount';
	}

	public static function getLabel() {
		return __( 'Add fixed discount on each rule execution', 'advanced-dynamic-pricing-for-woocommerce' );
	}

	public static function getTemplatePath() {
		return WC_ADP_PLUGIN_VIEWS_PATH . 'cart_adjustments/discount.php';
	}

	public static function getGroup() {
		return CartAdjustmentsLoader::GROUP_DISCOUNT;
	}

	public function __construct() {
		$this->amount_indexes = array( 'coupon_value' );
	}

	/**
	 * @param float $coupon_value
	 */
	public function setCouponValue( $coupon_value ) {
		$this->coupon_value = $coupon_value;
	}

	/**
	 * @param string $coupon_code
	 */
	public function setCouponCode( $coupon_code ) {
		$this->coupon_code = $coupon_code;
	}

	public function getCouponValue()
	{
		return $this->coupon_value;
	}

	public function getCouponCode()
	{
		return $this->coupon_code;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return isset( $this->coupon_value ) OR isset( $this->coupon_code );
	}

	/**
	 * @param Rule $rule
	 * @param Cart $cart
	 */
	public function applyToCart( $rule, $cart ) {
	}

	/**
	 * @param Rule $rule
	 * @param Cart $cart
	 * @param CartItemsCollection $itemsCollection
	 */
	public function applyToCartWithItems( $rule, $cart, $itemsCollection ) {
		$context = $cart->get_context()->getGlobalContext();

		for( $i = 0; $i < $itemsCollection->get_total_qty(); $i++ ) {
			$cart->addCoupon( new Coupon( $context, Coupon::TYPE_FIXED_VALUE, $this->coupon_code, $this->coupon_value, $rule->getId() ) );
		}
	}

	/**
	 * @param Rule $rule
	 * @param Cart $cart
	 * @param CartSetCollection $setCollection
	 */
	public function applyToCartWithSets( $rule, $cart, $setCollection ) {
		$context = $cart->get_context()->getGlobalContext();

		for( $i = 0; $i < $setCollection->get_total_sets_qty(); $i++ ) {
			$cart->addCoupon( new Coupon( $context, Coupon::TYPE_FIXED_VALUE, $this->coupon_code, $this->coupon_value, $rule->getId() ) );
		}
	}
}

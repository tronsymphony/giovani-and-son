<?php

namespace ADP\BaseVersion\Includes\Rule\CartAdjustments;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItemsCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartSetCollection;
use ADP\BaseVersion\Includes\Cart\Structures\Fee;
use ADP\BaseVersion\Includes\Rule\CartAdjustmentsLoader;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustment;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments\CartAdjUsingCollection;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments\FeeCartAdj;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FeeAmountRepeatable extends AbstractCartAdjustment implements FeeCartAdj, CartAdjustment, CartAdjUsingCollection {
	/**
	 * @var float
	 */
	protected $fee_value;

	/**
	 * @var string
	 */
	protected $fee_name;

	/**
	 * @var string
	 */
	protected $fee_tax_class;

	public static function getType() {
		return 'fee_repeatable__amount';
	}

	public static function getLabel() {
		return __( 'Add fixed fee on each rule execution', 'advanced-dynamic-pricing-for-woocommerce' );
	}

	public static function getTemplatePath() {
		return WC_ADP_PLUGIN_VIEWS_PATH . 'cart_adjustments/fee.php';
	}

	public static function getGroup() {
		return CartAdjustmentsLoader::GROUP_FEE;
	}

	public function __construct() {
		$this->amount_indexes = array( 'fee_value' );
	}

	/**
	 * @param float $fee_value
	 */
	public function setFeeValue( $fee_value ) {
		$this->fee_value = $fee_value;
	}

	/**
	 * @param string $fee_name
	 */
	public function setFeeName( $fee_name ) {
		$this->fee_name = $fee_name;
	}

	/**
	 * @param string $fee_tax_class
	 */
	public function setFeeTaxClass( $fee_tax_class ) {
		$this->fee_tax_class = $fee_tax_class;
	}

	public function getFeeValue()
	{
		return $this->fee_value;
	}

	public function getFeeName()
	{
		return $this->fee_name;
	}

	public function getFeeTaxClass()
	{
		return $this->fee_tax_class;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return isset( $this->fee_value ) OR isset( $this->fee_name ) OR isset( $this->fee_tax_class );
	}

	/**
	 * @param Rule $rule
	 * @param Cart $cart
	 */
	public function applyToCart( $rule, $cart ) {
	}

	/**
	 * @param Rule                $rule
	 * @param Cart                $cart
	 * @param CartItemsCollection $itemsCollection
	 *
	 * @return bool
	 * @return bool
	 */
	public function applyToCartWithItems( $rule, $cart, $itemsCollection ) {
		$context = $cart->get_context()->getGlobalContext();
		$tax_class = ! empty( $this->fee_tax_class ) ? $this->fee_tax_class : "";

		for( $i = 0; $i < $itemsCollection->get_total_qty(); $i++ ) {
			$cart->addFee( new Fee( $context, Fee::TYPE_FIXED_VALUE, $this->fee_name, $this->fee_value, $tax_class, $rule->getId() ) );
		}

		return true;
	}

	/**
	 * @param Rule              $rule
	 * @param Cart              $cart
	 * @param CartSetCollection $setCollection
	 *
	 * @return bool
	 * @return bool
	 */
	public function applyToCartWithSets( $rule, $cart, $setCollection ) {
		$context = $cart->get_context()->getGlobalContext();
		$tax_class = ! empty( $this->fee_tax_class ) ? $this->fee_tax_class : "";

		for( $i = 0; $i < $setCollection->get_total_sets_qty(); $i++ ) {
			$cart->addFee( new Fee( $context, Fee::TYPE_FIXED_VALUE, $this->fee_name, $this->fee_value, $tax_class, $rule->getId() ) );
		}

		return true;
	}
}

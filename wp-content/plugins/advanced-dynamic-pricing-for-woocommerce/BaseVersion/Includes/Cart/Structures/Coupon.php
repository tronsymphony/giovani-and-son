<?php

namespace ADP\BaseVersion\Includes\Cart\Structures;

use ADP\BaseVersion\Includes\Context;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Coupon {
	const TYPE_ITEM_DISCOUNT = 'item';
	const TYPE_FREE_ITEM_PRICE = 'free_item';
	const TYPE_PERCENTAGE = 'percentage';
	const TYPE_FIXED_VALUE = 'fixed_value';

	const AVAILABLE_TYPES = array(
		self::TYPE_ITEM_DISCOUNT,
		self::TYPE_FREE_ITEM_PRICE,
		self::TYPE_PERCENTAGE,
		self::TYPE_FIXED_VALUE,
	);

	/**
	 * @var integer
	 */
	protected $ruleId;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var float
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $code;

	/**
	 * @var float
	 */
	protected $maxDiscount;

	/**
	 * @param Context $context
	 * @param string  $type
	 * @param string  $code
	 * @param float   $value
	 * @param integer $ruleId
	 */
	public function __construct( $context, $type, $code, $value, $ruleId ) {
		if ( ! in_array( $type, self::AVAILABLE_TYPES ) ) {
			$context->handle_error( new Exception( sprintf( "Coupon type '%s' not supported", $type ) ) );
		}

		$this->type   = $type;
		$this->code   = wc_format_coupon_code( $code );   // TODO apply wc_format_coupon_code?
		$this->value  = floatval( $value );
		$this->ruleId = $ruleId;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public function isType( $type ) {
		return $this->type === $type;
	}

	/**
	 * @param string $code
	 */
	public function setCode( $code ) {
		$this->code = (string) $code;
	}

	/**
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @param float $value
	 */
	public function setValue( $value ) {
		$this->value = floatval( $value );
	}

	/**
	 * @return float
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @return integer
	 */
	public function getRuleId() {
		return $this->ruleId;
	}

	/**
	 * @param float $amount
	 */
	public function setMaxDiscount( $amount ) {
		$this->maxDiscount = $amount;
	}

	/**
	 * @return float
	 */
	public function getMaxDiscount() {
		return $this->maxDiscount;
	}

	/**
	 * @return bool
	 */
	public function isMaxDiscountDefined() {
		return isset( $this->maxDiscount ) && $this->maxDiscount > 0;
	}
}

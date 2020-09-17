<?php

namespace ADP\BaseVersion\Includes\Rule\Structures;

class RoleDiscount {
	const AVAILABLE_DISCOUNT_TYPES = array(
		Discount::TYPE_AMOUNT,
		Discount::TYPE_FIXED_VALUE,
		Discount::TYPE_PERCENTAGE,
	);

	/**
	 * @var Discount
	 */
	protected $discount;

	/**
	 * @var string
	 */
	protected $roles;

	/**
	 * @param Discount $discount
	 */
	public function __construct( $discount ) {
		if ( $discount instanceof Discount && in_array( $discount->getType(), self::AVAILABLE_DISCOUNT_TYPES ) ) {
			$this->discount = $discount;
		}
		$this->roles = array();
	}

	/**
	 * @param string $roles
	 *
	 * @return RoleDiscount
	 */
	public function setRoles( $roles ) {
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 * @param Discount $discount
	 */
	public function setDiscount( $discount ) {
		$this->discount = $discount;
	}

	/**
	 * @return Discount
	 */
	public function getDiscount() {
		return $this->discount;
	}

	public function isReplaceWithCartAdjustment() {
		return false;
	}
}

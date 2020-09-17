<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItemsCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartSetCollection;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class RoleDiscountStrategy {
	/**
	 * @var SingleItemRule|PackageRule
	 */
	protected $rule;

	/**
	 * @param SingleItemRule|PackageRule $rule
	 */
	public function __construct( $rule ) {
		$this->rule = $rule;
	}

	/**
	 * @param Cart                $cart
	 * @param CartItemsCollection $collection
	 */
	public function processItems( &$cart, &$collection ) {
		$roleDiscounts = $this->rule->getRoleDiscounts();

		if ( ! $roleDiscounts ) {
			return;
		}

		if ( ! ( $currentUserRoles = $cart->get_context()->getCustomer()->getRoles() ) ) {
			return;
		}

		foreach ( $roleDiscounts as $roleDiscount ) {
			if ( ! count( array_intersect( $roleDiscount->getRoles(), $currentUserRoles ) ) ) {
				continue;
			}

			if ( ! $roleDiscount->getDiscount() ) {
				continue;
			}

			$priceCalculator = new PriceCalculator( $this->rule, $roleDiscount->getDiscount() );

			foreach ( $collection->get_items() as &$item ) {
				$priceCalculator->applyItemDiscount( $item, $cart, $roleDiscount );
			}
		}
	}

	/**
	 * @param Cart              $cart
	 * @param CartSetCollection $collection
	 */
	public function processSets( &$cart, &$collection ) {
		$roleDiscounts = $this->rule->getRoleDiscounts();

		if ( ! $roleDiscounts ) {
			return;
		}

		if ( ! ( $currentUserRoles = $cart->get_context()->getCustomer()->getRoles() ) ) {
			return;
		}

		foreach ( $roleDiscounts as $roleDiscount ) {
			if ( ! count( array_intersect( $roleDiscount->getRoles(), $currentUserRoles ) ) ) {
				continue;
			}

			$priceCalculator = new PriceCalculator( $this->rule, $roleDiscount->getDiscount() );
			foreach ( $collection->get_sets() as $set ) {
				$priceCalculator->calculatePriceForSet( $set, $cart, $roleDiscount );
			}
		}
	}

}
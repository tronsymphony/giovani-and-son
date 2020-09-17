<?php

namespace ADP\BaseVersion\Includes\Cart;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartContext;
use ADP\BaseVersion\Includes\Cart\Structures\ShippingAdjustment;
use ADP\BaseVersion\Includes\External\WC\WcShippingRateFacade;
use ADP\BaseVersion\Includes\External\WC\WcTotalsFacade;
use WC_Cart;
use WC_Shipping_Rate;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class CartShippingProcessor {
	/**
	 * @var ShippingAdjustment[]
	 */
	protected $adjustments = array();

	/**
	 * @var CartContext
	 */
	protected $cartContext;

	public function __construct() {
	}

	/**
	 * @param Cart $cart
	 */
	public function refresh( $cart ) {
		$this->cartContext = $cart->get_context();
		$this->adjustments = array();

		foreach ( $cart->getShippingAdjustments() as $adjustment ) {
			/** Free shipping rewrites others */
			if ( $adjustment->isType( $adjustment::TYPE_FREE ) ) {
				$this->adjustments = array( clone $adjustment );

				return;
			}

			$this->adjustments[] = clone $adjustment;
		}
	}

	/**
	 * @param WC_Cart $wcCart
	 */
	public function sanitize( $wcCart ) {
		$this->adjustments = array();
	}

	public function setFilterToEditPackageRates() {
		add_filter( 'woocommerce_package_rates', array( $this, 'packageRates' ), 10, 2 );
	}

	public function unsetFilterToEditPackageRates() {
		remove_filter( 'woocommerce_package_rates', array( $this, 'packageRates' ), 10 );
	}

	public function setFilterToEditShippingMethodLabel() {
		add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'shippingMethodFullLabel' ), 10, 2 );
	}

	public function unsetFilterToEditShippingMethodLabel() {
		remove_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'shippingMethodFullLabel' ), 10 );
	}

	/**
	 * To apply shipping we have to clear stored packages in session to allow 'woocommerce_package_rates' filter run
	 */
	public function purgeCalculatedPackagesInSession() {
		foreach ( WC()->shipping()->get_packages() as $index => $value ) {
			$key = "shipping_for_package_" . $index;
			unset( WC()->session->$key );
		}
	}

	/**
	 * @param WC_Shipping_Rate[] $rates
	 * @param array               $package
	 *
	 * @return WC_Shipping_Rate[]
	 */
	public function packageRates( $rates, $package ) {
		if ( count( $this->adjustments ) < 1 ) {
			return $rates;
		}

		foreach ( $rates as &$rate ) {
			$cost = $rate->get_cost();

			$adjustments = array();

			foreach ( $this->adjustments as $adjustment ) {
				$adjustment = clone $adjustment;

				if ( $adjustment->isType( $adjustment::TYPE_AMOUNT ) ) {
					$amount = $adjustment->getValue();
				} elseif ( $adjustment->isType( $adjustment::TYPE_PERCENTAGE ) ) {
					$amount = $cost * $adjustment->getValue() / 100;
				} elseif ( $adjustment->isType( $adjustment::TYPE_FIXED_VALUE ) ) {
					$amount = $cost - $adjustment->getValue();
				} elseif ( $adjustment->isType( $adjustment::TYPE_FREE ) ) {
					$amount = $cost;
				} else {
					continue;
				}

				$adjustment->setAmount( $amount );
				$adjustments[] = $adjustment;
			}

			$appliedAdjustments = array();
			foreach ( $adjustments as $adjustment ) {
				/** @var ShippingAdjustment $adjustment */

				// maximize discount by default
				$appliedAdjustment = reset( $appliedAdjustments );

				if ( $appliedAdjustment === false || $appliedAdjustment->getAmount() <= $adjustment->getAmount() ) {
					$appliedAdjustments = array( $adjustment );
				}
			}


			$newCost = $cost;
			$this->fixAmounts( $newCost, $appliedAdjustments );

			$rateWrapper = new WcShippingRateFacade( $rate );
			$rateWrapper->setNewCost( $newCost );
			foreach ( $appliedAdjustments as $adjustment ) {
				$rateWrapper->applyAdjustment( $adjustment );
			}
			$rateWrapper->modifyMeta();
			$rate = $rateWrapper->getRate();
		}

		return $rates;
	}

	/**
	 * @param WC_Cart $wcCart
	 */
	public function updateTotals( $wcCart ) {
		$globalContext = $this->cartContext->getGlobalContext();
		$totalsWrapper = new WcTotalsFacade( $globalContext, $wcCart );
		$totalsWrapper->insertShippingData( $this->adjustments );
	}

	/**
	 * Do not allow negative prices.
	 * Remember to change the amounts for the correct story.
	 *
	 * @param float                $rateCost
	 * @param ShippingAdjustment[] $appliedAdjustments
	 */
	protected function fixAmounts( &$rateCost, &$appliedAdjustments ) {
		foreach ( $appliedAdjustments as &$adjustment ) {
			$amount   = $adjustment->getAmount();
			$rateCost -= $amount;

			if ( $rateCost < 0 ) {
				$adjustment->setAmount( $amount + $rateCost );
				$rateCost = 0;
			}
		}
	}

	/**
	 * @param string            $label
	 * @param WC_Shipping_Rate $rate
	 *
	 * @return string
	 */
	public function shippingMethodFullLabel( $label, $rate ) {
		$rateWrapper = new WcShippingRateFacade( $rate );
		if ( ! $rateWrapper->isAffected() ) {
			return $label;
		}

		$initial_cost = $rateWrapper->getInitialPrice();
		$initial_tax  = array_sum( $rateWrapper->getInitialPriceTaxes() );

		if ( WC()->cart->display_prices_including_tax() ) {
			$initial_cost_html = '<del>' . wc_price( $initial_cost + $initial_tax ) . '</del>';
		} else {
			$initial_cost_html = '<del>' . wc_price( $initial_cost ) . '</del>';
		}
		$initial_cost_html = preg_replace( '/\samount/is', 'wdp-amount', $initial_cost_html );

		return preg_replace( '/(<span[^>]*>)/is', $initial_cost_html . ' $1', $label, 1 );
	}

}

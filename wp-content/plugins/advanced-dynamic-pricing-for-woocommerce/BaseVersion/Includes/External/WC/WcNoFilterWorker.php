<?php

namespace ADP\BaseVersion\Includes\External\WC;

use Exception;
use ReflectionClass;
use ReflectionException;
use WC_Cart;
use WC_Cart_Totals;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WcNoFilterWorker {
	const FLAG_ALLOW_PRICE_HOOKS = 'allow_price_hooks';

	/**
	 * @param WC_Cart $wcCart
	 * @param array $flags
	 */
	public function calculateTotals( &$wcCart, ...$flags ) {
		try {
			$reflection = new ReflectionClass( $wcCart );
			$property   = $reflection->getMethod( 'reset_totals' );
			$property->setAccessible( true );
			$property->invoke( $wcCart );
		} catch ( ReflectionException $e ) {
			return;
		}

		try {
			global $wp_filter;

			$filters = array();

			if ( ! in_array( self::FLAG_ALLOW_PRICE_HOOKS, $flags ) ) {
				$filters[] = 'woocommerce_product_get_price';
				$filters[] = 'woocommerce_product_variation_get_price';
			}

			$filters[] = 'woocommerce_calculate_totals';
			$filters[] = 'woocommerce_calculated_total';

			$tmp_filters = array();

			foreach ( $filters as $filter ) {
				if ( isset($wp_filter[ $filter ]) ) {
					$tmp_filters[ $filter ] = $wp_filter[ $filter ];
					unset( $wp_filter[ $filter ] );
				}
			}

			new WC_Cart_Totals( $wcCart );

			foreach ( $tmp_filters as $tag => $hook ) {
				$wp_filter[ $tag ] = $tmp_filters[ $tag ];
			}
		} catch ( Exception $e ) {
			return;
		}
	}

	/**
	 * @param WC_Cart $wcCart
	 * @param int $productId
	 * @param float $qty
	 * @param int $variationId
	 * @param array $variation
	 * @param array $cartItemData
	 *
	 * @return string|false
	 */
	public function addToCart( &$wcCart, $productId, $qty, $variationId, $variation, $cartItemData = array() ) {
		// TODO kill all hooks
		remove_action( 'woocommerce_add_to_cart', array( WC()->cart, 'calculate_totals' ), 20 );
		remove_action( 'woocommerce_add_to_cart', array( $wcCart, 'calculate_totals' ), 20 );
		try {
			$key = $wcCart->add_to_cart( $productId, $qty, $variationId, $variation, $cartItemData );
		} catch ( Exception $e ) {
			$key = false;
		}
		add_action( 'woocommerce_add_to_cart', array( WC()->cart, 'calculate_totals' ), 20, 0 );

		return $key;
	}
}

<?php

namespace ADP\BaseVersion\Includes\External\Cmp;

use ADP\BaseVersion\Includes\Context;
use WC_Subscriptions_Product;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\External\WC\WcNoFilterWorker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WoocsCmp {
	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var \WOOCS|null
	 */
	protected $woocs;

	/**
	 * @param Context $context
	 */
	public function __construct( $context ) {
		$this->context = $context;
		$this->loadRequirements();
	}

	/**
	 * @return bool
	 */
	public function isActive() {
		return ! is_null( $this->woocs ) && ( $this->woocs instanceof \WOOCS );
	}

	public function loadRequirements() {
		if ( ! did_action( 'plugins_loaded' ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called earlier the %2$s action.',
				'advanced-dynamic-pricing-for-woocommerce' ), 'load_requirements', 'plugins_loaded' ), WC_ADP_VERSION );
		}

		$this->woocs = isset( $GLOBALS['WOOCS'] ) ? $GLOBALS['WOOCS'] : null;
	}

	/**
	 * @return float|null
	 */
	public function getRate() {
		if ( ! $this->isActive() ) {
			return null;
		}

		$currency         = $this->woocs->current_currency;
		$default_currency = $this->woocs->default_currency;

		return floatval( $this->getProp( $currency, 'rate' ) ) / floatval( $this->getProp( $default_currency,
				'rate' ) );
	}

	/**
	 * @param string $currency
	 *
	 * @return array|null
	 */
	protected function get_currency( $currency ) {
		if ( ! $this->isActive() ) {
			return null;
		}

		$currency_data = null;
		$currencies    = $this->woocs->get_currencies();

		if ( isset( $currencies[ $currency ] ) && ! is_null( $currencies[ $currency ] ) ) {
			$currency_data = $currencies[ $currency ];
		}

		return $currency_data;
	}

	/**
	 * @param string $currency
	 * @param string $prop
	 *
	 * @return mixed|null
	 */
	protected function getProp( $currency, $prop ) {
		$currency = $this->get_currency( $currency );

		return ! is_null( $currency ) && isset( $currency[ $prop ] ) ? $currency[ $prop ] : null;
	}
}

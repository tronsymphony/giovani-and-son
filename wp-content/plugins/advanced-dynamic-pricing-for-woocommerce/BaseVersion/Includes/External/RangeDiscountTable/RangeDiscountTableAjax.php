<?php

namespace ADP\BaseVersion\Includes\External\RangeDiscountTable;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\Customizer\Customizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class RangeDiscountTableAjax {
	const ACTION = 'get_table_with_product_bulk_table';

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var RangeDiscountTable
	 */
	protected $rangeDiscountTable;

	/**
	 * @param Context $context
	 * @param Customizer $customizer
	 */
	public function __construct( $context, $customizer ) {
		$this->context            = $context;
		$this->rangeDiscountTable = new RangeDiscountTable( $this->context, $customizer );
	}

	public function register() {
		add_action( "wp_ajax_nopriv_" . self::ACTION, array( $this, "handle" ) );
		add_action( "wp_ajax_" . self::ACTION, array( $this, "handle" ) );
	}

	public function handle() {
		$productID = ! empty( $_REQUEST['product_id'] ) ? $_REQUEST['product_id'] : false;
		if ( ! $productID ) {
			wp_send_json_error();
		}

		if ( $content = $this->rangeDiscountTable->getProductTableContent( $productID ) ) {
			wp_send_json_success( $content );
		} else {
			wp_send_json_error( "" );
		}
	}
}
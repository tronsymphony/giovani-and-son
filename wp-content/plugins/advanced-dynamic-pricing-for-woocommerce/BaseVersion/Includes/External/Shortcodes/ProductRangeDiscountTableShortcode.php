<?php

namespace ADP\BaseVersion\Includes\External\Shortcodes;


use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\Customizer\Customizer;
use ADP\BaseVersion\Includes\External\RangeDiscountTable\RangeDiscountTable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ProductRangeDiscountTableShortcode {
	const NAME = 'adp_product_bulk_rules_table';

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var Customizer
	 */
	protected $customizer;

	/**
	 * @param Context    $context
	 * @param Customizer $customizer
	 */
	public function __construct( $context, $customizer ) {
		$this->context    = $context;
		$this->customizer = $customizer;
	}

	/**
	 * @param Context    $context
	 * @param Customizer $customizer
	 */
	public static function register( $context, $customizer ) {
		$shortcode = new self( $context, $customizer );
		add_shortcode( self::NAME, array( $shortcode, 'getContent' ) );
	}

	public function getContent( $args ) {
		$rangeDiscountTable = new RangeDiscountTable( $this->context, $this->customizer );

		return $rangeDiscountTable->getProductTableContent();
	}
}
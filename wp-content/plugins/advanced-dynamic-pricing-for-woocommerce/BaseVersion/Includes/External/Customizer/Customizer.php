<?php

namespace ADP\BaseVersion\Includes\External\Customizer;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\DiscountMessage;
use ADP\BaseVersion\Includes\External\RangeDiscountTable\RangeDiscountTable;
use WP_Customize_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Customizer {
	const LAYOUT_VERBOSE = RangeDiscountTable::LAYOUT_VERBOSE;
	const LAYOUT_SIMPLE = RangeDiscountTable::LAYOUT_SIMPLE;
	const ANY = 'any';

	protected static $option_name = 'woocommerce_wdp_bulk_table';
	protected $options = array();

	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * Customizer constructor.
	 *
	 * @param Context $context
	 */
	public function __construct( $context ) {
		$this->context = $context;
		$this->init();
	}

	public function run_style_customize() {
		add_action( 'wp_head', function () {
			$this->customize_css();
		} );
	}

	public function customize_register() {
		add_action( 'customize_register', array( $this, 'add_sections' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_controls_scripts' ), 999 );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
	}

	protected function init() {
		$this->options['wdp_product_bulk_table'] = array(
			'key'      => RangeDiscountTable::CONTEXT_PRODUCT_PAGE,
			'title'    => __( 'Product bulk table (Advanced Dynamic Pricing)', 'advanced-dynamic-pricing-for-woocommerce' ),
			'priority' => 200,
			'options'  => $this->get_product_table_options( 'wdp_product_bulk_table' ),
		);

		$this->options['wdp_category_bulk_table'] = array(
			'key'      => RangeDiscountTable::CONTEXT_CATEGORY_PAGE,
			'title'    => __( 'Category bulk table (Advanced Dynamic Pricing)', 'advanced-dynamic-pricing-for-woocommerce' ),
			'priority' => 200,
			'options'  => $this->get_category_table_options( 'wdp_category_bulk_table' ),
		);

		$this->options['wdp_discount_message'] = array(
			'key'      => DiscountMessage::PANEL_KEY,
			'title'    => __( 'Discount message (Advanced Dynamic Pricing)', 'advanced-dynamic-pricing-for-woocommerce' ),
			'priority' => 200,
			'options'  => $this->get_discount_message_options( 'wdp_discount_message' ),
		);
	}

	protected function init_font_options( $panel_id, $section ) {
		$map_section_and_css_selector = array(
			"{$panel_id}-table_header"  => '.wdp_bulk_table_content .wdp_pricing_table_caption',
			"{$panel_id}-table_columns" => '.wdp_bulk_table_content table thead td',
			"{$panel_id}-table_body"    => '.wdp_bulk_table_content table tbody td',
			"{$panel_id}-table_footer"  => '.wdp_bulk_table_content .wdp_pricing_table_footer',
		);

		if ( empty( $map_section_and_css_selector[ $section ] ) ) {
			return false;
		}
		$selector = $map_section_and_css_selector[ $section ];

		$font_options = array(
			"{$panel_id}-emphasis_bold"   => array(
				'label'             => __( 'Bold text', 'advanced-dynamic-pricing-for-woocommerce' ),
				'default'           => false,
				'sanitize_callback' => 'wc_bool_to_string',
				'control_class'     => 'ADP\BaseVersion\Includes\External\Customizer\Controls\FontEmphasisBold',
				'priority'          => 10,

				'apply_type'       => 'css',
				'selector'         => $selector,
				'css_option_name'  => 'font-weight',
				'css_option_value' => 'bold',
				'layout'           => self::ANY,
			),
			"{$panel_id}-emphasis_italic" => array(
				'label'             => __( 'Italic text', 'advanced-dynamic-pricing-for-woocommerce' ),
				'default'           => false,
				'sanitize_callback' => 'wc_bool_to_string',
				'control_class'     => 'ADP\BaseVersion\Includes\External\Customizer\Controls\FontEmphasisItalic',
				'priority'          => 20,

				'apply_type'       => 'css',
				'selector'         => $selector,
				'css_option_name'  => 'font-style',
				'css_option_value' => 'italic',
				'layout'           => self::ANY,
			),
			"{$panel_id}-text_align"      => array(
				'label'         => __( 'Text Align', 'advanced-dynamic-pricing-for-woocommerce' ),
				'default'       => '',
				'control_class' => 'ADP\BaseVersion\Includes\External\Customizer\Controls\TextAlign',
				'priority'      => 30,

				'apply_type'      => 'css',
				'selector'        => $selector,
				'css_option_name' => 'text-align',
				'layout'          => self::ANY,
			),
			"{$panel_id}-text_color"      => array(
				'label'             => __( 'Text color', 'advanced-dynamic-pricing-for-woocommerce' ),
				'default'           => '#6d6d6d',
				'sanitize_callback' => 'sanitize_hex_color',
				'control_class'     => '\WP_Customize_Color_Control',
				'priority'          => 10,

				'apply_type'      => 'css',
				'selector'        => $selector,
				'css_option_name' => 'color',
				'layout'          => self::ANY,
			),
		);

		// bulk_table_header BOLD by default
		if ( "{$panel_id}-bulk_table_header" == $section ) {
			$font_options["{$panel_id}-emphasis_bold"]['default'] = true;
		}

		return $font_options;
	}

	protected function get_product_table_options( $panel_id ) {
		$type = 'product';

		$product_options = array(
			"{$panel_id}-table"         => array(
				'title'    => __( 'Options', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 10,
				'options'  => array(
					'table_layout'              => array(
						'label'        => __( 'Product table layout', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'      => self::LAYOUT_VERBOSE,
						'control_type' => 'select',
						'choices'      => array(
							self::LAYOUT_VERBOSE => __( 'Display ranges as rows', 'advanced-dynamic-pricing-for-woocommerce' ),
							self::LAYOUT_SIMPLE  => __( 'Display ranges as headers', 'advanced-dynamic-pricing-for-woocommerce' ),
						),
						'priority'     => 5,

						'apply_type' => 'filter',
//						'hook'       => "wdp_{$type}_bulk_table_action",
						'layout'     => self::ANY,
					),
					'product_bulk_table_action' => array(
						'label'        => __( 'Product Bulk Table position', 'advanced-dynamic-pricing-for-woocommerce' ),
						'description'  => __( 'You can use shortcode [adp_product_bulk_rules_table] in product template.', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'      => 'woocommerce_after_add_to_cart_form',
						'control_type' => 'select',
						'choices'      => apply_filters( 'wdp_product_bulk_table_places', array(
							'woocommerce_before_single_product_summary' => __( 'Above product summary', 'advanced-dynamic-pricing-for-woocommerce' ),
							'woocommerce_after_single_product_summary'  => __( 'Below product summary', 'advanced-dynamic-pricing-for-woocommerce' ),
							'woocommerce_before_single_product'         => __( 'Above product', 'advanced-dynamic-pricing-for-woocommerce' ),
							'woocommerce_after_single_product'          => __( 'Below product', 'advanced-dynamic-pricing-for-woocommerce' ),
							'woocommerce_before_add_to_cart_form'       => __( 'Above add to cart', 'advanced-dynamic-pricing-for-woocommerce' ),
							'woocommerce_after_add_to_cart_form'        => __( 'Below add to cart', 'advanced-dynamic-pricing-for-woocommerce' ),
							'woocommerce_product_meta_start'            => __( 'Above product meta', 'advanced-dynamic-pricing-for-woocommerce' ),
							'woocommerce_product_meta_end'              => __( 'Below product meta', 'advanced-dynamic-pricing-for-woocommerce' ),
						) ),
						'priority'     => 10,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_action",
						'layout'     => self::ANY,
					),
					'show_discounted_price'     => array(
						'label'             => __( 'Show discounted price', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => true,
						'priority'          => 20,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
						'hook'       => "wdp_show_discounted_price_in_{$type}_bulk_table",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'show_discount_column'      => array(
						'label'             => __( 'Show discount column', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => true,
						'priority'          => 30,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
						'hook'       => "wdp_show_product_discount_in_{$type}_bulk_table",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'show_footer'               => array(
						'label'             => __( 'Show footer', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => true,
						'priority'          => 40,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
						'hook'       => "wdp_show_footer_in_{$type}_bulk_table",
						'layout'     => self::ANY,
					),
				),

			),
			"{$panel_id}-table_header"  => array(
				'title'    => __( 'Style header', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 20,
				'options'  => array(
					'use_message_as_title' => array(
						'label'             => __( 'Use bulk table message as table header', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => false,
						'priority'          => 50,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
						'hook'       => "wdp_use_message_as_{$type}_bulk_table_header",
						'layout'     => self::ANY,
					),
					'bulk_title'           => array(
						'label'    => __( 'Header bulk title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Bulk deal', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 50,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_header_for_bulk_title",
						'layout'     => self::ANY,
					),
					'tier_title'           => array(
						'label'    => __( 'Header tier title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Tier deal', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 50,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_header_for_tier_title",
						'layout'     => self::ANY,
					),
				),
			),
			"{$panel_id}-table_columns" => array(
				'title'    => __( 'Style columns', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 30,
				'options'  => array(
					'qty_column_title'                      => array(
						'label'    => __( 'Quantity column title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Quantity', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 50,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_qty_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'discount_column_title'                 => array(
						'label'    => __( 'Discount column title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Discount', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 60,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_discount_price_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'discount_column_title_for_rule_fixed_price' => array(
						'label'    => __( 'Discount column title, for fixed price', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Discount', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 65,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_rule_fixed_price_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'discount_column_title_for_fixed_price' => array(
						'label'    => __( 'Discounted price column title, for fixed price', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Fixed price', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 70,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_fixed_price_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'discounted_price_title'                => array(
						'label'    => __( 'Discounted price column title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Discounted price', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 80,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_discounted_price_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'header_background_color'               => array(
						'label'             => __( 'Background color', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => '#efefef',
						'sanitize_callback' => 'sanitize_hex_color',
						'control_class'     => 'WP_Customize_Color_Control',
						'priority'          => 90,

						'apply_type'      => 'css',
						'selector'        => '.wdp_bulk_table_content table thead td',
						'css_option_name' => 'background-color',
						'layout'          => self::ANY,
					),
				),
			),
			"{$panel_id}-table_body"    => array(
				'title'    => __( 'Style body', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 40,
				'options'  => array(
					'body_background_color' => array(
						'label'             => __( 'Background color', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => '#ffffff',
						'sanitize_callback' => 'sanitize_hex_color',
						'control_class'     => 'WP_Customize_Color_Control',
						'priority'          => 50,

						'apply_type'      => 'css',
						'selector'        => '.wdp_bulk_table_content table tbody td',
						'css_option_name' => 'background-color',
						'layout'          => self::ANY,
					),
				),
			),
			"{$panel_id}-table_footer"  => array(
				'title'    => __( 'Style footer', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 50,
				'options'  => array(),
			),
		);


		foreach ( $product_options as $section => &$section_data ) {
			if ( $font_options = $this->init_font_options( $panel_id, $section ) ) {
				$section_data['options'] = array_merge( $font_options, $section_data['options'] );
			}
		}

		return $product_options;
	}

	protected function get_category_table_options( $panel_id ) {
		$type = 'category';

		$category_options = array(
			"{$panel_id}-table"         => array(
				'title'    => __( 'Options', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 10,
				'options'  => array(
					'table_layout'               => array(
						'label'        => __( 'Category table layout', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'      => self::LAYOUT_VERBOSE,
						'control_type' => 'select',
						'choices'      => array(
							self::LAYOUT_VERBOSE => __( 'Display ranges as rows', 'advanced-dynamic-pricing-for-woocommerce' ),
						),
						'priority'     => 5,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_action",
						'layout'     => self::ANY,
					),
					'category_bulk_table_action' => array(
						'label'        => __( 'Category Bulk Table position', 'advanced-dynamic-pricing-for-woocommerce' ),
						'description'  => __( 'You can use shortcode [adp_product_bulk_rules_table] in product template.', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'      => 'woocommerce_before_shop_loop',
						'control_type' => 'select',
						'choices'      => apply_filters( 'wdp_category_bulk_table_places', array(
							'woocommerce_before_shop_loop' => __( 'At top of the page', 'advanced-dynamic-pricing-for-woocommerce' ),
							'woocommerce_after_shop_loop'  => __( 'At bottom of the page', 'advanced-dynamic-pricing-for-woocommerce' ),
						) ),
						'priority'     => 10,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_action",
						'layout'     => self::ANY,
					),

					'show_discount_column' => array(
						'label'             => __( 'Show discount column', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => true,
						'priority'          => 30,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
						'hook'       => "wdp_show_product_discount_in_{$type}_bulk_table",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'show_footer'          => array(
						'label'             => __( 'Show footer', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => true,
						'priority'          => 40,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
						'hook'       => "wdp_show_footer_in_{$type}_bulk_table",
						'layout'     => self::ANY,
					),
				),

			),
			"{$panel_id}-table_header"  => array(
				'title'    => __( 'Style header', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 20,
				'options'  => array(
					'use_message_as_title' => array(
						'label'             => __( 'Use bulk table message as table header', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => false,
						'priority'          => 50,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
						'hook'       => "wdp_use_message_as_{$type}_bulk_table_header",
						'layout'     => self::ANY,
					),
					'bulk_title'           => array(
						'label'    => __( 'Header bulk title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Bulk deal', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 50,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_header_for_bulk_title",
						'layout'     => self::ANY,
					),
					'tier_title'           => array(
						'label'    => __( 'Header tier title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Tier deal', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 50,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_header_for_tier_title",
						'layout'     => self::ANY,
					),
				),
			),
			"{$panel_id}-table_columns" => array(
				'title'    => __( 'Style columns', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 30,
				'options'  => array(
					'qty_column_title'                      => array(
						'label'    => __( 'Quantity column title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Quantity', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 50,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_qty_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'discount_column_title'                 => array(
						'label'    => __( 'Discount column title', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Discount', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 60,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_discount_price_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'discount_column_title_for_rule_fixed_price' => array(
						'label'    => __( 'Discount column title, for fixed price', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Discount', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 65,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_rule_fixed_price_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'discount_column_title_for_fixed_price' => array(
						'label'    => __( 'Discounted price column title, for fixed price', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( 'Fixed price', 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 70,

						'apply_type' => 'filter',
						'hook'       => "wdp_{$type}_bulk_table_fixed_price_title",
						'layout'     => self::LAYOUT_VERBOSE,
					),
					'header_background_color'               => array(
						'label'             => __( 'Background color', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => '#efefef',
						'sanitize_callback' => 'sanitize_hex_color',
						'control_class'     => 'WP_Customize_Color_Control',
						'priority'          => 90,

						'apply_type'      => 'css',
						'selector'        => '.wdp_bulk_table_content table thead td',
						'css_option_name' => 'background-color',
						'layout'          => self::ANY,
					),
				),
			),
			"{$panel_id}-table_body"    => array(
				'title'    => __( 'Style body', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 40,
				'options'  => array(
					'body_background_color' => array(
						'label'             => __( 'Background color', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => '#ffffff',
						'sanitize_callback' => 'sanitize_hex_color',
						'control_class'     => 'WP_Customize_Color_Control',
						'priority'          => 50,

						'apply_type'      => 'css',
						'selector'        => '.wdp_bulk_table_content table tbody td',
						'css_option_name' => 'background-color',
						'layout'          => self::ANY,
					),
				),
			),
			"{$panel_id}-table_footer"  => array(
				'title'    => __( 'Style footer', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 50,
				'options'  => array(),
			),
		);

		foreach ( $category_options as $section => &$section_data ) {
			if ( $font_options = $this->init_font_options( $panel_id, $section ) ) {
				$section_data['options'] = array_merge( $font_options, $section_data['options'] );
			}
		}

		return $category_options;
	}

	protected function get_discount_message_options( $panel_id ) {
		$sections = array(
			"{$panel_id}-global"                                => array(
				'title'    => __( 'Global options', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 5,
				'options'  => array(
					'amount_saved_label' => array(
						'label'    => __( 'Amount saved label', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'  => __( "Amount Saved", 'advanced-dynamic-pricing-for-woocommerce' ),
						'priority' => 5,

						'apply_type' => 'filter',
					),
				)
			),
			"{$panel_id}-" . DiscountMessage::CONTEXT_CART      => array(
				'title'    => __( 'Cart', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 10,
				'options'  => array(
					'enable'   => array(
						'label'             => __( 'Enable', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => false,
						'priority'          => 5,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
					),
					'position' => array(
						'label'        => __( 'Position', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'      => "woocommerce_cart_totals_before_order_total",
						'control_type' => 'select',
						'choices'      => apply_filters( "wdp_" . DiscountMessage::CONTEXT_CART . "_discount_message_places",
							array(
								'woocommerce_cart_totals_before_order_total' => __( 'Before order total', 'advanced-dynamic-pricing-for-woocommerce' ),
								'woocommerce_cart_totals_after_order_total'  => __( 'After order total', 'advanced-dynamic-pricing-for-woocommerce' ),
							) ),
						'priority'     => 10,

						'apply_type' => 'filter',
					),
				)
			),
			"{$panel_id}-" . DiscountMessage::CONTEXT_MINI_CART => array(
				'title'    => __( 'Mini Cart', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 15,
				'options'  => array(
					'enable'   => array(
						'label'             => __( 'Enable', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => false,
						'priority'          => 5,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
					),
					'position' => array(
						'label'        => __( 'Position', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'      => "woocommerce_mini_cart_contents",
						'control_type' => 'select',
						'choices'      => apply_filters( "wdp_" . DiscountMessage::CONTEXT_MINI_CART . "_discount_message_places",
							array(
								'woocommerce_before_mini_cart_contents' => __( 'Before mini cart contents', 'advanced-dynamic-pricing-for-woocommerce' ),
								'woocommerce_mini_cart_contents'        => __( 'After mini cart contents', 'advanced-dynamic-pricing-for-woocommerce' ),
							) ),
						'priority'     => 10,

						'apply_type' => 'filter',
					),
				)
			),
			"{$panel_id}-" . DiscountMessage::CONTEXT_CHECKOUT  => array(
				'title'    => __( 'Checkout', 'advanced-dynamic-pricing-for-woocommerce' ),
				'priority' => 20,
				'options'  => array(
					'enable'   => array(
						'label'             => __( 'Enable', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'           => false,
						'priority'          => 5,
						'control_type'      => 'checkbox',
						'sanitize_callback' => 'wc_string_to_bool',

						'apply_type' => 'filter',
					),
					'position' => array(
						'label'        => __( 'Position', 'advanced-dynamic-pricing-for-woocommerce' ),
						'default'      => "woocommerce_review_order_after_cart_contents",
						'control_type' => 'select',
						'choices'      => apply_filters( "wdp_" . DiscountMessage::CONTEXT_CHECKOUT . "_discount_message_places",
							array(
								'woocommerce_review_order_before_cart_contents' => __( 'Before cart contents', 'advanced-dynamic-pricing-for-woocommerce' ),
								'woocommerce_review_order_after_cart_contents'  => __( 'After cart contents', 'advanced-dynamic-pricing-for-woocommerce' ),
								'woocommerce_review_order_after_order_total'    => __( 'After order total', 'advanced-dynamic-pricing-for-woocommerce' ),
							) ),
						'priority'     => 10,

						'apply_type' => 'filter',
					),
				)
			),
		);

		return $sections;
	}

	public function customize_css() {
		$css          = array();
		$attr_options = get_theme_mod( self::$option_name );
		$context      = $this->context;
		$important    = ! $context->is( $context::CUSTOMIZER ) ? '! important' : "";

		$is_product       = $context->is( $context::WC_PRODUCT_PAGE );
		$product_loop     = $context->is( $context::PRODUCT_LOOP );
		$is_category_page = $context->is( $context::WC_CATEGORY_PAGE );

		$panel_id = $is_product || $product_loop ? 'wdp_product_bulk_table' : ( $is_category_page ? 'wdp_category_bulk_table' : false );
		if ( empty( $panel_id ) || empty( $this->options[ $panel_id ] ) ) {
			return;
		}
		$panel_data = $this->options[ $panel_id ];

		if ( empty( $panel_data['options'] ) && ! is_array( $panel_data['options'] ) ) {
			return;
		}
		foreach ( $panel_data['options'] as $section_id => $section_settings ) {
			foreach ( $section_settings['options'] as $option_id => $option_data ) {
				if ( empty( $option_data['apply_type'] ) ) {
					continue;
				}
				if ( 'css' == $option_data['apply_type'] && $option_data['selector'] ) {
					$default = $option_data['default'];
					if ( ! isset( $attr_options[ $panel_id ][ $section_id ][ $option_id ] ) ) {
						$option_value = $default;
					} else {
						$option_value = $attr_options[ $panel_id ][ $section_id ][ $option_id ];
					}
					if ( ! empty( $option_data['css_option_value'] ) ) {
						if ( $option_value ) {
							$css[] = sprintf( "%s { %s: %s ! important}", $option_data['selector'],
								$option_data['css_option_name'], $option_data['css_option_value'] );
						}
					} else {
						if ( $option_value ) {
							$css[] = sprintf( "%s { %s: %s %s}", $option_data['selector'],
								$option_data['css_option_name'], $option_value, $important );
						}
					}
				}
			}
		}
		?>
        <style type="text/css">
            <?php echo join(' ', $css); ?>
        </style>
		<?php

	}

	public function get_theme_options() {
		if ( ! did_action( 'wp_loaded' ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s action.', 'woocommerce' ), __NAMESPACE__ . '/Customizer::get_theme_options', 'wp_loaded' ), '2.2.2' );

			return array();
		}

		$result       = array();
		$attr_options = get_theme_mod( self::$option_name );

		foreach ( $this->options as $panel_id => $panel_data ) {
			if ( empty( $panel_data['options'] ) || empty( $panel_data['key'] ) ) {
				continue;
			}

			$key = $panel_data['key'];

			$section_options = array();
			foreach ( $panel_data['options'] as $section_id => $section_settings ) {
				if ( ! isset( $section_settings['options'] ) ) {
					continue;
				}

				$section_key = str_replace( $panel_id . '-', "", $section_id );

				$options = array();
				foreach ( $section_settings['options'] as $option_id => $option_data ) {
					if ( empty( $option_data['apply_type'] ) ) {
						continue;
					}

					// font options
					$option_key = str_replace( $panel_id . '-', "", $option_id );

					$default = $option_data['default'];
					if ( ! isset( $attr_options[ $panel_id ][ $section_id ][ $option_id ] ) ) {
						$attr_option = $default;
					} else {
						$attr_option = $attr_options[ $panel_id ][ $section_id ][ $option_id ];
					}

					/**
					 * Do not apply saved value which not in choices
					 * e.g. delete add_action
					 */
					$choices = isset( $option_data['choices'] ) ? $option_data['choices'] : array();
					if ( $choices && empty( $choices[ $attr_option ] ) ) {
						$attr_option = $default;
					}

					$options[ $option_key ] = $attr_option;
				}

				$section_options[ $section_key ] = $options;
			}

			$result[ $key ] = $section_options;
		}

		return $result;
	}

	/**
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function add_sections( $wp_customize ) {
		foreach ( $this->options as $panel_id => $panel_data ) {
			$panel_title   = ! empty( $panel_data['title'] ) ? $panel_data['title'] : null;
			$panel_options = ! empty( $panel_data['options'] ) ? $panel_data['options'] : null;

			if ( ! $panel_title || ! $panel_options ) {
				continue;
			}

			$wp_customize->add_panel( $panel_id, array(
					'title'    => $panel_title,
					'priority' => ! empty( $panel_data['priority'] ) ? $panel_data['priority'] : 200,
				) );

			foreach ( $panel_options as $section_id => $section_settings ) {
				$this->add_section( $wp_customize, $section_id, $section_settings, $panel_id );
			}
		}

	}


	/**
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 * @param string                $section_id Parent menu id
	 * @param string                $panel_id
	 * @param array                 $section_settings (See above)
	 */
	protected function add_section( $wp_customize, $section_id, $section_settings, $panel_id ) {
		if ( ! empty( $section_settings['options'] ) ) {
			$wp_customize->add_section( $section_id, array(
					'title'    => $section_settings['title'],
					'priority' => isset( $section_settings['priority'] ) ? $section_settings['priority'] : 20,
					'panel'    => $panel_id,
				) );

			foreach ( $section_settings['options'] as $option_id => $data ) {
				$setting = sprintf( '%s[%s][%s][%s]', self::$option_name, $panel_id, $section_id, $option_id );
				$this->add_option( $wp_customize, $setting, $section_id, $data );
			}
		}
	}

	/**
	 * @param $wp_customize WP_Customize_Manager Theme Customizer object.
	 * @param $setting string Option id
	 * @param $section_id string Parent menu id
	 * @param $data array Option data
	 */
	protected function add_option( $wp_customize, $setting, $section_id, $data ) {
		$priority    = ! empty( $data['priority'] ) ? $data['priority'] : 20;
		$description = ! empty( $data['description'] ) ? $data['description'] : "";

		$transport = 'refresh';
		if ( $data['apply_type'] == 'css' ) {
			$transport = 'postMessage';
		}

		$wp_customize->add_setting( $setting, array(
				'default'    => $data['default'],
				'capability' => 'edit_theme_options',
				'transport'  => $transport,
				'priority'   => $priority,
			) );


		if ( ! empty( $data['control_class'] ) && class_exists( $data['control_class'] ) ) {
			$class   = $data['control_class'];
			$control = new $class( $wp_customize, $setting, array(
				'label'       => $data['label'],
				'description' => $description,
				'section'     => $section_id,
				'settings'    => $setting,
				'priority'    => $priority,
			) );
			$wp_customize->add_control( $control );
		} else {
			$wp_customize->add_control( $setting, array(
					'label'       => $data['label'],
					'description' => $description,
					'section'     => $section_id,
					'settings'    => $setting,
					'type'        => isset( $data['control_type'] ) ? $data['control_type'] : 'text',
					'choices'     => isset( $data['choices'] ) ? $data['choices'] : array(),
				) );
		}
	}

	public function customizer_controls_scripts() {
		$baseVersionUrl = WC_ADP_PLUGIN_URL . "/BaseVersion/";
		wp_enqueue_style( 'wc-plc-customizer-control-css', $baseVersionUrl . 'assets/css/customize-controls.css', array(), WC_ADP_VERSION );
		wp_enqueue_script( 'wc-plc-customizer-control-js', $baseVersionUrl . 'assets/js/customize-controls.js', array(), WC_ADP_VERSION );
	}

	public function customize_preview_init() {
		$baseVersionUrl = WC_ADP_PLUGIN_URL . "/BaseVersion/";
		wp_enqueue_script( 'wc-plc-customizer-preview-js', $baseVersionUrl . 'assets/js/wdp-customize-preview.js', array(), WC_ADP_VERSION, true );

		$css_controls = array();
		foreach ( $this->options as $panel_id => $panel_data ) {
			if ( empty( $panel_data['options'] ) ) {
				continue;
			}

			foreach ( $panel_data['options'] as $section_id => $section_settings ) {
				if ( isset( $section_settings['options'] ) ) {
					foreach ( $section_settings['options'] as $option_id => $option_data ) {
						if ( empty( $option_data['apply_type'] ) ) {
							continue;
						}

						if ( 'css' == $option_data['apply_type'] ) {
							$control_id       = sprintf( '%s[%s][%s][%s]', self::$option_name, $panel_id, $section_id,
								$option_id );
							$selector         = $option_data['selector'];
							$css_option_name  = $option_data['css_option_name'];
							$css_option_value = isset( $option_data['css_option_value'] ) ? $option_data['css_option_value'] : null;

							$css_controls[ $control_id ] = array(
								'selector'         => $selector,
								'css_option_name'  => $css_option_name,
								'css_option_value' => $css_option_value,
							);
						}
					}
				}
			}
		}

		$localize = array(
			'css_controls' => $css_controls,
		);
		wp_localize_script( 'wc-plc-customizer-preview-js', 'wdp_customize_preview', $localize );
	}

}

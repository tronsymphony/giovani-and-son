<?php

namespace ADP\BaseVersion\Includes;

use ADP\BaseVersion\Includes\Settings\StoreStrategy;
use ADP\Settings\OptionBuilder;
use ADP\Settings\OptionsList;
use ADP\Settings\OptionsManager;

class OptionsInstaller {
	public static function install() {
		$settings    = new OptionsManager( new StoreStrategy() );
		$optionsList = new OptionsList();

		static::register_settings( $optionsList );

		$settings->installOptions( $optionsList );
		$settings->load();

		return $settings;
	}

	/**
	 * @param $optionsList OptionsList
	 */
	public static function register_settings( &$optionsList ) {
		$builder = new OptionBuilder();

		$optionsList->register(
			$builder::boolean( 'show_matched_bulk', false, __( 'Show matched bulk', 'advanced-dynamic-pricing-for-woocommerce' ) ),
			$builder::boolean(
				'show_matched_cart_adjustments',
				false,
				__( 'Show matched cart adjustments', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'show_matched_cart_adjustments',
				false,
				__( 'Show matched cart adjustments', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::boolean(
				'show_matched_get_products',
				false,
				__( 'Show matched get products', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'show_matched_adjustments',
				false,
				__( 'Show matched adjustments', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'show_matched_deals',
				false,
				__( 'Show matched deals', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'show_matched_bulk_table',
				true,
				__( 'Show bulk table on product page', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::boolean(
				'show_category_bulk_table',
				false,
				__( 'Show bulk table on category page', 'advanced-dynamic-pricing-for-woocommerce' )
			),


			$builder::boolean(
				'show_striked_prices',
				true,
				__( 'Show striked prices in the cart', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::boolean(
				'show_onsale_badge',
				false,
				__( 'Show On Sale badge if product price was modified', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::integer(
				'limit_results_in_autocomplete',
				25,
				__( 'Show first X results in autocomplete', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::integer(
				'rule_max_exec_time',
				5,
				__( 'Disable rule if it runs longer than X seconds', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::integer(
				'rules_per_page',
				50,
				__( 'Rules per page', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'support_shortcode_products_on_sale',
				false,
				__( 'Support shortcode [adp_products_on_sale]', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'support_shortcode_products_bogo',
				false,
				__( 'Support shortcode [adp_products_bogo]', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'show_cross_out_subtotal_in_cart_totals',
				false,
				__( 'Show striked subtotal in cart totals', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::selective(
				'bulk_table_calculation_mode',
				__( 'Calculate price based on', 'advanced-dynamic-pricing-for-woocommerce' ),
				array(
					"only_bulk_rule_table" => __( "Current bulk rule", 'advanced-dynamic-pricing-for-woocommerce' ),
					"all"                  => __( "All active rules", 'advanced-dynamic-pricing-for-woocommerce' ),
				),
				"only_bulk_rule_table"
			),


			$builder::boolean(
				'combine_discounts',
				false,
				__( 'Combine multiple fixed discounts', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::shortText(
				'default_discount_name',
				__( 'Coupon', 'advanced-dynamic-pricing-for-woocommerce' ),
				__( 'Default discount name', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'combine_fees',
				false,
				__( 'Combine multiple fees', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::shortText(
				'default_fee_name',
				__( 'Fee', 'advanced-dynamic-pricing-for-woocommerce' ),
				__( 'Default fee name', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::shortText(
				'default_fee_tax_class',
				"",
				__( 'Default fee tax class', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'enable_product_html_template',
				false,
				__( 'Product price html template|Enable', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::shortText(
				'price_html_template',
				"{{price_html}}",
				__( 'Product price html template|Output template', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::shortText(
				'initial_price_context',
				"nofilter",
				__( 'Use prices modified by other plugins', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'do_not_modify_price_at_product_page',
				false,
				__( 'Don\'t modify product price on product page', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'discount_table_ignores_conditions',
				false,
				__( 'Show bulk table regardless to conditions', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'use_first_range_as_min_qty',
				false,
				__( 'Use first range as minimum quantity if bulk rule is active', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'show_message_after_add_free_product',
				false,
				__( 'Show message after adding free product|Enable', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::shortText(
				'message_template_after_add_free_product',
				__( "Added {{qty}} free {{product_name}}", 'advanced-dynamic-pricing-for-woocommerce' ),
				__( 'Show message after adding free product|Output template', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'is_calculate_based_on_wc_precision',
				false,
				__( 'Round up totals to match modified item prices', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'replace_price_with_min_bulk_price',
				false,
				__( 'Replace price with lowest bulk price|Enable', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::shortText(
				'replace_price_with_min_bulk_price_template',
				__( "From {{price}} {{price_suffix}}", 'advanced-dynamic-pricing-for-woocommerce' ),
				__( 'Replace price with lowest bulk price|Output template', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'uninstall_remove_data',
				false,
				__( 'Remove all data on uninstall', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'load_in_backend',
				false,
				__( 'Apply pricing rules to backend orders', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'update_prices_while_doing_cron',
				false,
				__( 'Apply pricing rules while doing cron', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'update_prices_while_doing_rest_api',
				false,
				__( 'Apply pricing rules while doing REST API', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'allow_to_edit_prices_in_po',
				false,
				__( 'Still allow to edit prices in Phone Orders', 'advanced-dynamic-pricing-for-woocommerce' )
			),
			$builder::boolean(
				'suppress_other_pricing_plugins',
				false,
				__( 'Suppress other pricing plugins in frontend', 'advanced-dynamic-pricing-for-woocommerce' )
			),


			$builder::boolean(
				'allow_to_exclude_products',
				true,
				__( 'Allow to exclude products in filters', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::boolean(
				'show_debug_bar',
				false,
				__( 'Show debug panel at bottom of the page', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::selective(
				'discount_for_onsale',
				__( 'Apply discount on "Onsale" products', 'advanced-dynamic-pricing-for-woocommerce' ),
				array(
					"sale_price"                  => __( "Use sale price", 'advanced-dynamic-pricing-for-woocommerce' ),
					"discount_regular"            => __( "Discount regular price", 'advanced-dynamic-pricing-for-woocommerce' ),
					"discount_sale"               => __( "Discount sale price", 'advanced-dynamic-pricing-for-woocommerce' ),
					"compare_discounted_and_sale" => __( "Best between discounted and sale", 'advanced-dynamic-pricing-for-woocommerce' ),
				),
				"compare_discounted_and_sale"
			),

			$builder::boolean(
				'is_override_cents',
				false,
				__( 'Cents|Override the cents on the calculated price.', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::integer(
				'prices_ends_with',
				99,
				__( 'Cents|If selected, prices will end with: 0.', 'advanced-dynamic-pricing-for-woocommerce' )
			),

			$builder::selective(
				'disable_external_coupons',
				__( 'Disable external coupons', 'advanced-dynamic-pricing-for-woocommerce' ),
				array(
					"dont_disable"                 => __( "Don't disable", 'advanced-dynamic-pricing-for-woocommerce' ),
					"if_any_rule_applied"          => __( "If any rule applied", 'advanced-dynamic-pricing-for-woocommerce' ),
					"if_any_of_cart_items_updated" => __( "If any of cart items updated", 'advanced-dynamic-pricing-for-woocommerce' ),
				),
				"dont_disable"
			),

			$builder::boolean(
				'hide_coupon_word_in_totals',
				false,
				__( 'Hide "Coupon" word in cart totals', 'advanced-dynamic-pricing-for-woocommerce' )
			)

	    );

	}
}

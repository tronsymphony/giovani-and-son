<?php
/**
 * Woocommerce Wholesale Prices Settings
 *
 * @author      Rymera Web
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WWP_Settings' ) ) {

    class WWP_Settings extends WC_Settings_Page {

        /*
        * @since WWP 1.11
        * We are adding settings by transferring setting options from WWPP to WWP.
        * These options include "Wholesale Price Text", "Disable coupons for wholesale users" and "Hide Original Price".
        * Note that these options we are still using the wwpp_ prefix to maintain values across WWP and WWPP.
        * 
        * @since WWPP 1.24
        * The setting options will be removed in WWPP and its logic codes.
        * WWP will handle the transferred options in this version.
        */

        /**
         * Constructor.
         */
        public function __construct() {

            $this->id    = 'wwp_settings';
            $this->label = __( 'Wholesale Prices', 'woocommerce-wholesale-prices' );

            add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 30 ); // 30 so it is after the emails tab
            add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
            add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );

            add_action( 'woocommerce_admin_field_upgrade_content' , array( $this, 'render_upgrade_content' ) );

            // Remove upgrade banner tab when wwpp is active
            add_filter( 'wwp_filter_settings_sections' , array( $this, 'remove_upgrade_tab' ) );

            add_action( 'woocommerce_admin_field_wwp_upsells_buttons' , array( $this, 'wwp_upsells_buttons' ) );
            
            do_action( 'wwp_settings_construct' );

        }

        /**
         * Get sections.
         *
         * @return array
         * @since 1.0.0
         */
        public function get_sections() {

            $sections = array(
                ''                              =>  apply_filters( 'wwp_filter_settings_general_section_title' , __( 'General' , 'woocommerce-wholesale-prices' ) ),
                'wwpp_setting_price_section'    => __( 'Price' , 'woocommerce-wholesale-prices' ),
                'wwpp_setting_tax_section'      => __( 'Tax' , 'woocommerce-wholesale-prices' ),
                'wwp_upgrade_section'           => __( 'Upgrade' , 'woocommerce-wholesale-prices' ),
            );

            $sections = apply_filters( 'wwp_filter_settings_sections' , $sections );

            return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );

        }

        /**
         * Output the settings.
         *
         * @since 1.0.0
         */
        public function output() {

            global $current_section;

            $settings = $this->get_settings( $current_section );
            WC_Admin_Settings::output_fields( $settings );

        }

        /**
         * Save settings.
         *
         * @since 1.0.0
         * @since 1.6.0 Passed the current section on the wwp_before_save_settings and wwp_after_save_settings action filters.
         */
        public function save() {

            global $current_section;

            $settings = $this->get_settings( $current_section );

            do_action( 'wwp_before_save_settings' , $settings , $current_section );

            WC_Admin_Settings::save_fields( $settings );

            do_action( 'wwp_after_save_settings' , $settings , $current_section );

        }

        /**
         * Get settings array.
         *
         * @param string $current_section
         *
         * @return mixed
         * @since 1.0.0
         */
        public function get_settings( $current_section = '' ) {

            $settings = array();

            if ( $current_section == '' ) {

                // General Settings
                $wwpGeneralSettings = apply_filters( 'wwp_general_section_settings' , $this->_get_general_section_settings() ) ;
                $settings = array_merge( $settings , $wwpGeneralSettings );

            } else if ( $current_section === 'wwpp_setting_price_section' ) {

                // Price Section
                $wwp_price_settings = apply_filters( 'wwp_price_section_settings' , $this->_get_price_section_settings() );
                $settings = array_merge( $settings , $wwp_price_settings );
                
            } else if ( 
                        $current_section === 'wwpp_setting_tax_section' &&
                        !WWP_Helper_Functions::is_plugin_active( 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php' )
                    ) {

                // Tax Section
                $wwp_tax_settings = apply_filters( 'wwp_tax_section_settings' , $this->_get_tax_section_settings() );
                $settings = array_merge( $settings , $wwp_tax_settings );

            } else if ( $current_section === 'wwp_upgrade_section' ) {

                // Upgrade Section
                $wwp_upgrade_settings = apply_filters( 'wwp_upgrade_section_settings' , $this->_get_upgrade_section_settings() );
                $settings = array_merge( $settings , $wwp_upgrade_settings );
            
            }

            $settings = apply_filters( 'wwp_settings_section_content' , $settings , $current_section );

            return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

        }
        
        /**
         * General Setting.
         * This setting comes from WWPP. We maintain the prefix wwpp_ to avoid any with duplicate setting value.
         *
         * @since 1.11
         * @access public
         */
        private function _get_general_section_settings() {

            return array(

                array(
                    'name'  =>  __( 'Wholesale Prices Settings' , 'woocommerce-wholesale-prices' ),
                    'type'  =>  'title',
                    'desc'  =>  '',
                    'id'    =>  'wwp_general_settings_section_title'
                ),
                array(
                    'name'      =>  __( 'Disable Coupons For Wholesale Users', 'woocommerce-wholesale-prices' ),
                    'type'      =>  'checkbox',
                    'desc'      =>  __( 'If checked, this will prevent wholesale users from using coupons' , 'woocommerce-wholesale-prices' ),
                    'id'        =>  'wwpp_settings_disable_coupons_for_wholesale_users',
                    'class'     =>  'wwpp_settings_disable_coupons_for_wholesale_users'
                ),
                array(
                    'type'  =>  'sectionend',
                    'id'    =>  'wwp_genera_settings_sectionend'
                )

            );
            
        }
        
        /**
         * Price settings section options. This setting comes from WWPP. We maintain the prefix wwpp_ to avoid any with duplicate setting value.
         *
         * @since 1.11
         * @access public
         * 
         * @return array
         */
        private function _get_price_section_settings() {

            return array(

                array(
                    'name'  =>  __( 'Price Options', 'woocommerce-wholesale-prices' ),
                    'type'  =>  'title',
                    'desc'  =>  '',
                    'id'    =>  'wwp_settings_price_section_title'
                ),

                array(
                    'name'      =>  __( 'Wholesale Price Text' , 'woocommerce-wholesale-prices' ),
                    'type'      =>  'text',
                    'desc'      =>  '',
                    'desc_tip'  =>  __( 'Default is "Wholesale Price:"', 'woocommerce-wholesale-prices' ),
                    'id'        =>  'wwpp_settings_wholesale_price_title_text',
                    'class'     =>  'wwpp_settings_wholesale_price_title_text'
                ),
                
                array(
                    'name'      =>  __( 'Hide Original Price' , 'woocommerce-wholesale-prices' ),
                    'type'      =>  'checkbox',
                    'desc'      =>  __( 'Hide original price instead of showing a crossed out price if a wholesale price is present.', 'woocommerce-wholesale-prices' ),
                    'desc_tip'  =>  '',
                    'id'        =>  'wwpp_settings_hide_original_price',
                    'class'     =>  'wwpp_settings_hide_original_price'
                ),
                
                array(
                    'type'  =>  'sectionend',
                    'id'    =>  'wwp_settings_price_sectionend'
                )

            );

        }

        /**
         * Price settings section options. This setting comes from WWPP. We maintain the prefix wwpp_ to avoid any with duplicate setting value.
         *
         * @since 1.11
         * @access public
         * 
         * @return array
         */
        private function _get_tax_section_settings() {

            return array(

                array(
                    'name'  =>  __( 'Tax Options', 'woocommerce-wholesale-prices' ),
                    'type'  =>  'title',
                    'desc'  =>  '',
                    'id'    =>  'wwpp_settings_tax_section_title'
                ),

                array(
                    'name'      =>  __( 'Tax Exemption', 'woocommerce-wholesale-prices' ),
                    'type'      =>  'checkbox',
                    'desc'      =>  __( 'Do not apply tax to all wholesale roles', 'woocommerce-wholesale-prices' ),
                    'desc_tip'  =>  __( 'Removes tax for all wholesale roles. All wholesale prices will display excluding tax throughout the store, cart and checkout. The display settings below will be ignored.', 'woocommerce-wholesale-prices' ),
                    'id'        =>  'wwp_settings_tax_exempt_wholesale_users'
                ),

                array(
                    'name'      =>  __( 'Display Prices in the Shop', 'woocommerce-wholesale-prices' ),
                    'type'      =>  'select',
                    'class'     => 'wc-enhanced-select',
                    'desc'      =>  __( 'Choose how wholesale roles see all prices throughout your shop pages.', 'woocommerce-wholesale-prices' ),
                    'desc_tip'  =>  __( 'Note: If the option above of "Tax Exempting" wholesale users is enabled, then wholesale prices on shop pages will not include tax regardless the value of this option.', 'woocommerce-wholesale-prices' ),
                    'options'   =>  array(
                        ''      =>  __( '--Use woocommerce default--' , 'woocommerce-wholesale-prices' ),
                        'incl'  =>  __( 'Including tax (Premium)' , 'woocommerce-wholesale-prices' ),
                        'excl'  =>  __( 'Excluding tax (Premium)' , 'woocommerce-wholesale-prices' )
                    ),
                    'default'   =>  '',
                    'id'        =>  'wwp_settings_incl_excl_tax_on_wholesale_price',
                ),

                array(
                    'name'      =>  __( 'Display Prices During Cart and Checkout', 'woocommerce-wholesale-prices' ),
                    'type'      =>  'select',
                    'class'     => 'wc-enhanced-select',
                    'desc'      =>  __( 'Choose how wholesale roles see all prices on the cart and checkout pages.', 'woocommerce-wholesale-prices' ),
                    'desc_tip'  =>  __( 'Note: If the option above of "Tax Exempting" wholesale users is enabled, then wholesale prices on cart and checkout page will not include tax regardless the value of this option.', 'woocommerce-wholesale-prices' ),
                    'options'   =>  array(
                        ''      =>  __( '--Use woocommerce default--' , 'woocommerce-wholesale-prices' ),
                        'incl'  =>  __( 'Including tax (Premium)' , 'woocommerce-wholesale-prices' ),
                        'excl'  =>  __( 'Excluding tax (Premium)' , 'woocommerce-wholesale-prices' )
                    ),
                    'default'   =>  '',
                    'id'        =>  'wwp_settings_wholesale_tax_display_cart',
                ),

                array(
                    'name'      => __( 'Override Regular Price Suffix' , 'woocommerce-wholesale-prices' ),
                    'type'      => 'text',
                    'desc'      => __( 'Override the price suffix on regular prices for wholesale users.' , 'woocommerce-wholesale-prices' ),
                    'desc_tip'  => __( 'Make this blank to use the default price suffix. You can also use prices substituted here using one of the following {price_including_tax} and {price_excluding_tax}.' , 'woocommerce-wholesale-prices' ),
                    'id'        => 'wwp_settings_override_price_suffix_regular_price'
                ),

                array(
                    'name'      => __( 'Wholesale Price Suffix' , 'woocommerce-wholesale-prices' ),
                    'type'      => 'text',
                    'desc'      => __( 'Set a specific price suffix specifically for wholesale prices.' , 'woocommerce-wholesale-prices' ),
                    'desc_tip'  => __( 'Make this blank to use the default price suffix. You can also use prices substituted here using one of the following {price_including_tax} and {price_excluding_tax}.' ),
                    'id'        => 'wwp_settings_override_price_suffix'
                ),

                array(
                    'type'  =>  'sectionend',
                    'id'    =>  'wwpp_settings_tax_divider1_sectionend'
                ),

                array(
                    'name'  =>  __( 'Wholesale Role / Tax Exemption Mapping', 'woocommerce-wholesale-prices' ),
                    'type'  =>  'title',
                    'desc'  =>  sprintf( __( 'Specify tax exemption per wholesale role. Overrides general <b>"Tax Exemption"</b> option above.

                                    In the Premium add-on you can map specific wholesale roles to be tax exempt which gives you more control. This is useful for classifying customers
                                    based on their tax exemption status so you can separate those who need to pay tax and those who don\'t.
                                    
                                    This feature and more is available in the <a target="_blank" href="%1$s">Premium add-on</a> and we also have other wholesale tools available as part of the <a target="_blank" href="%2$s">Wholesale Suite Bundle</a>.' , 'woocommerce-wholesale-prices' ) ,
                                    'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=freeplugin&utm_medium=upsell&utm_campaign=wwptaxexemptionwwpplink',
                                    'https://wholesalesuiteplugin.com/bundle/?utm_source=freeplugin&utm_medium=upsell&utm_campaign=wwptaxexemptionbundlelink' )
                ),

                array(
                    'name'  =>  '',
                    'type'  =>  'wholesale_role_tax_options_mapping_controls',
                    'desc'  =>  ''
                ),

                array(
                    'type'  =>  'sectionend',
                    'id'    =>  'wwp_settings_tax_divider2_sectionend'
                ),

                array(
                    'name'  =>  __( 'Wholesale Role / Tax Class Mapping', 'woocommerce-wholesale-prices' ),
                    'type'  =>  'title',
                    'desc'  => sprintf( __( 'Specify tax classes per wholesale role.
                                    
                                    In the Premium add-on you can map specific wholesale role to specific tax classes. You can also hide those mapped tax classes from your regular
                                    customers making it possible to completely separate tax functionality for wholesale customers.
                                    
                                    This feature and more is available in the <a target="_blank" href="%1$s">Premium add-on</a> and we also have other wholesale tools available as part of the <a target="_blank" href="%2$s">Wholesale Suite Bundle</a>.' , 'woocommerce-wholesale-prices' ) , 
                                    'https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=freeplugin&utm_medium=upsell&utm_campaign=wwptaxclasswwpplink',
                                    'https://wholesalesuiteplugin.com/bundle/?utm_source=freeplugin&utm_medium=upsell&utm_campaign=wwptaxclassbundlelink' ),
                    'id'    =>  'wwp_settings_wholesale_role_tax_class_mapping_section_title'
                ),

                array(
                    'name'  =>  '',
                    'type'  =>  'wwp_upsells_buttons',
                    'desc'  =>  ''
                ),

                array(
                    'type'  =>  'sectionend',
                    'id'    =>  'wwp_settings_tax_sectionend'
                )

            );

        }

        /**
         * Price settings section options. This setting comes from WWPP. We maintain the prefix wwpp_ to avoid any with duplicate setting value.
         *
         * @since 1.11
         * @access public
         * 
         * @return array
         */
        private function _get_upgrade_section_settings() {

            // Only show Upgrade tab when WWPP is deactivated
            if( WWP_Helper_Functions::is_plugin_active( 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php' ) )
                return array();

            return array(

                array(
                    'name'  =>  '',
                    'type'  =>  'title',
                    'desc'  =>  '',
                    'id'    =>  'wwp_settings_upgrade_section_title'
                ),

                array(
                    'name'  =>  '',
                    'type'  =>  'upgrade_content',
                    'desc'  =>  '',
                    'id'    =>  'wwp_settings_upgrade_content',
                ),
                
                array(
                    'type'  =>  'sectionend',
                    'id'    =>  'wwp_settings_upgrade_sectionend'
                )

            );

        }

        /**
         * Render upgrade content
         *
         * @param $value
         * @since 1.11
         */
        public function render_upgrade_content( $value ) {

            ob_start();
            require_once ( WWP_VIEWS_PATH . 'view-wwp-upgrade-upsell.php' );
            echo ob_get_clean();

        }

        /**
         * Render upgrade content if WWPP is active.
         *
         * @param $sections
         * @since 1.11
         * @return array
         */
        public function remove_upgrade_tab( $sections ) {
            
            if( WWP_Helper_Functions::is_plugin_active( 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php' ) && isset( $sections[ 'wwp_upgrade_section' ] ) )
                unset( $sections[ 'wwp_upgrade_section' ] );
                
            return $sections;

        }
        /**
         * WWPP upsell buttons.
         *
         * @param $value
         * @since 1.11
         * @return array
         */
        public function wwp_upsells_buttons( $value ) { ?>
            <tr>
                <td style="padding: 0px; display: flex; padding-top: 20px;">
                    
                    <a class="wws-bundle-btn" target="_blank" href="https://wholesalesuiteplugin.com/bundle/?utm_source=freeplugin&utm_medium=upsell&utm_campaign=wwptaxbundlebutton">
                        <div>
                            <span><b><?php _e( 'Wholesale Suite Bundle &rarr;' , 'woocommerce-wholesale-prices' ); ?></b></span>
                            <span><?php _e( '3x wholesale plugins' , 'woocommerce-wholesale-prices' ); ?></span>
                        </div>
                    </a> 
                    <a class="wwpp-addon" target="_blank" href="https://wholesalesuiteplugin.com/woocommerce-wholesale-prices-premium/?utm_source=freeplugin&utm_medium=upsell&utm_campaign=wwptaxwwppbutton"><?php _e( 'Wholeasale Prices Premium Add-on &rarr;' , 'woocommerce-wholesale-prices' ); ?></a>
                </td>
            </tr><?php
        }

    }

}

return new WWP_Settings();

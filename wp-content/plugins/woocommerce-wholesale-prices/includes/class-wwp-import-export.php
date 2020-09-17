<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WWP_Import_Export' ) ) {

    /**
     * Model that houses the logic of wholesale roles admin page.
     *
     * @since 1.11.5
     */
    class WWP_Import_Export {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of WWP_Import_Export.
         *
         * @since 1.11.5
         * @access private
         * @var WWP_Import_Export
         */
        private static $_instance;
        
        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 1.11.5
         * @access private
         * @var WWP_Wholesale_Roles
         */
        private $_wwp_wholesale_roles;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
        */

        /**
         * WWP_Import_Export constructor.
         *
         * @since 1.11.5
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Import_Export model.
         */
        public function __construct( $dependencies ) {

            $this->_wwp_wholesale_roles = $dependencies[ 'WWP_Wholesale_Roles' ];

        }

        /**
         * Ensure that only one instance of WWP_Import_Export is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.11.5
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Import_Export model.
         * @return WWP_Import_Export
         */
        public static function instance( $dependencies = array() ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }
        
        /**
         * Bug Fix: When decimal separator is set to comma, the wholesale price is not imported properly.
         *
         * @since 1.11.5
         * @access public
         * 
         * @param array     $data       WC Product Data
         * @param object    $importer   WC_Product_CSV_Importer Object
         * @return array
         */
        public function wholesale_price_import( $data ) {

            $decimal_separator = get_option( 'woocommerce_price_decimal_sep' );

            if( $decimal_separator !== '.' && !empty( $data[ 'meta_data' ] ) ) {
                $all_roles              = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();
                $wholesale_price_meta   = array();
                
                foreach( $all_roles as $role_key => $role_data )
                    $wholesale_price_meta[] = $role_key . "_wholesale_price";
                    
                foreach( $data[ 'meta_data' ] as $index => $meta ) {
                    if( in_array( $meta[ 'key' ] , $wholesale_price_meta ) )
                        $data[ 'meta_data' ][ $index ][ 'value'] = str_replace( ',' , '.' , $meta[ 'value' ] );
                }

            }

            return $data;

        }
        
        /*
        |---------------------------------------------------------------------------------------------------------------
        | Execute model
        |---------------------------------------------------------------------------------------------------------------
        */
        
        /**
         * Execute model.
         *
         * @since 1.11.5
         * @access public
         */
        public function run() {
            
            // WC Import
            add_filter( 'woocommerce_product_import_process_item_data'  , array( $this , 'wholesale_price_import' ) , 10 , 1 );
    
        }

    }

}
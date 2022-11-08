<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Yoast_SEO_Import_Extension {

        public function __construct() {

                if ( $this->isActiveYoastSEO() ) {

                        add_filter( 'wpie_pre_post_field_mapping_section', array( $this, "get_view" ), 10, 2 );

                        add_filter( 'wpie_pre_term_field_mapping_section', array( $this, "get_view" ), 10, 2 );

                        add_filter( 'wpie_pre_user_field_mapping_section', array( $this, "get_view" ), 10, 2 );

                        add_filter( 'wpie_pre_attribute_field_mapping_section', array( $this, "get_view" ), 10, 2 );

                        add_filter( 'wpie_import_addon', array( $this, "init_addon" ), 10, 2 );

                        add_filter( 'wpie_import_yoast_addon', array( $this, "add_addon_option" ) );

                        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
                }
        }

        public function add_addon_option() {

                return true;
        }

        public function enqueue_scripts() {


                wp_enqueue_script( 'wpie-yoast-seo-js', WPIE_IMPORT_ADDON_URL . '/yoast-seo/yoast-seo.min.js', [ 'jquery' ], WPIE_PLUGIN_VERSION );

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/yoast-seo/class-wpie-yoast-seo.php';

                if ( file_exists( $fileName ) ) {
                        require_once($fileName);
                }

                $yoast_seo = new \wpie\import\seo\WPIE_Yoast_SEO_Import();

                $data = [ 'cf' => $yoast_seo->get_all_fields() ];

                wp_localize_script( 'wpie-yoast-seo-js', 'wpieYoastSettings', $data );
        }

        public function get_view( $sections = [], $wpie_import_type = "" ) {

                if ( in_array( $wpie_import_type, [ "shop_coupon", "comments", "product_reviews", "shop_order", "users", "shop_customer", "product_attributes" ] ) ) {
                        return $sections;
                }

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/yoast-seo/wpie-yoast-seo-tab.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);

                        if ( function_exists( "wpie_import_get_yoast_seo_tab" ) ) {
                                $sections = wpie_import_get_yoast_seo_tab( $sections, $wpie_import_type );
                        }
                }
                unset( $fileName );

                return $sections;
        }

        public function init_addon( $addons = [], $wpie_import_type = "" ) {

                if ( in_array( $wpie_import_type, [ "shop_coupon", "comments", "product_reviews", "shop_order", "users", "shop_customer", "product_attributes" ] ) ) {
                        return $addons;
                }


                if ( !in_array( '\wpie\import\seo\WPIE_Yoast_SEO_Import', $addons ) ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/yoast-seo/class-wpie-yoast-seo.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }
                        unset( $fileName );

                        $addons[] = '\wpie\import\seo\WPIE_Yoast_SEO_Import';
                }

                return $addons;
        }

        private function isActiveYoastSEO() {

                if ( defined( "WPSEO_VERSION" ) ) {
                        return true;
                }

                if ( function_exists( 'is_plugin_active' ) && (is_plugin_active( "wordpress-seo/wp-seo.php" ) || is_plugin_active( "wordpress-seo-premium/wp-seo-premium.php" ) ) ) {

                        return true;
                }

                return false;
        }

}

new WPIE_Yoast_SEO_Import_Extension();

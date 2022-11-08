<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Yoast_SEO_Export_Extension {

        public function __construct() {

                if ( $this->isActiveYoastSEO() ) {

                        add_filter( 'wpie_prepare_post_fields', array( $this, 'prepare_yoast_addon' ), 10, 2 );

                        add_filter( 'wpie_prepare_taxonomy_fields', array( $this, 'prepare_yoast_addon' ), 10, 2 );

                        add_filter( 'wpie_prepare_export_addons', array( $this, 'prepare_yoast_addon' ), 10, 2 );
                }
        }

        public function prepare_yoast_addon( $addons = [], $export_type = "post" ) {

                $export_type = is_array( $export_type ) && isset( $export_type[ 0 ] ) ? $export_type[ 0 ] : $export_type;

                if ( in_array( $export_type, [ "shop_coupon", "comments", "product_reviews", "shop_order", "users", "shop_customer", "product_attributes" ] ) ) {
                        return $addons;
                }

                if ( $export_type === "taxonomies" ) {

                        $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/yoast-seo/class-wpie-yoast-seo_taxonomy.php';

                        $class = '\wpie\export\yoast_seo\WPIE_Yoast_SEO_Taxonomy_Export';
                } else {
                        $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/yoast-seo/class-wpie-yoast-seo.php';

                        $class = '\wpie\export\yoast_seo\WPIE_Yoast_SEO_Export';
                }

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                if ( $class != "" && !in_array( $class, $addons ) ) {
                        $addons[] = $class;
                }

                unset( $class, $fileName );

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

new WPIE_Yoast_SEO_Export_Extension();

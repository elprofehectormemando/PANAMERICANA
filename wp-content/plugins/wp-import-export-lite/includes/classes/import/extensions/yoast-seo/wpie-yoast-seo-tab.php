<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( !function_exists( "wpie_import_get_yoast_seo_tab" ) ) {

        function wpie_import_get_yoast_seo_tab( $sections = [], $wpie_import_type = "" ) {

                $html = "";

                if ( $wpie_import_type === "taxonomies" ) {

                        if ( is_readable( __DIR__ . '/views/taxonomy.php' ) ) {
                                require_once __DIR__ . '/views/taxonomy.php';
                        }

                        if ( function_exists( "wpie_import_get_yoast_seo_taxonomy_tab" ) ) {
                                $html = wpie_import_get_yoast_seo_taxonomy_tab( $wpie_import_type );
                        }
                } elseif ( in_array( $wpie_import_type, [ "users", "shop_customer" ] ) ) {
                        if ( is_readable( __DIR__ . '/views/user.php' ) ) {
                                require_once __DIR__ . '/views/user.php';
                        }
                        if ( function_exists( "wpie_import_get_yoast_seo_user_tab" ) ) {
                                $html = wpie_import_get_yoast_seo_user_tab( $wpie_import_type );
                        }
                } else {
                        if ( is_readable( __DIR__ . '/views/post.php' ) ) {
                                require_once __DIR__ . '/views/post.php';
                        }

                        if ( function_exists( "wpie_import_get_yoast_seo_post_tab" ) ) {
                                $html = wpie_import_get_yoast_seo_post_tab( $wpie_import_type );
                        }
                }


                $sections = empty( $html ) ? $sections : array_replace( $sections, [ '233.789' => $html ] );

                unset( $html );

                return $sections;
        }

}
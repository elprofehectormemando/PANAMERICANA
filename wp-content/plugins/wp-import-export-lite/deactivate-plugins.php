<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( ! function_exists( "wpie_auto_deactivate_pro_plugins" ) ) {

        function wpie_auto_deactivate_pro_plugins() {

                $plugins = [];

                if ( is_plugin_active( 'woo-import-export/woo-import-export.php' ) || is_plugin_active( 'vj-wp-import-export/vj-wp-import-export.php' ) ) {

                        $plugins[] = 'wp-import-export-lite/wp-import-export-lite.php';
                }
                if ( ! empty( $plugins ) ) {
                        deactivate_plugins( $plugins );
                }
        }

}

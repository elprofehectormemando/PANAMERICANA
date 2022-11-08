<?php


/**
 * Compatibility with BBQ Firewall Plugin
 *
 * Prevents conflict with BBQ Firewall Plugin and Allow Ajax Request
 *
 * @since 3.9.2
 */
defined( 'ABSPATH' ) || exit;

if ( !defined( 'BBQ_VERSION' ) ) {
        return;
}

add_action( 'plugins_loaded', 'wpie_remove_bbq_core', 0, -999 );

if ( !function_exists( 'wpie_remove_bbq_core' ) ) {

        function wpie_remove_bbq_core() {

                $action = isset( $_REQUEST[ 'action' ] ) ? sanitize_text_field( $_REQUEST[ 'action' ] ) : "";

                if ( empty( $action ) || substr( $action, 0, 4 ) !== "wpie" ) {
                        return;
                }

                remove_action( 'plugins_loaded', 'bbq_core' );
        }

}


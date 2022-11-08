<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( substr( wpie_sanitize_field( $_REQUEST[ 'action' ] ), 0, 11 ) == 'wpie_export' ) {

        if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-actions.php' ) ) {

                require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-actions.php');

                $action = new \wpie\export\actions\WPIE_Export_Actions();

                unset( $action );
        }
} elseif ( substr( wpie_sanitize_field( $_REQUEST[ 'action' ] ), 0, 11 ) == 'wpie_import' ) {

        if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-actions.php' ) ) {
                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-actions.php');
                $action = new \wpie\import\WPIE_Import_Actions();

                unset( $action );
        }
} elseif ( substr( wpie_sanitize_field( $_REQUEST[ 'action' ] ), 0, 8 ) == 'wpie_ext' ) {

        if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-extensions.php' ) ) {
                require_once(WPIE_CLASSES_DIR . '/class-wpie-extensions.php');
                $wpie_ext = new \wpie\addons\WPIE_Extension();
                unset( $wpie_ext );
        }
} else {
        if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-common-action.php' ) ) {
                require_once(WPIE_CLASSES_DIR . '/class-wpie-common-action.php');
                $action = new WPIE_Common_Actions();
                unset( $action );
        }
}


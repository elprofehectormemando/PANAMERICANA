<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( !defined( 'SORT_FLAG_CASE' ) ) {
        define( 'SORT_FLAG_CASE', 5 );
}

$wp_support_version = array( "4.5", "4.6", "4.7", "4.8", "4.9", "5.0", "5.1" );

global $wp_version;

foreach ( $wp_support_version as $version ) {

        if ( version_compare( $wp_version, $version, '<' ) ) {

                if ( file_exists( __DIR__ . '/wordpress_' . $version . '.php' ) ) {
                        require_once(__DIR__ . '/wordpress_' . $version . '.php');
                }
        }
}

$plugis = [ "bbq_firewall" ];

foreach ( $plugis as $plugin ) {

        if ( file_exists( __DIR__ . '/plugins/' . $plugin . '.php' ) ) {
                require_once(__DIR__ . '/plugins/' . $plugin . '.php');
        }
}

unset( $wp_support_version );


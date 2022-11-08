<?php


/*
  Plugin Name: WP Import Export Lite
  Description: The Advanced and powerful solution for importing and exporting data to WordPress. Import and Export to Posts, Pages, and Custom Post Types. Ability to update existing data, and much more.
  Version: 3.9.23
  Author: VJInfotech
  Author URI: http://www.vjinfotech.com
  Text Domain: wp-import-export-lite
  Domain Path: /languages/
 */

if ( !defined( 'ABSPATH' ) ) {
	die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( realpath( plugin_dir_path( __FILE__ ) ) . '/deactivate-plugins.php' ) ) {
	require_once(realpath( plugin_dir_path( __FILE__ ) ) . '/deactivate-plugins.php');
        add_action( 'admin_init', 'wpie_auto_deactivate_pro_plugins' );
}

// Plugin version
if ( !defined( 'WPIE_PLUGIN_VERSION' ) ) {
	define( 'WPIE_PLUGIN_VERSION', '3.9.23' );
}
// Plugin version
if ( !defined( 'WPIE_DB_VERSION' ) ) {
	define( 'WPIE_DB_VERSION', '1.0.0' );
}

// Plugin base name
if ( !defined( 'WPIE_PLUGIN_FILE' ) ) {
	define( 'WPIE_PLUGIN_FILE', __FILE__ );
}

// Plugin Folder Path
if ( !defined( 'WPIE_PLUGIN_DIR' ) ) {
	define( 'WPIE_PLUGIN_DIR', realpath( plugin_dir_path( WPIE_PLUGIN_FILE ) ) . '/' );
}

$plugin_url = plugin_dir_url( WPIE_PLUGIN_FILE );

if ( is_ssl() ) {
	$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
}
if ( !defined( 'WPIE_PLUGIN_URL' ) ) {
	define( 'WPIE_PLUGIN_URL', untrailingslashit( $plugin_url ) );
}

// Plugin site path
if ( !defined( 'WPIE_PLUGIN_SITE' ) ) {
	define( 'WPIE_PLUGIN_SITE', 'http://www.vjinfotech.com' );
}
if ( !defined( 'WPIE_PLUGIN_API' ) ) {
	define( 'WPIE_PLUGIN_API', 'http://api.vjinfotech.com/' );
}
if ( !defined( 'WPIE_DOC_URL' ) ) {
	define( 'WPIE_DOC_URL', 'http://plugins.vjinfotech.com/wordpress-import-export/documentation/' );
}
if ( !defined( 'WPIE_SUPPORT_URL' ) ) {
	define( 'WPIE_SUPPORT_URL', 'http://www.vjinfotech.com/support/' );
}

$wpupload_dir = wp_upload_dir();

$wpie_upload_dir = $wpupload_dir[ 'basedir' ] . '/wp-import-export-lite';

$wpie_upload_url = $wpupload_dir[ 'baseurl' ] . '/wp-import-export-lite';

if ( !defined( 'WPIE_SITE_UPLOAD_DIR' ) ) {
	define( 'WPIE_SITE_UPLOAD_DIR', $wpupload_dir[ 'basedir' ] );
}

unset( $wpupload_dir );

if ( !defined( 'WPIE_UPLOAD_DIR' ) ) {
	define( 'WPIE_UPLOAD_DIR', $wpie_upload_dir );
}

if ( !defined( 'WPIE_UPLOAD_URL' ) ) {
	define( 'WPIE_UPLOAD_URL', $wpie_upload_url );
}
unset( $wpie_upload_url );

if ( !defined( 'WPIE_ASSETS_URL' ) ) {
	define( 'WPIE_ASSETS_URL', WPIE_PLUGIN_URL . '/assets' );
}

if ( !defined( 'WPIE_UPLOAD_EXPORT_DIR' ) ) {
	define( 'WPIE_UPLOAD_EXPORT_DIR', WPIE_UPLOAD_DIR . "/export" );
}

if ( !defined( 'WPIE_UPLOAD_IMPORT_DIR' ) ) {
	define( 'WPIE_UPLOAD_IMPORT_DIR', WPIE_UPLOAD_DIR . "/import" );
}

if ( !defined( 'WPIE_UPLOAD_TEMP_DIR' ) ) {
	define( 'WPIE_UPLOAD_TEMP_DIR', WPIE_UPLOAD_DIR . "/temp" );
}
if ( !defined( 'WPIE_UPLOAD_MAIN_DIR' ) ) {
	define( 'WPIE_UPLOAD_MAIN_DIR', WPIE_UPLOAD_DIR . "/upload" );
}

wp_mkdir_p( $wpie_upload_dir );

unset( $wpie_upload_dir );

if ( !is_dir( WPIE_UPLOAD_EXPORT_DIR ) ) {
	wp_mkdir_p( WPIE_UPLOAD_EXPORT_DIR );
}

if ( !is_dir( WPIE_UPLOAD_IMPORT_DIR ) ) {
	wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR );
}
if ( !is_dir( WPIE_UPLOAD_TEMP_DIR ) ) {
	wp_mkdir_p( WPIE_UPLOAD_TEMP_DIR );
}
if ( !is_dir( WPIE_UPLOAD_MAIN_DIR ) ) {
	wp_mkdir_p( WPIE_UPLOAD_MAIN_DIR );
}

if ( wp_is_writable( WPIE_UPLOAD_DIR ) && is_dir( WPIE_UPLOAD_DIR ) ) {
	@touch( WPIE_UPLOAD_DIR . '/index.php' );
}

if ( wp_is_writable( WPIE_UPLOAD_EXPORT_DIR ) && is_dir( WPIE_UPLOAD_EXPORT_DIR ) ) {
	@touch( WPIE_UPLOAD_EXPORT_DIR . '/index.php' );
}

if ( wp_is_writable( WPIE_UPLOAD_IMPORT_DIR ) && is_dir( WPIE_UPLOAD_IMPORT_DIR ) ) {
	@touch( WPIE_UPLOAD_IMPORT_DIR . '/index.php' );
}
if ( wp_is_writable( WPIE_UPLOAD_TEMP_DIR ) && is_dir( WPIE_UPLOAD_TEMP_DIR ) ) {
	@touch( WPIE_UPLOAD_TEMP_DIR . '/index.php' );
}
if ( wp_is_writable( WPIE_UPLOAD_MAIN_DIR ) && is_dir( WPIE_UPLOAD_MAIN_DIR ) ) {
	@touch( WPIE_UPLOAD_MAIN_DIR . '/index.php' );
}

if ( !defined( 'WPIE_IMPORT_ADDON_URL' ) ) {
	define( 'WPIE_IMPORT_ADDON_URL', WPIE_PLUGIN_URL . '/includes/classes/import/extensions' );
}
if ( !defined( 'WPIE_EXPORT_ADDON_URL' ) ) {
	define( 'WPIE_EXPORT_ADDON_URL', WPIE_PLUGIN_URL . '/includes/classes/export/extensions' );
}

if ( !defined( 'WPIE_CSS_URL' ) ) {
	define( 'WPIE_CSS_URL', WPIE_ASSETS_URL . '/css' );
}

if ( !defined( 'WPIE_JS_URL' ) ) {
	define( 'WPIE_JS_URL', WPIE_ASSETS_URL . '/js' );
}

if ( !defined( 'WPIE_IMAGES_URL' ) ) {
	define( 'WPIE_IMAGES_URL', WPIE_ASSETS_URL . '/images' );
}

if ( !defined( 'WPIE_INCLUDES_DIR' ) ) {
	define( 'WPIE_INCLUDES_DIR', WPIE_PLUGIN_DIR . '/includes' );
}

if ( !defined( 'WPIE_LIBRARIES_DIR' ) ) {
	define( 'WPIE_LIBRARIES_DIR', WPIE_PLUGIN_DIR . '/libraries' );
}
if ( !defined( 'WPIE_CLASSES_DIR' ) ) {
	define( 'WPIE_CLASSES_DIR', WPIE_INCLUDES_DIR . '/classes' );
}

if ( !defined( 'WPIE_IMPORT_CLASSES_DIR' ) ) {
	define( 'WPIE_IMPORT_CLASSES_DIR', WPIE_CLASSES_DIR . '/import' );
}

if ( !defined( 'WPIE_EXPORT_CLASSES_DIR' ) ) {
	define( 'WPIE_EXPORT_CLASSES_DIR', WPIE_CLASSES_DIR . '/export' );
}

if ( !defined( 'WPIE_VIEW_DIR' ) ) {
	define( 'WPIE_VIEW_DIR', WPIE_INCLUDES_DIR . '/views' );
}

if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-schedule.php' ) ) {
	require_once(WPIE_CLASSES_DIR . '/class-wpie-schedule.php');

	new \wpie\WPIE_Schedule();
}
if ( file_exists( WPIE_PLUGIN_DIR . '/support/support.php' ) ) {
	require_once(WPIE_PLUGIN_DIR . '/support/support.php');
}

if ( file_exists( WPIE_CLASSES_DIR . '/function.php' ) ) {
	require_once(WPIE_CLASSES_DIR . '/function.php');
}

if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-extensions.php' ) ) {
	require_once(WPIE_CLASSES_DIR . '/class-wpie-extensions.php');

	$wpie_ext = new \wpie\addons\WPIE_Extension();

	$wpie_ext->wpie_init_extensions();

	unset( $wpie_ext );
}


if ( file_exists( WPIE_CLASSES_DIR . '/class-updates.php' ) ) {
	require_once(WPIE_CLASSES_DIR . '/class-updates.php');

	new \wpie\Updates();
}

if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST[ 'action' ] ) && substr( wpie_sanitize_field( $_REQUEST[ 'action' ] ), 0, 4 ) == 'wpie' ) {

	if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-action.php' ) ) {
		require_once(WPIE_CLASSES_DIR . '/class-wpie-action.php');
	}
} elseif ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-general.php' ) ) {
	require_once(WPIE_CLASSES_DIR . '/class-wpie-general.php');

	new \wpie\core\WPIE_General();
}
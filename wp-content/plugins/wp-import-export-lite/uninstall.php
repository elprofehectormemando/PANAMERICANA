<?php


/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * @link    https://codecanyon.net/item/wordpress-import-export/24035782
 * @since   1.0.11
 * @package WP Import Export
 */
// If uninstall not called from WordPress, then exit.
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit;
}

//remove schedule import / exprot
wp_clear_scheduled_hook( "wpie_cron_schedule_import" );
wp_clear_scheduled_hook( "wpie_cron_schedule_export" );

if ( get_option( "wpie_delete_on_uninstall", 0 ) != 1 ) {
        return true;
}

global $wpdb;

//remove options
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wpie_%'" );

//remove tables
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wpie_template" );

// Delete all user meta related to Woo Import Export.
delete_metadata( 'user', '', 'dismissed_wpie_file_security_notice', '', true );

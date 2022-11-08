<?php

/**
 * Functions and actions related to updates.
 *
 * @since      1.5.0
 * @package    wpie
 * @subpackage wpie\Core
 * @author     VJinfotech <support@vjinfotech.com>
 */

namespace wpie;

defined( 'ABSPATH' ) || exit;

/**
 * Updates class
 * 
 * Functions and actions related to updates.
 *
 * @since 1.5.0
 * 
 * @class Updates auto update for plugin
 */
class Updates {

        /**
         * Updates that need to be run
         *
         * @since  1.5.0
         * @access private
         * 
         * @var    array
         */
        private static $updates = [
                '1.5.0' => 'update-1.5.0.php'
        ];

        /**
         * Class Constructor
         * 
         * Register hooks.
         * 
         * @since  1.5.0
         * @access public
         */
        public function __construct() {

                add_action( 'admin_init', [ $this, 'do_updates' ] );
        }

        /**
         * Check if any update is required.
         * 
         * @since  1.5.0
         * @access public
         */
        public function do_updates() {

                $installed_version = get_option( 'wpie_plugin_version' );

                // Maybe it's the first install.
                if ( ! $installed_version ) {
                        return;
                }

                if ( version_compare( $installed_version, WPIE_PLUGIN_VERSION, '<' ) ) {
                        $this->perform_updates();
                }
        }

        /**
         * Perform plugin updates.
         * 
         * Perform all database updates.
         * 
         * @since  1.5.0
         * @access public
         */
        private function perform_updates() {

                $installed_version = get_option( 'wpie_plugin_version' );

                foreach ( self::$updates as $version => $path ) {

                        $abs_path = WPIE_CLASSES_DIR . "/updates/" . $path;

                        if ( version_compare( $installed_version, $version, '<' ) && file_exists( $abs_path ) ) {
                                require_once $abs_path;
                        }
                }

                // Save install date.
                if ( false === boolval( get_option( 'wpie_install_date' ) ) ) {
                        update_option( 'wpie_install_date', current_time( 'timestamp' ) );
                }

                update_option( 'wpie_plugin_version', WPIE_PLUGIN_VERSION );

                update_option( 'wpie_db_version', WPIE_DB_VERSION );
        }

}

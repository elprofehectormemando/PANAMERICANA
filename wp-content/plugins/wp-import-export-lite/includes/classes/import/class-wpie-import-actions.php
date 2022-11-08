<?php


namespace wpie\import;

use wpie\Security;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php');
}

if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-security.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-security.php');
}

class WPIE_Import_Actions extends WPIE_Import {

        public function __construct() {

                $this->wpie_init_import_addons();

                add_action( 'wp_ajax_wpie_import_validate_uploads', array( $this, 'wpie_import_validate_uploads' ) );

                add_action( 'wp_ajax_wpie_import_get_filtered_records', array( $this, 'wpie_import_get_filtered_records' ) );

                add_action( 'wp_ajax_wpie_import_change_file', array( $this, 'wpie_import_change_file' ) );

                add_action( 'wp_ajax_wpie_import_get_fields', array( $this, 'wpie_import_get_fields' ) );

                add_action( 'wp_ajax_wpie_import_update_data', array( $this, 'wpie_import_update_data' ) );

                add_action( 'wp_ajax_wpie_import_data', array( $this, 'wpie_import_site_data' ) );

                add_action( 'wp_ajax_wpie_import_get_templates', array( $this, 'wpie_import_get_templates' ) );

                add_action( 'wp_ajax_wpie_import_get_settings', array( $this, 'wpie_import_get_settings' ) );

                add_action( 'wp_ajax_wpie_import_save_templates', array( $this, 'wpie_import_save_templates' ) );

                add_action( 'wp_ajax_wpie_import_get_template_data', array( $this, 'wpie_import_get_template_data' ) );

                add_action( 'wp_ajax_wpie_import_update_status', array( $this, 'wpie_import_update_status' ) );

                add_action( 'wp_ajax_wpie_import_get_config', array( $this, 'wpie_import_get_config' ) );

                add_action( 'wp_ajax_wpie_import_process_reimport', array( $this, 'process_reimport' ) );
        }

        private function wpie_init_import_addons() {

                if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-extensions.php' ) ) {
                        require_once(WPIE_CLASSES_DIR . '/class-wpie-extensions.php');

                        $wpie_ext = new \wpie\addons\WPIE_Extension();

                        $wpie_ext->wpie_init_extensions( "import" );

                        unset( $wpie_ext );
                }
        }

        public function wpie_import_validate_uploads() {

                Security::verify_request( 'wpie_new_import' );

                parent::wpie_parse_upload_file();
        }

        public function wpie_import_get_filtered_records() {
                Security::verify_request( 'wpie_new_import' );

                parent::wpie_import_get_filtered_records();
        }

        public function wpie_import_get_fields() {
                Security::verify_request( 'wpie_new_import' );

                parent::wpie_get_import_fields();
        }

        public function wpie_import_update_data() {
                Security::verify_request( 'wpie_new_import' );

                parent::wpie_import_save_data();
        }

        public function wpie_import_site_data() {
                Security::verify_request( 'wpie_new_import' );

                parent::wpie_import_data();
        }

        public function wpie_import_get_templates() {
                Security::verify_request( 'wpie_new_import' );

                parent::wpie_get_template_list();
        }

        public function wpie_import_get_settings() {
                Security::verify_request( 'wpie_new_import' );

                parent::wpie_get_settings_list();
        }

        public function wpie_import_save_templates() {
                Security::verify_request( 'wpie_new_import' );

                parent::wpie_import_save_template_data();
        }

        public function wpie_import_get_template_data() {
                Security::verify_request( 'wpie_new_import' );

                parent::wpie_import_get_template_info();
        }

        public function wpie_import_update_status() {
                Security::verify_request( 'wpie_new_import' );
                parent::wpie_import_update_process_status();
        }

        public function wpie_import_get_config() {
                Security::verify_request( 'wpie_new_import' );
                parent::get_config_file();
        }

        public function process_reimport() {
                Security::verify_request( 'wpie_new_import' );
                parent::process_reimport_data();
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

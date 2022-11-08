<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-security.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-security.php');
}

class WPIE_URL_Upload_Extension {

        public function __construct() {

                add_filter( 'wpie_import_upload_sections', array( $this, 'get_url_upload_views' ), 10, 1 );

                add_action( 'wp_ajax_wpie_import_upload_file_from_url', array( $this, 'upload_file_from_url' ) );
        }

        public function get_url_upload_views( $wpie_sections = array() ) {

                $wpie_sections[ "wpie_import_url_file_upload" ] = array(
                        "label" => __( "Upload From URL", 'wp-import-export-lite' ),
                        "icon"  => 'fas fa-link',
                        "view"  => WPIE_IMPORT_CLASSES_DIR . "/extensions/url-upload/wpie-url-upload-view.php",
                );

                return $wpie_sections;
        }

        public function upload_file_from_url() {

                \wpie\Security::verify_request( 'wpie_new_import' );

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/url-upload/class-wpie-url-upload.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                $upload = new wpie\import\upload\url\WPIE_URL_Upload();

                $wpie_import_id = isset( $_POST[ 'wpie_import_id' ] ) ? intval( wpie_sanitize_field( $_POST[ 'wpie_import_id' ] ) ) : 0;

                $file_url = isset( $_POST[ "file_url" ] ) ? wpie_sanitize_field(  $_POST[ "file_url" ]  ) : '';

                $file = $upload->wpie_download_file_from_url( $wpie_import_id, $file_url );

                unset( $upload, $fileName, $file_url, $wpie_import_id );

                $return_value = array( 'status' => 'error' );

                if ( is_wp_error( $file ) ) {
                        $return_value[ 'message' ] = $file->get_error_message();
                } elseif ( empty( $file ) ) {
                        $return_value[ 'erorr_message' ] = __( 'Failed to upload files', 'wp-import-export-lite' );
                } elseif ( $file == "processing" ) {
                        $return_value[ 'status' ]  = 'success';
                        $return_value[ 'message' ] = 'processing';
                } else {

                        $return_value[ 'file_list' ] = isset( $file[ 'file_list' ] ) ? $file[ 'file_list' ] : array();

                        $return_value[ 'file_count' ] = count( $return_value[ 'file_list' ] );

                        $return_value[ 'wpie_import_id' ] = isset( $file[ 'wpie_import_id' ] ) ? $file[ 'wpie_import_id' ] : 0;

                        $return_value[ 'file_name' ] = isset( $file[ 'file_name' ] ) ? $file[ 'file_name' ] : "";

                        $return_value[ 'file_size' ] = isset( $file[ 'file_size' ] ) ? $file[ 'file_size' ] : "";

                        $return_value[ 'status' ] = 'success';
                }

                echo json_encode( $return_value );

                die();
        }

}

new WPIE_URL_Upload_Extension();

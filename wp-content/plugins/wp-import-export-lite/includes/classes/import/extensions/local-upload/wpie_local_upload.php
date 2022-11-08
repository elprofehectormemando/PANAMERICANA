<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-security.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-security.php');
}

class WPIE_Local_Upload_Extension {

        public function __construct() {

                add_filter( 'wpie_import_upload_sections', array( $this, 'get_local_upload_view' ), 10, 1 );

                add_action( 'wp_ajax_wpie_import_local_upload_file', array( $this, 'upload_local_file' ) );
        }

        public function get_local_upload_view( $wpie_sections = array() ) {

                $wpie_sections[ "wpie_import_local_upload" ] = array(
                        "label" => __( "Upload from Desktop", 'wp-import-export-lite' ),
                        "icon"  => 'fas fa-upload',
                        "view"  => WPIE_IMPORT_CLASSES_DIR . "/extensions/local-upload/wpie-local-upload-view.php",
                );

                return $wpie_sections;
        }

        public function upload_local_file() {

                \wpie\Security::verify_request( 'wpie_new_import' );

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/local-upload/class-wpie-local-upload.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);
                }
                $upload = new \wpie\import\upload\local\WPIE_Local_Upload();

                $file = $upload->upload_local_file();

                unset( $fileName, $upload );

                $return_value = array( 'status' => 'error' );

                if ( is_wp_error( $file ) ) {
                        $return_value[ 'message' ] = $file->get_error_message();
                } elseif ( empty( $file ) ) {
                        $return_value[ 'erorr_message' ] = __( 'Failed to upload files', 'wp-import-export-lite' );
                } elseif ( $file == "processing" ) {
                        $return_value[ 'status' ]  = 'success';
                        $return_value[ 'message' ] = 'processing';
                } else {

                        $return_value = array_merge( $return_value, $file );

                        $return_value[ 'file_count' ] = isset( $file[ 'file_list' ] ) ? count( $file[ 'file_list' ] ) : 0;

                        $return_value[ 'status' ] = 'success';
                }

                unset( $file );

                echo json_encode( $return_value );

                die();
        }

}

new WPIE_Local_Upload_Extension();

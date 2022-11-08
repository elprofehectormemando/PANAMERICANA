<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-security.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-security.php');
}

class WPIE_Existing_File_Upload_Extension {

        public function __construct() {

                add_filter( 'wpie_import_upload_sections', array( $this, 'get_existing_file_view' ), 10, 1 );

                add_action( 'wp_ajax_wpie_import_set_existing_file', array( $this, 'prepare_existing_file' ) );
        }

        public function get_existing_file_view( $wpie_sections = array() ) {

                $wpie_sections[ "wpie_import_existing_file_upload" ] = array(
                        "label" => __( "Use existing file", 'wp-import-export-lite' ),
                        "icon"  => 'fas fa-paperclip',
                        "view"  => WPIE_IMPORT_CLASSES_DIR . "/extensions/existing-file/wpie-existing-file-view.php",
                );

                return $wpie_sections;
        }

        public function prepare_existing_file() {

                \wpie\Security::verify_request( 'wpie_new_import' );

                $require_file = WPIE_IMPORT_CLASSES_DIR . '/extensions/existing-file/class-existing-file.php';

                if ( file_exists( $require_file ) ) {

                        require_once($require_file);
                }

                $upload = new \wpie\import\upload\existingfile\WPIE_Existing_File();

                $fileName       = isset( $_GET[ 'file_name' ] ) ? wpie_sanitize_field( $_GET[ 'file_name' ] ) : "";
                $wpie_import_id = isset( $_GET[ 'wpie_import_id' ] ) ? absint( wpie_sanitize_field( $_GET[ 'wpie_import_id' ] ) ) : 0;

                $file = $upload->wpie_upload_file( $fileName, $wpie_import_id );

                unset( $fileName );

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
                unset( $file );

                echo json_encode( $return_value );

                unset( $return_value );

                die();
        }

}

new WPIE_Existing_File_Upload_Extension();

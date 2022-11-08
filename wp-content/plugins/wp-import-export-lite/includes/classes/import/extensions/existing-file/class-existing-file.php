<?php

namespace wpie\import\upload\existingfile;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php');
}

class WPIE_Existing_File extends \wpie\import\upload\WPIE_Upload {

        public function __construct() {
                
        }

        public function wpie_upload_file( $fileName = "", $wpie_import_id = "" ) {

                if ( empty( $fileName ) ) {

                        unset( $fileName );

                        return new \WP_Error( 'wpie_import_error', __( 'File Name is empty', 'wp-import-export-lite' ) );
                }

                $filePath = WPIE_UPLOAD_MAIN_DIR . "/" . $fileName;

                if ( ! file_exists( $filePath ) ) {

                        unset( $fileName, $filePath );

                        return new \WP_Error( 'wpie_import_error', __( 'File not exist', 'wp-import-export-lite' ) );
                }


                chmod( $filePath, 0755 );

                $newfiledir = parent::wpie_create_safe_dir_name( $fileName );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks" );

                copy( $filePath, WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName );

                unset( $filePath );

                return parent::wpie_manage_import_file( $fileName, $newfiledir, $wpie_import_id );
        }

}

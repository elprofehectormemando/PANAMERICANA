<?php

namespace wpie\import\upload\url;

use WP_Error;
use wpie\import\Downloader\Manager as Downloader;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php');
}

if ( is_readable( WPIE_IMPORT_CLASSES_DIR . '/downloader/manager.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/downloader/manager.php');
}

class WPIE_URL_Upload extends \wpie\import\upload\WPIE_Upload {

        public function __construct() {
                
        }

        public function wpie_download_file_from_url( $wpie_import_id = 0, $file_url = "" ) {

                if ( empty( $file_url ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File URL is empty', 'wp-import-export-lite' ) );
                }
                $download_manager = new Downloader();
                $file = $download_manager->download( $file_url );
                unset( $download_manager );

                if ( is_wp_error( $file ) ) {
                        return $file;
                } elseif ( ! is_dir( WPIE_UPLOAD_IMPORT_DIR ) || ! wp_is_writable( WPIE_UPLOAD_IMPORT_DIR ) ) {

                        return new \WP_Error( 'wpie_import_error', __( 'Uploads folder is not writable', 'wp-import-export-lite' ) );
                }

                $fileName = pathinfo( $file, PATHINFO_BASENAME );

                $newfiledir = parent::wpie_create_safe_dir_name( $fileName );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks" );

                $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName;

                chmod( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/", 0755 );

                if ( \file_exists( $file ) ) {
                        copy( $file, WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName );
                        unlink( $file );
                }

                unset( $file_url, $filePath );

                return parent::wpie_manage_import_file( $fileName, $newfiledir, $wpie_import_id );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

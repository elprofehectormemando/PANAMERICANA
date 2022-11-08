<?php

namespace wpie\import\FileFormat;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class Manager {

        private $file;
        private $to;

        public function extract( $file = "", $to = "" ) {

                $file = WPIE_UPLOAD_IMPORT_DIR . "/" . ltrim( $file, './' );


                if ( ! is_readable( $file ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Uploaded file is not readable', 'wp-import-export-lite' ) );
                }
                $this->file = $file;
                $this->to = WPIE_UPLOAD_IMPORT_DIR . "/" . ltrim( $to, './' );

                if ( preg_match( '%\W(gz)$%i', trim( $file ) ) ) {
                        return $this->gz_extract();
                } elseif ( preg_match( '%\W(zip)$%i', trim( $file ) ) ) {
                        return $this->zip_extract();
                } elseif ( preg_match( '%\W(tar)$%i', trim( $file ) ) ) {
                        return $this->tar_extract();
                }

                return new \WP_Error( 'wpie_import_error', __( "can't parse uploaded file", 'wp-import-export-lite' ) );
        }

        private function tar_extract() {

                if ( ! class_exists( '\PharData' ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Class PharData Not Found', 'wp-import-export-lite' ) );
                }

                $phar = new \PharData( $this->file );

                $phar->extractTo( $this->to );

                unset( $phar );
        }

        private function zip_extract() {

                if ( function_exists( "\unzip_file" ) ) {
                        return unzip_file( $this->file, $this->to );
                } elseif ( function_exists( "\wpie_unzip_file" ) ) {
                        return wpie_unzip_file( $this->file, $this->to );
                }
        }

        private function gz_extract() {

                if ( ! function_exists( "\gzopen" ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Function GZOPEN Not Exist', 'wp-import-export-lite' ) );
                }

                $filename = substr( \basename( $this->file ), 0, -3 );

                if ( ! preg_match( '%\W(xml|zip|csv|xls|xlsx|xml|ods|txt|json|tar)$%i', trim( $filename ) ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Uploaded file must be XML, CSV, ZIP, XLS, XLSX, XML, ODS, TXT, JSON, TAR', 'wp-import-export-lite' ) );
                }

                $newpath = $this->to . DIRECTORY_SEPARATOR . $filename;

                $fp = fopen( $newpath, 'w' );

                if ( $fp === false ) {
                        return new \WP_Error( 'wpie_import_error', __( "Can't Create file", 'wp-import-export-lite' ) );
                }

                $gfp = gzopen( $this->file, 'rb' );
                if ( $gfp === false ) {
                        fclose( $fp );
                        return new \WP_Error( 'wpie_import_error', __( "can't open GZ file", 'wp-import-export-lite' ) );
                }

                while ( ! gzeof( $gfp ) ) {

                        fwrite( $fp, gzread( $gfp, 1024 ) );
                }

                gzclose( $gfp );
                fclose( $fp );

                unset( $gfp, $fp );

                if ( preg_match( '%\W(tar)$%i', trim( $filename ) ) ) {
                        $this->file = $newpath;
                        return $this->tar_extract();
                } elseif ( preg_match( '%\W(zip)$%i', trim( $filename ) ) ) {
                        $this->file = $newpath;
                        return $this->zip_extract();
                }

                return $newpath;
        }

}

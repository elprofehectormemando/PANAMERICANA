<?php


namespace wpie\import\Downloader;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class Download {

        private $url         = "";
        private $sslverify   = false;
        private $redirection = 5;
        private $timeout     = 3000;

        public function __construct() {
                
        }

        public function download_file( $url = "" ) {

                $this->url = $url;

                if ( empty( $this->url ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File Download Error : File URL is empty', 'wp-import-export-lite' ) );
                }

                $wp_file = $this->wp_download();

                if ( is_wp_error( $wp_file ) ) {

                        $guzzle_file = $this->guzzle_download();

                        if ( !is_wp_error( $guzzle_file ) ) {
                                $wp_file = $guzzle_file;
                        }
                }

                return $wp_file;
        }

        private function wp_download() {

                $filename = time() . rand() . ".tmp";

                $file = get_temp_dir() . $filename;

                $response = wp_safe_remote_get( $this->url, [ 'timeout' => $this->timeout, 'stream' => true, 'filename' => $file, 'sslverify' => $this->sslverify ] );

                if ( is_wp_error( $response ) ) {

                        if ( file_exists( $file ) ) {
                                unlink( $file );
                        }
                        return $response;
                }

                if ( 200 != wp_remote_retrieve_response_code( $response ) ) {

                        if ( file_exists( $file ) ) {
                                unlink( $file );
                        }
                        return new \WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
                }

                $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

                if ( $content_md5 ) {

                        $md5_check = verify_file_md5( $file, $content_md5 );

                        if ( is_wp_error( $md5_check ) ) {

                                if ( file_exists( $file ) ) {
                                        unlink( $file );
                                }
                                return $md5_check;
                        }

                        unset( $md5_check );
                }


                return $file;
        }

        private function guzzle_download() {

                $filename = time() . rand() . ".tmp";

                $file = get_temp_dir() . $filename;

                \wpie_load_vendor_autoloader();

                try {
                        $client = new \GuzzleHttp\Client();

                        $response = $client->request( 'GET', $this->url, [ 'sink' => $file, 'verify' => false ] );
                } catch ( \Exception $e ) {
                        return new \WP_Error( 'download_error', $e->getMessage() );
                }

                if ( 200 != $response->getStatusCode() ) {

                        if ( file_exists( $file ) ) {
                                unlink( $file );
                        }
                        return new \WP_Error( 'download_error', __( "File Download Error : Invalid Status Code", 'wp-import-export-lite' ) );
                }

                $content_md5 = $response->getHeaderLine( 'content-type' );

                if ( $content_md5 ) {

                        $md5_check = verify_file_md5( $file, $content_md5 );

                        if ( is_wp_error( $md5_check ) ) {

                                if ( file_exists( $file ) ) {
                                        unlink( $file );
                                }
                                return $md5_check;
                        }

                        unset( $md5_check );
                }


                return $file;
        }

}

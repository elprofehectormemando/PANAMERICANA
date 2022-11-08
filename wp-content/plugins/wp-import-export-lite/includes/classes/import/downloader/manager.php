<?php


namespace wpie\import\Downloader;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( is_readable( WPIE_IMPORT_CLASSES_DIR . '/downloader/file-header.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/downloader/file-header.php');
}

class Manager {

        private $url          = null;
        private $original_url = null;
        private $filename     = null;

        public function __construct() {
                
        }

        public function get_filename( $url = "", $type = "file" ) {

                if ( empty( $url ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File Download Error : File URL is empty', 'wp-import-export-lite' ) );
                }

                if ( !preg_match( '%^(http|ftp)s?://%i', $url ) ) {
                        return new \WP_Error( 'wpie_import_error', sprintf( __( 'File Download Error : URL `%s` is not valid.', 'wp-import-export-lite' ), $url ) );
                }

                $this->original_url = $url;

                $this->url = $this->process_url( $url );

                $header = new File_Header( $this->url, $type );

                $this->filename = $header->get_filename();

                unset( $header );

                return $this->filename;
        }

        public function setFilename( $name = "" ) {
                $this->filename = $name;
        }

        public function download( $url = "", $type = "file" ) {

                if ( empty( $this->original_url ) || ((!empty( $url )) && $this->original_url !== $url ) ) {

                        $filename = $this->get_filename( $url, $type );

                        if ( is_wp_error( $filename ) ) {
                                return $filename;
                        }
                }

                if ( is_readable( WPIE_IMPORT_CLASSES_DIR . '/downloader/download.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/downloader/download.php');
                }

                $download_manager = new Download();

                $file = $download_manager->download_file( $this->url );

                unset( $download_manager );

                if ( is_wp_error( $file ) ) {

                        return $file;
                }

                $dir = pathinfo( $file, PATHINFO_DIRNAME );

                $new_file = $dir . DIRECTORY_SEPARATOR . $this->filename;

                $error = null;

                if ( !is_readable( $file ) ) {
                        $error = new \WP_Error( 'wpie_import_error', __( 'File Download Error : File is not readable', 'wp-import-export-lite' ) );
                } elseif ( filesize( $file ) === false || filesize( $file ) < 1 ) {
                        $error = new \WP_Error( 'invalid_image', __( 'File Download Error : Empty File', 'wp-import-export-lite' ) );
                } elseif ( !rename( $file, $new_file ) ) {
                        $error = new \WP_Error( 'wpie_import_error', __( 'File Download Error : Something Wrong, File rename not work.', 'wp-import-export-lite' ) );
                }

                if ( $error !== null ) {
                        if ( file_exists( $file ) ) {
                                unlink( $file );
                        }
                        return $error;
                }

                return $new_file;
        }

        private function process_url( $url = "", $format = 'csv' ) {

                $url = str_replace( " ", "%20", $url );

                preg_match( '/(?<=.com\/).*?(?=\/d)/', $url, $match );

                if ( isset( $match[ 0 ] ) && !empty( $match[ 0 ] ) ) {
                        $type = $match[ 0 ];
                } else {
                        $type = null;
                }

                $parse  = parse_url( $url );
                $domain = isset( $parse[ 'host' ] ) ? $parse[ 'host' ] : '';
                unset( $match, $parse );

                if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $match ) ) {
                        $domain = isset( $match[ 'domain' ] ) ? $match[ 'domain' ] : "";
                }
                unset( $match );

                if ( !empty( $domain ) ) {
                        switch ( $domain ) {
                                case 'dropbox.com':
                                        if ( substr( $url, -4 ) == 'dl=0' ) {
                                                $url = str_replace( 'dl=0', 'dl=1', $url );
                                        }
                                        break;
                                case 'google.com':
                                        if ( !empty( $type ) ) {
                                                switch ( $type ) {
                                                        case 'file':
                                                                $pattern = '/(?<=\/file\/d\/).*?(?=\/view)/';
                                                                preg_match( $pattern, $url, $match );
                                                                $file_id = isset( $match[ 0 ] ) ? $match[ 0 ] : null;
                                                                if ( empty( $file_id ) ) {
                                                                        $pattern = '/(?<=\/file\/d\/).*?(?=\/edit)/';
                                                                        preg_match( $pattern, $url, $match );
                                                                        $file_id = isset( $match[ 0 ] ) ? $match[ 0 ] : null;
                                                                }
                                                                if ( !empty( $file_id ) ) {
                                                                        $url = 'https://drive.google.com/uc?export=download&id=' . $file_id;
                                                                }
                                                                break;
                                                        case 'spreadsheets':
                                                                $pattern = '/(?<=\/spreadsheets\/d\/).*?(?=\/view)/';
                                                                preg_match( $pattern, $url, $match );
                                                                $file_id = isset( $match[ 0 ] ) ? $match[ 0 ] : null;
                                                                if ( empty( $file_id ) ) {
                                                                        $pattern = '/(?<=\/spreadsheets\/d\/).*?(?=\/edit)/';
                                                                        preg_match( $pattern, $url, $match );
                                                                        $file_id = isset( $match[ 0 ] ) ? $match[ 0 ] : null;
                                                                }
                                                                if ( !empty( $file_id ) ) {
                                                                        $url = 'https://docs.google.com/spreadsheets/d/' . $file_id . '/export?format=' . $format;
                                                                }
                                                                break;
                                                }
                                        }
                                        break;
                        }
                }

                if ( class_exists( '\Requests_IRI' ) && class_exists( '\Requests_IDNAEncoder' ) ) {
                        $iri       = new \Requests_IRI( $url );
                        $iri->host = \Requests_IDNAEncoder::encode( $iri->ihost );
                        $url       = $iri->uri;
                        unset( $iri );
                }

                return $url;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

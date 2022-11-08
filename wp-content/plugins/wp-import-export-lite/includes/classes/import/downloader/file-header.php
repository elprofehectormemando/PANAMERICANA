<?php


namespace wpie\import\Downloader;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class File_Header {

        private $url;
        private $type;

        public function __construct( $url = "", $type = "file" ) {
                $this->url  = $url;
                $this->type = $type;
        }

        public function get_filename() {

                $headers = $this->get_headers();

                if ( is_wp_error( $headers ) ) {
                        return $headers;
                }

                return $this->generate_filename( $headers );
        }

        private function get_headers() {

                $headers = @get_headers( $this->url, 1 );

                if ( empty( $headers ) || !is_array( $headers ) || (isset( $headers[ 0 ] ) && strpos( $headers[ 0 ], "403" ) !== false) ) {
                        return $this->get_headers_by_wp_request();
                }

                $data   = [];
                $status = "";

                foreach ( $headers as $key => $value ) {
                        if ( is_int( $key ) ) {
                                $status = $value;
                                continue;
                        } elseif ( is_array( $value ) ) {
                                $value = end( $value );
                        }
                        $key = strtolower( $key );

                        preg_replace( '#(\s+)#i', ' ', $value );

                        $data[ $key ] = $value;
                }

                $status_code = 0;
                if ( preg_match( '#^HTTP/(1\.\d)[ \t]+(\d+)#i', $status, $matches ) ) {
                        $status_code = isset( $matches[ 2 ] ) && !empty( $matches[ 2 ] ) ? intval( $matches[ 2 ] ) : 0;
                }

                $data[ "status" ] = $status_code;

                if ( $status_code !== 200 ) {
                        $error = get_status_header_desc( $status_code );
                        if ( empty( $error ) ) {
                                $error = sprintf( __( "File Download Error : %s invalid http response status code", 'wp-import-export-lite' ), $status_code );
                        }
                        return new \WP_Error( 'wpie_error', $error );
                }

                return $data;
        }

        private function get_headers_by_wp_request() {

                $response = wp_safe_remote_head( $this->url, [ 'timeout' => 30, 'redirection' => 10, 'sslverify' => false ] );

                if ( is_wp_error( $response ) ) {

                        $headers = $this->get_headers_by_guzzle_request();

                        if ( !is_wp_error( $headers ) ) {
                                return $headers;
                        }

                        return $response;
                }

                if ( 200 != wp_remote_retrieve_response_code( $response ) ) {

                        return new \WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
                }

                return wp_remote_retrieve_headers( $response );
        }

        private function get_headers_by_guzzle_request() {

                \wpie_load_vendor_autoloader();

                try {
                        $client = new \GuzzleHttp\Client();

                        $response = $client->request( 'HEAD', $this->url, [ 'verify' => false ] );
                } catch ( \Exception $e ) {
                        return new \WP_Error( 'download_error', $e->getMessage() );
                }

                if ( 200 != $response->getStatusCode() ) {

                        return new \WP_Error( 'download_error', __( "File Download Error : Invalid Status Code", 'wp-import-export-lite' ) );
                }

                return $response->getHeaders();
        }

        private function generate_filename( $headers = [] ) {

                $url_data  = parse_url( urldecode( $this->url ) );
                $url_path  = isset( $url_data[ 'path' ] ) ? $url_data[ 'path' ] : "";
                $path_info = trim( $url_path ) !== "" ? pathinfo( $url_path ) : pathinfo( urldecode( $this->url ) );
                $url_ext   = isset( $path_info[ 'extension' ] ) && !empty( $path_info[ 'extension' ] ) ? strtolower( trim( $path_info[ 'extension' ] ) ) : "";

                $valid_filename = "";
                $valid_ext      = "";

                if ( trim( $url_ext ) !== "" ) {

                        $temp_ext = $this->search_ext_from_data( $url_ext );

                        if ( trim( $temp_ext ) !== "" ) {

                                $valid_ext = $temp_ext;

                                $_filename      = isset( $path_info[ 'filename' ] ) && !empty( $path_info[ 'filename' ] ) ? $path_info[ 'filename' ] : "";
                                $valid_filename = empty( $_filename ) ? "" : $_filename . '.' . strtolower( trim( $valid_ext ) );
                                unset( $_filename );
                        }
                        unset( $temp_ext );
                }


                if ( empty( trim( $valid_ext ) ) ) {

                        $content_type = isset( $headers[ 'content-type' ] ) ? $headers[ 'content-type' ] : "";

                        //get ext from headers
                        $content_ext = $this->search_ext_from_data( $content_type );
                        if ( trim( $content_ext ) !== "" ) {
                                $valid_ext = $content_ext;
                        }
                        unset( $content_type, $content_ext );
                }

                $temp_filename = "";

                if ( empty( trim( $valid_filename ) ) ) {

                        //get headers data
                        $content_disposition = isset( $headers[ 'content-disposition' ] ) ? $headers[ 'content-disposition' ] : "";

                        //get filename from headers
                        $filename = $this->get_filename_from_content_disposition( $content_disposition );

                        if ( trim( $filename ) !== "" ) {
                                $valid_filename = pathinfo( $filename, PATHINFO_FILENAME );
                                $temp_filename  = $filename;
                        }
                        unset( $content_disposition, $filename );
                }

                if ( (trim( $valid_ext ) === "" || $valid_ext === "jpeg" ) && trim( $temp_filename ) !== "" ) {

                        $temp_ext = pathinfo( $temp_filename, PATHINFO_EXTENSION );

                        $temp_ext = $this->search_ext_from_data( $temp_ext );

                        if ( trim( $temp_ext ) !== "" && ( trim( $valid_ext ) === "" || $temp_ext === "jpg") ) {

                                $valid_ext = $temp_ext;
                        }

                        unset( $temp_ext );
                }

                if ( trim( $valid_filename ) === "" ) {
                        $valid_filename = time() . uniqid();
                }

                $valid_filename = $this->rtrim_str( $valid_filename, "." . $valid_ext ) . "." . $valid_ext;

                if ( !$this->is_valid_file( $valid_filename ) ) {
                        return new \WP_Error( 'wpie_error', __( "File Download Error : Invalid File Format", 'wp-import-export-lite' ) );
                }

                return $valid_filename;
        }

        private function rtrim_str( $str = "", $mask = "" ) {

                if ( empty( $str ) || empty( $mask ) || strpos( strtolower( $str ), strtolower( $mask ) ) === false ) {
                        return $str;
                }

                $mask = strtolower( $mask );

                $mask_len = strlen( $mask );

                while ( strtolower( substr( $str, -($mask_len) ) ) === $mask ) {
                        $str = substr( $str, 0, -($mask_len) );
                }

                return $str;
        }

        private function get_filename_from_content_disposition( $content_disposition = "" ) {

                if ( empty( $content_disposition ) ) {
                        return "";
                }

                $regex = '/.*?filename=(?<fn>[^\s]+|\x22[^\x22]+\x22)\x3B?.*$/m';

                $new_file_data = null;

                $original_name = "";

                if ( preg_match( $regex, $content_disposition, $new_file_data ) ) {

                        if ( isset( $new_file_data[ 'fn' ] ) && !empty( $new_file_data[ 'fn' ] ) ) {
                                $wp_filetype = wp_check_filetype( $new_file_data[ 'fn' ] );
                                if ( isset( $wp_filetype[ 'ext' ] ) && (!empty( $wp_filetype[ 'ext' ] )) && isset( $wp_filetype[ 'type' ] ) && (!empty( $wp_filetype[ 'type' ] )) ) {
                                        $original_name = $new_file_data[ 'fn' ];
                                }
                        }
                }

                if ( empty( $original_name ) ) {

                        $regex = '/.*filename=([\'\"]?)([^\"]+)\1/';

                        if ( preg_match( $regex, $content_disposition, $new_file_data ) ) {

                                if ( isset( $new_file_data[ '2' ] ) && !empty( $new_file_data[ '2' ] ) ) {
                                        $wp_filetype = wp_check_filetype( $new_file_data[ '2' ] );
                                        if ( isset( $wp_filetype[ 'ext' ] ) && (!empty( $wp_filetype[ 'ext' ] )) && isset( $wp_filetype[ 'type' ] ) && (!empty( $wp_filetype[ 'type' ] )) ) {
                                                $original_name = $new_file_data[ '2' ];
                                        }
                                }
                        }
                }

                return $original_name;
        }

        private function search_ext_from_data( $data = "" ) {

                if ( is_array( $data ) ) {
                        $data = implode( " ", $data );
                }
                $data = strtolower( trim( $data ) );

                if ( $data === "" ) {
                        return "";
                }

                if ( $this->type === "file" ) {
                        return $this->get_file_ext( $data );
                } elseif ( $this->type === "media" ) {

                        $fileExt    = "";
                        $mime_types = \wp_get_mime_types();

                        foreach ( $mime_types as $ext => $type ) {
                                if ( strpos( $data, $type ) !== false || strpos( $data, $ext ) !== false ) {
                                        $fileExt = $ext;
                                        break;
                                }
                        }
                        return $fileExt;
                }

                return $this->get_image_ext( $data );
        }

        private function get_file_ext( $data = "" ) {

                $ext = "";

                if ( strpos( $data, "text/xml" ) !== false || strpos( $data, "application/xml" ) !== false || $data === "xml" ) {
                        $ext = "xml";
                } elseif ( strpos( $data, "text/plain" ) !== false || $data === "txt" ) {
                        $ext = "txt";
                } elseif ( strpos( $data, "text/csv" ) !== false || $data === "csv" ) {
                        $ext = "csv";
                } elseif ( strpos( $data, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ) !== false || $data === "xlsx" ) {
                        $ext = "xlsx";
                } elseif ( strpos( $data, 'application/vnd.ms-excel' ) !== false || $data === "xls" ) {
                        $ext = "xls";
                } elseif ( strpos( $data, 'json' ) !== false ) {
                        $ext = "json";
                } elseif ( strpos( $data, 'zip' ) !== false ) {
                        $ext = "zip";
                } elseif ( strpos( $data, 'application/vnd.oasis.opendocument.spreadsheet' ) !== false || $data === "ods" ) {
                        $ext = "ods";
                }

                return $ext;
        }

        private function get_image_ext( $data = "" ) {

                $ext = "";

                if ( strpos( $data, "jpeg" ) !== false ) {
                        $ext = "jpeg";
                } elseif ( strpos( $data, "jpe" ) !== false ) {
                        $ext = "jpe";
                } elseif ( strpos( $data, "jpg" ) !== false ) {
                        $ext = "jpg";
                } elseif ( strpos( $data, "gif" ) !== false ) {
                        $ext = "gif";
                } elseif ( strpos( $data, "png" ) !== false ) {
                        $ext = "png";
                } elseif ( strpos( $data, 'bmp' ) !== false ) {
                        $ext = "bmp";
                } elseif ( strpos( $data, 'tiff' ) !== false ) {
                        $ext = "tiff";
                } elseif ( strpos( $data, 'tif' ) !== false ) {
                        $ext = "tif";
                } elseif ( strpos( $data, 'icon' ) !== false ) {
                        $ext = "ico";
                } elseif ( strpos( $data, 'svg' ) !== false ) {
                        $ext = "svg";
                } elseif ( strpos( $data, 'webp' ) !== false ) {
                        $ext = "webp";
                } elseif ( strpos( $data, 'heic' ) !== false ) {
                        $ext = "heic";
                }

                return $ext;
        }

        private function is_valid_file( $filename = "" ) {

                if ( $this->type === "file" && !preg_match( '%\W(xml|zip|csv|xls|xlsx|xml|ods|txt|json)$%i', trim( $filename ) ) ) {

                        return false;
                } elseif ( $this->type === "image" && !preg_match( '%\W(jpg|jpeg|jpe|gif|png|bmp|tif|tiff|ico|heic|webp|svg)$%i', trim( $filename ) ) ) {

                        return false;
                } elseif ( $this->type === "media" ) {

                        $media = \wp_check_filetype( trim( $filename ) );

                        if ( is_array( $media ) && isset( $media[ 'type' ] ) && $media[ 'type' ] === false ) {
                                return false;
                        }
                }

                return true;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

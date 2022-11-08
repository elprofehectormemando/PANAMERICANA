<?php


namespace wpie\import\chunk\csv;

use WP_Error;
use wpie\lib\xml\array2xml;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-chunk.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-chunk.php');
}

class WPIE_CSV_Chunk extends \wpie\import\chunk\WPIE_Chunk {

        public function __construct() {
                
        }

        public function process_csv( $fileDir = "", $file_name = "", $baseDir = "", $wpie_csv_delimiter = ",", $wpie_xml_fileName = "", $is_new_req = 0, $is_first_row_title = 1 ) {

                $file = WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

                $newFileDir = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse";

                $is_first_row_title = intval( $is_first_row_title ) > 0 ? 1 : 0;

                if ( empty( $file ) || is_readable( $file ) === false ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File is not Readable', 'wp-import-export-lite' ) );
                }

                if ( intval( $is_new_req ) === 1 || empty( $wpie_csv_delimiter ) ) {
                        $wpie_csv_delimiter = $this->analyse_file( $file );
                } else {
                        $wpie_csv_delimiter = $this->detect_delimiter( $wpie_csv_delimiter );
                }

                $enclosure = $this->detect_enclosure( $file, $wpie_csv_delimiter );

                if ( file_exists( WPIE_LIBRARIES_DIR . '/xml/class-wpie-array2xml.php' ) ) {
                        require_once(WPIE_LIBRARIES_DIR . '/xml/class-wpie-array2xml.php');
                }

                $converter = new \wpie\lib\xml\array2xml\ArrayToXml();

                $converter->create_root( "wpiedata" );

                $headers = [];

                $wfp = fopen( $file, "rb" );

                while ( ($keys = fgetcsv( $wfp, 0, $wpie_csv_delimiter, $enclosure )) !== false ) {

                        if ( $this->is_empty_array( $keys ) ) {
                                continue;
                        }
                        if ( empty( $headers ) ) {

                                $keys[ 0 ] = isset( $keys[ 0 ] ) ? $this->remove_utf8_bom( $keys[ 0 ] ) : "";

                                if ( $is_first_row_title === 1 ) {
                                        foreach ( $keys as $key => $value ) {

                                                $value = trim( strtolower( preg_replace( '/[^a-z0-9_]/i', '', $value ) ) );

                                                if ( preg_match( '/^[0-9]{1}/', $value ) ) {
                                                        $value = 'el_' . trim( strtolower( $value ) );
                                                }

                                                $value = (!empty( $value )) ? $value : 'undefined' . $key;

                                                if ( isset( $headers[ $key ] ) ) {
                                                        $key = $this->unique_array_key_name( $key, $headers );
                                                }

                                                $headers[ $key ] = $this->unescape_data( $value );
                                        }

                                        continue;
                                } else {
                                        $fieldkey_count = 1;

                                        foreach ( $keys as $key => $value ) {
                                                $value = "field_" . $fieldkey_count;

                                                if ( isset( $headers[ $key ] ) ) {
                                                        $key = $this->unique_array_key_name( $key, $headers );
                                                }

                                                $headers[ $key ] = $this->unescape_data( $value );
                                                $fieldkey_count++;
                                        }
                                }
                        }

                        $fileData = array();

                        foreach ( $keys as $key => $value ) {

                                $header = isset( $headers[ $key ] ) ? $headers[ $key ] : "";

                                if ( !empty( $header ) ) {

                                        if ( isset( $fileData[ $header ] ) ) {
                                                $header = $this->unique_array_key_name( $header, $fileData );
                                        }

                                        $fileData[ $header ] = $this->unescape_data( $value );
                                        
                                }
                        }

                        $converter->addNode( $converter->root, "item", $fileData, 0 );

                        unset( $fileData );
                }

                $converter->saveFile( $newFileDir . '/' . $wpie_xml_fileName . '1.xml' );

                unset( $file, $newFileDir, $converter, $headers );

                return [ 'delimiter' => $wpie_csv_delimiter ];
        }

        /**
         * Escape a string to be used in a CSV context
         *
         * Malicious input can inject formulas into CSV files, opening up the possibility
         * for phishing attacks and disclosure of sensitive information.
         *
         * Additionally, Excel exposes the ability to launch arbitrary commands through
         * the DDE protocol.
         *
         * @see http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
         * @see https://hackerone.com/reports/72785
         *
         * @since 3.8.1
         * @param string $data CSV field to escape.
         * @return string
         */
        private function unescape_data( $data = "" ) {

                if ( empty( $data ) || !is_string( $data ) ) {
                        return $data;
                }

                $active_content_triggers = [ "'=", "'+", "'-", "'@" ];

                if ( in_array( mb_substr( trim( $data ), 0, 2 ), $active_content_triggers, true ) ) {
                        $data = mb_substr( $data, 1 );
                }
                return $data;
        }

        /**
         * Remove UTF-8 BOM signature.
         *
         * @param string $string String to handle.
         *
         * @return string
         */
        protected function remove_utf8_bom( $string ) {

                if ( empty( $string ) ) {
                        return $string;
                }

                if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
                        $string = substr( $string, 3 );
                }

                return $string;
        }

        /**
         * Auto Detect and Correct Delimiter
         *
         * @param string $delimiter String to verify.
         *
         * @return string
         */
        protected function detect_delimiter( $delimiter = "," ) {

                if ( empty( $delimiter ) ) {
                        return ",";
                }

                if ( in_array( $delimiter, [ 'comma', ',' ] ) || strpos( $delimiter, "," ) !== false ) {
                        $delimiter = ',';
                } elseif ( in_array( $delimiter, [ 'semicolon', ';' ] ) || strpos( $delimiter, ";" ) !== false ) {
                        $delimiter = ';';
                } elseif ( in_array( $delimiter, [ 'pipe', '|' ] ) || strpos( $delimiter, "|" ) !== false ) {
                        $delimiter = '|';
                } elseif ( strpos( $delimiter, "t" ) !== false ) {
                        $delimiter = "\t";
                }

                return $delimiter;
        }

        private function analyse_file( $file = "" ) {

                if ( empty( $file ) || is_readable( $file ) === false ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File is not Readable', 'wp-import-export-lite' ) );
                }

                // read in file
                $fh       = fopen( $file, 'r' );
                $contents = fgets( $fh );
                fclose( $fh );

                // specify allowed field delimiters
                $delimiters = [
                        'comma'      => ',',
                        'semicolon'  => ';',
                        'pipe'       => '|',
                        'tabulation' => "\t"
                ];

                $delimiters = apply_filters( 'wp_import_export_specified_delimiters', $delimiters );

                $delim = ",";
                $count = 0;
                // loop and count each delimiter instance
                if ( !empty( $delimiters ) ) {

                        foreach ( $delimiters as $delimiter_key => $delimiter ) {

                                $total = count( str_getcsv( $contents, $delimiter ) );

                                if ( $total > $count ) {
                                        $delim = $delimiter;
                                        $count = $total;
                                }
                        }
                }

                return $delim;
        }

        private function detect_enclosure( $file = "", $delimiter = ',' ) {

                if ( empty( $file ) || is_readable( $file ) === false ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File is not Readable', 'wp-import-export-lite' ) );
                }

                $fp = fopen( $file, "r" );

                $enclosure = '"';

                $count = 0;

                $data_size = 0;

                while ( ($keys = fgetcsv( $fp, 0, $delimiter, $enclosure )) !== false ) {

                        if ( $this->is_empty_array( $keys ) ) {
                                continue;
                        }
                        $data_size = count( $keys );

                        $keys[ 0 ] = isset( $keys[ 0 ] ) ? $this->remove_utf8_bom( $keys[ 0 ] ) : "";

                        foreach ( $keys as $key => $value ) {

                                if ( !empty( $value ) && substr( $value, 0, 1 ) === "'" && substr( $value, -1 ) === "'" ) {
                                        $count++;
                                        continue;
                                }
                                break;
                        }

                        break;
                }
                fclose( $fp );

                if ( $data_size > 0 && $data_size === $count ) {
                        $enclosure = "'";
                }

                return $enclosure;
        }

        private function is_empty_array( $var = null ) {

                if ( !is_array( $var ) || (is_array( $var ) && count( $var ) === 0 ) ) {
                        return true;
                }
                return strlen( trim( implode( "", $var ) ) ) === 0;
        }

        private function unique_array_key_name( $key = "", $array = array() ) {

                $count = 1;

                $new_key = $key;

                while ( isset( $array[ $key ] ) ) {

                        $key = $new_key . "_" . $count;
                        $count++;
                }

                unset( $count, $new_key );

                return $key;
        }

}

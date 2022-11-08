<?php


namespace wpie\import\record;

use wpie\lib\xml\xml2array;
use XMLReader;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Record {

        private $wpie_fileName = "wpie-import-data-";
        public $record_length  = 0;
        public $tag_list       = array();

        public function __construct() {
                
        }

        public function get_records( $fileName = "", $xpath = "", $start = false, $length = false, $total = false, $tags = false, $xmlView = "single_array" ) {

                if ( file_exists( WPIE_LIBRARIES_DIR . '/xml/class-wpie-xml2array.php' ) ) {
                        require_once(WPIE_LIBRARIES_DIR . '/xml/class-wpie-xml2array.php');
                }

                $converter = new \wpie\lib\xml\xml2array\XmlToArray( $fileName );

                if ( !$converter->isValidXml() ) {
                        return "";
                }

                if ( $xpath != '//' ) {
                        $converter->set_xpath( $xpath );
                }
                $records = $converter->get_records( $start, $length, $xmlView );

                if ( $total === true ) {
                        $this->record_length = $converter->get_record_length();
                } else {
                        $this->record_length = 0;
                }
                if ( $tags === true ) {
                        $this->tag_list = $converter->get_tags();
                } else {
                        $this->tag_list = array();
                }

                unset( $converter );

                return $records;
        }

        public function auto_fetch_records_by_template( $template_options = array() ) {

                $xpath = isset( $template_options[ "xpath" ] ) ? "/" . wp_unslash( $template_options[ "xpath" ] ) : "";

                $root = isset( $template_options[ "root" ] ) ? wpie_sanitize_field( wp_unslash( $template_options[ "root" ] ) ) : "";

                $start = isset( $template_options[ "start" ] ) ? intval( wpie_sanitize_field( $template_options[ "start" ] ) ) : 0;

                $length = isset( $template_options[ "length" ] ) ? intval( wpie_sanitize_field( $template_options[ "length" ] ) ) : 1;

                $activeFile = isset( $template_options[ 'activeFile' ] ) ? $template_options[ 'activeFile' ] : "";

                $importFile = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $file_name = $fileData[ 'fileName' ] ? $fileData[ 'fileName' ] : "";

                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                $type = explode( '.', $file_name );

                $fileType = end( $type );

                $file_count = 1;

                $newFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" . $this->wpie_fileName . $file_count . '.xml';

                $isValid = $this->validateXmlFile( $newFile );

                $node_list = array();

                if ( $root == "" ) {

                        $node_list = $isValid ? $this->wpie_get_node_list( $newFile ) : [];

                        $root = $isValid ? $this->wpie_get_root_node( $node_list ) : "";

                        $xpath = "//" . $root;
                }

                $data = array();

                $data[ "root" ] = $root;

                $data[ "xpath" ] = $xpath;

                $data[ "node_list" ] = $node_list;

                $data[ "file_type" ] = $fileType;

                $data[ "content" ] = $isValid ? $this->get_records( $newFile, $xpath, $start, $length, true, true, "xml" ) : "";

                $data[ "count" ] = $this->record_length;

                $data[ "filter_element" ] = $this->tag_list;

                unset( $xpath, $root, $start, $length, $activeFile, $importFile, $fileData, $file_name, $baseDir, $type, $fileType, $file_count, $newFile, $node_list );

                return $data;
        }

        private function validateXmlFile( $file ) {

                if ( !file_exists( $file ) || filesize( $file ) === 0 ) {
                        return false;
                }

                $fp = fopen( $file, 'r' );

                if ( $fp === false ) {
                        return false;
                }

                $data = fread( $fp, 20 );

                $data = preg_replace( '/[^[:print:]]/', '', $data );

                fclose( $fp );

                if ( mb_substr( trim( $data ), 0, 1 ) === "<" || \mb_strpos( $data, "<?xml" ) !== false ) {
                        unset( $data, $fp );
                        return true;
                }

                unset( $data, $fp );

                return true;
        }

        private function wpie_get_root_node( $nodeList = array() ) {

                $wpie_xpath = "";

                if ( !empty( $nodeList ) ) {

                        $preset_elements = array( 'item', 'property', 'listing', 'hotel', 'record', 'article', 'node', 'post', 'book', 'item_0', 'job', 'deal', 'product', 'entry' );

                        foreach ( $nodeList as $element_name => $value ) {
                                if ( in_array( strtolower( $element_name ), $preset_elements ) ) {
                                        $wpie_xpath = $element_name;
                                        break;
                                }
                        }
                        unset( $preset_elements );

                        if ( empty( $wpie_xpath ) ) {
                                foreach ( $nodeList as $element => $count ) {
                                        $wpie_xpath = $element;
                                        break;
                                }
                        }
                }

                return $wpie_xpath;
        }

        private function wpie_get_node_list( $filePath = "" ) {

                if ( !file_exists( $filePath ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File not exist', 'wp-import-export-lite' ) );
                }
                if ( filesize( $filePath ) === 0 ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File is Empty', 'wp-import-export-lite' ) );
                }

                $nodeList = array();

                $reader = new \XMLReader();

                $reader->open( $filePath );

                $reader->setParserProperty( XMLReader::VALIDATE, false );

                while ( $reader->read() ) {

                        switch ( $reader->nodeType ) {

                                case (XMLREADER::ELEMENT):

                                        $localName = $reader->name;

                                        if ( isset( $nodeList[ $localName ] ) ) {
                                                $nodeList[ $localName ]++;
                                        } else {
                                                $nodeList[ $localName ] = 1;
                                        }
                                        unset( $localName );

                                        break;
                                default:

                                        break;
                        }
                }

                unset( $reader );

                return $nodeList;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

<?php


namespace wpie\import\chunk;

use DOMDocument;
use DOMXPath;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Chunk {

        private $wpie_fileName = "wpie-import-data-";

        public function __construct() {
                
        }

        public function process_data( $template_options = array() ) {

                $xpath = isset( $template_options[ "xpath" ] ) ? "/" . wp_unslash( $template_options[ "xpath" ] ) : "";

                $activeFile = isset( $template_options[ 'activeFile' ] ) ? $template_options[ 'activeFile' ] : "";

                $importFile = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $file_name = $fileData[ 'fileName' ] ? $fileData[ 'fileName' ] : "";

                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                $wpie_file_processing_type = isset( $template_options[ "wpie_file_processing_type" ] ) ? intval( wpie_sanitize_field( $template_options[ "wpie_file_processing_type" ] ) ) : "iterative";

                $split_file = 0;

                if ( $wpie_file_processing_type == "iterative" ) {
                        $split_file = isset( $template_options[ "wpie_import_split_file" ] ) ? absint( wpie_sanitize_field( $template_options[ "wpie_import_split_file" ] ) ) : 1;
                }

                $file_count = 1;

                $newFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" . $this->wpie_fileName . $file_count . '.xml';

                $dom = new \DOMDocument( '1.0', "UTF-8" );

                $dom->formatOutput = true;

                $dom->strictErrorChecking = false;

                $dom->recover = true;

                $dom->preserveWhiteSpace = false;

                $old = libxml_use_internal_errors( true );

                $new_xml_data = file_get_contents( $newFile );

                if ( !empty( $new_xml_data ) ) {
                        $new_xml_data = preg_replace( '%xmlns\s*=\s*([\'"]).*\1%sU', '', $new_xml_data );
                }

                $dom->loadXML( $new_xml_data );

                libxml_use_internal_errors( $old );

                $domxpath = new \DOMXPath( $dom );

                $elements = $domxpath->query( $xpath );

                $file_root = "wpiedata";

                unset( $importFile, $activeFile, $fileData, $newFile, $domxpath, $dom );

                if ( $elements !== false && $elements->length > 0 ) {

                        $fileDom = new \DOMDocument( '1.0', "UTF-8" );

                        $fileDom->formatOutput = true;

                        $fileDom->strictErrorChecking = false;

                        $fileDom->recover = true;

                        $fileDom->preserveWhiteSpace = false;

                        $rootElement = $fileDom->createElement( $file_root );

                        $rootChild = $fileDom->appendChild( $rootElement );

                        $filecount = 1;

                        for ( $i = 0; $i < $elements->length; $i++ ) {

                                $rootChild->appendChild( $fileDom->importNode( $elements->item( $i ), true ) );

                                if ( $split_file == 1 && ($i + 1) % 1000 == 0 ) {

                                        $fileDom->save( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . $filecount . '.xml' );

                                        $filecount++;

                                        unset( $fileDom );

                                        $fileDom = new \DOMDocument( '1.0', "UTF-8" );

                                        $fileDom->formatOutput = true;

                                        $fileDom->strictErrorChecking = false;

                                        $fileDom->recover = true;

                                        $fileDom->preserveWhiteSpace = false;

                                        $rootElement = $fileDom->createElement( $file_root );

                                        $rootChild = $fileDom->appendChild( $rootElement );

                                        unset( $rootElement );
                                }
                        }

                        $fileDom->save( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . $filecount . '.xml' );

                        unset( $fileDom );
                }

                unset( $file_count, $file_root, $elements );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

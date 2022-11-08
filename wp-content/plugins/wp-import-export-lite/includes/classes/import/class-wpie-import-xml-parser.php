<?php


namespace wpie\import\parser\xml;

use DOMDocument;
use XMLReader;
use DOMXPath;
use DOMElement;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_XML_Parser {

        private $wpie_fileName = "wpie-import-data-";
        private $wpie_filtering_element = array();
        private $wpie_element_data = array();
        private $wpie_element_length = 0;

        public function __construct() {
                
        }

        public function wpie_get_xml_filtered_records( $template_data = null ) {

                $xpath = isset( $_POST[ "xpath" ] ) ? "/" . wpie_sanitize_field( wp_unslash( $_POST[ "xpath" ] ) ) : "";

                $root = isset( $_POST[ "root" ] ) ? wpie_sanitize_field( wp_unslash( $_POST[ "root" ] ) ) : "";

                $start = isset( $_POST[ "start" ] ) ? intval( wpie_sanitize_field( $_POST[ "start" ] ) ) : 0;

                $length = isset( $_POST[ "length" ] ) ? intval( wpie_sanitize_field( $_POST[ "length" ] ) ) : 1;

                $template_options = maybe_unserialize( $template_data->options );

                $activeFile = isset( $template_options[ 'activeFile' ] ) ? wpie_sanitize_field( $template_options[ 'activeFile' ] ) : "";

                $importFile = isset( $template_options[ 'importFile' ] ) ? wpie_sanitize_field( $template_options[ 'importFile' ] ) : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? wpie_sanitize_field( $importFile[ $activeFile ] ) : "";

                $file_name = $fileData[ 'fileName' ] ? wpie_sanitize_field( $fileData[ 'fileName' ] ) : "";

                $fileDir = $fileData[ 'fileDir' ] ? wpie_sanitize_field( $fileData[ 'fileDir' ] ) : "";

                $baseDir = $fileData[ 'baseDir' ] ? wpie_sanitize_field( $fileData[ 'baseDir' ] ) : "";

                $type = explode( '.', $file_name );

                $fileType = end( $type );

                $newFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" . $this->wpie_fileName . '1.xml';

                return $this->wpie_get_filtered_records( $start, $length, $root, $xpath, $newFile, $fileType );
        }

        private function wpie_get_filtered_records( $start = 0, $length = 1, $root = "node", $xpath = "/node", $filePath = "", $fileType = "csv" ) {

                if ( !file_exists( $filePath ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'XML File Not Found', 'wp-import-export-lite' ) );
                }

                $node_list = "";

                if ( $root == "" ) {

                        $node_list = $this->wpie_get_node_list( $filePath );

                        $root = $this->wpie_get_root_node( $node_list );

                        $xpath = "//" . $root;
                }
                $data = array();

                $data[ "root" ] = $root;

                $data[ "xpath" ] = $xpath;

                $data[ "node_list" ] = $node_list;

                $data[ "file_type" ] = $fileType;

                $this->wpie_parse_xml( $filePath, $root, $xpath, false, $start, $length, "", "" );

                $data[ "count" ] = $this->wpie_element_length;

                $data[ "content" ] = array();

                if ( !empty( $this->wpie_element_data ) ) {

                        if ( isset( $this->wpie_element_data[ 0 ] ) && !empty( $this->wpie_element_data[ 0 ] ) ) {

                                $filter_element = $this->wpie_get_xml_elements_for_filtring( $this->wpie_element_data[ 0 ] );

                                if ( is_wp_error( $filter_element ) ) {
                                        return $filter_element;
                                }

                                $data[ "filter_element" ] = $filter_element;
                        }

                        foreach ( $this->wpie_element_data as $item ) {

                                $temp_content = $this->wpie_render_xml_element( $item, false, '/', 0, false );

                                if ( is_wp_error( $temp_content ) ) {
                                        return $temp_content;
                                }

                                $data[ "content" ][] = $temp_content;
                        }
                }
                unset( $this->wpie_element_data );

                unset( $this->wpie_element_length );

                return $data;
        }

        private function wpie_get_xml_elements_for_filtring( DOMElement $el, $originPath = '', $lvl = 0 ) {

                $filtering_elements = array();

                $path = $originPath;

                if ( "" != $path ) {

                        if ( $lvl > 1 ) {

                                $path .= "->" . $el->nodeName;
                        } else {
                                $path = $el->nodeName;
                        }

                        if ( empty( $this->wpie_filtering_element[ $path ] ) ) {
                                $this->wpie_filtering_element[ $path ] = 1;
                        } else {
                                $this->wpie_filtering_element[ $path ]++;
                        }

                        $filtering_elements[] = $path . '[' . $this->wpie_filtering_element[ $path ] . ']'; // '<option value="' . $path . '[' . self::$option_paths[$path] . ']">' . $path . '[' . self::$option_paths[$path] . ']</option>';
                } else {
                        $path = $el->nodeName;
                }

                if ( !empty( $el->attributes ) ) {
                        foreach ( $el->attributes as $attr ) {
                                if ( empty( $originPath ) ) {
                                        $filtering_elements[] = "@" . $attr->nodeName;
                                } else {
                                        $filtering_elements[] = $path . '[' . $this->wpie_filtering_element[ $path ] . ']' . '/@' . $attr->nodeName;
                                }
                        }
                }

                if ( $el->hasChildNodes() ) {
                        foreach ( $el->childNodes as $child ) {
                                if ( $child instanceof DOMElement ) {
                                        $element_data = $this->wpie_get_xml_elements_for_filtring( $child, $path, $lvl + 1 );
                                        $filtering_elements = array_merge( $filtering_elements, $element_data );
                                }
                        }
                }
                return $filtering_elements;
        }

        private function wpie_render_xml_element( DOMElement $el, $path = '/', $ind = 1, $lvl = 0, $shorten = false ) {

                $xml_data = array();

                if ( $lvl > 1 ) {

                        $path .= $el->nodeName;
                } else {
                        $path = $el->nodeName;
                }

                $alternativePath = $path;

                if ( !$el->parentNode instanceof DOMDocument and $ind > 0 ) {
                        $path .= "[" . $ind . "]";
                }

                $xml_data[ 'name' ] = $el->nodeName;

                $xml_data[ 'path' ] = $path;

                $xml_data[ 'attr' ] = $this->wpie_render_xml_attributes( $el, $path . '/' );

                $xml_data[ 'text' ] = "";

                if ( $el->hasChildNodes() ) {

                        $is_render_collapsed = $ind > 1;

                        if ( 1 == $el->childNodes->length and $el->childNodes->item( 0 ) instanceof DOMText ) {

                                $xml_data[ 'text' ] = $this->wpie_render_xml_text( trim( $el->childNodes->item( 0 )->wholeText ), $shorten, $is_render_collapsed );
                        } else {

                                $indexes = array();

                                foreach ( $el->childNodes as $eli => $child ) {

                                        if ( $child instanceof DOMElement ) {

                                                empty( $indexes[ $child->nodeName ] ) and $indexes[ $child->nodeName ] = 0;

                                                $indexes[ $child->nodeName ]++;

                                                $childNodes = $this->wpie_render_xml_element( $child, $path . '/', $indexes[ $child->nodeName ], $lvl + 1, $shorten );

                                                if ( !empty( $childNodes ) ) {
                                                        $xml_data[ 'childNodes' ][] = $childNodes;
                                                }
                                        } elseif ( $child instanceof DOMCdataSection ) {

                                                $xml_data[ 'text' ] = $this->wpie_render_xml_text( trim( $child->wholeText ), $shorten, false, true );
                                        } elseif ( $child instanceof DOMText ) {

                                                if ( $el->childNodes->item( $eli - 1 ) and ( $el->childNodes->item( $eli - 1 ) instanceof DOMCdataSection) ) {
                                                        
                                                } elseif ( $el->childNodes->item( $eli + 1 ) and ( $el->childNodes->item( $eli + 1 ) instanceof DOMCdataSection) ) {
                                                        
                                                } else {

                                                        $xml_data[ 'text' ] = $this->wpie_render_xml_text( trim( $child->wholeText ), $shorten );
                                                }
                                        } elseif ( $child instanceof DOMComment ) {

                                                $xml_data[ 'text' ] = $child->nodeValue;
                                        }
                                }
                        }
                }

                // $xml_data['attr'] = $this->wpie_render_xml_attributes($el);


                return $xml_data;
        }

        private function wpie_render_xml_attributes( DOMElement $el, $path = '/' ) {

                $attr_data = array();

                if ( !empty( $el->attributes ) ) {
                        foreach ( $el->attributes as $attr ) {
                                $attr_data[ $attr->nodeName ] = $attr->value;
                        }
                }

                return $attr_data;
        }

        private function wpie_render_xml_text( $text, $shorten = false, $is_render_collapsed = false, $is_cdata = false ) {

                if ( empty( $text ) and 0 !== ( int ) $text ) {
                        return;
                }
                if ( preg_match( '%\[more:(\d+)\]%', $text, $mtch ) ) {
                        return intval( $mtch[ 1 ] );
                }
                $more = '';

                if ( $shorten and preg_match( '%^(.*?\s+){20}(?=\S)%', $text, $mtch ) ) {

                        $text = $mtch[ 0 ];

                        $more = '<span class="xml-more">[' . __( 'more', 'wp-import-export-lite' ) . ']</span>';
                }

                $is_short = strlen( $text ) <= 40;

                $text = htmlspecialchars( $text );

                if ( $is_cdata ) {

                        $text = htmlspecialchars( "<![CDATA[" ) . $text . htmlspecialchars( "]]>" );
                }

                return $text . $more;
        }

        private function wpie_get_node_list( $filePath ) {

                $nodeList = array();

                $reader = new \XMLReader();

                $reader->open( $filePath );

                $reader->setParserProperty( XMLReader::VALIDATE, false );

                while ( $reader->read() ) {

                        switch ( $reader->nodeType ) {

                                case (XMLREADER::ELEMENT):

                                        $localName = str_replace( "_colon_", ":", $reader->localName );

                                        if ( array_key_exists( str_replace( ":", "_", $localName ), $nodeList ) ) {
                                                $nodeList[ str_replace( ":", "_", $localName ) ]++;
                                        } else {
                                                $nodeList[ str_replace( ":", "_", $localName ) ] = 1;
                                        }

                                        break;
                                default:

                                        break;
                        }
                }
                unset( $reader );

                return $nodeList;
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

                        if ( empty( $wpie_xpath ) ) {
                                foreach ( $nodeList as $element => $count ) {
                                        $wpie_xpath = $element;
                                        break;
                                }
                        }
                }

                return $wpie_xpath;
        }

        private function wpie_parse_xml( $filePath = "", $root = "", $xpath = "", $save_as_xml = false, $start = 0, $length = 1, $baseDir = "", $split_file = false ) {

                if ( !file_exists( $filePath ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Uploads folder is not writable', 'wp-import-export-lite' ) );
                }

                if ( !is_readable( $filePath ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Uploads file is not readable', 'wp-import-export-lite' ) );
                }

                $reader = new \XMLReader();

                $reader->open( $filePath );

                $reader->setParserProperty( XMLReader::VALIDATE, false );

                $xml_data = "";

                $temp_count = 0;

                while ( $reader->read() ) {

                        switch ( $reader->nodeType ) {

                                case (XMLREADER::ELEMENT):

                                        $localName = str_replace( "_colon_", ":", $reader->localName );

                                        if ( strtolower( str_replace( ":", "_", $localName ) ) == strtolower( $root ) ) {

                                                $xml_data .= $this->wpie_validate_rss_string( preg_replace( '%xmlns.*=\s*([\'"&quot;]).*\1%sU', '', $reader->readOuterXML() ) );

                                                $temp_count++;

                                                if ( $temp_count % 1000 == 0 ) {

                                                        $this->wpie_process_xml_element( $xml_data, $xpath, $save_as_xml, $start, $length, $baseDir, $split_file );

                                                        $xml_data = "";
                                                }
                                        }

                                        break;
                                default:
                                        // code ...
                                        break;
                        }
                }
                if ( $xml_data != "" ) {
                        $this->wpie_process_xml_element( $xml_data, $xpath, $save_as_xml, $start, $length, $baseDir, $split_file );
                }

                unset( $reader );

                unset( $xml_data );

                return true;
        }

        private function wpie_process_xml_element( $xml_data = "", $xpath = "", $save_as_xml = false, $start = 0, $length = 1, $baseDir = "", $split_file = false ) {

                $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><wpiexmlwrapper>" . "\n" . $xml_data . "</wpiexmlwrapper>";

                unset( $xml_data );

                $dom = new \DOMDocument( '1.0', "UTF-8" );

                $old = libxml_use_internal_errors( true );

                $xml = preg_replace( '%xmlns\s*=\s*([\'"]).*\1%sU', '', $xml );

                $dom->loadXML( $xml );

                unset( $xml );

                libxml_use_internal_errors( $old );

                $domxpath = new \DOMXPath( $dom );

                $elements = $domxpath->query( $xpath );

                if ( $elements !== false && $elements->length > 0 ) {

                        $new_legnth = $this->wpie_element_length;

                        $this->wpie_element_length += $elements->length;

                        if ( $length == -1 || count( $this->wpie_element_data ) < $length ) {

                                foreach ( $elements as $element ) {

                                        if ( $length == -1 || ($new_legnth >= $start) && $new_legnth < ($start + $length) ) {

                                                if ( $save_as_xml ) {

                                                        $element = $dom->saveXML( $element );

                                                        $this->wpie_element_data[] = $element;

                                                        $this->wpie_write_final_data( $baseDir, $split_file, false );
                                                } else {
                                                        $this->wpie_element_data[] = $element;
                                                }
                                        }
                                        $new_legnth++;

                                        if ( $length > 0 && count( $this->wpie_element_data ) >= $length ) {
                                                break;
                                        }
                                }
                                if ( $save_as_xml ) {
                                        $this->wpie_write_final_data( $baseDir, $split_file, true );
                                }
                        }
                }

                unset( $elements );

                unset( $dom );

                unset( $domxpath );
        }

        private function wpie_write_final_data( $baseDir = "", $split_file = false, $is_final_data = false ) {

                if ( $is_final_data || count( $this->wpie_element_data ) % 1000 == 0 ) {

                        $new_xml = implode( "", $this->wpie_element_data );

                        $this->wpie_element_data = array();

                        $xml_header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><wpiexmlwrapper>";

                        $xml_header_end = "</wpiexmlwrapper>";

                        $filecount = 1;

                        if ( $split_file ) {

                                while ( file_exists( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . $filecount . '.xml' ) ) {

                                        $filecount++;
                                }
                                file_put_contents( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . $filecount . '.xml', $xml_header . $new_xml . $xml_header_end );
                        } else {

                                if ( file_exists( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . $filecount . '.xml' ) ) {
                                        $xml_header = "";
                                }

                                file_put_contents( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . $filecount . '.xml', $xml_header . $new_xml, FILE_APPEND );
                        }

                        unset( $xml_header );

                        unset( $new_xml );
                }
        }

        private function wpie_validate_rss_string( $feed = "" ) {

                $feed = str_replace( "_colon_", ":", $feed );

                $pattern = '/(<\w+):([\w+|\.|-]+[ |>]{1})/i';

                $replacement = '$1_$2';

                $feed = preg_replace( $pattern, $replacement, $feed );

                $pattern = '/(<\/\w+):([\w+|\.|-]+>)/i';

                $replacement = '$1_$2';

                $feed = preg_replace( $pattern, $replacement, $feed );

                $is_replace_colons = apply_filters( 'wpie_replace_colons_in_attribute_names', true );

                if ( $is_replace_colons ) {

                        $pattern = '/(\s+\w+):(\w+[=]{1})/i';

                        $replacement = '$1_$2';

                        $feed = preg_replace( $pattern, $replacement, $feed );
                }

                $pattern = '/(<\w+):([\w+|\.|-]+\/>)/i';

                $replacement = '$1_$2';

                $feed = preg_replace( $pattern, $replacement, $feed );

                $is_preprocess_enabled = apply_filters( 'wpie_is_xml_preprocess_enabled', true );

                if ( $is_preprocess_enabled ) {

                        $feed = str_replace( "_ampersand_", "&", $feed );
                }

                return $this->wpie_preprocess_xml( $feed );
        }

        private function wpie_preprocess_xml( $xml = "" ) {

                $this->wpie_cdata = array();

                $is_preprocess_enabled = apply_filters( 'is_xml_preprocess_enabled', true );

                if ( $is_preprocess_enabled ) {

                        $xml = preg_replace_callback( '/<!\[CDATA\[.*?\]\]>/s', array( $this, 'wpie_import_cdata_filter' ), $xml );

                       // $xml = preg_replace( '/&([^amp;|^gt;|^lt;]+)/i', '&amp;$1', $xml );

                        if ( !empty( $this->wpie_cdata ) ) {
                                foreach ( $this->wpie_cdata as $key => $val ) {
                                        $xml = str_replace( '{{CPLACE_' . ($key + 1) . '}}', $val, $xml );
                                }
                        }
                }
                return $xml;
        }

        public function wpie_import_cdata_filter( $matches = array() ) {
                $this->wpie_cdata[] = $matches[ 0 ];
                return '{{CPLACE_' . count( $this->wpie_cdata ) . '}}';
        }

        public function wpie_import_process_file( $template_options ) {

                $xpath = isset( $template_options[ "xpath" ] ) ? "/" . wp_unslash( $template_options[ "xpath" ] ) : "";

                $root = isset( $template_options[ "root" ] ) ? wpie_sanitize_field( wp_unslash( $template_options[ "root" ] ) ) : "";

                $start = isset( $template_options[ "start" ] ) ? intval( wpie_sanitize_field( $template_options[ "start" ] ) ) : 0;

                $length = isset( $template_options[ "length" ] ) ? intval( wpie_sanitize_field( $template_options[ "length" ] ) ) : 1;

                $activeFile = isset( $template_options[ 'activeFile' ] ) ? $template_options[ 'activeFile' ] : "";

                $importFile = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $file_name = $fileData[ 'fileName' ] ? $fileData[ 'fileName' ] : "";

                $fileDir = $fileData[ 'fileDir' ] ? $fileData[ 'fileDir' ] : "";

                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                $wpie_import_file_processing = isset( $template_options[ "wpie_import_file_processing" ] ) ? wpie_sanitize_field( $template_options[ "wpie_import_file_processing" ] ) : "";

                $split_file = false;

                if ( $wpie_import_file_processing == "chunk" ) {

                        $wpie_import_split_file = isset( $template_options[ "wpie_import_split_file" ] ) ? wpie_sanitize_field( $template_options[ "wpie_import_split_file" ] ) : "";

                        if ( $wpie_import_split_file == 1 ) {
                                $split_file = true;
                        }
                }

                $newFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" . $this->wpie_fileName . '1.xml';

                $this->wpie_parse_xml( $newFile, $root, $xpath, true, 0, -1, $baseDir, $split_file );

                if ( !$split_file ) {
                        file_put_contents( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . '1.xml', "</wpiexmlwrapper>", FILE_APPEND );
                }
        }

        public function get_import_records( $template_data = NULL ) {

                $template_options = isset( $template_data->options ) && trim( $template_data->options ) != "" ? maybe_unserialize( $template_data->options ) : array();

                $xpath = isset( $template_options[ "xpath" ] ) ? "/" . wp_unslash( $template_options[ "xpath" ] ) : "";

                $root = isset( $template_options[ "root" ] ) ? wpie_sanitize_field( wp_unslash( $template_options[ "root" ] ) ) : "";

                $process_data = isset( $template_data->process_log ) ? maybe_unserialize( $template_data->process_log ) : array();

                $start = (isset( $process_data[ 'imported' ] ) && $process_data[ 'imported' ] != "") ? $process_data[ 'imported' ] : 0;

                $wpie_file_processing_type = isset( $template_options[ "wpie_file_processing_type" ] ) ? intval( wpie_sanitize_field( $template_options[ "wpie_file_processing_type" ] ) ) : "iterative";

                $split_file = "";

                $length = -1;

                if ( $wpie_file_processing_type == "iterative" ) {
                        $length = isset( $template_options[ "wpie_records_per_request" ] ) ? intval( wpie_sanitize_field( $template_options[ "wpie_records_per_request" ] ) ) : 20;
                        $split_file = isset( $template_options[ "wpie_import_split_file" ] ) ? wpie_sanitize_field( $template_options[ "wpie_import_split_file" ] ) : "";
                }

                $activeFile = isset( $template_options[ 'activeFile' ] ) ? $template_options[ 'activeFile' ] : "";

                $importFile = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $file_name = $fileData[ 'fileName' ] ? $fileData[ 'fileName' ] : "";

                $fileDir = $fileData[ 'fileDir' ] ? $fileData[ 'fileDir' ] : "";

                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                $type = explode( '.', $file_name );

                $fileType = end( $type );

                if ( $split_file == 1 ) {

                        $chunks = 1000;

                        if ( $start > $chunks ) {
                                $start_file = ($start / $chunks) + 1;
                                $start = $start % $chunks;
                        } else {
                                $start_file = 1;
                                $start = $start;
                        }

                        $newFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . $start_file . '.xml';
                } else {
                        $newFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . '1.xml';
                }
                $this->wpie_element_data = array();

                $this->wpie_element_length = 0;

                $this->wpie_parse_xml( $newFile, $root, $xpath, false, $start, $length, "", "" );

                $data = array();

                if ( !empty( $this->wpie_element_data ) ) {

                        foreach ( $this->wpie_element_data as $item ) {

                                $data[] = $this->wpie_final_render_xml_element( $item, false, '/', 0, false );

                                if ( is_wp_error( $data ) ) {
                                        break;
                                }
                        }
                }
                unset( $this->wpie_element_data );

                unset( $this->wpie_element_length );

                return $data;
        }

        protected function wpie_parse_import_data( $wpie_import_data = array(), $wpie_str = "" ) {

                if ( is_array( $wpie_import_data ) && !empty( $wpie_import_data ) ) {
                        $wpie_str = str_replace( array_keys( $wpie_import_data ), array_values( $wpie_import_data ), $wpie_str );
                }
                return $wpie_str;
        }

        private function wpie_final_render_xml_element( DOMElement $el, $path = '/', $ind = 1, $lvl = 0 ) {

                $xml_data = array();

                if ( $lvl > 1 ) {

                        $path .= $el->nodeName;
                } else {
                        $path = $el->nodeName;
                }

                $alternativePath = $path;

                if ( !$el->parentNode instanceof DOMDocument and $ind > 0 ) {
                        $path .= "[" . $ind . "]";
                }

                if ( $el->hasChildNodes() ) {

                        if ( !empty( $el->attributes ) ) {
                                foreach ( $el->attributes as $attr ) {
                                        $xml_data[ $path . '/@' . $attr->nodeName ] = $attr->value;
                                }
                        }

                        if ( 1 == $el->childNodes->length and $el->childNodes->item( 0 ) instanceof DOMText ) {

                                $xml_data[ $path ] = $el->childNodes->item( 0 )->wholeText;
                        } else {

                                $indexes = array();

                                foreach ( $el->childNodes as $eli => $child ) {

                                        if ( $child instanceof DOMElement ) {

                                                empty( $indexes[ $child->nodeName ] ) and $indexes[ $child->nodeName ] = 0;

                                                $indexes[ $child->nodeName ]++;

                                                $childNodes = $this->wpie_final_render_xml_element( $child, $path . '/', $indexes[ $child->nodeName ], $lvl + 1 );

                                                if ( !empty( $childNodes ) ) {
                                                        $xml_data = array_merge( $xml_data, $childNodes );
                                                }
                                        } elseif ( $child instanceof DOMCdataSection ) {

                                                $xml_data[ $path ] = $child->wholeText;
                                        } elseif ( $child instanceof DOMText ) {
                                                //   $xml_data[$path] = $child->wholeText;
                                        } elseif ( $child instanceof DOMComment ) {

                                                $xml_data[ $path ] = $child->nodeValue;
                                        }
                                }
                        }
                }

                if ( !empty( $el->attributes ) ) {
                        foreach ( $el->attributes as $attr ) {
                                $xml_data[ $path . '/@' . $attr->nodeName ] = $attr->value;
                        }
                }

                return $xml_data;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

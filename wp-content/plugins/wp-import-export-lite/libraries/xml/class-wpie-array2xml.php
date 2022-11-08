<?php


namespace wpie\lib\xml\array2xml;

class ArrayToXml {

        /**
         * The root DOM Document.
         *
         * @var DOMDocument
         */
        protected $document;

        /**
         * Set to enable replacing space with underscore.
         *
         * @var bool
         */
        protected $replaceSpacesByUnderScoresInKeyNames = true;

        /**
         * Root element of xml
         *
         * @var string
         */
        public $root;

        /**
         * Skip empty nodes
         *
         * @var string
         */
        private $skip_empty = false;

        /**
         * Construct a new instance.
         *
         * @param string[] $array
         * @param string|array $rootElement
         * @param bool $replaceSpacesByUnderScoresInKeyNames
         * @param string $xmlEncoding
         * @param string $xmlVersion
         *
         * @throws DOMException
         */
        public function __construct( $replaceSpacesByUnderScoresInKeyNames = true, $xmlEncoding = 'UTF-8', $xmlVersion = '1.0', $formatOutput = true ) {

                $this->document = new \DOMDocument( $xmlVersion, $xmlEncoding );

                $this->replaceSpacesByUnderScoresInKeyNames = $replaceSpacesByUnderScoresInKeyNames;

                $this->document->formatOutput = $formatOutput;

                // $this->convertElement($this->root, $array, 0);
        }

        public function load_file( $fileName = "" ) {
                if ( file_exists( $fileName ) ) {
                        $this->document->load( $fileName );
                }
        }

        public function getElementsByTagName( $name = "" ) {
                $this->document->getElementsByTagName( $name );
        }

        public function skip_empty() {
                $this->skip_empty = true;
        }

        public function append_child( $parent_tag = "", $key = "", $value = array() ) {

                $elements = $this->document->getElementsByTagName( $parent_tag );

                if ( empty( $elements ) || is_null( $elements ) || !$elements->item( 0 ) ) {
                        $element = $this->create_root( $parent_tag );
                } else {
                        $element = $elements->item( 0 );
                }

                $this->addNode( $element, $key, $value );
        }

        public function create_root( $rootElement = '' ) {
                $this->root = $this->createRootElement( $rootElement );

                return $this->document->appendChild( $this->root );
        }

        /**
         * Convert the given array to an xml string.
         *
         * @param string[] $array
         * @param string|array $rootElement
         * @param bool $replaceSpacesByUnderScoresInKeyNames
         * @param string $xmlEncoding
         * @param string $xmlVersion
         *
         * @return string
         */
        public static function convert( array $array, $rootElement = '', $replaceSpacesByUnderScoresInKeyNames = true, $xmlEncoding = null, $xmlVersion = '1.0' ) {
                $converter = new static( $array, $rootElement, $replaceSpacesByUnderScoresInKeyNames, $xmlEncoding, $xmlVersion );

                return $converter->toXml();
        }

        /**
         * Return as XML.
         *
         * @return string
         */
        public function toXml() {
                return $this->document->saveXML();
        }

        /**
         * save XML to specific file
         *
         * @param string $fileName
         */
        public function saveFile( $fileName = "" ) {
                return $this->document->save( $fileName );
        }

        /**
         * Return as DOM object.
         *
         * @return DOMDocument
         */
        public function toDom() {
                return $this->document;
        }

        /**
         * Parse individual element.
         *
         * @param DOMElement $element
         * @param string|string[] $value
         */
        public function convertElement( \DOMElement $element, $value, $lvl = 0 ) {

                if ( !is_array( $value ) ) {

                        if ( !is_null( $value ) && $value != "" ) {

                                if ( is_string( $value ) && seems_utf8( $value ) === false ) {
                                        $value = utf8_encode( $value );
                                }

                                $value = $this->removeControlCharacters( $value );

                                $element->appendChild( $this->document->createCDATASection( $value ) );
                        } else {
                                $element->nodeValue = "";
                        }

                        unset( $value );

                        return;
                } elseif ( is_array( $value ) && !empty( $value ) ) {

                        foreach ( $value as $key => $data ) {

                                if ( ($key === '_attributes') || ($key === '@attributes') ) {
                                        $this->addAttributes( $element, $data );
                                } elseif ( (($key === '_value') || ($key === '@value')) && is_string( $data ) ) {
                                        $element->appendChild( $this->document->createCDATASection( $data ) );
                                } elseif ( (($key === '_cdata') || ($key === '@cdata')) && is_string( $data ) ) {
                                        $element->appendChild( $this->document->createCDATASection( $data ) );
                                } elseif ( (($key === '_mixed') || ($key === '@mixed')) && is_string( $data ) ) {
                                        $fragment = $this->document->createDocumentFragment();
                                        $fragment->appendXML( $data );
                                        $element->appendChild( $fragment );
                                        unset( $fragment );
                                } else {
                                        if ( is_numeric( $key ) ) {

                                                $key = "item";

                                                if ( $lvl > 0 ) {

                                                        $key = $key . "_" . $lvl;
                                                }
                                        }
                                        $this->addNode( $element, $key, $data, $lvl );
                                }
                        }
                }
        }

        /**
         * Add node.
         *
         * @param DOMElement $element
         * @param string $key
         * @param string|string[] $value
         */
        public function addNode( \DOMElement $element, $key, $value, $lvl = 0 ) {

                if ( $this->skip_empty === true && is_scalar( $value ) && trim( $value ) === "" ) {
                        return;
                }

                $key = preg_replace( '/[^a-z0-9_]/i', '', $key );

                if ( $this->replaceSpacesByUnderScoresInKeyNames ) {
                        $key = str_replace( ' ', '_', $key );
                }

                $child = $this->document->createElement( strtolower( $key ) );

                $element->appendChild( $child );

                $this->convertElement( $child, $value, $lvl + 1 );

                unset( $key, $child );
        }

        /**
         * Add attributes.
         *
         * @param DOMElement $element
         * @param string[] $data
         */
        protected function addAttributes( $element, $data ) {
                if ( !empty( $data ) ) {
                        foreach ( $data as $attrKey => $attrVal ) {
                                $element->setAttribute( $attrKey, $attrVal );
                        }
                }
        }

        /**
         * Create the root element.
         *
         * @param  string|array $rootElement
         * @return DOMElement
         */
        protected function createRootElement( $rootElement ) {
                if ( is_string( $rootElement ) ) {
                        $rootElementName = $rootElement ?: 'root';

                        unset( $rootElement );

                        return $this->document->createElement( strtolower( $rootElementName ) );
                }

                $rootElementName = isset( $rootElement[ 'rootElementName' ] ) ? $rootElement[ 'rootElementName' ] : 'root';

                $element = $this->document->createElement( strtolower( $rootElementName ) );

                unset( $rootElementName );

                if ( is_array( $rootElement ) && !empty( $rootElement ) ) {
                        foreach ( $rootElement as $key => $value ) {
                                if ( $key !== '_attributes' && $key !== '@attributes' ) {
                                        continue;
                                }

                                $this->addAttributes( $element, $rootElement[ $key ] );
                        }
                }
                unset( $rootElement );

                return $element;
        }

        /**
         * @param $valuet
         * @return string
         */
        protected function removeControlCharacters( $value ) {
                return preg_replace( '/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $value );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

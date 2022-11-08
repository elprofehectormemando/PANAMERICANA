<?php


namespace wpie\lib\xml\xml2array;

class XmlToArray {

	protected $document;
	protected $domxpath;
	protected $xpathelement;
	private $xmldata		 = array();
	private $wpie_filtering_element	 = array();
	private $isValidXml		 = true;

	public function __construct( $fileName = "", $xml = "", $xmlEncoding = 'UTF-8', $xmlVersion = '1.0', $formatOutput = true, $preserveWhiteSpace = false ) {

		$this->document = new \DOMDocument( $xmlVersion, $xmlEncoding );

		$this->document->formatOutput = $formatOutput;

		$this->document->strictErrorChecking = false;

		$this->document->recover = true;

		$this->document->preserveWhiteSpace = $preserveWhiteSpace;

		$new_xml = "";

		if ( !empty( $fileName ) && file_exists( $fileName ) ) {
			$new_xml = file_get_contents( $fileName );
		} elseif ( !empty( $xml ) ) {
			$new_xml = $xml;
		}

		if ( mb_substr( $new_xml, 0, 1 ) !== "<" && \mb_strpos( $new_xml, "<?xml" ) === false ) {
			$this->isValidXml = false;
			return;
		}

		if ( !empty( $new_xml ) ) {

			$new_xml = preg_replace( '%xmlns\s*=\s*([\'"]).*\1%sU', '', $new_xml );

			if ( !seems_utf8( $new_xml ) ) {
				$new_xml = utf8_encode( $new_xml );
			}

			$new_xml = preg_replace( '/[^\x9\xa\x20-\xD7FF\xE000-\xFFFD]/', '', $new_xml );

			$this->document->loadXML( $new_xml );
		}
	}

	public function isValidXml() {
		return $this->isValidXml;
	}

	public function set_xpath( $xpath = "" ) {

		$this->domxpath = new \DOMXPath( $this->document );

		$this->xpathelement = $this->domxpath->query( $xpath );
	}

	public function get_record_length() {

		if ( isset( $this->xpathelement->length ) ) {
			return $this->xpathelement->length;
		}
		return 0;
	}

	public function get_records( $start = false, $length = false, $xmlView = "array" ) {

		$result = [];

		if ( $this->xpathelement !== false && $this->xpathelement->length > 0 ) {

			if ( $start === false || $length === false ) {
				$new_start	 = 0;
				$final_length	 = $this->xpathelement->length;
			} else {
				$new_start	 = max( absint( $start ), 0 );
				$length		 = min( $length, $this->xpathelement->length );
				$final_length	 = $start + $length;
			}

			for ( $i = $new_start; $i < $final_length; $i++ ) {

				if ( $i >= $this->xpathelement->length ) {
					break;
				}

				if ( $xmlView == "xml" ) {
					$result[] = $this->convertXMLView( $this->xpathelement->item( $i ), false, '/', 0 );
				} elseif ( $xmlView = "single_array" ) {

					$this->xmldata = [];

					$this->convertSingleArray( $this->xpathelement->item( $i ), false, '/', 0 );

					$result[] = $this->xmldata;
				} else {
					$result[] = $this->convertDomElement( $this->xpathelement->item( $i ) );
				}
			}
			unset( $new_start, $final_length, $i );
		}

		return $result;
	}

	public function get_tags() {
		if ( $this->xpathelement !== false && $this->xpathelement->length > 0 ) {
			$this->wpie_filtering_element = array();
			return $this->get_tag_list( $this->xpathelement->item( 0 ) );
		}
	}

	private function get_tag_list( \DOMElement $element, $originPath = '', $lvl = 0 ) {

		$filtering_elements = array();

		$path = $originPath;

		if ( "" != $path ) {

			if ( $lvl > 1 ) {

				$path .= "->" . $element->nodeName;
			} else {
				$path = $element->nodeName;
			}

			if ( empty( $this->wpie_filtering_element[ $path ] ) ) {
				$this->wpie_filtering_element[ $path ] = 1;
			} else {
				$this->wpie_filtering_element[ $path ]++;
			}

			$filtering_elements[] = $path . '[' . $this->wpie_filtering_element[ $path ] . ']';
		} else {
			$path = $element->nodeName;
		}

		if ( !empty( $element->attributes ) ) {
			foreach ( $element->attributes as $attr ) {
				if ( empty( $originPath ) ) {
					$filtering_elements[] = "@" . $attr->nodeName;
				} else {
					$filtering_elements[] = $path . '[' . $this->wpie_filtering_element[ $path ] . ']' . '/@' . $attr->nodeName;
				}
			}
		}

		if ( $element->hasChildNodes() ) {
			foreach ( $element->childNodes as $child ) {
				if ( $child instanceof \DOMElement ) {
					$element_data		 = $this->get_tag_list( $child, $path, $lvl + 1 );
					$filtering_elements	 = array_merge( $filtering_elements, $element_data );
					unset( $element_data );
				}
			}
		}
		unset( $path );
		return $filtering_elements;
	}

	public function toArray() {

		$result = array();

		if ( $this->document->hasChildNodes() ) {

			$children = $this->document->childNodes;

			foreach ( $children as $child ) {
				$result[ $child->nodeName ] = $this->convertDomElement( $child );
			}
			unset( $children );
		}

		return $result;
	}

	private function convertSingleArray( \DOMElement $element, $path = '/', $ind = 1, $lvl = 0 ) {

		if ( is_null( $element ) ) {
			return;
		}

		if ( $lvl > 1 ) {

			$path .= $element->nodeName;
		} else {
			$path = $element->nodeName;
		}

		if ( !$element->parentNode instanceof \DOMDocument and $ind > 0 ) {
			$path .= "[" . $ind . "]";
		}

		if ( $element->hasAttributes() ) {

			foreach ( $element->attributes as $attr ) {

				$this->xmldata[ "{" . $path . "/@" . $attr->nodeName . "}" ] = $attr->value;
			}
		}

		if ( $element->hasChildNodes() ) {

			$index = array();

			foreach ( $element->childNodes as $key => $node ) {

				$index[ $node->nodeName ] = isset( $index[ $node->nodeName ] ) ? ++$index[ $node->nodeName ] : 1;

				if ( $node instanceof \DOMCdataSection ) {
					$this->xmldata[ "{" . $path . "}" ] = isset( $node->data ) ? $node->data : (isset( $node->nodeValue ) ? $node->nodeValue : "");
				} elseif ( $node instanceof \DOMText ) {
					$this->xmldata[ "{" . $path . "}" ] = $node->textContent;
				} elseif ( $node instanceof \DOMElement ) {

					$this->convertSingleArray( $node, $path . '/', $index[ $node->nodeName ], $lvl + 1 );
				}
			}
			unset( $index );
		} else {
			$this->xmldata[ "{" . $path . "}" ] = "";
		}

		unset( $element );
	}

	private function convertXMLView( \DOMElement $element, $path = '/', $ind = 1, $lvl = 0 ) {

		if ( is_null( $element ) ) {
			return;
		}

		if ( $lvl > 1 ) {

			$path .= $element->nodeName;
		} else {
			$path = $element->nodeName;
		}

		if ( !$element->parentNode instanceof \DOMDocument and $ind > 0 ) {
			$path .= "[" . $ind . "]";
		}

		$result = array();

		$result[ "name" ] = $element->nodeName;

		$result[ "path" ] = $path;

		if ( $element->hasAttributes() ) {

			$attributes = $this->convertAttributes( $element->attributes );

			if ( !empty( $attributes ) ) {
				$result[ "@attributes" ] = $attributes;
			}
			unset( $attributes );
		}

		if ( $element->hasChildNodes() ) {

			$index = array();

			foreach ( $element->childNodes as $key => $node ) {

				$index[ $node->nodeName ] = isset( $index[ $node->nodeName ] ) ? ++$index[ $node->nodeName ] : 1;

				if ( $node instanceof \DOMCdataSection ) {
					$result[ "value" ] = isset( $node->data ) ? $node->data : (isset( $node->nodeValue ) ? $node->nodeValue : "");
				} elseif ( $node instanceof \DOMText ) {
					$result[ "value" ] = $node->textContent;
				} elseif ( $node instanceof \DOMElement ) {

					$result[ "value" ][] = $this->convertXMLView( $node, $path . '/', $index[ $node->nodeName ], $lvl + 1 );
				}
			}
			unset( $index );
		}

		unset( $element, $path );

		if ( empty( $result ) ) {
			return "";
		}

		return $result;
	}

	private function convertDomElement( \DOMElement $element ) {

		if ( is_null( $element ) ) {
			return "";
		}

		$result = array();

		if ( $element->hasAttributes() ) {

			$attributes = $this->convertAttributes( $element->attributes );

			if ( !empty( $attributes ) ) {
				$result[ "@attributes" ] = $attributes;
			}
			unset( $attributes );
		}

		if ( $element->hasChildNodes() ) {

			$index = array();

			foreach ( $element->childNodes as $key => $node ) {

				$index[ $node->nodeName ] = isset( $index[ $node->nodeName ] ) ? ++$index[ $node->nodeName ] : 0;

				if ( $node instanceof \DOMCdataSection ) {
					$result = isset( $node->data ) ? $node->data : (isset( $node->nodeValue ) ? $node->nodeValue : "");
				} elseif ( $node instanceof \DOMText ) {
					$result = $node->textContent;
				} elseif ( $node instanceof \DOMElement ) {

					$nodeData = $this->convertDomElement( $node );

					if ( $index[ $node->nodeName ] > 0 ) {

						if ( $index[ $node->nodeName ] == 1 ) {

							$result[ $node->nodeName ] = array( $result[ $node->nodeName ], $nodeData );
						} else {
							$result[ $node->nodeName ][] = $nodeData;
						}
					} else {
						$result[ $node->nodeName ] = $nodeData;
					}
					unset( $nodeData );
				}
			}
			unset( $index );
		}

		unset( $element );

		if ( empty( $result ) ) {
			return "";
		}

		return $result;
	}

	private function convertAttributes( \DOMNamedNodeMap $nodeMap ) {

		if ( $nodeMap->length === 0 ) {
			return null;
		}

		$attributes = array();

		/** @var DOMAttr $item */
		foreach ( $nodeMap as $item ) {
			$attributes[ $item->name ] = $item->value;
		}

		return $attributes;
	}

	public function __destruct() {
		foreach ( $this as $key => $value ) {
			unset( $this->$key );
		}
	}

}

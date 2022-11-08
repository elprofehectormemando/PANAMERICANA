<?php


namespace wpie\import\base;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

abstract class WPIE_Import_Base {

        protected $wpie_import_id     = 0;
        protected $wpie_import_option = array();
        protected $import_log         = array();
        protected $process_log        = array();
        protected $wpie_import_record = array();
        protected $is_new_item        = true;
        protected $item_id            = 0;
        protected $item;
        protected $existing_item_id   = 0;
        protected $wpie_final_data    = array();
        protected $log_service        = false;
        protected $backup_service     = false;
        protected $import_type;
        protected $as_draft           = false;
        protected $base_dir           = false;
        protected $wpie_fileName      = "wpie-import-data-";
        protected $addons             = array();
        protected $addon_error        = false;
        protected $addon_log          = array();
        protected $import_username    = "";

        public function __construct() {
                
        }

        protected function get_item_id() {
                return $this->item_id;
        }

        protected function get_user_id() {

                if ( $this->import_type == "post" ) {

                        $id = wpie_sanitize_field( $this->get_field_value( "post_author" ) );

                        $user = get_user_by( "id", $id );

                        if ( $user !== false ) {
                                unset( $user );
                                return $id;
                        }

                        unset( $id, $user );
                }
                return get_current_user_id();
        }

        /**
         * Get field value from file or options
         *
         * @since 1.0.0
         *
         * @param string    $field          Field to get value
         * @param bool      $is_option      if true then get value from option
         * @param bool      $as_specified   if value is as_specified then get value from $field."_as_specified" filed value
         * @return mixed if is_option is true then value will get from direct option else from file.
         */
        protected function get_field_value( $field = "", $is_option = false, $as_specified = false ) {

                if ( empty( $field ) ) {
                        return "";
                }

                if ( $is_option ) {
                        $field_data = isset( $this->wpie_import_option[ $field ] ) ? $this->wpie_import_option[ $field ] : "";
                } elseif ( isset( $this->wpie_import_option[ $field ] ) && !empty( $this->wpie_import_option[ $field ] ) ) {
                        $field_data = $this->get_field( $this->wpie_import_option[ $field ] );
                } else {
                        $field_data = "";
                }

                if ( $as_specified && $field_data == "as_specified" ) {

                        $field = isset( $this->wpie_import_option[ $field . "_as_specified_data" ] ) ? $this->wpie_import_option[ $field . "_as_specified_data" ] : "";

                        $field_data = $this->get_field( $field );
                }

                $field_data = $this->map_deep( $field_data, [ __CLASS__, 'validate_data' ] );

                return $field_data;
        }

        public static function validate_data( $data = null ) {

                if ( empty( trim( $data ) ) ) {
                        return $data;
                }

                return preg_replace( '/(?<!\\[)(\\{)((\/?)[-_0-9a-zA-Z]{1,70}\\[([0-9]{1,2})\\])+}/', "", $data );
        }

        /**
         * Maps a function to all non-iterable elements of an array or an object.
         *
         * This is similar to `array_walk_recursive()` but acts upon objects too.
         *
         * @since 1.4.0
         *
         * @param mixed    $value    The array, object, or scalar.
         * @param callable $callback The function to map onto $value.
         * @return mixed The value with the callback applied to all non-arrays and non-objects inside it.
         */
        private function map_deep( $value, $callback ) {

                if ( is_array( $value ) ) {
                        foreach ( $value as $index => $item ) {
                                $value[ $index ] = $this->map_deep( $item, $callback );
                        }
                } elseif ( is_object( $value ) ) {
                        $object_vars = get_object_vars( $value );
                        foreach ( $object_vars as $property_name => $property_value ) {
                                $value->$property_name = $this->map_deep( $property_value, $callback );
                        }
                } else {
                        $value = call_user_func( $callback, $value );
                }

                return $value;
        }

        public function get_field( $field = "" ) {

                if ( is_array( $field ) ) {
                        $field = array_map( array( $this, "get_field" ), $field );
                } elseif ( is_array( $this->wpie_import_record ) && !empty( $this->wpie_import_record ) ) {

                        if ( $this->has_shortcode( $field ) ) {
                                $data = $this->encode_shortcode_char( array_values( $this->wpie_import_record ) );
                        } else {
                                $data = array_values( $this->wpie_import_record );
                        }
                        $field = str_replace( array_keys( $this->wpie_import_record ), $data, $field );

                        if ( $this->has_shortcode( $field ) ) {
                                $field = $this->apply_shortcode( $field );
                                $field = $this->decode_shortcode_char( $field );
                        }
                        unset( $data );
                }
                return $field;
        }

        private function decode_shortcode_char( $data = [] ) {

                return str_replace( [ "wpie_square_bracket_open", "wpie_square_bracket_close", "wpie_double_quote", "wpie_single_quote" ], [ "[", "]", '"', "'" ], $data );
        }

        private function encode_shortcode_char( $data = [] ) {

                return str_replace( [ "[", "]", '"', "'" ], [ "wpie_square_bracket_open", "wpie_square_bracket_close", "wpie_double_quote", "wpie_single_quote" ], $data );
        }

        private function apply_shortcode( $content = "" ) {

                if ( empty( $content ) ) {
                        return $content;
                }

                if ( is_array( $content ) ) {
                        return array_map( [ $this, "apply_shortcode" ], $content );
                }

                if ( !$this->has_shortcode( $content ) ) {
                        return $content;
                }

                return $this->do_shortcode( wp_unslash( $content ) );
        }

        private function has_shortcode( $content = "" ) {

                if ( strpos( strtolower( trim( $content ) ), "[wpie_function" ) === false ) {
                        return false;
                }
                return true;
        }

        private function do_shortcode( $content = "", $ignore_html = false ) {

                global $importTemplate;

                $username = isset( $importTemplate->username ) ? $importTemplate->username : "";

                if ( empty( $username ) ) {
                        return "";
                }
                $user = get_user_by( "login", $username );

                if ( (!$user) || (!\user_can( $user, 'wpie_add_shortcode' )) ) {
                        return "";
                }

                // Find all registered tag names in $content.
                preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
                $tagnames = array_intersect( [ 'wpie_function' ], $matches[ 1 ] );

                if ( empty( $tagnames ) ) {
                        return $content;
                }

                $content = \do_shortcodes_in_html_tags( $content, $ignore_html, $tagnames );

                $pattern = \get_shortcode_regex( $tagnames );
                $content = \preg_replace_callback( "/$pattern/", [ $this, 'do_shortcode_tag' ], $content );

                // Always restore square braces so we don't break things like <!--[if IE ]>.
                $content = \unescape_invalid_shortcodes( $content );

                return $content;
        }

        /**
         * Regular Expression callable for do_shortcode() for calling shortcode hook.
         *
         * @see get_shortcode_regex for details of the match array contents.
         *
         * @since 2.5.0
         * @access private
         *
         * @global array $shortcode_tags
         *
         * @param array $m Regular expression match array
         * @return string|false False on failure.
         */
        public function do_shortcode_tag( $m ) {

                // Allow [[foo]] syntax for escaping a tag.
                if ( '[' === $m[ 1 ] && ']' === $m[ 6 ] ) {
                        return substr( $m[ 0 ], 1, -1 );
                }

                $attr = shortcode_parse_atts( $m[ 3 ] );

                $content = isset( $m[ 5 ] ) ? $m[ 5 ] : null;

                return $m[ 1 ] . $this->process_shortcode( $content, $attr ) . $m[ 6 ];
        }

        public function process_shortcode( $content = "", $attr = [] ) {

                if ( empty( $attr ) ) {
                        return "";
                }

                $custom_function = "";

                if ( isset( $attr[ 'custom_function' ] ) ) {
                        $custom_function = $attr[ 'custom_function' ];
                        unset( $attr[ 'custom_function' ] );
                }

                if ( empty( $custom_function ) ) {
                        foreach ( $attr as $key => $value ) {
                                if ( strtotime( trim( $custom_function ) ) === "custom_function" ) {
                                        $custom_function = $value;
                                        unset( $attr[ $key ] );
                                        break;
                                }
                        }
                }

                if ( empty( $custom_function ) || !is_callable( $custom_function ) ) {
                        return "";
                }

                $new_attr = [];
                if ( !empty( $attr ) ) {
                        foreach ( $attr as $key => $value ) {
                                $key              = preg_replace( "/[^a-zA-Z0-9]+/", "", $this->decode_shortcode_char( $key ) );
                                $value            = $this->decode_shortcode_char( $value );
                                $new_attr[ $key ] = $value;
                        }
                }

                return call_user_func( $custom_function, $attr, $content );
        }

        protected function is_update_field( $field = "" ) {

                if ( empty( $field ) ) {
                        return false;
                }
                if ( $this->is_new_item ) {
                        return true;
                }

                $update_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update', true ) );

                if ( $update_method == 'all' ) {
                        return true;
                } elseif ( $update_method == 'price_stock' ) {
                        return false;
                }

                return absint( $this->get_field_value( "is_update_item_" . $field, true ) ) == 1;
        }

        public function strUnserialize( $str = "" ) {

                if ( !is_string( $str ) || trim( $str ) === "" || !is_serialized( $str ) ) {
                        return $str;
                }

                $unserialize = \maybe_unserialize( $str );

                if ( $unserialize !== false ) {
                        return $unserialize;
                }

                $validStr = preg_replace_callback( '!s:\d+:"(.*?)";!s',
                        function ( $m ) {
                                return "s:" . strlen( $m[ 1 ] ) . ':"' . $m[ 1 ] . '";';
                        }, $str
                );

                $unserialize = \maybe_unserialize( $validStr );

                if ( $unserialize !== false ) {
                        return $unserialize;
                }

                return $str;
        }

        protected function update_meta( $meta_key = "", $meta_val = "" ) {

                $meta_val = $this->strUnserialize( $meta_val );

                $meta_val = \wp_slash( $meta_val );

                if ( $this->import_type == "taxonomy" || $this->import_type == "product_attribute" ) {
                        update_term_meta( $this->item_id, $meta_key, $meta_val );
                } elseif ( $this->import_type == "user" || $this->import_type == "shop_customer" ) {
                        update_user_meta( $this->item_id, $meta_key, $meta_val );
                } elseif ( $this->import_type == "comment" ) {
                        update_comment_meta( $this->item_id, $meta_key, $meta_val );
                } else {
                        update_post_meta( $this->item_id, $meta_key, $meta_val );
                }

                do_action( 'wpie_after_update_meta', $this->item_id, $meta_key, $meta_val, $this->import_type );
        }

        protected function get_meta( $meta_key = "", $is_single = false ) {

                if ( !empty( $meta_key ) && !empty( $this->item_id ) ) {
                        if ( $this->import_type == "taxonomy" || $this->import_type == "product_attribute" ) {
                                return get_term_meta( $this->item_id, $meta_key, $is_single );
                        } elseif ( $this->import_type == "user" ) {
                                return get_user_meta( $this->item_id, $meta_key, $is_single );
                        } elseif ( $this->import_type == "comment" ) {
                                return get_comment_meta( $this->item_id, $meta_key, $is_single );
                        } else {
                                return get_post_meta( $this->item_id, $meta_key, $is_single );
                        }
                }
        }

        protected function remove_meta( $meta_key = "" ) {
                if ( !empty( $meta_key ) && !empty( $this->item_id ) ) {
                        if ( $this->import_type == "taxonomy" || $this->import_type == "product_attribute" ) {
                                delete_term_meta( $this->item_id, $meta_key );
                        } elseif ( $this->import_type == "user" ) {
                                delete_user_meta( $this->item_id, $meta_key );
                        } elseif ( $this->import_type == "comment" ) {
                                delete_comment_meta( $this->item_id, $meta_key );
                        } else {
                                delete_post_meta( $this->item_id, $meta_key );
                        }
                }
        }

        protected function is_term_exists( $term, $taxonomy = '', $parent = null ) {

                return apply_filters( 'wpie_is_term_exists', term_exists( $term, $taxonomy, $parent ), $term, $taxonomy, $parent );
        }

        protected function get_date( $date = "", $format = "" ) {

                if ( empty( $date ) ) {
                        $date = date( "Y-m-d H:i:s" );
                }

                $format = empty( trim( $format ) ) ? "Y-m-d H:i:s" : $format;

                if ( !strtotime( $date ) ) {

                        $date = $this->get_valid_date( $date );
                }

                if ( !strtotime( $date ) ) {
                        return false;
                }

                return date( $format, strtotime( $date ) );
        }

        private function get_valid_date( $date = "" ) {

                if ( empty( $date ) ) {
                        return $date;
                }

                $separator = "";

                $date_separators = [ '/', '-', '.' ];

                foreach ( $date_separators as $sep ) {

                        if ( strpos( $date, $sep ) !== false ) {
                                $separator = $sep;

                                break;
                        }
                }

                if ( empty( $separator ) ) {
                        return $date;
                }

                $date_separators = array_diff( $date_separators, [ $separator ] );

                $new_date = "";

                foreach ( $date_separators as $sep ) {

                        $new_date = str_replace( $separator, $sep, $date );

                        if ( strtotime( $new_date ) ) {
                                break;
                        } else {
                                $new_date = "";
                        }
                }

                unset( $date_separators );

                return empty( $new_date ) ? $date : $new_date;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

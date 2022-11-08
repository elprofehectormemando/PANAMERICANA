<?php


namespace wpie\export\base;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

abstract class WPIE_Export_Base {

        protected $export_type;
        protected $export_taxonomy_type;
        protected $export_id;
        protected $template_options;
        protected $process_log;
        protected $export_data;
        protected $is_preview        = false;
        protected $opration          = "export";
        protected $preview_data      = array();
        protected $export_labels     = array();
        protected $addons            = array();
        protected $has_multiple_rows = false;

        public function __construct() {
                
        }

        protected function taxonomies_by_object_type( $object_type = null, $output = 'names' ) {

                global $wp_taxonomies;

                is_array( $object_type ) or $object_type = array( $object_type );

                $field = ('names' == $output) ? 'name' : false;

                $taxonomy = array();

                if ( !empty( $wp_taxonomies ) ) {

                        foreach ( $wp_taxonomies as $key => $obj ) {

                                if ( array_intersect( $object_type, $obj->object_type ) ) {

                                        $taxonomy[ $key ] = $obj;
                                }
                        }
                }

                if ( $field ) {

                        $taxonomy = wp_list_pluck( $taxonomy, $field );
                }

                unset( $field, $object_type );

                return $taxonomy;
        }

        protected function add_filter_rule( $filters = array(), $is_int = false, $table_alias = false ) {

                global $wpdb;

                $condition = isset( $filters[ 'condition' ] ) ? $filters[ 'condition' ] : "";
                $value     = isset( $filters[ 'value' ] ) ? $filters[ 'value' ] : "";
                $element   = isset( $filters[ 'element' ] ) ? $filters[ 'element' ] : "";
                $clause    = isset( $filters[ 'clause' ] ) ? $filters[ 'clause' ] : "";

                $return_data = "";

                if ( !empty( $condition ) ) {

                        switch ( $condition ) {
                                case 'equals':
                                        if ( in_array( $element, array( 'post_date', 'comment_date', 'user_registered', 'user_role' ) ) ) {
                                                $return_data = $wpdb->prepare( 'LIKE %s', "%" . $value . "%" );
                                        } else {
                                                $return_data = "= " . (($is_int && is_numeric( $value )) ? intval( $value ) : $wpdb->prepare( '%s', $value ));
                                        }
                                        break;
                                case 'not_equals':
                                        if ( in_array( $element, array( 'post_date', 'comment_date', 'user_registered', 'user_role' ) ) ) {
                                                $return_data = $wpdb->prepare( 'NOT LIKE %s', "%" . $value . "%" );
                                        } else {
                                                $return_data = "!= " . (($is_int && is_numeric( $value )) ? intval( $value ) : $wpdb->prepare( '%s', $value ));
                                        }
                                        break;
                                case 'greater':
                                        $return_data = "> " . (($is_int && is_numeric( $value )) ? intval( $value ) : $wpdb->prepare( '%s', $value ));
                                        break;
                                case 'equals_or_greater':
                                        $return_data = ">= " . (($is_int && is_numeric( $value )) ? intval( $value ) : $wpdb->prepare( '%s', $value ));
                                        break;
                                case 'less':
                                        $return_data = "< " . (($is_int && is_numeric( $value )) ? intval( $value ) : $wpdb->prepare( '%s', $value ));
                                        break;
                                case 'equals_or_less':
                                        $return_data = "<= " . (($is_int && is_numeric( $value )) ? intval( $value ) : $wpdb->prepare( '%s', $value ));
                                        break;
                                case 'contains':
                                        $return_data = $wpdb->prepare( 'LIKE %s', "%" . $value . "%" );
                                        break;
                                case 'not_contains':
                                        $return_data = $wpdb->prepare( 'NOT LIKE %s', "%" . $value . "%" );
                                        break;
                                case 'is_empty':
                                        $return_data = "IS NULL";
                                        break;
                                case 'is_not_empty':
                                        $return_data = "IS NOT NULL";
                                        if ( $table_alias ) {
                                                $return_data .= " AND $table_alias.meta_value <> '' ";
                                        }
                                        break;
                                case 'in':
                                        $in_value = [];

                                        if ( !empty( $value ) ) {

                                                $value = explode( ",", $value );

                                                foreach ( $value as $_val ) {
                                                        $in_value[] = $wpdb->prepare( '%s', $_val );
                                                }
                                        }
                                        $in_value = empty( $in_value ) ? "" : implode( ",", $in_value );

                                        $return_data = "IN (" . $in_value . ")";

                                        break;
                                case 'not_in':

                                        $in_value = [];

                                        if ( !empty( $value ) ) {

                                                $value = explode( ",", $value );

                                                foreach ( $value as $_val ) {
                                                        $in_value[] = $wpdb->prepare( '%s', $_val );
                                                }
                                        }
                                        $in_value = empty( $in_value ) ? "" : implode( ",", $in_value );

                                        $return_data = "NOT IN (" . $in_value . ")";

                                        break;
                                default:
                                        break;
                        }
                }

                if ( !empty( $clause ) ) {
                        $return_data .= " " . $clause . " ";
                }

                unset( $condition, $value, $element, $clause );

                return $this->addlaceholder( $return_data );
        }

        /**
         * Get Plugin database table with prefix        
         *
         * @since 4.0.0
         * @static
         * 
         * @return Object $wpdb Object
         */
        public function addlaceholder( $query = "" ) {

                if ( empty( $query ) ) {
                        return $query;
                }
                global $wpdb;

                return method_exists( $wpdb, "remove_placeholder_escape" ) ? $wpdb->remove_placeholder_escape( $query ) : $query;
        }

        protected function add_date_filter_rule( $rule = array() ) {

                $value = isset( $rule[ 'value' ] ) ? $rule[ 'value' ] : "";

                $condition = isset( $rule[ 'condition' ] ) ? $rule[ 'condition' ] : "";

                $date = $this->get_date( $value );

                if ( $condition === "greater" && strpos( $value, ":" ) === false ) {
                        $date = date( "Y-m-d", strtotime( '+1 day', strtotime( $date ) ) );
                }

                unset( $condition, $value );

                return $date;
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

        protected function remove_prefix( $str = "", $prefix = "" ) {

                if ( substr( $str, 0, strlen( $prefix ) ) == $prefix ) {
                        $str = substr( $str, strlen( $prefix ) );
                }

                return $str;
        }

        protected function apply_user_function( $data = "", $is_enabled = false, $php_fun = "" ) {

                try {
                        if ( $is_enabled && !empty( $php_fun ) && function_exists( $php_fun ) ) {

                                $data = call_user_func( $php_fun, $data );
                        }
                } catch ( Exception $ex ) {
                        
                } catch ( Error $err ) {
                        
                }

                return $data;
        }

        public function get_taxonomies_by_post_type( $export_type = array( "post" ), $cats_type = 'wpie_tax', $is_attr = false, $excludes = [] ) {

                $post_taxonomies = array_diff_key( $this->taxonomies_by_object_type( $export_type, 'object' ), array_flip( array( 'post_format' ) ) );

                $taxonomies = [];

                if ( !empty( $post_taxonomies ) ) {

                        foreach ( $post_taxonomies as $slug => $tax ) {

                                if ( !empty( $excludes ) && in_array( $slug, $excludes ) ) {
                                        continue;
                                }
                                if ( (!$is_attr && strpos( $tax->name, "pa_" ) !== 0) || ( $is_attr && strpos( $tax->name, "pa_" ) === 0) ) {

                                        if ( $tax->name == "product_type" ) {
                                                $tax_name = __( "Product Type", 'wp-import-export-lite' );
                                        } elseif ( $tax->name == "product_visibility" ) {
                                                $tax_name = __( "Product Visibility", 'wp-import-export-lite' );
                                        } else {
                                                $tax_name = isset( $tax->label ) ? $tax->label : $tax->name;
                                        }
                                        if ( $is_attr ) {
                                                $tax_name = ( isset( $tax->labels ) && isset( $tax->labels->singular_name )) ? $tax->labels->singular_name : $tax_name;
                                        }
                                        $taxonomies[] = array(
                                                'name'         => (trim( $tax_name ) === "" ? $tax->name : $tax_name),
                                                'type'         => $cats_type,
                                                'taxName'      => $tax->name,
                                                'isTax'        => true,
                                                'hierarchical' => $tax->hierarchical
                                        );
                                        unset( $tax_name );
                                }
                        }
                }

                unset( $post_taxonomies );

                return $taxonomies;
        }

        protected function get_date_field( $date_type = "", $timestamp = "", $date_format = "", $default_format = "Y-m-d H:i:s" ) {

                if ( empty( $timestamp ) ) {
                        return $timestamp;
                } else {
                        $timestamp = ( int ) $timestamp;
                }
                if ( $date_type == "unix" ) {
                        $date = $timestamp;
                } else {

                        if ( empty( $date_format ) ) {
                                $date_format = $default_format;
                        }

                        $date = date( $date_format, $timestamp );

                        unset( $date_format );
                }

                return $date;
        }

        protected function is_term_exists( $term, $taxonomy = '', $parent = null ) {

                return apply_filters( 'wpie_is_term_exists', term_exists( $term, $taxonomy, $parent ), $term, $taxonomy, $parent );
        }

}

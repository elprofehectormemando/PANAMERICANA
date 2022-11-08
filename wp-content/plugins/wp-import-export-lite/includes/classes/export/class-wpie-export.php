<?php


namespace wpie\export;

use wpie\export\post;
use wpie\export\taxonomy;
use wpie\lib\xml\array2xml;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Export {

        protected function get_template_list() {

                global $wpdb;

                $content_type = isset( $_POST[ 'content_type' ] ) ? wpie_sanitize_field( $_POST[ 'content_type' ] ) : "post";

                $results = $wpdb->get_results( $wpdb->prepare( "SELECT `id`,`options` FROM " . $wpdb->prefix . "wpie_template where `opration_type` = %s AND `opration`='export_template' ORDER BY `" . $wpdb->prefix . "wpie_template`.`id` DESC", $content_type ) );

                $data = array();

                if ( !empty( $results ) ) {

                        $count = 0;

                        foreach ( $results as $template ) {

                                $data[ $count ][ 'id' ] = isset( $template->id ) ? $template->id : 0;

                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                $data[ $count ][ 'name' ] = isset( $options[ 'template_name' ] ) ? $options[ 'template_name' ] : "";

                                unset( $options );

                                $count++;
                        }

                        unset( $count );
                }

                unset( $content_type, $results );

                $return_value = array();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'data' ] = $data;

                unset( $data );

                echo json_encode( $return_value );

                die();
        }

        protected function get_export_settings_list() {

                global $wpdb;

                $content_type = isset( $_POST[ 'content_type' ] ) ? wpie_sanitize_field( $_POST[ 'content_type' ] ) : "post";

                $results = $wpdb->get_results( $wpdb->prepare( "SELECT `id`,`options`,`create_date` FROM " . $wpdb->prefix . "wpie_template where `opration_type` = %s AND `opration`='export' ORDER BY `" . $wpdb->prefix . "wpie_template`.`id` DESC", $content_type ) );

                $data = [];

                if ( !empty( $results ) ) {

                        $count = 0;

                        foreach ( $results as $template ) {

                                $data[ $count ][ 'id' ] = isset( $template->id ) ? $template->id : 0;

                                $date = isset( $template->create_date ) ? $template->create_date : "";

                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                $fileName = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                                $data[ $count ][ 'name' ] = $date . " " . $fileName;

                                unset( $options );

                                $count++;
                        }

                        unset( $count );
                }

                unset( $content_type, $results );

                $return_value = array();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'data' ] = $data;

                unset( $data );

                echo json_encode( $return_value );

                die();
        }

        public function prepare_fields( $export_type = "", $taxonomy_type = "", $attribute_taxonomy = "" ) {
                return $this->init_export( $export_type, "fields", [ "wpie_taxonomy_type" => $taxonomy_type, "wpie_attribute_taxonomy" => $attribute_taxonomy ] );
        }

        protected function get_field_list() {

                $export_type = isset( $_GET[ 'export_type' ] ) ? wpie_sanitize_field( $_GET[ 'export_type' ] ) : "post";

                $taxonomy_type = isset( $_GET[ 'taxonomy_type' ] ) ? wpie_sanitize_field( $_GET[ 'taxonomy_type' ] ) : "";

                $attribute_taxonomy = isset( $_GET[ 'attribute_taxonomy' ] ) && !empty( $_GET[ 'attribute_taxonomy' ] ) ? explode( ",", wpie_sanitize_field( $_GET[ 'attribute_taxonomy' ] ) ) : [];

                $fields = $this->prepare_fields( $export_type, $taxonomy_type, $attribute_taxonomy );

                if ( is_wp_error( $fields ) ) {

                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = $fields->get_error_message();
                } else {
                        $return_value[ 'status' ] = 'success';

                        $return_value[ 'fields' ] = $fields;
                }

                unset( $export_type, $taxonomy_type, $fields );

                echo json_encode( $return_value );

                die();
        }

        public function init_export( $export_type = "post", $opration = "export", $template = null ) {

                $export_engine = "";

                if ( $export_type == "taxonomies" ) {

                        if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-taxonomy.php' ) ) {

                                require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-taxonomy.php');
                        }

                        $export_engine = '\wpie\export\taxonomy\WPIE_Taxonomy';
                } elseif ( $export_type == "comments" || $export_type == "product_reviews" ) {

                        if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-comment.php' ) ) {

                                require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-comment.php');
                        }
                        $export_engine = '\wpie\export\comment\WPIE_Comment';
                } else {

                        if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-post.php' ) ) {

                                require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-post.php');
                        }
                        $export_engine = '\wpie\export\post\WPIE_Post';
                }

                $export_engine = apply_filters( 'wpie_export_engine_init', $export_engine, $export_type, $template );

                $export_process = array();

                if ( class_exists( $export_engine ) ) {

                        $export_data = new $export_engine();

                        if ( method_exists( $export_data, "init_engine" ) ) {
                                $export_process = $export_data->init_engine( $export_type, $opration, $template );
                        }

                        unset( $export_data );
                } else {
                        return new \WP_Error( 'wpie_import_error', sprintf( __( 'Class %s Not Exist', 'wp-import-export-lite' ), $export_engine ) );
                }

                unset( $export_engine, $export_type );

                return $export_process;
        }

        private function get_deafult_export_type() {

                $types = [
                        "post"       => __( 'Post', 'wp-import-export-lite' ),
                        "page"       => __( 'Page', 'wp-import-export-lite' ),
                        "taxonomies" => __( 'Taxonomies | Categories | Tags', 'wp-import-export-lite' ),
                        "users"      => __( 'Users', 'wp-import-export-lite' ),
                        "comments"   => __( 'Comments', 'wp-import-export-lite' ),
                ];

                if ( \defined( 'WC_VERSION' ) || \class_exists( '\WooCommerce' ) ) {

                        $types[ "product" ]            = __( 'WooCommerce Products', 'wp-import-export-lite' );
                        $types[ "product_reviews" ]    = __( 'Product Reviews', 'wp-import-export-lite' );
                        $types[ "product_attributes" ] = __( 'Product Attributes', 'wp-import-export-lite' );
                        $types[ "shop_order" ]         = __( 'WooCommerce Orders', 'wp-import-export-lite' );
                        $types[ "shop_coupon" ]        = __( 'WooCommerce Coupons', 'wp-import-export-lite' );
                        $types[ "shop_customer" ]      = __( 'WooCommerce Customers', 'wp-import-export-lite' );
                }
                return $types;
        }

        public function get_export_type() {

                $export_type = $this->get_deafult_export_type();

                $custom_export_type = get_post_types( [ '_builtin' => true ], 'objects' ) + get_post_types( [ '_builtin' => false, 'show_ui' => true ], 'objects' ) + get_post_types( [ '_builtin' => false, 'show_ui' => false ], 'objects' );

                if ( empty( $custom_export_type ) ) {
                        return $export_type;
                }

                $hidden_posts = [
                        'attachment',
                        'revision',
                        'nav_menu_item',
                        'shop_webhook',
                        'import_users',
                        'wp-types-group',
                        'wp-types-user-group',
                        'wp-types-term-group',
                        'acf-field',
                        'acf-field-group',
                        'custom_css',
                        'customize_changeset',
                        'oembed_cache',
                        'wp_block',
                        'user_request',
                        'scheduled-action',
                        'product_variation',
                        'shop_order_refund'
                ];

                foreach ( $custom_export_type as $key => $data ) {

                        if ( in_array( $key, $hidden_posts ) ) {
                                continue;
                        }

                        if ( isset( $export_type[ $key ] ) ) {
                                continue;
                        }

                        $label = isset( $data->labels ) && isset( $data->labels->singular_name ) ? $data->labels->singular_name : "";

                        if ( trim( $label ) === "" ) {

                                $label = isset( $data->labels ) && isset( $data->labels->name ) ? $data->labels->name : "";

                                if ( trim( $label ) === "" ) {
                                        continue;
                                }
                        }

                        $export_type[ $key ] = $label;
                }


                unset( $custom_export_type );

                return $export_type;
        }

        public function wpie_get_taxonomies() {

                $taxonomies = get_taxonomies( false, 'objects' );

                $data = [
                        "category"    => __( 'Post Categories', 'wp-import-export-lite' ),
                        "product_cat" => __( 'Product Categories', 'wp-import-export-lite' ),
                        "post_tag"    => __( 'Post Tags', 'wp-import-export-lite' ),
                        "product_tag" => __( 'Product Tags', 'wp-import-export-lite' ),
                ];

                $excludes = apply_filters( 'wpie_exclude_taxonomies', [ 'nav_menu', 'link_category', "post_format" ] );

                if ( !empty( $taxonomies ) ) {

                        foreach ( $taxonomies as $key => $taxonomy ) {

                                if ( in_array( $key, $excludes ) || isset( $data[ $key ] ) || (isset( $taxonomy->public ) && $taxonomy->public === false) ) {
                                        continue;
                                }

                                $data[ $key ] = ucwords( str_replace( '_', ' ', $key ) );
                        }
                }

                unset( $taxonomies );

                return $data;
        }

        public function get_attribute_list() {

                global $wpdb;

                return $wpdb->get_results( "SELECT attribute_name,attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name != ''  ORDER BY attribute_name ASC;" );
        }

        public function wpie_get_attribute_taxonomies() {

                $taxonomies = get_taxonomies( false, 'objects' );

                if ( !empty( $taxonomies ) ) {

                        foreach ( $taxonomies as $key => $taxonomy ) {

                                if ( in_array( $key, [ 'nav_menu', 'link_category' ] ) || isset( $data[ $key ] ) || (isset( $taxonomy->show_in_nav_menus ) && $taxonomy->show_in_nav_menus === false) ) {
                                        continue;
                                }

                                $data[ $key ] = ucwords( str_replace( '_', ' ', $key ) );
                        }
                }

                unset( $taxonomies );

                return $data;
        }

        protected function get_export_rule() {

                $wpie_export_rules = array(
                        'wpie_tax'              => array(
                                'in'     => __( 'In', 'wp-import-export-lite' ),
                                'not_in' => __( 'Not In', 'wp-import-export-lite' )
                        ),
                        'wpie_date'             => array(
                                'equals'            => __( 'equals', 'wp-import-export-lite' ),
                                'not_equals'        => __( "doesn't equal", 'wp-import-export-lite' ),
                                'greater'           => __( 'newer than', 'wp-import-export-lite' ),
                                'equals_or_greater' => __( 'equal to or newer than', 'wp-import-export-lite' ),
                                'less'              => __( 'older than', 'wp-import-export-lite' ),
                                'equals_or_less'    => __( 'equal to or older than', 'wp-import-export-lite' ),
                                'contains'          => __( 'contains', 'wp-import-export-lite' ),
                                'not_contains'      => __( "doesn't contain", 'wp-import-export-lite' ),
                                'is_empty'          => __( 'is empty', 'wp-import-export-lite' ),
                                'is_not_empty'      => __( 'is not empty', 'wp-import-export-lite' ),
                        ),
                        'wpie_capabilities'     => array(
                                'contains'     => __( 'contains', 'wp-import-export-lite' ),
                                'not_contains' => __( "doesn't contain", 'wp-import-export-lite' ),
                        ),
                        'wpie_user'             => array(
                                'equals'       => __( 'equals', 'wp-import-export-lite' ),
                                'not_equals'   => __( "doesn't equal", 'wp-import-export-lite' ),
                                'contains'     => __( 'contains', 'wp-import-export-lite' ),
                                'not_contains' => __( "doesn't contain", 'wp-import-export-lite' ),
                                'is_empty'     => __( 'is empty', 'wp-import-export-lite' ),
                                'is_not_empty' => __( 'is not empty', 'wp-import-export-lite' ),
                        ),
                        'wpie_term_parent_slug' => array(
                                'equals'            => __( 'equals', 'wp-import-export-lite' ),
                                'not_equals'        => __( "doesn't equal", 'wp-import-export-lite' ),
                                'greater'           => __( 'greater than', 'wp-import-export-lite' ),
                                'equals_or_greater' => __( 'equal to or greater than', 'wp-import-export-lite' ),
                                'less'              => __( 'less than', 'wp-import-export-lite' ),
                                'equals_or_less'    => __( 'equal to or less than', 'wp-import-export-lite' ),
                                'is_empty'          => __( 'is empty', 'wp-import-export-lite' ),
                                'is_not_empty'      => __( 'is not empty', 'wp-import-export-lite' ),
                        ),
                        'default'               => array(
                                'equals'            => __( 'equals', 'wp-import-export-lite' ),
                                'not_equals'        => __( "doesn't equal", 'wp-import-export-lite' ),
                                'greater'           => __( 'greater than', 'wp-import-export-lite' ),
                                'equals_or_greater' => __( 'equal to or greater than', 'wp-import-export-lite' ),
                                'less'              => __( 'less than', 'wp-import-export-lite' ),
                                'equals_or_less'    => __( 'equal to or less than', 'wp-import-export-lite' ),
                                'contains'          => __( 'contains', 'wp-import-export-lite' ),
                                'not_contains'      => __( "doesn't contain", 'wp-import-export-lite' ),
                                'is_empty'          => __( 'is empty', 'wp-import-export-lite' ),
                                'is_not_empty'      => __( 'is not empty', 'wp-import-export-lite' ),
                                'in'                => __( 'In', 'wp-import-export-lite' ),
                                'not_in'            => __( 'Not In', 'wp-import-export-lite' )
                        )
                );

                $return_value = array();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'wpie_export_rule' ] = apply_filters( "wpie_export_ruels", $wpie_export_rules );

                echo json_encode( $return_value );

                die();
        }

        protected function save_template_data() {

                global $wpdb;

                $template_name = isset( $_POST[ 'template_name' ] ) ? wpie_sanitize_field( $_POST[ 'template_name' ] ) : "";

                $template_id = isset( $_POST[ 'template_id' ] ) ? absint( wpie_sanitize_field( $_POST[ 'template_id' ] ) ) : 0;

                if ( $template_id > 0 ) {

                        $options = $wpdb->get_var( $wpdb->prepare( "SELECT `options` FROM " . $wpdb->prefix . "wpie_template where `id`=%d", $template_id ) );

                        if ( !is_null( $options ) ) {

                                $options = maybe_unserialize( $options );

                                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                                $new_options = wp_unslash( $_POST );

                                $new_options[ 'template_name' ] = isset( $options[ 'template_name' ] ) ? $options[ 'template_name' ] : "";

                                $new_values = array();

                                $new_values[ 'options' ] = maybe_serialize( $new_options );

                                $wpdb->update( $wpdb->prefix . "wpie_template", $new_values, array( 'id' => $template_id ) );

                                $return_value[ 'status' ] = 'success';

                                $return_value[ 'message' ] = __( 'Setting updated successfully', 'wp-import-export-lite' );

                                echo json_encode( $return_value );

                                die();
                        }
                }
                $is_exist = false;

                if ( !empty( $template_name ) ) {

                        $results = $wpdb->get_results( "SELECT `id`,`options` FROM " . $wpdb->prefix . "wpie_template where `opration`='export_template'" );

                        if ( !empty( $results ) ) {

                                foreach ( $results as $template ) {

                                        $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                        $temp_name = isset( $options[ 'template_name' ] ) ? $options[ 'template_name' ] : "";

                                        if ( !empty( $temp_name ) && $temp_name == $template_name ) {
                                                $is_exist = true;
                                                break;
                                        }
                                        unset( $options, $temp_name );
                                }
                        }

                        unset( $results );
                }
                if ( $is_exist === false ) {

                        $new_values = array();

                        $new_values[ 'opration' ] = "export_template";

                        $new_values[ 'opration_type' ] = isset( $_POST[ 'wpie_export_type' ] ) ? wpie_sanitize_field( $_POST[ 'wpie_export_type' ] ) : "post";

                        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                        $new_values[ 'options' ] = maybe_serialize( wp_unslash( $_POST ) );

                        $new_values[ 'create_date' ] = current_time( 'mysql' );

                        $new_values[ 'unique_id' ] = uniqid();

                        $current_user = wp_get_current_user();

                        if ( $current_user && isset( $current_user->user_login ) ) {
                                $new_values[ 'username' ] = $current_user->user_login;
                        }

                        $wpdb->insert( $wpdb->prefix . "wpie_template", $new_values );

                        unset( $new_values, $current_user );

                        $template_id = $wpdb->insert_id;

                        $return_value = array();

                        if ( $template_id && absint( $template_id ) > 0 ) {

                                $return_value[ 'status' ] = 'success';

                                $return_value[ 'template_id' ] = $template_id;

                                $return_value[ 'message' ] = __( 'Setting Saved Successfully', 'wp-import-export-lite' );
                        } else {

                                $return_value[ 'status' ] = 'error';

                                $return_value[ 'message' ] = __( 'Fail to save Setting in database', 'wp-import-export-lite' );
                        }
                        unset( $template_id );
                } else {
                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = __( 'Setting Name Already Exists', 'wp-import-export-lite' );
                }

                echo json_encode( $return_value );

                die();
        }

        protected function get_template() {

                $return_value = array();

                $template_id = isset( $_GET[ 'template_id' ] ) ? absint( wpie_sanitize_field( $_GET[ 'template_id' ] ) ) : 0;

                if ( $template_id > 0 ) {

                        $template_data = $this->get_template_by_id( $template_id );

                        if ( $template_data !== false && isset( $template_data->options ) ) {

                                $options = isset( $template_data->options ) ? wp_unslash( maybe_unserialize( $template_data->options ) ) : array();

                                $template_data->fields_data = isset( $options[ 'fields_data' ] ) ? wp_unslash( $options[ 'fields_data' ] ) : array();

                                $return_value[ 'message' ] = 'success';

                                $return_value[ 'data' ] = $options;
                        } else {
                                $return_value[ 'status' ] = 'error';

                                $return_value[ 'message' ] = __( 'Template Not Found', 'wp-import-export-lite' );
                        }

                        unset( $template_data );
                } else {
                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = __( 'Template Not Found', 'wp-import-export-lite' );
                }
                unset( $template_id );

                echo json_encode( $return_value );

                die();
        }

        protected function get_template_by_id( $export_id = 0 ) {

                if ( !empty( $export_id ) && absint( $export_id ) > 0 ) {

                        global $wpdb;

                        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "wpie_template where `id` = %d", $export_id ) );

                        if ( !empty( $results ) && isset( $results[ 0 ] ) ) {
                                return $results[ 0 ];
                        }
                }
                return false;
        }

        protected function update_process_status() {

                global $wpdb;

                $return_value = array( "status" => "error" );

                $wpie_import_id = isset( $_GET[ 'wpie_export_id' ] ) ? absint( wpie_sanitize_field( $_GET[ 'wpie_export_id' ] ) ) : 0;

                if ( $wpie_import_id < 1 ) {

                        $process_status = isset( $_GET[ 'process_status' ] ) ? wpie_sanitize_field( $_GET[ 'process_status' ] ) : "";

                        $new_satus = "";

                        if ( $process_status == "bg" ) {

                                $new_satus = "background";

                                $return_value[ 'message' ] = __( 'Background Process Successfully Set', 'wp-import-export-lite' );
                        } elseif ( $process_status == "stop" ) {

                                $new_satus = "stopped";

                                $return_value[ 'message' ] = __( 'Process Stopped Successfully', 'wp-import-export-lite' );
                        }

                        unset( $process_status );

                        if ( $new_satus != "" ) {

                                $final_data = array(
                                        'last_update_date' => current_time( 'mysql' ),
                                        'status'           => $new_satus,
                                );

                                $wpdb->update( $wpdb->prefix . "wpie_template", $final_data, array( 'id' => $wpie_import_id ) );

                                unset( $final_data );

                                $return_value[ 'status' ] = 'success';
                        } else {
                                $return_value[ 'message' ] = __( 'Empty Status', 'wp-import-export-lite' );
                        }

                        unset( $new_satus );
                } else {
                        $return_value[ 'message' ] = __( 'Template id not found', 'wp-import-export-lite' );
                }

                unset( $wpie_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function get_safe_dir_name( $str = "", $separator = 'dash', $lowercase = true ) {

                if ( $separator == 'dash' ) {
                        $search  = '_';
                        $replace = '-';
                } else {
                        $search  = '-';
                        $replace = '_';
                }

                $trans = array(
                        '&\#\d+?;'       => '',
                        '&\S+?;'         => '',
                        '\s+'            => $replace,
                        '[^a-z0-9\-\._]' => '',
                        $search . '+'    => $replace,
                        $search . '$'    => $replace,
                        '^' . $search    => $replace,
                        '\.+$'           => ''
                );

                $str = strip_tags( $str );

                foreach ( $trans as $key => $val ) {
                        $str = preg_replace( "#" . $key . "#i", $val, $str );
                }

                if ( $lowercase === true ) {
                        $str = strtolower( $str );
                }
                unset( $search, $replace, $trans );

                return md5( trim( wp_unslash( $str ) ) . time() );
        }

        protected function init_new_export() {

                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                $export_id = $this->generate_template( wp_unslash( $_POST ), 'export' );

                $return_value = array();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'export_id' ] = $export_id;

                unset( $export_id );

                echo json_encode( $return_value );

                die();
        }

        protected function generate_template( $options = array(), $template_type = 'export', $status = 'processing' ) {

                $options[ 'max_item_count' ] = apply_filters( 'wpie_export_max_item_count', 1, $options );

                $file_data = $this->set_file_headers( $options );

                $options[ 'fileName' ] = isset( $file_data[ 'filename' ] ) ? $file_data[ 'filename' ] : "";

                $options[ 'fileDir' ] = isset( $file_data[ 'filedir' ] ) ? $file_data[ 'filedir' ] : "";

                $total = 0;

                if ( isset( $options[ "total" ] ) ) {

                        $total = absint( $options[ "total" ] );

                        unset( $options[ "total" ] );
                }

                $wpie_export_type = (isset( $options[ 'wpie_export_type' ] ) && trim( $options[ 'wpie_export_type' ] ) != "") ? wpie_sanitize_field( $options[ 'wpie_export_type' ] ) : "post";

                $current_time = current_time( 'mysql' );

                $new_values = array();

                $new_values[ 'opration' ] = $template_type;

                $new_values[ 'opration_type' ] = $wpie_export_type;

                $new_values[ 'process_lock' ] = 0;

                $new_values[ 'process_log' ] = maybe_serialize( array( "total" => $total ) );

                $new_values[ 'status' ] = $status;

                $new_values[ 'options' ] = maybe_serialize( $options );

                $new_values[ 'create_date' ] = $current_time;

                $new_values[ 'last_update_date' ] = $current_time;

                $new_values[ 'unique_id' ] = uniqid();

                $current_user = wp_get_current_user();

                if ( $current_user && isset( $current_user->user_login ) ) {
                        $new_values[ 'username' ] = $current_user->user_login;
                }

                global $wpdb;

                $wpdb->insert( $wpdb->prefix . "wpie_template", $new_values );

                unset( $options, $file_data, $total, $wpie_export_type, $current_time, $new_values );

                return $wpdb->insert_id;
        }

        private function set_file_headers( $template_data = array() ) {

                $wpie_export_type = (isset( $template_data[ 'wpie_export_type' ] ) && trim( $template_data[ 'wpie_export_type' ] ) != "") ? array( wpie_sanitize_field( $template_data[ 'wpie_export_type' ] ) ) : array( "post" );

                $export_type = $this->get_export_type();

                $temp_wpie_export_type = $wpie_export_type[ 0 ];

                unset( $wpie_export_type );

                $exported_data = ( isset( $export_type[ $temp_wpie_export_type ] ) && !empty( $export_type[ $temp_wpie_export_type ] ) ) ? $export_type[ $temp_wpie_export_type ] : "post";

                unset( $export_type );

                if ( $temp_wpie_export_type == "taxonomies" ) {

                        $taxonomy_data = $this->wpie_get_taxonomies();

                        $tax_temp_data = (isset( $template_data[ 'wpie_taxonomy_type' ] ) && trim( $template_data[ 'wpie_taxonomy_type' ] ) != "") ? wpie_sanitize_field( $template_data[ 'wpie_taxonomy_type' ] ) : "";

                        if ( !empty( $tax_temp_data ) && isset( $taxonomy_data[ $tax_temp_data ] ) && !empty( $taxonomy_data[ $tax_temp_data ] ) ) {
                                $exported_data = $taxonomy_data[ $tax_temp_data ];
                        }
                        unset( $tax_temp_data, $taxonomy_data );
                }
                unset( $temp_wpie_export_type );

                $filename = sanitize_file_name( (isset( $template_data[ 'wpie_export_file_name' ] ) && trim( $template_data[ 'wpie_export_file_name' ] ) != "") ? $template_data[ 'wpie_export_file_name' ] : $exported_data . ' Export ' . date( 'Y M d His' ) );

                $filename = apply_filters( 'wpie_export_file_name', $filename );

                $filename = pathinfo( $filename, PATHINFO_FILENAME ) . '.csv';

                $export_dir = $this->get_safe_dir_name( $filename );

                wp_mkdir_p( WPIE_UPLOAD_EXPORT_DIR . "/" . $export_dir );

                $filepath = WPIE_UPLOAD_EXPORT_DIR . '/' . $export_dir . '/' . $filename;

                $fh = @fopen( $filepath, 'w+' );

                $wpie_export_include_bom = (isset( $template_data[ 'wpie_export_include_bom' ] ) && trim( $template_data[ 'wpie_export_include_bom' ] ) != "") ? wpie_sanitize_field( $template_data[ 'wpie_export_include_bom' ] ) : "";

                if ( $wpie_export_include_bom == 1 ) {
                        fwrite( $fh, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
                }

                fclose( $fh );

                unset( $exported_data, $filepath, $fh, $template_data, $wpie_export_include_bom );

                return array( "filename" => $filename, "filedir" => $export_dir );
        }

        protected function init_export_process() {

                $return_value = array( "status" => "error" );

                $export_id = isset( $_GET[ 'export_id' ] ) ? absint( wpie_sanitize_field( $_GET[ 'export_id' ] ) ) : 0;

                if ( $export_id > 0 ) {

                        $template = $this->get_template_by_id( $export_id );

                        if ( $template !== false ) {

                                $export_type = isset( $template->opration_type ) ? $template->opration_type : "post";

                                $process_log = $this->init_export( $export_type, "export", $template );

                                $return_value[ 'exported_records' ] = isset( $process_log[ 'exported' ] ) ? $process_log[ 'exported' ] : 0;

                                $total = isset( $process_log[ 'total' ] ) ? $process_log[ 'total' ] : 0;

                                unset( $export_type, $process_log );

                                if ( $return_value[ 'exported_records' ] >= $total ) {

                                        $return_value[ 'export_status' ] = 'completed';
                                } else {
                                        $return_value[ 'export_status' ] = 'processing';
                                }

                                unset( $total );

                                $return_value[ 'status' ] = 'success';
                        } else {
                                $return_value[ 'message' ] = __( 'Template not found', 'wp-import-export-lite' );
                        }
                        unset( $template );
                } else {
                        $return_value[ 'message' ] = __( 'Template not found', 'wp-import-export-lite' );
                }

                unset( $export_id );

                echo json_encode( $return_value );

                die();
        }

        protected function prepare_file() {

                $return_value = array( "status" => "error" );

                $export_id = isset( $_GET[ 'export_id' ] ) ? absint( wpie_sanitize_field( $_GET[ 'export_id' ] ) ) : 0;

                $process = $this->process_export_file( $export_id );

                if ( is_wp_error( $process ) ) {
                        $return_value[ 'message' ] = $process->get_error_message();
                } else {
                        $return_value[ 'status' ] = 'success';
                }

                echo json_encode( $return_value );

                die();
        }

        protected function process_export_file( $export_id = "" ) {

                if ( $export_id > 0 ) {

                        $template = $this->get_template_by_id( $export_id );

                        if ( $template !== false ) {

                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                $filename = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                                $fileDir = isset( $options[ 'fileDir' ] ) ? $options[ 'fileDir' ] : "";

                                $is_package = isset( $options[ 'is_package' ] ) ? intval( $options[ 'is_package' ] ) : 0;

                                $skip_empty_nodes = isset( $options[ 'wpie_skip_empty_nodes' ] ) ? intval( $options[ 'wpie_skip_empty_nodes' ] ) === 1 : false;

                                if ( $template->opration === "schedule_export" ) {
                                        $is_package = isset( $options[ 'is_migrate_package' ] ) ? intval( $options[ 'is_migrate_package' ] ) : 0;
                                }

                                $delim = isset( $options[ 'wpie_csv_field_separator' ] ) ? $options[ 'wpie_csv_field_separator' ] : ",";

                                $type = isset( $options[ 'wpie_export_file_type' ] ) && !empty( $options[ 'wpie_export_file_type' ] ) ? $options[ 'wpie_export_file_type' ] : "csv";

                                $new_type = "";

                                if ( $is_package === 0 ) {

                                        if ( $type != "" || $type != "csv" ) {

                                                switch ( $type ) {

                                                        case "xml" :
                                                                $data = $this->csv2xml( $filename, $fileDir, $skip_empty_nodes );

                                                                break;
                                                        case "json" :
                                                                $data = $this->csv2json( $filename, $fileDir );
                                                                break;
                                                        case "xls" :
                                                        case "xlsx" :
                                                        case "ods" :
                                                                $data = $this->csv2excel( $filename, $fileDir, $type );
                                                                break;
                                                }

                                                if ( isset( $data ) && is_wp_error( $data ) ) {
                                                        return $data;
                                                }

                                                $new_type = $type;
                                        }
                                } else {

                                        $is_success = $this->create_zip( $options );

                                        if ( is_wp_error( $is_success ) ) {

                                                return $data;
                                        }

                                        unset( $is_success );

                                        $new_type = "zip";
                                }

                                if ( $new_type != "" ) {

                                        $options[ 'fileName' ] = str_replace( ".csv", "." . $new_type, $filename );

                                        global $wpdb;

                                        $wpdb->update( $wpdb->prefix . "wpie_template", array( 'options' => maybe_serialize( $options ) ), array( 'id' => $export_id ) );
                                }

                                $extra_copy_path = isset( $options[ 'extra_copy_path' ] ) && !empty( $options[ 'extra_copy_path' ] ) ? ltrim( trailingslashit( sanitize_text_field( $options[ 'extra_copy_path' ] ) ), '/\\' ) : "";

                                if ( !empty( $extra_copy_path ) && is_dir( WPIE_SITE_UPLOAD_DIR . "/" . $extra_copy_path ) ) {

                                        @copy( WPIE_UPLOAD_EXPORT_DIR . '/' . $fileDir . '/' . $options[ 'fileName' ], WPIE_SITE_UPLOAD_DIR . "/" . $extra_copy_path . $options[ 'fileName' ] );
                                }

                                unset( $options, $filename, $fileDir, $is_package, $delim, $type, $new_type );
                        } else {
                                return new \WP_Error( 'woo_import_export_error', __( 'Template not found', 'wp-import-export-lite' ) );
                        }
                        unset( $template );
                } else {
                        return new \WP_Error( 'woo_import_export_error', __( 'Template not found', 'wp-import-export-lite' ) );
                }

                return true;
        }

        protected function create_zip( $options = array() ) {

                if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-import-config.php' ) ) {
                        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-import-config.php');
                }

                WPIE_Import_Config::generate( $options );

                $zip = new \ZipArchive();

                $filename = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                $fileDir = isset( $options[ 'fileDir' ] ) ? $options[ 'fileDir' ] : "";

                $zipfile = WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . str_replace( ".csv", ".zip", $filename );

                if ( $zip->open( $zipfile, \ZIPARCHIVE::CREATE ) != TRUE ) {

                        return new \WP_Error( 'woo_import_export_error', __( 'Could not open archive', 'wp-import-export-lite' ) );
                }

                unset( $zipfile );

                $zip->addFile( WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . $filename, $filename );

                $zip->addFile( WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/config.json", "config.json" );

                $zip->close();

                unset( $zip );

                return true;
        }

        private function csv2excel( $filename = "", $fileDir = "", $type = "xlsx" ) {

                $file = WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . $filename;

                if ( !file_exists( $file ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File not found', 'wp-import-export-lite' ) );
                }

                wpie_load_vendor_autoloader();

                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $file );

                unset( $reader, $file );

                if ( $type == "xls" ) {
                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls( $spreadsheet );
                } elseif ( $type == "ods" ) {
                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Ods( $spreadsheet );
                } else {
                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx( $spreadsheet );
                }
                $writer->save( WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . str_replace( ".csv", "." . $type, $filename ) );

                $spreadsheet->disconnectWorksheets();

                unset( $writer, $spreadsheet );

                return true;
        }

        private function csv2json( $filename = "", $fileDir = "" ) {

                $file = WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . $filename;

                if ( !file_exists( $file ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File not found', 'wp-import-export-lite' ) );
                }

                $csv = array();

                if ( ($handle = fopen( $file, 'r' )) !== FALSE ) {

                        $headersList = fgetcsv( $handle, 0, ',' );
                        $headers     = [];
                        foreach ( $headersList as $header ) {

                                if ( in_array( $header, $headers ) ) {
                                        $temp       = 1;
                                        $tempHeader = $header;
                                        while ( in_array( $tempHeader, $headers ) ) {
                                                $tempHeader = $header . " " . $temp;
                                                $temp++;
                                        }
                                        $header = $tempHeader;
                                }
                                $headers [] = $header;
                        }

                        while ( $row = fgetcsv( $handle, 0, ',' ) ) {
                                $csv[] = array_combine( $headers, $row );
                        }

                        fclose( $handle );
                }

                file_put_contents( WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . str_replace( ".csv", ".json", $filename ), wp_json_encode( $csv ) );

                unset( $file, $csv );

                return true;
        }

        private function csv2xml( $filename = "", $fileDir = "", $skip_empty_nodes = false ) {

                $file = WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . $filename;

                if ( !file_exists( $file ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File not found', 'wp-import-export-lite' ) );
                }

                if ( file_exists( WPIE_LIBRARIES_DIR . '/xml/class-wpie-array2xml.php' ) ) {
                        require_once(WPIE_LIBRARIES_DIR . '/xml/class-wpie-array2xml.php');
                }

                $converter = new \wpie\lib\xml\array2xml\ArrayToXml();

                $converter->create_root( "wpiedata" );

                if ( $skip_empty_nodes ) {
                        $converter->skip_empty();
                }

                $headers = array();

                $wfp = fopen( $file, "rb" );

                unset( $file );

                while ( ($keys = fgetcsv( $wfp, 0 )) !== false ) {

                        if ( empty( $headers ) ) {

                                foreach ( $keys as $key => $value ) {

                                        $value = trim( strtolower( preg_replace( '/[^a-z0-9_]/i', '', $value ) ) );

                                        if ( preg_match( '/^[0-9]{1}/', $value ) ) {
                                                $value = 'el_' . trim( strtolower( $value ) );
                                        }

                                        $value = (!empty( $value )) ? $value : 'undefined' . $key;

                                        if ( isset( $headers[ $key ] ) ) {
                                                $key = $this->unique_array_key_name( $key, $headers );
                                        }

                                        $headers[ $key ] = $value;
                                }

                                continue;
                        }

                        $fileData = array();

                        foreach ( $keys as $key => $value ) {

                                $header = isset( $headers[ $key ] ) ? $headers[ $key ] : "";

                                if ( !empty( $header ) ) {

                                        if ( isset( $fileData[ $header ] ) ) {
                                                $header = $this->unique_array_key_name( $header, $fileData );
                                        }

                                        $fileData[ $header ] = $value;
                                }
                                unset( $header );
                        }

                        $converter->addNode( $converter->root, "item", $fileData, 0 );

                        unset( $fileData );
                }

                $converter->saveFile( WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . str_replace( ".csv", ".xml", $filename ) );

                unset( $converter, $headers );

                return true;
        }

        protected function get_item_count() {

                $export_type = isset( $_POST[ 'wpie_export_type' ] ) ? wpie_sanitize_field( $_POST[ 'wpie_export_type' ] ) : "post";

                $return_value = array();

                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                $return_value[ "totalRecords" ] = $this->init_export( $export_type, "count", wp_unslash( $_POST ) );

                unset( $export_type );

                $return_value[ 'status' ] = 'success';

                echo json_encode( $return_value );

                die();
        }

        protected function get_preview() {

                $export_type = isset( $_POST[ 'wpie_export_type' ] ) ? wpie_sanitize_field( $_POST[ 'wpie_export_type' ] ) : "post";

                $return_value = array();

                $_POST[ 'wpie_records_per_iteration' ] = isset( $_POST[ 'length' ] ) ? absint( wpie_sanitize_field( $_POST[ 'length' ] ) ) : 10;

                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                $return_value[ 'data' ] = $this->init_export( $export_type, "preview", wp_unslash( $_POST ) );

                unset( $export_type );

                $return_value[ 'recordsTotal' ] = isset( $_POST[ 'total' ] ) ? absint( $_POST[ 'total' ] ) : 0;

                $return_value[ 'recordsFiltered' ] = $return_value[ 'recordsTotal' ];

                $return_value[ 'status' ] = 'success';

                echo json_encode( $return_value );

                die();
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

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

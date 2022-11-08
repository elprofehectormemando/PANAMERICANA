<?php


namespace wpie\import;

use wpie\import\upload\validate\WPIE_Upload_Validate;
use wpie\import\chunk\WPIE_Chunk;
use wpie\import\upload\WPIE_Upload;
use wpie\import\Compatibility\Manager as AddOns;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Import {

        public function __construct() {
                
        }

        private function get_deafult_import_type() {

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

        public function wpie_get_import_type() {

                $import_type = $this->get_deafult_import_type();

                $custom_import_type = get_post_types( [ '_builtin' => true ], 'objects' ) + get_post_types( [ '_builtin' => false, 'show_ui' => true ], 'objects' ) + get_post_types( [ '_builtin' => false, 'show_ui' => false ], 'objects' );

                if ( empty( $custom_import_type ) ) {
                        return $import_type;
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

                foreach ( $custom_import_type as $key => $data ) {

                        if ( in_array( $key, $hidden_posts ) ) {
                                continue;
                        }

                        if ( isset( $import_type[ $key ] ) ) {
                                continue;
                        }

                        $label = isset( $data->labels ) && isset( $data->labels->singular_name ) ? $data->labels->singular_name : "";

                        if ( trim( $label ) === "" ) {

                                $label = isset( $data->labels ) && isset( $data->labels->name ) ? $data->labels->name : "";

                                if ( trim( $label ) === "" ) {
                                        continue;
                                }
                        }

                        $import_type[ $key ] = $label;
                }

                unset( $custom_import_type );

                return $import_type;
        }

        public function get_attribute_list() {

                global $wpdb;

                return $wpdb->get_results( "SELECT attribute_name,attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name != ''  ORDER BY attribute_name ASC;" );
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

                                if ( in_array( $key, $excludes ) || isset( $data[ $key ] ) || (isset( $taxonomy->show_in_nav_menus ) && $taxonomy->show_in_nav_menus === false) ) {
                                        continue;
                                }

                                $data[ $key ] = ucwords( str_replace( '_', ' ', $key ) );
                        }
                }

                unset( $taxonomies );

                return $data;
        }

        public function wpie_get_all_taxonomies( $exclude_taxonomies = array(), $object_type = array(), $field = 'name' ) {

                $taxonomies = get_taxonomies( false, 'objects' );

                $ignore_taxonomies = array( 'nav_menu', 'link_category' );

                if ( !empty( $exclude_taxonomies ) ) {
                        $ignore_taxonomies = array_merge( $ignore_taxonomies, $exclude_taxonomies );
                }

                $result = array();

                if ( !empty( $taxonomies ) ) {

                        foreach ( $taxonomies as $_key => $taxonomy ) {

                                //Co-Authors Plus Plugin support
                                if ( in_array( $_key, $ignore_taxonomies ) || (isset( $taxonomy->public ) && $taxonomy->public === false) ) {
                                        continue;
                                }

                                if ( !empty( $object_type ) ) {

                                        $temp = 0;

                                        if ( is_array( $taxonomy->object_type ) ) {
                                                foreach ( $taxonomy->object_type as $value ) {
                                                        if ( in_array( $value, $object_type ) ) {
                                                                $temp++;
                                                                break;
                                                        }
                                                }
                                        }
                                        if ( $temp === 0 ) {
                                                continue;
                                        }
                                        unset( $temp );
                                }

                                if ( $field == 'name' ) {
                                        if ( !empty( $taxonomy->labels->name ) && strpos( $taxonomy->labels->name, "_" ) === false ) {
                                                $result[ $_key ] = $taxonomy->labels->name;
                                        } else {
                                                $result[ $_key ] = empty( $taxonomy->labels->singular_name ) ? $taxonomy->name : $taxonomy->labels->singular_name;
                                        }
                                } elseif ( $field == 'keytitle' ) {
                                        if ( $_key === "product_cat" ) {
                                                $_label = "Product Category";
                                        } else {
                                                $_label = ucwords( str_replace( '_', ' ', $_key ) );
                                        }
                                        $result[ $_key ] = $_label;
                                } elseif ( $field == 'all' ) {
                                        $result[ $_key ] = $taxonomy;
                                }
                        }
                }

                if ( $field != 'all' ) {
                        asort( $result, SORT_FLAG_CASE | SORT_STRING );
                } else {
                        asort( $result );
                }

                unset( $exclude_taxonomies, $taxonomies, $ignore_taxonomies, $object_type, $field );

                return $result;
        }

        public function wpie_generate_template( $options = array(), $opration = 'import', $status = 'processing', $unique_id = "", $import_id = 0, $username = "" ) {

                global $wpdb;

                $wpie_import_type = (isset( $options[ 'wpie_import_type' ] ) && trim( $options[ 'wpie_import_type' ] ) != "") ? $options[ 'wpie_import_type' ] : "post";

                $current_time = current_time( 'mysql' );

                $new_values = array();

                $new_values[ 'opration' ] = $opration;

                $new_values[ 'opration_type' ] = $wpie_import_type;

                $new_values[ 'process_lock' ] = 0;

                $new_values[ 'process_log' ] = "";

                $new_values[ 'status' ] = $status;

                $new_values[ 'options' ] = maybe_serialize( wp_unslash( $options ) );

                $new_values[ 'last_update_date' ] = $current_time;

                $is_update = false;

                if ( absint( $import_id ) > 0 ) {
                        $is_update = $wpdb->update( $wpdb->prefix . "wpie_template", $new_values, [ "id" => absint( $import_id ) ] );
                }

                if ( $is_update === false || absint( $is_update ) === 0 ) {

                        if ( empty( $unique_id ) ) {
                                $unique_id = uniqid();
                        }

                        $new_values[ 'create_date' ] = $current_time;

                        $new_values[ 'unique_id' ] = $unique_id;

                        if ( $username === "" ) {

                                $current_user = wp_get_current_user();

                                if ( $current_user && isset( $current_user->user_login ) ) {
                                        $new_values[ 'username' ] = $current_user->user_login;
                                }
                        } else {
                                $new_values[ 'username' ] = $username;
                        }

                        $wpdb->insert( $wpdb->prefix . "wpie_template", $new_values );

                        $import_id = $wpdb->insert_id;
                }


                unset( $current_time, $wpie_import_type, $new_values );

                return $import_id;
        }

        public function get_template_by_id( $wpie_import_id = 0 ) {

                if ( intval( $wpie_import_id ) > 0 ) {

                        global $wpdb;

                        $results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "wpie_template where `id` = %d limit 0,1", intval( $wpie_import_id ) ) );

                        if ( !empty( $results ) ) {
                                return $results;
                        }
                }

                return false;
        }

        public function get_template_by_ref( $ref = "" ) {

                if ( !empty( $ref ) ) {

                        global $wpdb;

                        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "wpie_template where `unique_id` = %s ORDER BY `id` ASC limit 0,1", $ref ) );
                }

                return false;
        }

        protected function wpie_parse_upload_file() {

                $return_value = array( 'status' => 'error' );

                $wpie_import_id = isset( $_GET[ "wpie_import_id" ] ) ? intval( wpie_sanitize_field( $_GET[ "wpie_import_id" ] ) ) : 0;

                if ( $wpie_import_id != 0 ) {

                        $template_data = $this->get_template_by_id( $wpie_import_id );

                        if ( $template_data ) {

                                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload-validate.php' ) ) {
                                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload-validate.php');
                                }

                                $data_parser = new WPIE_Upload_Validate();

                                $wpie_csv_delimiter = isset( $_GET[ "wpie_csv_delimiter" ] ) ? wpie_sanitize_field( $_GET[ "wpie_csv_delimiter" ] ) : ",";

                                $is_first_row_title = isset( $_GET[ "wpie_file_first_row_is_title" ] ) ? wpie_sanitize_field( $_GET[ "wpie_file_first_row_is_title" ] ) : 1;

                                $template_options = isset( $template_data->options ) ? maybe_unserialize( $template_data->options ) : [];

                                $importFile = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : [];

                                $activeFile = isset( $_GET[ 'activeFile' ] ) ? wpie_sanitize_field( $_GET[ 'activeFile' ] ) : "";

                                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : [];

                                $sheetData = $this->getSheetData( $fileData );

                                $activeSheet = isset( $_GET[ 'activeSheet' ] ) ? wpie_sanitize_field( $_GET[ 'activeSheet' ] ) : "";

                                $activeSheet = empty( $activeSheet ) ? (isset( $sheetData[ 'activeSheet' ] ) ? $sheetData[ 'activeSheet' ] : '') : $activeSheet;

                                $activeFormat = isset( $_GET[ 'activeFormat' ] ) ? wpie_sanitize_field( $_GET[ 'activeFormat' ] ) : false;

                                $data = $data_parser->wpie_parse_upload_data( $template_data, $wpie_csv_delimiter, $is_first_row_title, false, false, $activeSheet, $activeFormat );

                                if ( is_wp_error( $data ) ) {
                                        $return_value[ 'message' ] = $data->get_error_message();
                                } else {

                                        $file_path = isset( $fileData[ 'fileDir' ] ) ? wpie_sanitize_field( $fileData[ 'fileDir' ] ) : "";

                                        $file_name = isset( $fileData[ 'fileName' ] ) ? wpie_sanitize_field( $fileData[ 'fileName' ] ) : "";

                                        $file = WPIE_UPLOAD_IMPORT_DIR . "/" . $file_path . "/" . $file_name;

                                        if ( is_readable( $file ) ) {
                                                $return_value[ 'file_name' ] = $file_name;
                                                $return_value[ 'file_size' ] = filesize( $file );
                                        }
                                        if ( is_array( $data ) && isset( $data[ 'delimiter' ] ) ) {
                                                $return_value[ 'delimiter' ] = $data[ 'delimiter' ];
                                        }
                                        $activeSheet = isset( $_GET[ 'activeSheet' ] ) ? wpie_sanitize_field( $_GET[ 'activeSheet' ] ) : "";

                                        $return_value[ 'sheetList' ]   = isset( $sheetData[ 'sheetList' ] ) ? $sheetData[ 'sheetList' ] : [];
                                        $return_value[ 'activeSheet' ] = empty( $activeSheet ) ? (isset( $sheetData[ 'activeSheet' ] ) ? $sheetData[ 'activeSheet' ] : '') : $activeSheet;
                                        $return_value[ 'status' ]      = "success";
                                        $return_value[ 'message' ]     = __( 'File is Valid', 'wp-import-export-lite' );
                                }
                                unset( $data_parser, $wpie_csv_delimiter, $data );
                        } else {
                                $return_value[ 'message' ] = __( 'Template Not Found', 'wp-import-export-lite' );
                        }
                        unset( $template_data );
                } else {
                        $return_value[ 'message' ] = __( 'Data Not Found', 'wp-import-export-lite' );
                }

                unset( $wpie_import_id );

                echo json_encode( $return_value );

                die();
        }

        public function getSheetData( $activeFileData ) {

                $activeFileName = isset( $activeFileData[ 'fileName' ] ) ? $activeFileData[ 'fileName' ] : [];
                $sheetList      = [];
                $activeSheet    = "";

                if ( preg_match( '%\W(xls|xlsx)$%i', trim( $activeFileName ) ) ) {

                        $activeFileDir = isset( $activeFileData[ 'fileDir' ] ) ? $activeFileData[ 'fileDir' ] : "";

                        $excelFileName = WPIE_UPLOAD_IMPORT_DIR . "/" . $activeFileDir . "/" . $activeFileName;

                        if ( file_exists( $excelFileName ) ) {

                                wpie_load_vendor_autoloader();

                                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $excelFileName );

                                if ( $spreadsheet->getSheetCount() > 1 ) {

                                        $activeSheetData = $spreadsheet->getActiveSheet();

                                        $activeSheet = $activeSheetData->getTitle();

                                        $sheetList = $spreadsheet->getSheetNames();
                                }
                                $spreadsheet->disconnectWorksheets();

                                unset( $spreadsheet );
                        }
                }

                return [ "sheetList" => $sheetList, "activeSheet" => $activeSheet ];
        }

        protected function wpie_import_get_filtered_records() {

                $return_value = array( 'status' => 'error' );

                $wpie_import_id = isset( $_POST[ "wpie_import_id" ] ) ? intval( wpie_sanitize_field( $_POST[ "wpie_import_id" ] ) ) : 0;

                $template_data = $this->get_template_by_id( $wpie_import_id );

                if ( $template_data ) {

                        $template_options = maybe_unserialize( $template_data->options );

                        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                        $new_template_data = array_merge( $template_options, wp_unslash( $_POST ) );

                        if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-record.php' ) ) {
                                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-record.php');
                        }

                        $records = new \wpie\import\record\WPIE_Record();

                        $parse_data = $records->auto_fetch_records_by_template( $new_template_data );

                        if ( is_wp_error( $parse_data ) ) {
                                $return_value[ 'message' ] = $parse_data->get_error_message();
                        } else {

                                $return_value = $parse_data;

                                if ( isset( $parse_data[ 'count' ] ) && absint( $parse_data[ 'count' ] ) > 0 ) {

                                        global $wpdb;

                                        $wpdb->update( $wpdb->prefix . "wpie_template", array( 'last_update_date' => current_time( 'mysql' ), 'process_log' => maybe_serialize( array( "total" => absint( $parse_data[ 'count' ] ) ) ) ), array( 'id' => $wpie_import_id ) );
                                }

                                $return_value[ 'status' ] = 'success';
                        }

                        unset( $records, $parse_data );
                } else {

                        $return_value[ 'message' ] = __( 'Template Not Found', 'wp-import-export-lite' );
                }
                unset( $wpie_import_id, $template_data );

                echo json_encode( $return_value );

                die();
        }

        protected function wpie_get_import_fields() {

                $return_value = array( 'status' => 'error' );

                $type = isset( $_GET[ "type" ] ) ? wpie_sanitize_field( $_GET[ "type" ] ) : "";

                if ( !empty( $type ) ) {

                        $fileName = "";

                        if ( $type == "taxonomies" ) {
                                $fileName = WPIE_IMPORT_CLASSES_DIR . '/fields/wpie-taxonomy.php';
                        } elseif ( $type == "comments" || $type == "product_reviews" ) {
                                $fileName = WPIE_IMPORT_CLASSES_DIR . '/fields/wpie-comments.php';
                        } else {
                                $fileName = WPIE_IMPORT_CLASSES_DIR . '/fields/wpie-post.php';
                        }

                        $fileName = apply_filters( 'wpie_import_mapping_fields_file', $fileName, $type );

                        if ( file_exists( $fileName ) ) {
                                require_once($fileName);
                        }

                        $fields = apply_filters( 'wpie_import_mapping_fields', array(), $type );

                        ksort( $fields );

                        $field_data = "";

                        if ( !empty( $fields ) ) {
                                foreach ( $fields as $section ) {
                                        $field_data .= balanceTags( $section );
                                }
                        }

                        $return_value[ 'update_fields' ] = apply_filters( 'wpie_import_update_existing_item_fields', "", $type );

                        $return_value[ 'search_fields' ] = apply_filters( 'wpie_import_search_existing_item', "", $type );

                        $return_value[ 'fields' ] = $field_data;

                        unset( $fileName, $fields, $field_data );

                        $return_value[ 'status' ] = 'success';
                } else {

                        $return_value[ 'message' ] = __( 'Import Type is undefind', 'wp-import-export-lite' );
                }

                unset( $type );

                echo json_encode( $return_value );

                die();
        }

        public function wpie_finalyze_template_data( $opration = "import" ) {

                $wpie_import_id = isset( $_POST[ "wpie_import_id" ] ) ? absint( wpie_sanitize_field( $_POST[ "wpie_import_id" ] ) ) : 0;

                if ( $wpie_import_id > 0 ) {

                        global $wpdb;

                        $new_values = array();

                        $template_data = $this->get_template_by_id( $wpie_import_id );

                        if ( $template_data ) {

                                $template_options = maybe_unserialize( $template_data->options );
                        } else {
                                $template_options = array();
                        }

                        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                        $new_template_data = array_merge( $template_options, wp_unslash( $_POST ) );

                        $new_values[ 'options' ] = maybe_serialize( $new_template_data );

                        $new_values[ 'opration' ] = $opration;

                        $new_values[ 'opration_type' ] = isset( $_POST[ 'wpie_import_type' ] ) ? wpie_sanitize_field( $_POST[ 'wpie_import_type' ] ) : "post";

                        $bg = isset( $_POST[ 'bg' ] ) ? absint( wpie_sanitize_field( $_POST[ 'bg' ] ) ) : 0;

                        if ( $bg == 1 ) {
                                $new_values[ 'status' ] = "background";
                        } else {
                                $new_values[ 'status' ] = "processing";
                        }

                        $wpdb->update( $wpdb->prefix . "wpie_template", $new_values, array( 'id' => $wpie_import_id ) );

                        if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-csv-chunk.php' ) ) {
                                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-csv-chunk.php');
                        }

                        $chunk = new WPIE_Chunk();

                        $result = $chunk->process_data( $new_template_data );

                        unset( $template_data, $template_options, $bg, $new_values, $chunk, $new_template_data, $wpie_import_id );

                        return $result;
                }

                unset( $wpie_import_id );
        }

        protected function wpie_import_save_data() {

                $return_value = array( 'status' => 'error' );

                $result = $this->wpie_finalyze_template_data();

                if ( is_wp_error( $result ) ) {
                        $return_value[ 'message' ] = $result->get_error_message();
                } else {

                        $return_value[ 'status' ] = 'success';
                }

                echo json_encode( $return_value );

                die();
        }

        protected function wpie_import_data() {

                $return_value = array( 'status' => 'error' );

                $wpie_import_id = isset( $_GET[ "wpie_import_id" ] ) ? intval( wpie_sanitize_field( $_GET[ "wpie_import_id" ] ) ) : 0;

                if ( $wpie_import_id != 0 ) {

                        $import_process = $this->wpie_import_process_data( $wpie_import_id );

                        if ( is_wp_error( $import_process ) ) {

                                $return_value[ 'message' ] = $import_process->get_error_message();
                        } else {

                                $return_value[ 'status' ] = 'success';

                                $process_log = isset( $import_process[ 'process_log' ] ) ? $import_process[ 'process_log' ] : array();

                                $import_log = isset( $import_process[ 'import_log' ] ) ? $import_process[ 'import_log' ] : "";

                                $return_value[ 'imported' ] = isset( $process_log[ 'imported' ] ) ? intval( $process_log[ 'imported' ] ) : 0;

                                $return_value[ 'created' ] = isset( $process_log[ 'created' ] ) ? intval( $process_log[ 'created' ] ) : 0;

                                $return_value[ 'updated' ] = isset( $process_log[ 'updated' ] ) ? intval( $process_log[ 'updated' ] ) : 0;

                                $return_value[ 'skipped' ] = isset( $process_log[ 'skipped' ] ) ? intval( $process_log[ 'skipped' ] ) : 0;

                                $return_value[ 'import_log' ] = $import_log;

                                unset( $import_log, $process_log );
                        }

                        unset( $import_process );
                }
                unset( $wpie_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function wpie_import_process_data( $wpie_import_id = 0 ) {


                $template_data = $this->get_template_by_id( $wpie_import_id );

                if ( !$template_data ) {
                        return new \WP_Error( 'wpie_import_error', __( 'Template Not Found', 'wp-import-export-lite' ) );
                }

                global $wpdb;

                $wpdb->update( $wpdb->prefix . "wpie_template", array( 'process_lock' => 1 ), array( 'id' => $wpie_import_id ) );

                $wpie_import_type = (isset( $template_data->opration_type ) && trim( $template_data->opration_type ) != "") ? $template_data->opration_type : "post";

                $this->add_compatibility( $template_data );

                $import_engine = "";

                if ( $wpie_import_type == "taxonomies" ) {

                        if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-taxonomy.php' ) ) {

                                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-taxonomy.php');

                                $import_engine = 'wpie\import\taxonomy\WPIE_Taxonomy';
                        }
                } elseif ( $wpie_import_type == "comments" || $wpie_import_type == "product_reviews" ) {

                        if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-comment.php' ) ) {

                                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-comment.php');
                        }

                        $import_engine = 'wpie\import\comment\WPIE_Comment';
                } else {

                        if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-post.php' ) ) {

                                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-post.php');

                                $import_engine = 'wpie\import\post\WPIE_Post';
                        }
                }

                $import_engine = apply_filters( 'wpie_import_engine_init', $import_engine, $wpie_import_type, $template_data );

                $import_process = array();

                if ( class_exists( $import_engine ) ) {

                        $import_data = new $import_engine();

                        if ( method_exists( $import_data, "wpie_import_data" ) ) {

                                $import_process = $import_data->wpie_import_data( $template_data );
                        }

                        unset( $import_data );
                }

                $final_data = array(
                        'last_update_date' => current_time( 'mysql' ),
                        'process_lock'     => 0
                );

                $wpdb->update( $wpdb->prefix . "wpie_template", $final_data, array( 'id' => $wpie_import_id ) );

                unset( $template_data, $wpie_import_type, $import_engine, $final_data );

                return $import_process;
        }

        private function add_compatibility( $template_data ) {

                if ( is_readable( WPIE_IMPORT_CLASSES_DIR . '/compatibility/manager.php' ) ) {

                        require_once(WPIE_IMPORT_CLASSES_DIR . '/compatibility/manager.php');

                        new AddOns( $template_data );
                }
        }

        protected function wpie_get_template_list() {

                global $wpdb;

                $content_type = isset( $_GET[ 'wpie_import_type' ] ) ? wpie_sanitize_field( $_GET[ 'wpie_import_type' ] ) : "post";

                $results = $wpdb->get_results( $wpdb->prepare( "SELECT `id`,`options` FROM " . $wpdb->prefix . "wpie_template where `opration_type` = %s AND `opration`='import_template' ORDER BY `" . $wpdb->prefix . "wpie_template`.`id` DESC", $content_type ) );

                $data = array();

                if ( !empty( $results ) ) {

                        $count = 0;

                        foreach ( $results as $template ) {

                                $data[ $count ][ 'id' ] = isset( $template->id ) ? $template->id : 0;

                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                $data[ $count ][ 'name' ] = isset( $options[ 'wpie_template_name' ] ) ? $options[ 'wpie_template_name' ] : "";

                                $count++;

                                unset( $options );
                        }

                        unset( $count );
                }
                unset( $content_type, $results );

                $return_value = array();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'templates' ] = $data;

                unset( $data );

                echo json_encode( $return_value );

                die();
        }

        protected function wpie_get_settings_list() {

                global $wpdb;

                $type = isset( $_POST[ 'type' ] ) ? wpie_sanitize_field( $_POST[ 'type' ] ) : "post";

                $results = $wpdb->get_results( $wpdb->prepare( "SELECT `id`,`options`,`create_date` FROM " . $wpdb->prefix . "wpie_template where `opration_type` = %s AND `opration`='import' ORDER BY `" . $wpdb->prefix . "wpie_template`.`id` DESC", $type ) );

                $data = array();

                if ( !empty( $results ) ) {

                        $count = 0;

                        foreach ( $results as $template ) {

                                $data[ $count ][ 'id' ] = isset( $template->id ) ? $template->id : 0;

                                $date = isset( $template->create_date ) ? $template->create_date : "";

                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : [];

                                $importFile = isset( $options[ 'importFile' ] ) ? $options[ 'importFile' ] : array();

                                $activeFile = isset( $options[ 'activeFile' ] ) ? $options[ 'activeFile' ] : "";

                                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                                $fileName = isset( $fileData[ 'originalName' ] ) ? $fileData[ 'originalName' ] : "";

                                $data[ $count ][ 'name' ] = $date . " " . $fileName;

                                $count++;

                                unset( $options );
                        }

                        unset( $count );
                }
                unset( $type, $results );

                $return_value = array();

                $return_value[ 'status' ] = 'success';

                $return_value[ 'templates' ] = $data;

                unset( $data );

                echo json_encode( $return_value );

                die();
        }

        protected function wpie_import_save_template_data() {

                $return_value = array();

                global $wpdb;

                $template_id = isset( $_POST[ 'template_id' ] ) ? absint( wpie_sanitize_field( $_POST[ 'template_id' ] ) ) : 0;

                if ( $template_id > 0 ) {

                        $options = $wpdb->get_var( $wpdb->prepare( "SELECT `options` FROM " . $wpdb->prefix . "wpie_template where `id`=%d", $template_id ) );

                        if ( !is_null( $options ) ) {

                                $options = maybe_unserialize( $options );

                                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                                $new_options = wp_unslash( $_POST );

                                $new_options[ 'wpie_template_name' ] = isset( $options[ 'wpie_template_name' ] ) ? $options[ 'wpie_template_name' ] : "";

                                $new_values = array();

                                $new_values[ 'options' ] = maybe_serialize( wp_unslash( $new_options ) );

                                $wpdb->update( $wpdb->prefix . "wpie_template", $new_values, array( 'id' => $template_id ) );

                                $return_value[ 'status' ] = 'success';

                                $return_value[ 'message' ] = __( 'Setting Updated Successfully', 'wp-import-export-lite' );

                                echo json_encode( $return_value );

                                die();
                        }
                }

                $template_name = isset( $_POST[ 'wpie_template_name' ] ) ? wpie_sanitize_field( $_POST[ 'wpie_template_name' ] ) : "";

                $is_exist = false;

                if ( !empty( $template_name ) ) {

                        $results = $wpdb->get_results( "SELECT `id`,`options` FROM " . $wpdb->prefix . "wpie_template where `opration`='import_template'" );

                        if ( !empty( $results ) ) {

                                foreach ( $results as $template ) {

                                        $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                        $temp_name = isset( $options[ 'wpie_template_name' ] ) ? $options[ 'wpie_template_name' ] : "";

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

                        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Escaping for HTML will break functionality.
                        $template_id = $this->wpie_generate_template( wp_unslash( $_POST ), "import_template", "completed" );

                        $return_value[ 'status' ] = 'success';

                        $return_value[ 'template_id' ] = $template_id;

                        unset( $template_id );

                        $return_value[ 'message' ] = __( 'Setting Saved Successfully', 'wp-import-export-lite' );
                } else {
                        $return_value[ 'status' ] = 'error';

                        $return_value[ 'message' ] = __( 'Setting Name Already Exists', 'wp-import-export-lite' );
                }

                echo json_encode( $return_value );

                die();
        }

        protected function wpie_import_get_template_info() {

                $return_value = array( 'status' => 'error' );

                $template_id = isset( $_GET[ "wpie_template_id" ] ) ? absint( wpie_sanitize_field( $_GET[ "wpie_template_id" ] ) ) : 0;

                if ( $template_id > 0 ) {

                        $template_data = $this->get_template_by_id( $template_id );

                        $return_value[ 'status' ] = 'success';

                        $return_value[ 'template_data' ] = isset( $template_data->options ) ? maybe_unserialize( $template_data->options ) : array();

                        $return_value[ 'message' ] = __( 'Template Successfully Saved', 'wp-import-export-lite' );

                        unset( $template_data );
                }

                unset( $template_id );

                echo json_encode( $return_value );

                die();
        }

        protected function wpie_import_update_csv_delimiter() {

                $return_value = array( 'status' => 'error' );

                $wpie_import_id = isset( $_GET[ "wpie_import_id" ] ) ? intval( wpie_sanitize_field( $_GET[ "wpie_import_id" ] ) ) : 0;

                if ( $wpie_import_id != 0 ) {

                        $template_data = $this->get_template_by_id( $wpie_import_id );

                        if ( $template_data ) {

                                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload-validate.php' ) ) {
                                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload-validate.php');
                                }

                                $data_validate = new WPIE_Upload_Validate();

                                $data = $data_validate->wpie_parse_upload_data( $template_data );

                                if ( is_wp_error( $data ) ) {
                                        $return_value[ 'message' ] = $data->get_error_message();
                                } else {
                                        $return_value[ 'status' ]  = "success";
                                        $return_value[ 'message' ] = __( 'File is Valid', 'wp-import-export-lite' );
                                }
                                unset( $data_validate, $data );
                        } else {
                                $return_value[ 'message' ] = __( 'Template Not Found', 'wp-import-export-lite' );
                        }

                        unset( $template_data );
                } else {
                        $return_value[ 'message' ] = __( 'Data Not Found', 'wp-import-export-lite' );
                }

                unset( $wpie_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function wpie_import_update_process_status() {

                $return_value = array( 'status' => 'error' );

                $wpie_import_id = isset( $_GET[ "wpie_import_id" ] ) ? absint( wpie_sanitize_field( $_GET[ "wpie_import_id" ] ) ) : 0;

                if ( $wpie_import_id != 0 ) {

                        $wpie_status = isset( $_GET[ "status" ] ) ? wpie_sanitize_field( $_GET[ "status" ] ) : "";

                        $status = "";

                        if ( $wpie_status == "bg" ) {
                                $status = "background";
                        }

                        if ( $status != "" ) {

                                $final_data = array(
                                        'last_update_date' => current_time( 'mysql' ),
                                        'process_lock'     => 0,
                                        'status'           => $status
                                );

                                global $wpdb;

                                $wpdb->update( $wpdb->prefix . "wpie_template", $final_data, array( 'id' => $wpie_import_id ) );

                                unset( $final_data );
                        }

                        unset( $wpie_status, $status );

                        $return_value[ 'status' ] = "success";
                } else {
                        $return_value[ 'message' ] = __( 'Template Not Found', 'wp-import-export-lite' );
                }

                unset( $wpie_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function get_config_file() {

                $return_value = array( 'status' => 'error' );

                $wpie_import_id = isset( $_GET[ "import_id" ] ) ? absint( wpie_sanitize_field( $_GET[ "import_id" ] ) ) : 0;

                if ( $wpie_import_id != 0 ) {

                        $template = $this->get_template_by_id( $wpie_import_id );

                        if ( $template !== false ) {

                                $option = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                $activeFile = isset( $option[ 'activeFile' ] ) ? $option[ 'activeFile' ] : "";

                                $importFile = isset( $option[ 'importFile' ] ) ? $option[ 'importFile' ] : array();

                                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                                $configFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/config/config.json";

                                if ( file_exists( $configFile ) ) {

                                        $return_value[ 'config' ] = json_decode( file_get_contents( $configFile ) );
                                }

                                unset( $option, $activeFile, $importFile, $fileData, $baseDir, $configFile );
                        }

                        unset( $template );

                        $return_value[ 'status' ] = "success";
                } else {
                        $return_value[ 'message' ] = __( 'Template Not Found', 'wp-import-export-lite' );
                }

                unset( $wpie_import_id );

                echo json_encode( $return_value );

                die();
        }

        protected function process_reimport_data() {

                $return_value = array( "status" => "error" );

                $import_id = isset( $_GET[ 'import_id' ] ) ? absint( $_GET[ 'import_id' ] ) : 0;

                if ( $import_id > 0 ) {

                        $ref_id = isset( $_GET[ 'ref_id' ] ) ? wpie_sanitize_field( $_GET[ 'ref_id' ] ) : "";

                        $nonce = isset( $_GET[ 'nonce' ] ) ? wpie_sanitize_field( $_GET[ 'nonce' ] ) : "";

                        $validate_nonce = wp_verify_nonce( $nonce, $import_id . $ref_id );

                        if ( $validate_nonce === 1 || $validate_nonce === 2 ) {

                                $ref_template = $this->get_template_by_ref( $ref_id );

                                $new_import_id = 0;

                                $ref_base_dir = "";

                                $is_completed = false;

                                if ( !empty( $ref_template ) ) {

                                        $ref_status = isset( $ref_template->status ) ? $ref_template->status : "";

                                        if ( $ref_status !== "completed" ) {
                                                $new_import_id = isset( $ref_template->id ) ? $ref_template->id : 0;

                                                $ref_option = isset( $ref_template->options ) ? maybe_unserialize( $ref_template->options ) : array();

                                                $ref_activeFile = isset( $ref_option[ 'activeFile' ] ) ? $ref_option[ 'activeFile' ] : "";

                                                $ref_importFile = isset( $ref_option[ 'importFile' ] ) ? $ref_option[ 'importFile' ] : array();

                                                $ref_fileData = isset( $ref_importFile[ $ref_activeFile ] ) ? $ref_importFile[ $ref_activeFile ] : "";

                                                $ref_base_dir = $ref_fileData[ 'baseDir' ] ? $ref_fileData[ 'baseDir' ] : "";

                                                $this->remove_dir( WPIE_UPLOAD_IMPORT_DIR . "/" . $ref_base_dir . "/" );

                                                unset( $ref_option, $ref_activeFile, $ref_importFile, $ref_fileData );
                                        } else {
                                                $is_completed = true;
                                        }
                                }

                                if ( $is_completed === false ) {

                                        $template = $this->get_template_by_id( $import_id );

                                        if ( $template !== false ) {

                                                $option = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                                $import_type = isset( $template->opration_type ) ? $template->opration_type : "post";

                                                $taxonomy_type = isset( $option[ 'wpie_taxonomy_type' ] ) ? $option[ 'wpie_taxonomy_type' ] : "";

                                                $activeFile = isset( $option[ 'activeFile' ] ) ? $option[ 'activeFile' ] : "";

                                                $importFile = isset( $option[ 'importFile' ] ) ? $option[ 'importFile' ] : array();

                                                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                                                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                                                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php' ) ) {
                                                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php');
                                                }

                                                $data_uploader = new WPIE_Upload();

                                                $new_dir_name = $data_uploader->wpie_create_safe_dir_name( $import_id );

                                                $this->custom_copy( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir, WPIE_UPLOAD_IMPORT_DIR . "/" . $new_dir_name );

                                                $file_data = array();

                                                $fileList = [];

                                                if ( !empty( $importFile ) ) {

                                                        foreach ( $importFile as $key => $value ) {

                                                                if ( is_array( $value ) ) {

                                                                        $base_dir = isset( $value[ 'baseDir' ] ) ? $value[ 'baseDir' ] : "";

                                                                        $fileDir = isset( $value[ 'fileDir' ] ) && !empty( $value[ 'fileDir' ] ) ? str_replace( $base_dir, $new_dir_name, $value[ 'fileDir' ] ) : "";

                                                                        $value[ 'baseDir' ] = $new_dir_name;

                                                                        $value[ 'fileDir' ] = $fileDir;

                                                                        $file_data[ $key ] = $value;

                                                                        $fileList[] = array(
                                                                                'fileKey'  => $key,
                                                                                'fileName' => isset( $value[ 'fileName' ] ) ? $value[ 'fileName' ] : ""
                                                                        );
                                                                }
                                                        }
                                                }

                                                $option[ 'importFile' ] = $file_data;

                                                $importFile = isset( $option[ 'importFile' ] ) ? $option[ 'importFile' ] : array();

                                                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                                                $fileDir = $fileData[ 'fileDir' ] ? $fileData[ 'fileDir' ] : "";

                                                $originalName = $fileData[ 'originalName' ] ? $fileData[ 'originalName' ] : "";

                                                if ( is_readable( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/log" ) ) {
                                                        $this->remove_dir( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/log/" );
                                                        wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/log" );
                                                }
                                                if ( is_readable( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunk" ) ) {
                                                        $this->remove_dir( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" );
                                                        wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks" );
                                                }

                                                $return_value[ "import_type" ] = $import_type;

                                                $return_value[ "taxonomy_type" ] = $taxonomy_type;

                                                $return_value[ "activeFile" ] = $activeFile;

                                                $sheetData = $this->getSheetData( $fileData );

                                                $return_value[ "sheetList" ] = isset( $sheetData[ 'sheetList' ] ) ? $sheetData[ 'sheetList' ] : [];

                                                $return_value[ "activeFormat" ] = isset( $option[ 'wpie_text_format_list' ] ) ? $option[ 'wpie_text_format_list' ] : "";

                                                $return_value[ "activeSheet" ] = isset( $option[ 'activeSheet' ] ) ? $option[ 'activeSheet' ] : "";

                                                $return_value[ "xpath" ] = isset( $option[ 'xpath' ] ) ? $option[ 'xpath' ] : "";

                                                $return_value[ "root" ] = isset( $option[ 'root' ] ) ? $option[ 'root' ] : "";

                                                $return_value[ 'wpie_import_id' ] = $this->wpie_generate_template( $option, 'import', 'processing', $ref_id, $new_import_id );

                                                $return_value[ 'file_list' ] = $fileList;

                                                $_path = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/original/" . $originalName;

                                                $return_value[ 'file_size' ] = is_readable( $_path ) ? filesize( $_path ) : 0;

                                                $return_value[ 'file_name' ] = $originalName;

                                                $return_value[ 'file_count' ] = count( $fileList );

                                                $return_value[ 'status' ] = "success";
                                        } else {
                                                $return_value[ 'message' ] = __( 'Template Not Found', 'wp-import-export-lite' );
                                        }
                                } else {
                                        $return_value[ "message" ] = esc_html__( 'Reimport for given link is completed. please generate new links for reimport from manage import', "wp-import-export-lite" );
                                }
                        } else {
                                $return_value[ "message" ] = esc_html__( 'Invalid Nonce. Go to Manage Import for new valid Reimport links', "wp-import-export-lite" );
                        }
                } else {
                        $return_value[ 'message' ] = __( 'Template not found', 'wp-import-export-lite' );
                }

                echo json_encode( $return_value );

                die();
        }

        private function custom_copy( $src = "", $dst = "" ) {

                if ( is_dir( $src ) ) {
                        // open the source directory 
                        $dir = opendir( $src );

                        // Make the destination directory if not exist 
                        if ( !is_dir( $dst ) ) {
                                wp_mkdir_p( $dst );
                        }

                        // Loop through the files in source directory 
                        while ( $file = readdir( $dir ) ) {

                                if ( ( $file != '.' ) && ( $file != '..' ) ) {
                                        if ( is_dir( $src . '/' . $file ) ) {

                                                // Recursively calling custom copy function 
                                                // for sub directory  
                                                $this->custom_copy( $src . '/' . $file, $dst . '/' . $file );
                                        } else {
                                                copy( $src . '/' . $file, $dst . '/' . $file );
                                        }
                                }
                        }

                        closedir( $dir );
                }
        }

        private function remove_dir( $targetDir = "" ) {

                if ( is_dir( $targetDir ) ) {

                        $cdir = scandir( $targetDir );

                        if ( is_array( $cdir ) && !empty( $cdir ) ) {
                                foreach ( $cdir as $key => $value ) {
                                        if ( !in_array( $value, array( ".", ".." ) ) ) {
                                                if ( is_dir( $targetDir . '/' . $value ) ) {
                                                        $this->remove_dir( $targetDir . '/' . $value );
                                                } else {
                                                        unlink( $targetDir . '/' . $value );
                                                }
                                        }
                                }
                        }

                        rmdir( $targetDir );

                        unset( $cdir );
                }
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

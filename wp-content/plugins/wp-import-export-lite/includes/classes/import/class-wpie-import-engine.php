<?php


namespace wpie\import\engine;

use wpie\import\backup\WPIE_Import_Backup;
use wpie\import\log\WPIE_Import_Log;
use wpie\import\images;
use wpie\import\record;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

abstract class WPIE_Import_Engine extends \wpie\import\base\WPIE_Import_Base {

        abstract function process_import_data();

        abstract protected function search_duplicate_item();

        public function wpie_import_data( $template_data = null ) {

                global $importTemplate, $importOptions, $wpieImportType, $wpieImportRecords;

                $importTemplate = $template_data;

                $this->wpie_import_id = $wpie_import_id       = isset( $template_data->id ) ? $template_data->id : 0;

                $this->wpie_import_option = $importOptions            = isset( $template_data->options ) && trim( $template_data->options ) != "" ? maybe_unserialize( $template_data->options ) : array();

                $wpieImportType = $field_data     = isset( $importOptions[ "wpie_import_type" ] ) ? $importOptions[ "wpie_import_type" ] : "";

                $this->import_username = isset( $template_data->username ) && trim( $template_data->username ) != "" ? $template_data->username : "";

                do_action( 'wpie_import_start', $this->wpie_import_id, $this->wpie_import_option );

                $this->init_services();

                $import_data = $this->get_records( $template_data );

                if ( is_wp_error( $import_data ) ) {
                        return $import_data;
                }

                $process_data = isset( $template_data->process_log ) ? maybe_unserialize( $template_data->process_log ) : array();

                unset( $template_data );

                $this->process_log = array(
                        'total'               => (isset( $process_data[ 'total' ] ) && $process_data[ 'total' ] != "") ? absint( $process_data[ 'total' ] ) : 0,
                        'imported'            => (isset( $process_data[ 'imported' ] ) && $process_data[ 'imported' ] != "") ? absint( $process_data[ 'imported' ] ) : 0,
                        'created'             => (isset( $process_data[ 'created' ] ) && $process_data[ 'created' ] != "") ? absint( $process_data[ 'created' ] ) : 0,
                        'updated'             => (isset( $process_data[ 'updated' ] ) && $process_data[ 'updated' ] != "") ? absint( $process_data[ 'updated' ] ) : 0,
                        'skipped'             => (isset( $process_data[ 'skipped' ] ) && $process_data[ 'skipped' ] != "") ? absint( $process_data[ 'skipped' ] ) : 0,
                        'last_records_id'     => (isset( $process_data[ 'last_records_id' ] ) && $process_data[ 'last_records_id' ] != "") ? absint( $process_data[ 'last_records_id' ] ) : 0,
                        'last_records_status' => (isset( $process_data[ 'last_records_status' ] ) && $process_data[ 'last_records_status' ] != "") ? $process_data[ 'last_records_status' ] : ''
                );

                unset( $process_data );

                $addon_class = apply_filters( 'wpie_import_addon', array(), wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) ) );

                if ( !empty( $addon_class ) ) {

                        foreach ( $addon_class as $key => $addon ) {

                                if ( class_exists( $addon ) ) {

                                        $this->addons[ $key ] = new $addon( $this->wpie_import_option, $this->import_type, $this->addon_error, $this->addon_log );
                                }
                        }
                }

                unset( $addon_class );

                $this->import_log = array();

                global $wpdb;

                if ( !empty( $import_data ) ) {

                        foreach ( $import_data as $data ) {

                                $this->reset_iteration_data();

                                $this->wpie_import_record = $wpieImportRecords        = $data;

                                $this->init_import_process();

                                $wpdb->update( $wpdb->prefix . "wpie_template", array( 'last_update_date' => current_time( 'mysql' ), 'process_log' => maybe_serialize( $this->process_log ) ), array( 'id' => $this->wpie_import_id ) );

                                $this->set_log( "" );
                        }
                }

                unset( $import_data );

                if ( !empty( $this->addons ) ) {

                        foreach ( $this->addons as $addon ) {

                                if ( method_exists( $addon, "task_completed" ) ) {

                                        $addon->task_completed();

                                        if ( !empty( $this->addon_log ) ) {

                                                $this->set_log( $this->addon_log );

                                                $this->addon_log = array();
                                        }

                                        if ( $this->addon_error === true ) {

                                                break;
                                        }
                                }
                        }
                }
                if ( $this->addon_error === true ) {

                        $this->remove_current_item();

                        return true;
                }

                $this->finalyze_process();

                if ( $this->process_log[ "total" ] !== 0 && $this->process_log[ "imported" ] >= $this->process_log[ "total" ] ) {

                        $wpdb->update( $wpdb->prefix . "wpie_template", array( 'last_update_date' => current_time( 'mysql' ), 'status' => "completed" ), array( 'id' => $this->wpie_import_id ) );
                }

                do_action( 'wpie_import_end', $this->wpie_import_id, $this->wpie_import_option );

                return array( 'process_log' => $this->process_log, 'import_log' => $this->import_log );
        }

        private function get_records() {

                global $importTemplate;

                $xpath = isset( $this->wpie_import_option[ "xpath" ] ) ? "/" . wp_unslash( $this->wpie_import_option[ "xpath" ] ) : "";

                $process_data = isset( $importTemplate->process_log ) ? maybe_unserialize( $importTemplate->process_log ) : [];

                $start = (isset( $process_data[ 'imported' ] ) && $process_data[ 'imported' ] != "") ? absint( $process_data[ 'imported' ] ) : 0;

                $last_records_status = isset( $process_data[ 'last_records_status' ] ) ? $process_data[ 'last_records_status' ] : "";

                if ( $last_records_status == "pending" ) {

                        if ( $start > 0 ) {
                                $start--;
                        }
                }

                $wpie_file_processing_type = isset( $this->wpie_import_option[ "wpie_import_file_processing" ] ) ? wpie_sanitize_field( $this->wpie_import_option[ "wpie_import_file_processing" ] ) : "chunk";

                $split_file = "";

                $length = false;

                if ( $wpie_file_processing_type == "chunk" ) {
                        $length     = isset( $this->wpie_import_option[ "wpie_records_per_request" ] ) ? absint( wpie_sanitize_field( $this->wpie_import_option[ "wpie_records_per_request" ] ) ) : 20;
                        $split_file = isset( $this->wpie_import_option[ "wpie_import_split_file" ] ) ? wpie_sanitize_field( $this->wpie_import_option[ "wpie_import_split_file" ] ) : "";
                }

                $activeFile = isset( $this->wpie_import_option[ 'activeFile' ] ) ? $this->wpie_import_option[ 'activeFile' ] : "";

                $importFile = isset( $this->wpie_import_option[ 'importFile' ] ) ? $this->wpie_import_option[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                $chunks = 1000;

                if ( $start !== false && $start >= $chunks ) {
                        $start_file = floor( $start / $chunks ) + 1;
                        $start      = $start % $chunks;
                } else {
                        $start_file = 1;
                        $start      = $start;
                }

                $newFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/chunks/" . $this->wpie_fileName . $start_file . '.xml';

                if ( !file_exists( $newFile ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File not exist', 'wp-import-export-lite' ) );
                }
                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-record.php' ) ) {

                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-record.php');
                }

                $records = new \wpie\import\record\WPIE_Record();

                $results = $records->get_records( $newFile, $xpath, $start, $length );

                unset( $xpath, $process_data, $start, $wpie_file_processing_type, $activeFile, $fileData, $baseDir, $chunks, $start_file, $newFile, $records );

                return $results;
        }

        private function reset_iteration_data() {

                $this->is_new_item = true;

                $this->item_id = 0;

                $this->existing_item_id = 0;

                $this->wpie_final_data = array();

                $this->as_draft = false;

                $this->item = false;
        }

        private function init_import_process() {

                global $wpdb;

                $is_search_duplicates = true;

                $this->set_log( "<strong>" . __( 'Record', 'wp-import-export-lite' ) . "</strong>" . " #" . ( $this->process_log[ 'imported' ] + 1) );

                if ( isset( $this->process_log[ 'last_records_status' ] ) && $this->process_log[ 'last_records_status' ] == 'pending' && isset( $this->process_log[ 'last_records_id' ] ) ) {

                        $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 1", intval( $this->process_log[ 'last_records_id' ] ) ) );

                        if ( $_post ) {

                                $this->is_new_item = false;

                                $is_search_duplicates = false;

                                $this->existing_item_id = intval( $this->process_log[ 'last_records_id' ] );

                                $this->set_log( __( 'Complete Pending Last Records', 'wp-import-export-lite' ) . " #" . $this->existing_item_id );
                        }

                        unset( $_post );
                }

                if ( !empty( $this->addons ) ) {

                        foreach ( $this->addons as $addon ) {

                                if ( method_exists( $addon, "before_item_import" ) ) {

                                        $addon->before_item_import( $this->wpie_import_record, $this->existing_item_id, $this->is_new_item, $is_search_duplicates );

                                        if ( !empty( $this->addon_log ) ) {

                                                $this->set_log( $this->addon_log );

                                                $this->addon_log = array();
                                        }

                                        if ( $this->addon_error === true ) {

                                                $this->remove_current_item();

                                                break;
                                        }
                                }
                        }
                }

                if ( $is_search_duplicates ) {
                        $this->search_duplicate_item();
                }

                if ( absint( $this->existing_item_id ) > 0 ) {

                        $this->is_new_item = false;

                        $this->set_log( __( 'Existing item found', 'wp-import-export-lite' ) . " #" . $this->existing_item_id );
                }

                $handle_items = $this->get_field_value( 'handle_items', true );

                if ( !$this->is_new_item && $handle_items == "new" ) {

                        $this->set_log( "<strong>" . __( 'SKIPPED', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Skip Existing Items', 'wp-import-export-lite' ) );

                        $this->process_log[ 'skipped' ]++;

                        $this->process_log[ 'imported' ]++;

                        unset( $handle_items );

                        return $this->existing_item_id;
                } elseif ( $this->is_new_item && $handle_items == "existing" ) {

                        $this->set_log( "<strong>" . __( 'SKIPPED', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Skip New Items', 'wp-import-export-lite' ) );

                        $this->process_log[ 'skipped' ]++;

                        $this->process_log[ 'imported' ]++;

                        unset( $handle_items );

                        return true;
                }

                if ( $this->backup_service !== false && $process_last_records === false && absint( $this->existing_item_id ) > 0 ) {

                        $is_success = $this->backup_service->create_backup( $this->existing_item_id, false );

                        if ( is_wp_error( $is_success ) ) {
                                $this->set_log( "<strong>" . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $is_success->get_error_message() );
                        }
                        unset( $is_success );
                }

                unset( $handle_items, $process_last_records );

                $item_id = $this->process_import_data();

                if ( $item_id === true ) {
                        unset( $item_id );
                        return true;
                }

                unset( $item_id );

                if ( !empty( $this->addons ) ) {

                        foreach ( $this->addons as $addon ) {

                                if ( method_exists( $addon, "after_item_import" ) ) {

                                        $addon->after_item_import( $this->item_id, $this->item, $this->is_new_item );

                                        if ( !empty( $this->addon_log ) ) {

                                                $this->set_log( $this->addon_log );

                                                $this->addon_log = array();
                                        }

                                        if ( $this->addon_error === true ) {

                                                break;
                                        }
                                }
                        }
                }
                if ( $this->addon_error === true ) {

                        $this->remove_current_item();

                        return true;
                }

                if ( $this->as_draft && $this->import_type === "post" ) {

                        $post_status = 'draft';

                        if ( isset( $this->item->post_type ) && $this->item->post_type === "product_variation" ) {
                                $post_status = "publish";
                        }

                        $wpdb->update( $wpdb->posts, array( 'post_status' => $post_status ), [ 'ID' => $this->item_id ] );
                }

                do_action( 'wpie_after_completed_item_import', $this->item_id, $this->wpie_import_record, $this->wpie_final_data, $this->wpie_import_option );

                $this->set_log( $this->import_type . ' #' . $this->item_id . ' ' . __( 'Successfully Imported', 'wp-import-export-lite' ) );

                $this->process_log[ 'last_records_status' ] = 'completed';

                $this->process_log[ 'last_activity' ] = date( 'Y-m-d H:i:s' );
        }

        protected function wpie_import_images() {

                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-images.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-images.php');
                }

                $wpie_images = new \wpie\import\images\WPIE_Images( $this->item_id, $this->is_new_item, $this->wpie_import_option, $this->wpie_import_record, $this->import_type );

                $image_data = $wpie_images->prepare_images();

                if ( !empty( $image_data ) ) {
                        if ( isset( $image_data[ 'as_draft' ] ) && $image_data[ 'as_draft' ] === true ) {
                                $this->as_draft = true;
                        }
                        if ( isset( $image_data[ 'import_log' ] ) && is_array( $image_data[ 'import_log' ] ) && !empty( $image_data[ 'import_log' ] ) ) {

                                array_map( array( $this, 'set_log' ), $image_data[ 'import_log' ] );
                        }
                }

                unset( $image_data, $wpie_images );
        }

        protected function wpie_import_attachments() {

                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-attachments.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-attachments.php');
                }

                $attachments = new \wpie\import\WPIE_Attachments( $this->item_id, $this->is_new_item, $this->wpie_import_option, $this->wpie_import_record, $this->import_type );

                $data = $attachments->prepare_attachments();

                if ( !empty( $data ) ) {

                        if ( isset( $data[ 'import_log' ] ) && is_array( $data[ 'import_log' ] ) && !empty( $data[ 'import_log' ] ) ) {

                                array_map( array( $this, 'set_log' ), $data[ 'import_log' ] );
                        }
                }

                unset( $data, $attachments );
        }

        protected function import_image_tags( $post_content = "" ) {

                if ( empty( $post_content ) ) {
                        return $post_content;
                }

                if ( strpos( trim( strtolower( $post_content ) ), "<img" ) === false ) {
                        return $post_content;
                }

                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-images.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-images.php');
                }

                $wpie_images = new \wpie\import\images\WPIE_Images( $this->item_id, $this->is_new_item, $this->wpie_import_option, $this->wpie_import_record, $this->import_type );

                $post_content = $wpie_images->relink_content_images( $post_content );

                unset( $wpie_images );

                return $post_content;
        }

        protected function wpie_import_cf() {

                $isUpdateAll = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update', true ) );

                $item_cf_option = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_cf', true ) );

                $existing_metas = array();

                $exclude_metas = array();

                $includes_metas = array();

                if ( $isUpdateAll == "all" || $item_cf_option == "all" ) {
                        $existing_metas = $this->get_meta();
                } elseif ( $item_cf_option == "excludes" ) {

                        $exclude_metas_input = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_cf_excludes_data' ) );

                        if ( !empty( $exclude_metas_input ) ) {
                                $exclude_metas = explode( ",", $exclude_metas_input );
                        }
                        unset( $exclude_metas_input );
                } elseif ( $item_cf_option == "includes" ) {

                        $includes_metas_input = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_cf_includes_data' ) );

                        if ( !empty( $includes_metas_input ) ) {
                                $includes_metas = explode( ",", $includes_metas_input );
                        }
                        unset( $includes_metas_input );
                }

                unset( $item_cf_option );

                $wpie_item_cf = $this->get_field_value( 'wpie_item_cf' );

                $not_add_empty = intval( wpie_sanitize_field( $this->get_field_value( 'wpie_item_not_add_empty', true ) ) );

                $cf = $this->get_cf_list( $wpie_item_cf );

                if ( !empty( $cf ) ) {

                        foreach ( $cf as $meta_key => $meta_value ) {

                                if ( isset( $existing_metas[ $meta_key ] ) ) {
                                        unset( $existing_metas[ $meta_key ] );
                                }
                                if ( !empty( $includes_metas ) && !in_array( $meta_key, $includes_metas ) ) {
                                        continue;
                                }
                                if ( !empty( $exclude_metas ) && in_array( $meta_key, $exclude_metas ) ) {
                                        continue;
                                }

                                if ( in_array( $meta_key, array( '_thumbnail_id', '_product_image_gallery', '_wpie_order_number' ) ) ) {
                                        continue;
                                }
                                if ( ($not_add_empty === 1 && ((is_scalar( $meta_value ) && trim( ( string ) $meta_value ) !== "") || (!empty( $meta_value ))) ) || $not_add_empty !== 1 ) {

                                        $this->update_meta( $meta_key, $meta_value );
                                }
                        }
                }

                if ( !empty( $existing_metas ) ) {
                        foreach ( $existing_metas as $meta ) {
                                $this->remove_meta( $meta );
                        }
                }
                unset( $existing_metas, $exclude_metas, $includes_metas, $cf );
        }

        private function get_cf_list( $wpie_item_cf ) {

                $cf = array();

                if ( !empty( $wpie_item_cf ) && is_array( $wpie_item_cf ) ) {

                        foreach ( $wpie_item_cf as $key => $value ) {

                                $option = isset( $value[ 'option' ] ) ? strtolower( trim( $value[ 'option' ] ) ) : "";

                                if ( $option === "serialized" ) {
                                        if ( isset( $value[ 'values' ] ) && !empty( $value[ 'values' ] ) ) {
                                                $_value = $this->get_cf_list( $value[ 'values' ] );
                                        } else {
                                                $_value = "";
                                        }
                                } else {
                                        $_value = (isset( $value[ 'value' ] ) && !empty( $value[ 'value' ] )) ? $value[ 'value' ] : "";
                                }

                                if ( isset( $value[ 'name' ] ) && !empty( $value[ 'name' ] ) ) {
                                        $meta_key        = $value[ 'name' ];
                                        $cf[ $meta_key ] = $_value;
                                } else {
                                        $cf[] = $_value;
                                }
                        }
                }

                return $cf;
        }

        private function init_services() {

                $activeFile = isset( $this->wpie_import_option[ 'activeFile' ] ) ? $this->wpie_import_option[ 'activeFile' ] : "";

                $importFile = isset( $this->wpie_import_option[ 'importFile' ] ) ? $this->wpie_import_option[ 'importFile' ] : array();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $this->base_dir = isset( $fileData[ 'baseDir' ] ) ? $fileData[ 'baseDir' ] : "";

                unset( $activeFile, $importFile, $fileData );

                $this->init_log_services();

                /* $is_import_reversable = isset($this->wpie_import_option['is_import_reversable']) ? $this->wpie_import_option['is_import_reversable'] : 0;

                  if ($is_import_reversable == 1) {
                  // $this->init_backup_services();
                  }

                 */
        }

        private function init_backup_services() {

                if ( $this->backup_service === false ) {

                        if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-backup.php' ) ) {

                                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-backup.php');
                        }
                        $import_type = wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) );

                        $wpie_taxonomy_type = wpie_sanitize_field( $this->get_field_value( 'wpie_taxonomy_type', true ) );

                        $this->backup_service = new \wpie\import\backup\WPIE_Import_Backup();

                        wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/backup" );

                        $data = $this->backup_service->init_backup_services( $import_type, $wpie_taxonomy_type, WPIE_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/backup" );

                        if ( is_wp_error( $data ) ) {
                                $this->set_log( "<strong>" . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $data->get_error_message() );
                        }
                        unset( $data );
                }
        }

        private function init_log_services() {

                if ( $this->log_service === false ) {

                        if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-log.php' ) ) {

                                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-log.php');
                        }

                        $this->log_service = new \wpie\import\log\WPIE_Import_Log();

                        wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/log" );

                        $data = $this->log_service->init_log_services( WPIE_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/log" );

                        if ( is_wp_error( $data ) ) {
                                $this->set_log( "<strong>" . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $data->get_error_message() );
                        }

                        unset( $data );
                }
        }

        protected function finalyze_process() {

                if ( method_exists( $this->log_service, "finalyze_process" ) ) {
                        $this->log_service->finalyze_process();

                        unset( $this->log_service, $this->backup_service );
                }
        }

        public function set_log( $log = "" ) {

                if ( !empty( $log ) ) {

                        if ( is_array( $log ) ) {

                                foreach ( $log as $_log_text ) {

                                        $this->prepare_log( $_log_text );
                                }
                        } else {
                                $this->prepare_log( $log );
                        }
                }
        }

        private function prepare_log( $log = "" ) {

                $data = "[" . date( 'h:i:s' ) . "] " . $log;

                $this->import_log[] = "<p>" . $data . "</p>";

                $this->log_service->add_log( $data );

                unset( $log, $data );
        }

        private function remove_current_item() {
                wp_delete_post( $this->item_id, true );
        }

        public function __destruct() {
                parent::__destruct();
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

<?php


namespace wpie\export\engine;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php');
}

abstract class WPIE_Export_Engine extends \wpie\export\base\WPIE_Export_Base {

        private $fp;
        private $csv_delim;

        abstract protected function get_fields();

        abstract protected function parse_rule( $filter );

        abstract protected function process_export();

        public function init_engine( $export_type = "post", $opration = "export", $template = null ) {

                if ( $export_type == "product" ) {
                        $this->export_type = array( "product", "product_variation" );
                } else {

                        $this->export_type = array( $export_type );
                }
                $this->export_taxonomy_type = isset( $template->wpie_taxonomy_type ) ? $template->wpie_taxonomy_type : "";

                $this->opration = strtolower( trim( $opration ) );

                if ( $opration === "fields" ) {
                        $this->template_options = $template;
                        return $this->get_fields();
                } elseif ( $opration === "count" ) {
                        return $this->get_item_data( $template );
                } elseif ( $opration === "ids" ) {
                        return $this->get_item_data( $template );
                } elseif ( $opration === "preview" ) {
                        $this->is_preview = true;
                        return $this->get_item_data( $template );
                } elseif ( $opration === "import_backup" ) {
                        return $this->get_backup_data( $template );
                } else {
                        return $this->init_export( $template );
                }
        }

        private function get_backup_data( $template ) {

                $this->template_options = $template;

                $this->process_log = array(
                        'exported' => isset( $this->template_options[ 'count' ] ) ? $this->template_options[ 'count' ] : 0,
                        'total'    => 0,
                );

                $backup_dir = isset( $this->template_options[ 'backup_dir' ] ) ? $this->template_options[ 'backup_dir' ] : "";

                $this->open_export_file( $backup_dir . "/backup.csv" );

                unset( $backup_dir );

                $this->init_export_addons();

                $id = isset( $this->template_options[ 'id' ] ) && absint( $this->template_options[ 'id' ] ) > 0 ? absint( $this->template_options[ 'id' ] ) : 0;

                $this->process_items( array( $id ) );

                unset( $id );

                $this->remove_addons();

                $this->close_export_file();
        }

        private function get_item_data( $template = null ) {

                global $wpieExportTemplate;

                $this->template_options = $wpieExportTemplate     = $template;

                $this->process_log = [ 'exported' => 0, 'total' => 0 ];

                if ( $this->is_preview ) {
                        $this->process_log[ 'exported' ]                        = isset( $this->template_options[ "start" ] ) ? $this->template_options[ "start" ] : 0;
                        $this->template_options[ 'wpie_records_per_iteration' ] = isset( $this->template_options[ "length" ] ) ? $this->template_options[ "length" ] : 10;
                }

                $this->manage_rules();

                $this->init_export_addons();

                $export = $this->process_export();

                $this->remove_addons();

                if ( isset( $this->is_preview ) && $this->is_preview == true ) {

                        unset( $export );

                        return $this->preview_data;
                } else {
                        return $export;
                }
        }

        private function init_export_addons() {

                $addon_class = apply_filters( 'wpie_prepare_export_addons', array(), $this->export_type );

                if ( !empty( $addon_class ) ) {

                        foreach ( $addon_class as $key => $addon ) {

                                if ( class_exists( $addon ) ) {

                                        $this->addons[ $key ] = new $addon();

                                        if ( method_exists( $this->addons[ $key ], "init_process" ) ) {

                                                $this->addons[ $key ]->init_process( $this->template_options );
                                        }
                                }
                        }
                }
        }

        private function remove_addons() {
                unset( $this->addons );
        }

        private function init_export( $template = null ) {

                global $wpdb, $wpieExportTemplate;

                $this->fp = false;

                $this->export_id = isset( $template->id ) ? $template->id : 0;

                $this->template_options = $wpieExportTemplate     = isset( $template->options ) ? maybe_unserialize( $template->options ) : [];

                $wpdb->update( $wpdb->prefix . "wpie_template", array( 'process_lock' => 1 ), array( 'id' => absint( $this->export_id ) ) );

                $process_data = isset( $template->process_log ) ? maybe_unserialize( $template->process_log ) : array();

                $this->process_log = array(
                        'exported' => (isset( $process_data[ 'exported' ] ) && $process_data[ 'exported' ] != "") ? absint( $process_data[ 'exported' ] ) : 0,
                        'total'    => (isset( $process_data[ 'total' ] ) && $process_data[ 'total' ] != "") ? absint( $process_data[ 'total' ] ) : 0,
                );

                $filename = isset( $this->template_options[ 'fileName' ] ) ? $this->template_options[ 'fileName' ] : "";

                $filedir = isset( $this->template_options[ 'fileDir' ] ) ? $this->template_options[ 'fileDir' ] : "";

                $dir = WPIE_UPLOAD_EXPORT_DIR . '/' . $filedir . '/';

                $filepath = $dir . $filename;

                if ( !is_dir( $dir ) || !wp_is_writable( $dir ) ) {

                        wp_mkdir_p( $dir );

                        $stat = @stat( $dir );
                        if ( $stat ) {
                                $dir_perms = $stat[ 'mode' ] & 0007777;
                        } else {
                                $dir_perms = 0777;
                        }

                        @chmod( $dir, $dir_perms );
                }

                unset( $process_data, $filename, $filedir );

                $this->manage_rules();

                $this->open_export_file( $filepath );

                if ( $this->fp === false ) {
                        return new \WP_Error( "wpie_import_error", __( "File is not Writable", "wp-import-export-lite" ) );
                }

                $this->init_export_addons();

                $this->process_export();

                $this->remove_addons();

                $this->close_export_file();

                $final_data = array(
                        'last_update_date' => current_time( 'mysql' ),
                        'process_lock'     => 0,
                );

                $wpdb->update( $wpdb->prefix . "wpie_template", $final_data, array( 'id' => $this->export_id ) );

                unset( $final_data, $filepath );

                return $this->process_log;
        }

        private function open_export_file( $filepath = "" ) {

                $this->fp = @fopen( $filepath, 'a+' );
        }

        private function close_export_file() {

                fclose( $this->fp );
        }

        protected function process_data() {

                if ( !empty( $this->export_data ) ) {

                        if ( $this->is_preview ) {

                                $this->process_log[ 'exported' ]++;

                                $this->preview_data[] = array_values( $this->export_data );
                        } else {
                                $file_type = (isset( $this->template_options[ 'wpie_export_file_type' ] ) && trim( $this->template_options[ 'wpie_export_file_type' ] ) != "") ? wpie_sanitize_field( $this->template_options[ 'wpie_export_file_type' ] ) : "csv";

                                if ( empty( $this->csv_delim ) ) {
                                        if ( $file_type == "csv" ) {
                                                $this->csv_delim = (isset( $this->template_options[ 'wpie_csv_field_separator' ] ) && trim( $this->template_options[ 'wpie_csv_field_separator' ] ) != "") ? wpie_sanitize_field( $this->template_options[ 'wpie_csv_field_separator' ] ) : ",";
                                        } else {
                                                $this->csv_delim = ",";
                                        }
                                }

                                if ( $this->process_log[ 'exported' ] == 0 && !empty( $this->export_labels ) ) {
                                        $this->addCsvData( array_values( $this->export_labels ) );
                                        unset( $this->export_labels );
                                }
                                if ( $this->has_multiple_rows ) {
                                        foreach ( $this->export_data as $data ) {

                                                $this->addCsvData( array_values( $data ) );
                                        }
                                } else {
                                        $this->addCsvData( array_values( $this->export_data ) );
                                }

                                $this->process_log[ 'exported' ]++;

                                $final_data = array(
                                        'last_update_date' => current_time( 'mysql' ),
                                        'process_log'      => maybe_serialize( $this->process_log ),
                                );

                                if ( $this->process_log[ 'exported' ] >= $this->process_log[ 'total' ] ) {

                                        $final_data[ 'status' ] = "completed";

                                        $extra_copy_path = isset( $this->template_options[ 'extra_copy_path' ] ) && !empty( $this->template_options[ 'extra_copy_path' ] ) ? ltrim( trailingslashit( sanitize_text_field( $this->template_options[ 'extra_copy_path' ] ) ), '/\\' ) : "";

                                        $is_package = isset( $this->template_options[ 'is_package' ] ) ? intval( $this->template_options[ 'is_package' ] ) : 0;

                                        if ( $this->opration === "schedule_export" ) {
                                                $is_package = isset( $this->template_options[ 'is_migrate_package' ] ) ? intval( $this->template_options[ 'is_migrate_package' ] ) : 0;
                                        }

                                        if ( $is_package === 0 && strtolower( $file_type ) === "csv" && !empty( $extra_copy_path ) && is_dir( WPIE_SITE_UPLOAD_DIR . "/" . $extra_copy_path ) ) {

                                                $filename = isset( $this->template_options[ 'fileName' ] ) ? $this->template_options[ 'fileName' ] : "";

                                                $filedir = isset( $this->template_options[ 'fileDir' ] ) ? $this->template_options[ 'fileDir' ] : "";

                                                $filepath = WPIE_UPLOAD_EXPORT_DIR . '/' . $filedir . '/' . $filename;

                                                copy( $filepath, WPIE_SITE_UPLOAD_DIR . '/' . $extra_copy_path . $filename );

                                                unset( $filename, $filedir, $filepath );
                                        }

                                        do_action( 'wpie_export_task_complete', $this->export_id, $this->opration, $this->template_options );
                                }

                                global $wpdb;

                                $wpdb->update( $wpdb->prefix . "wpie_template", $final_data, array( 'id' => $this->export_id ) );

                                do_action( 'wpie_export_complete', $this->export_id, $this->opration, $this->template_options );

                                unset( $final_data );
                        }
                }

                $this->export_data = array();
        }

        private function addCsvData( $data ) {

                if ( empty( $data ) ) {
                        return;
                }

                $data = array_map( [ $this, "validate_escape_csv_data" ], $data );
                fputcsv( $this->fp, $data, $this->csv_delim );
        }

        /**
         * Escape a string to be used in a CSV context
         *
         * Malicious input can inject formulas into CSV files, opening up the possibility
         * for phishing attacks and disclosure of sensitive information.
         *
         * Additionally, Excel exposes the ability to launch arbitrary commands through
         * the DDE protocol.
         *
         * @see http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
         * @see https://hackerone.com/reports/72785
         *
         * @since 3.8.1
         * @param string $data CSV field to escape.
         * @return string
         */
        private function escape_data( $data = "" ) {

                if ( empty( $data ) ) {
                        return "";
                }

                $active_content_triggers = [ '=', '+', '-', '@' ];

                if ( in_array( mb_substr( $data, 0, 1 ), $active_content_triggers, true ) ) {
                        $data = "'" . $data;
                }
                return $data;
        }

        /**
         * Validate and escape data ready for the CSV file.
         *
         * @since 3.8.1
         * @param  string $data Data to format.
         * @return string
         */
        private function validate_escape_csv_data( $data = "" ) {

                if ( empty( $data ) || !is_scalar( $data ) ) {
                        return "";
                }

                if ( is_bool( $data ) ) {
                        $data = $data ? 1 : 0;
                }

                if ( function_exists( 'mb_convert_encoding' ) ) {
                        $encoding = \mb_detect_encoding( $data, 'UTF-8, ISO-8859-1', true );
                        $data     = ( in_array( $encoding, [ 'UTF-8', 'UTF8' ] ) ) ? $data : \utf8_encode( $data );
                }

                return $this->escape_data( $data );
        }

        protected function manage_rules() {

                $wpie_export_condition = isset( $this->template_options[ 'wpie_filter_rule' ] ) ? wpie_sanitize_field( stripslashes_deep( $this->template_options[ 'wpie_filter_rule' ] ) ) : "";

                if ( !empty( $wpie_export_condition ) ) {

                        $wpie_filter_rule = explode( "~`|`~", $wpie_export_condition );

                        if ( is_array( $wpie_filter_rule ) && !empty( $wpie_filter_rule ) ) {

                                foreach ( $wpie_filter_rule as $data ) {

                                        if ( empty( $data ) ) {
                                                continue;
                                        }

                                        $options = explode( "`|~`", $data );

                                        $filter = array();

                                        $rule = isset( $options[ 0 ] ) ? wpie_sanitize_field( wp_unslash( $options[ 0 ] ) ) : "";

                                        if ( $rule != "" ) {

                                                $filter_data = json_decode( $rule, true );

                                                if ( isset( $filter_data[ 'type' ] ) && !empty( $filter_data[ 'type' ] ) ) {
                                                        $filter              = $filter_data;
                                                        $filter[ 'element' ] = $filter_data[ 'type' ];
                                                }
                                                unset( $rule, $filter_data );
                                        } else {
                                                unset( $options, $filter, $rule );
                                                continue;
                                        }

                                        $filter[ 'condition' ] = isset( $options[ 1 ] ) ? wpie_sanitize_field( wp_unslash( $options[ 1 ] ) ) : "";

                                        $filter[ 'value' ] = isset( $options[ 2 ] ) ? esc_sql( wpie_sanitize_field( wp_unslash( $options[ 2 ] ) ) ) : "";

                                        $filter[ 'clause' ] = isset( $options[ 3 ] ) ? wpie_sanitize_field( wp_unslash( $options[ 3 ] ) ) : "";

                                        if ( !empty( $filter[ 'element' ] ) && !empty( $filter[ 'condition' ] ) ) {

                                                $this->parse_rule( $filter );
                                        }

                                        unset( $filter );
                                }
                        }

                        unset( $wpie_filter_rule );
                }

                unset( $wpie_export_condition );
        }

        protected function get_unique_str() {
                return uniqid() . "__" . rand( 1, 999 );
        }

}

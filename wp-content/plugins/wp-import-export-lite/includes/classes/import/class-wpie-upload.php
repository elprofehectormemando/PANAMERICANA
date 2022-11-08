<?php


namespace wpie\import\upload;

use wpie\import;
use wpie\import\FileFormat\Manager as FileExtract;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Upload {

        public function __construct() {
                
        }

        public function wpie_get_upload_section() {

                return apply_filters( "wpie_import_upload_sections", array() );
        }

        public function wpie_create_safe_dir_name( $str = "", $separator = 'dash', $lowercase = true ) {

                if ( $separator == 'dash' ) {
                        $search = '_';
                        $replace = '-';
                } else {
                        $search = '-';
                        $replace = '_';
                }

                $trans = array(
                        '&\#\d+?;' => '',
                        '&\S+?;' => '',
                        '\s+' => $replace,
                        '[^a-z0-9\-\._]' => '',
                        $search . '+' => $replace,
                        $search . '$' => $replace,
                        '^' . $search => $replace,
                        '\.+$' => ''
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

        protected function wpie_manage_import_file( $fileName = "", $fileDir = "", $wpie_import_id = 0 ) {

                $relative_path = $fileDir . "/original";

                $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $relative_path;

                $current_file = $filePath . "/" . $fileName;

                $file_data = [];

                $fileList = [];

                $active_file = "";

                if ( !is_file( $current_file ) ) {

                        unset( $filePath, $current_file, $file_data, $fileList, $active_file );

                        return new \WP_Error( 'wpie_import_error', __( 'Uploaded file is empty', 'wp-import-export-lite' ) );
                } elseif ( !preg_match( '%\W(xml|zip|csv|xls|xlsx|xml|ods|txt|json|gz|tar)$%i', trim( $fileName ) ) ) {
                        unset( $filePath, $current_file, $file_data, $fileList, $active_file );

                        return new \WP_Error( 'wpie_import_error', __( 'Uploaded file must be XML, CSV, ZIP, XLS, XLSX, ODS, TXT, JSON, GZ, TAR', 'wp-import-export-lite' ) );
                } elseif ( preg_match( '%\W(zip|tar|gz)$%i', trim( $fileName ) ) ) {

                        $extract_path = $relative_path . "/extract";
                        WP_Filesystem();
                        wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/config" );
                        wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $extract_path );

                        if ( is_readable( WPIE_IMPORT_CLASSES_DIR . '/file-format/manager.php' ) ) {
                                require_once(WPIE_IMPORT_CLASSES_DIR . '/file-format/manager.php');
                        }

                        $data_extract = new FileExtract();
                        $data = $data_extract->extract( $relative_path . "/" . $fileName, $extract_path );
                        unset( $data_extract );
                        if ( is_wp_error( $data ) ) {
                                return $data;
                        }

                        $file_list = $this->wpie_get_file_list( WPIE_UPLOAD_IMPORT_DIR . "/" . $extract_path, true, false );

                        if ( !empty( $file_list ) ) {

                                if ( isset( $file_list[ "config.json" ] ) ) {

                                        $configPath = WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/config/config.json";

                                        if ( is_readable( $filePath . "/extract/config.json" ) ) {

                                                copy( $filePath . "/extract/config.json", $configPath );

                                                unlink( $filePath . "/extract/config.json" );
                                        }
                                        unset( $file_list[ "config.json" ] );
                                }

                                if ( !empty( $file_list ) ) {

                                        foreach ( $file_list as $key => $value ) {

                                                $new_key = $this->wpie_create_safe_dir_name( $key );

                                                $new_file_dir = "";

                                                if ( $key == $value ) {
                                                        $new_file_dir = $fileDir . "/original/extract";
                                                } else {
                                                        $new_file_dir = $fileDir . "/original/extract/" . dirname( $key );
                                                }
                                                $_new_filename = preg_replace( "/[^a-z0-9\_\-\.]/i", '', $value );

                                                $_temp_path = WPIE_UPLOAD_IMPORT_DIR . "/" . $new_file_dir . "/";

                                                rename( $_temp_path . $value, $_temp_path . $_new_filename );

                                                $file_data[ $new_key ] = array(
                                                        'fileDir' => $new_file_dir,
                                                        'fileName' => $_new_filename,
                                                        'originalName' => $fileName,
                                                        'baseDir' => $fileDir
                                                );

                                                $fileList[] = array(
                                                        'fileKey' => $new_key,
                                                        'fileName' => $_new_filename
                                                );
                                                if ( $active_file == "" ) {
                                                        $active_file = $new_key;
                                                }
                                                unset( $new_file_dir, $new_key, $_new_filename, $_temp_path );
                                        }
                                }
                        }
                } else {

                        $file_data[ $fileDir ] = array(
                                'fileDir' => $fileDir . "/original",
                                'fileName' => $fileName,
                                'originalName' => $fileName,
                                'baseDir' => $fileDir
                        );

                        $fileList[] = array(
                                'fileKey' => $fileDir,
                                'fileName' => sanitize_file_name( $fileName )
                        );

                        $active_file = $fileDir;
                }

                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php');
                }

                $wpie_import = new \wpie\import\WPIE_Import();

                $activeFileData = isset( $file_data[ $active_file ] ) ? $file_data[ $active_file ] : [];
                
                $sheetData = $wpie_import->getSheetData($activeFileData);
                
                $sheetList =  isset( $sheetData[ 'sheetList' ] ) ? $sheetData[ 'sheetList' ] : [];
                
                $activeSheet =  isset( $sheetData[ 'activeSheet' ] ) ? $sheetData[ 'activeSheet' ] : '';
                

                if ( $wpie_import_id > 0 ) {

                        global $wpdb;

                        $new_values = array();

                        $template_data = $wpie_import->get_template_by_id( $wpie_import_id );

                        if ( $template_data ) {

                                $template_options = isset( $template_data->options ) ? maybe_unserialize( $template_data->options ) : array();

                                $template_options[ 'importFile' ] = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : array();

                                $template_options[ 'importFile' ] = array_merge( $file_data, $template_options[ 'importFile' ] );

                                $template_options[ 'activeFile' ] = $active_file;

                                $template_options[ 'sheetList' ] = $sheetList;

                                $template_options[ 'activeSheet' ] = $activeSheet;

                                $new_values[ 'options' ] = maybe_serialize( $template_options );

                                unset( $template_options );
                        } else {

                                $new_values[ 'options' ] = maybe_serialize( array( "importFile" => $file_data, "activeFile" => $active_file, "sheetList" => $sheetList, "activeSheet" => $activeSheet ) );
                        }

                        $wpdb->update( $wpdb->prefix . "wpie_template", $new_values, array( 'id' => $wpie_import_id ) );

                        unset( $new_values, $template_data );
                } else {
                        $wpie_import_id = $wpie_import->wpie_generate_template( array( "importFile" => $file_data, "activeFile" => $active_file, "sheetList" => $sheetList, "activeSheet" => $activeSheet ), 'import-draft', 'processing' );
                }

                unset( $filePath, $file_data, $active_file, $wpie_import );

                return array( 'file_list' => $fileList, 'wpie_import_id' => $wpie_import_id, "file_name" => sanitize_file_name( $fileName ), "file_size" => filesize( $current_file ), "sheetList" => $sheetList, "activeSheet" => $activeSheet );
        }

        public function wpie_get_file_list( $targetDir = "", $remove_extra_files = true, $time_string = false ) {

                $result = array();

                if ( !isset( $this->wpie_date_format ) || empty( $this->wpie_date_format ) ) {
                        $this->wpie_date_format = get_option( 'date_format' );
                        $this->wpie_time_format = get_option( 'time_format' );
                }

                $cdir = scandir( $targetDir );

                if ( is_array( $cdir ) ) {

                        foreach ( $cdir as $key => $value ) {
                                if ( !in_array( $value, array( ".", ".." ) ) ) {
                                        if ( is_dir( $targetDir . '/' . $value ) ) {
                                                $new_data = $this->wpie_get_file_list( $targetDir . '/' . $value, $remove_extra_files, $time_string );
                                                if ( is_array( $new_data ) ) {
                                                        foreach ( $new_data as $new_key => $new_info ) {
                                                                $result[ $value . '/' . $new_key ] = $new_info;
                                                        }
                                                } else {
                                                        $result[ $value . '/' . $new_data ] = $new_data;
                                                }
                                                unset( $new_data );
                                        } else {
                                                if ( preg_match( '%\W(csv|xml|json|txt|xls|xlsx|ods)$%i', basename( $value ) ) ) {

                                                        if ( $time_string ) {
                                                                $value_data = $value . '&nbsp;&nbsp;&nbsp;' . date( $this->wpie_date_format . ' ' . $this->wpie_time_format, ( filectime( $targetDir . '/' . $value ) ) );
                                                        } else {
                                                                $value_data = $value;
                                                        }
                                                        $result[ $value ] = $value_data;

                                                        unset( $value_data );
                                                } elseif ( $remove_extra_files ) {
                                                        unlink( $targetDir . '/' . $value );
                                                }
                                        }
                                }
                        }
                }

                unset( $cdir );

                return $result;
        }

        private function unzip_file( $file, $to ) {

                global $wp_version;

                if ( version_compare( $wp_version, '4.6', '<' ) ) {
                        return wpie_unzip_file( $file, $to );
                } else {
                        return unzip_file( $file, $to );
                }
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

<?php


namespace wpie\import\upload\validate;

use wpie\import\chunk\csv;
use wpie\lib\xml\array2xml;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Writer;
use WP_Error;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Upload_Validate {

        private $wpie_fileName = "wpie-import-data-";

        public function __construct() {
                
        }

        public function wpie_parse_upload_data( $template_data = null, $wpie_csv_delimiter = ",", $is_first_row_title = 1, $activeFile = false, $wpie_import_id = false, $activeSheet = false, $fileFormat = false ) {

                if ( empty( $template_data ) ) {
                        return false;
                }

                global $wpdb;

                if ( is_array( $template_data ) ) {
                        $template_options = $template_data;
                } else {
                        $template_options = isset( $template_data->options ) ? maybe_unserialize( $template_data->options ) : array();
                }

                $importFile = isset( $template_options[ 'importFile' ] ) ? $template_options[ 'importFile' ] : array();

                if ( $activeFile === false ) {
                        $activeFile = isset( $_GET[ 'activeFile' ] ) ? wpie_sanitize_field( $_GET[ 'activeFile' ] ) : "";
                }

                if ( $activeSheet === false ) {
                        $activeSheet = isset( $_GET[ 'activeSheet' ] ) ? wpie_sanitize_field( $_GET[ 'activeSheet' ] ) : "";
                }

                if ( $wpie_import_id === false ) {
                        $wpie_import_id = isset( $_GET[ "wpie_import_id" ] ) ? intval( wpie_sanitize_field( $_GET[ "wpie_import_id" ] ) ) : 0;
                }

                $is_new_req = isset( $_GET[ "is_new_req" ] ) ? intval( wpie_sanitize_field( $_GET[ "is_new_req" ] ) ) : 0;

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : array();

                $file_path = isset( $fileData[ 'fileDir' ] ) ? wpie_sanitize_field( $fileData[ 'fileDir' ] ) : "";

                $file_name = isset( $fileData[ 'fileName' ] ) ? wpie_sanitize_field( $fileData[ 'fileName' ] ) : "";

                $baseDir = isset( $fileData[ 'baseDir' ] ) ? wpie_sanitize_field( $fileData[ 'baseDir' ] ) : "";

                $template_options[ 'activeFile' ] = $activeFile;

                $template_options[ 'activeSheet' ] = $activeSheet;

                if ( is_dir( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" ) ) {
                        $this->wpie_remove_old_files( WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" );
                }

                $wpdb->update( $wpdb->prefix . "wpie_template", array( "options" => maybe_serialize( $template_options ) ), array( 'id' => $wpie_import_id ) );

                $file = WPIE_UPLOAD_IMPORT_DIR . "/" . $file_path . "/" . $file_name;

                if ( !file_exists( $file ) ) {

                        unset( $template_options, $importFile, $activeFile, $file, $wpie_import_id, $fileData, $file_path, $file_name, $baseDir );

                        return new \WP_Error( 'wpie_import_error', __( 'File not found', 'wp-import-export-lite' ) );
                } elseif ( filesize( $file ) === 0 ) {

                        unset( $template_options, $importFile, $activeFile, $file, $wpie_import_id, $fileData, $file_path, $file_name, $baseDir );

                        return new \WP_Error( 'wpie_import_error', __( 'File is Empty. Please Choose another file', 'wp-import-export-lite' ) );
                }

                if ( preg_match( '%\W(txt)$%i', trim( $file_name ) ) ) {

                        if ( $fileFormat === false ) {
                                $fileFormat = isset( $_GET[ 'activeFormat' ] ) && !empty( $_GET[ 'activeFormat' ] ) ? wpie_sanitize_field( $_GET[ 'activeFormat' ] ) : "csv";
                        }

                        $newFileName = pathinfo( $file_name, PATHINFO_FILENAME ) . "." . $fileFormat;

                        $newFileName = \wp_unique_filename( WPIE_UPLOAD_IMPORT_DIR . "/" . $file_path, $newFileName );

                        $dest = WPIE_UPLOAD_IMPORT_DIR . "/" . $file_path . "/" . $newFileName;

                        if ( copy( $file, $dest ) === false ) {
                                return new \WP_Error( 'wpie_import_error', __( 'Fail to copy file', 'wp-import-export-lite' ) );
                        }

                        $file_name = $newFileName;

                        $file = $dest;
                }

                if ( preg_match( '%\W(xls|xlsx|ods)$%i', trim( $file_name ) ) ) {

                        unset( $template_options, $importFile, $activeFile, $file, $wpie_import_id, $fileData );

                        return $this->wpie_convert_excel_2_csv( $file_path, $file_name, $baseDir, $is_first_row_title, $activeSheet );
                } elseif ( preg_match( '%\W(csv)$%i', trim( $file_name ) ) ) {

                        unset( $template_options, $importFile, $activeFile, $file, $wpie_import_id, $fileData );

                        return $this->wpie_convert_csv_2_xml( $file_path, $file_name, $baseDir, $is_first_row_title, $wpie_csv_delimiter, $is_new_req );
                } elseif ( preg_match( '%\W(txt|json)$%i', trim( $file_name ) ) ) {

                        unset( $template_options, $importFile, $activeFile, $file, $wpie_import_id, $fileData );

                        return $this->wpie_convert_json_2_xml( $file_path, $file_name, $baseDir );
                } elseif ( preg_match( '%\W(xml)$%i', trim( $file_name ) ) ) {

                        $xmlFile = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/" . $this->wpie_fileName . "1.xml";

                        if ( !$this->validateFileData( $file, 'xml' ) ) {

                                $this->createEmptyFile( $xmlFile );
                        } else {
                                copy( $file, $xmlFile );
                        }

                        unset( $template_options, $importFile, $activeFile, $file, $wpie_import_id, $fileData, $file_path, $file_name, $baseDir );

                        return true;
                }

                unset( $template_options, $importFile, $activeFile, $file, $wpie_import_id, $fileData, $file_path, $file_name, $baseDir );

                return new \WP_Error( 'wpie_import_error', __( 'Invalid File to parse. Please Choose other FIle', 'wp-import-export-lite' ) );
        }

        private function wpie_convert_excel_2_csv( $fileDir = "", $file_name = "", $baseDir = "", $is_first_row_title = 1, $activeSheet = "" ) {

                $file = WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

                if ( !file_exists( $file ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File not found', 'wp-import-export-lite' ) );
                }

                $newFileName = wp_unique_filename( WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir, preg_replace( '%\W(xls|xlsx|ods)$%i', ".csv", $file_name ) );

                wpie_load_vendor_autoloader();

                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $file );

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv( $spreadsheet );

                if ( (!empty( $activeSheet )) && $spreadsheet->getSheetByName( $activeSheet ) ) {

                        $spreadsheet->setActiveSheetIndexByName( $activeSheet );

                        $sheetIndex = $spreadsheet->getActiveSheetIndex();

                        $writer->setSheetIndex( $sheetIndex );
                }

                $writer->save( WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . preg_replace( '%\W(xls|xlsx|ods)$%i', ".csv", $newFileName ) );

                $spreadsheet->disconnectWorksheets();

                $return_data = $this->wpie_convert_csv_2_xml( $fileDir, $newFileName, $baseDir, $is_first_row_title );

                unset( $file, $newFileName, $spreadsheet, $writer );

                return $return_data;
        }

        private function wpie_convert_csv_2_xml( $fileDir = "", $file_name = "", $baseDir = "", $is_first_row_title = 1, $wpie_csv_delimiter = ",", $is_new_req = 0 ) {

                if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-csv-chunk.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-csv-chunk.php');
                }

                $csv_chunk = new \wpie\import\chunk\csv\WPIE_CSV_Chunk();

                $return_data = $csv_chunk->process_csv( $fileDir, $file_name, $baseDir, $wpie_csv_delimiter, $this->wpie_fileName, $is_new_req, $is_first_row_title );

                unset( $csv_chunk );

                return $return_data;
        }

        private function wpie_convert_json_2_xml( $fileDir = "", $file_name = "", $baseDir = "" ) {


                $xmlFileName = $this->wpie_fileName . '1.xml';

                $xmlFilePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/parse/";

                if ( file_exists( WPIE_LIBRARIES_DIR . '/xml/class-wpie-array2xml.php' ) ) {
                        require_once(WPIE_LIBRARIES_DIR . '/xml/class-wpie-array2xml.php');
                }
                $xmlFile = $xmlFilePath . "/" . $xmlFileName;

                $file = WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

                if ( !$this->validateFileData( $file, 'json' ) ) {

                        $this->createEmptyFile( $xmlFile );

                        return $xmlFile;
                }

                $json = file_get_contents( $file );

                $file_data = json_decode( $json, true );

                if ( json_last_error() !== JSON_ERROR_NONE ) {

                        unset( $file, $json, $file_data );

                        $this->createEmptyFile( $xmlFile );

                        return $xmlFile;
                }

                $converter = new \wpie\lib\xml\array2xml\ArrayToXml();

                $converter->create_root( "wpiedata" );

                $converter->convertElement( $converter->root, $file_data, 0 );

                $converter->saveFile( $xmlFile );

                unset( $file, $json, $file_data, $converter );

                return $xmlFile;
        }

        private function wpie_remove_old_files( $targetDir = "" ) {

                $cdir = scandir( $targetDir );

                if ( is_array( $cdir ) && !empty( $cdir ) ) {
                        foreach ( $cdir as $key => $value ) {
                                if ( !in_array( $value, array( ".", ".." ) ) ) {
                                        if ( is_dir( $targetDir . '/' . $value ) ) {
                                                $this->wpie_remove_old_files( $targetDir . '/' . $value );
                                        } else {
                                                unlink( $targetDir . '/' . $value );
                                        }
                                }
                        }
                }
                unset( $cdir );
        }

        private function validateFileData( $file, $format = "xml" ) {

                if ( !file_exists( $file ) || filesize( $file ) === 0 ) {
                        return false;
                }

                $fp = fopen( $file, 'r' );

                if ( $fp === false ) {
                        return false;
                }

                $data = fread( $fp, 20 );

                fclose( $fp );

                $data = preg_replace( '/[^[:print:]]/', '', $data );

                $fileFirstChar = mb_substr( trim( $data ), 0, 1 );

                if ( $format === "json" ) {
                        if ( $fileFirstChar == '[' || $fileFirstChar == '{' ) {
                                return true;
                        }
                } else {
                        if ( $fileFirstChar == '<' || \mb_strpos( $data, "<?xml" ) !== false ) {
                                return true;
                        }
                }

                unset( $data, $fp );

                return false;
        }

        private function createEmptyFile( $file ) {

                $fp = fopen( $file, "w" );

                if ( $fp !== false ) {

                        if ( filesize( $file ) > 0 ) {
                                ftruncate( $fp, 0 );
                        }

                        fclose( $fp );

                        return true;
                }
                return false;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

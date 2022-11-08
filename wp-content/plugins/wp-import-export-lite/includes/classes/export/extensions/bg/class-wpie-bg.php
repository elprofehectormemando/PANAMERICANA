<?php


namespace wpie\export\bg;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php');
}

class WPIE_BG extends \wpie\export\WPIE_Export {

        public function __construct() {
                
        }

        public function init() {

                add_action( 'init', array( $this, 'init_process' ), 100 );

                add_filter( 'wpie_add_export_extension_process_btn', array( $this, 'add_bg_export_btn' ), 10, 1 );
        }

        public function add_bg_export_btn( $files = array() ) {

                $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/bg/wpie_bg_btn.php';

                if ( !in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }

        public function init_process() {

                if ( !$this->isValidRequest() ) {

                        $wpie_bg_and_cron_processing = \maybe_unserialize( \get_option( "wpie_bg_and_cron_processing", "" ) );

                        $cronMethod = isset( $wpie_bg_and_cron_processing[ 'method' ] ) ? $wpie_bg_and_cron_processing[ 'method' ] : "";

                        if ( $cronMethod !== "external" || !isset( $_GET[ 'wpie_cron_token' ] ) ) {
                                return true;
                        }

                        $respons = [
                                "status"  => "error",
                                "plugin"  => "WP Import Export",
                                "message" => "Invalid Request",
                        ];

                        echo json_encode( $respons );

                        die();
                }

                $this->unlockTemplate();

                $this->setBgExport();
        }

        public function setBgExport() {

                global $wpdb;

                $wpieProcess = \maybe_unserialize( \get_option( "wpie_bg_process", [] ) );

                if ( !is_array( $wpieProcess ) || empty( $wpieProcess ) ) {
                        $wpieProcess = [];
                }
                $wpieProcess[ 'processing' ] = isset( $wpieProcess[ 'processing' ] ) ? $wpieProcess[ 'processing' ] : [];

                $wpieProcess[ 'processing' ][ 'export' ] = !empty( $wpieProcess[ 'processing' ] ) && isset( $wpieProcess[ 'processing' ][ 'export' ] ) ? $wpieProcess[ 'processing' ][ 'export' ] : [];

                $template = $this->getTemplate( $wpieProcess[ 'processing' ][ 'export' ] );

                if ( !isset( $template->id ) ) {
                        return;
                }
                $templateId = $template->id;

                $process_log = isset( $template->process_log ) && !empty( $template->process_log ) ? maybe_unserialize( $template->process_log ) : [];

                $wpieProcess[ 'processing' ][ 'export' ][] = $templateId;

                \update_option( "wpie_bg_process", \maybe_serialize( $wpieProcess ) );

                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpie_template SET `process_lock` = 1 WHERE `id` = %d ", $templateId ) );

                $export_type = isset( $template->opration_type ) ? $template->opration_type : "post";

                $opration = isset( $template->opration ) ? $template->opration : "export";

                $process_log = $this->init_export( $export_type, $opration, $template );

                $wpieProcess[ 'processing' ][ 'export' ] = array_diff( $wpieProcess[ 'processing' ][ 'export' ], [ $templateId ] );

                \update_option( "wpie_bg_process", \maybe_serialize( $wpieProcess ) );

                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpie_template SET `process_lock` = 0 WHERE `id` = %d ", $templateId ) );

                $this->setCronMsg( $templateId );
        }

        public function getTemplate( $excludes = [] ) {

                global $wpdb;

                $idQuery = "";

                if ( !empty( $excludes ) ) {
                        $idQuery = " AND `id` NOT IN (" . implode( ",", array_map( "absint", array_unique( $excludes ) ) ) . ") ";
                }

                $template = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "wpie_template where `opration` in ('export','schedule_export') and `status` LIKE '%background%' and `process_lock` = 0 " . $idQuery . " ORDER BY `id` ASC limit 1" );

                if ( isset( $template->id ) && absint( $template->id ) > 0 ) {

                        return $template;
                }
                unset( $template );

                return false;
        }

        private function unlockTemplate() {

                global $wpdb;

                $wpieProcess = \maybe_unserialize( \get_option( "wpie_bg_process", [] ) );

                if ( !is_array( $wpieProcess ) || empty( $wpieProcess ) ) {
                        $wpieProcess = [];
                }

                $wpieProcess[ 'processing' ] = isset( $wpieProcess[ 'processing' ] ) ? $wpieProcess[ 'processing' ] : [];

                $processingIds = !empty( $wpieProcess[ 'processing' ] ) && isset( $wpieProcess[ 'processing' ][ 'export' ] ) ? $wpieProcess[ 'processing' ][ 'export' ] : [];

                if ( empty( $processingIds ) ) {
                        $this->updateTemplateLock();
                        return;
                }

                $idList = array_map( "absint", $processingIds );

                $templates = $wpdb->get_results( "SELECT `id`,`process_log`,`process_lock`,`last_update_date` FROM " . $wpdb->prefix . "wpie_template where `id` IN (" . implode( ",", $idList ) . ") ORDER BY `id` ASC" );

                $updateIds = [];

                foreach ( $templates as $template ) {

                        $id = isset( $template->id ) ? $template->id : 0;

                        if ( intval( $template->process_lock ) === 0 ) {
                                $updateIds[] = $id;
                                continue;
                        }

                        $currentTime = strtotime( current_time( "mysql" ) );

                        $allowTime = 60 * 5;

                        $last_update_date = isset( $template->last_update_date ) ? $template->last_update_date : "";

                        if ( $currentTime >= (strtotime( $last_update_date ) + $allowTime) ) {
                                $updateIds[] = $id;
                                continue;
                        }
                }

                $this->updateTemplateLock( $updateIds );

                $wpieProcess[ 'processing' ][ 'export' ] = array_diff( $processingIds, $updateIds );

                \update_option( "wpie_bg_process", maybe_serialize( $wpieProcess ) );
        }

        private function updateTemplateLock( $ids = [] ) {

                $ids = !empty( $ids ) ? array_map( "absint", $ids ) : [];

                $ids = !empty( $ids ) ? implode( ",", array_unique( $ids ) ) : "";

                $idQuery = "";

                if ( !empty( $ids ) ) {
                        $idQuery = " AND `id` IN (" . $ids . ")";
                }

                global $wpdb;

                $wpdb->query( "UPDATE {$wpdb->prefix}wpie_template SET `process_lock` = 0 WHERE `process_lock` = 1 and `opration` in ('export','schedule_export') " . $idQuery );
        }

        private function isValidRequest() {

                $wpie_bg_and_cron_processing = \maybe_unserialize( \get_option( "wpie_bg_and_cron_processing", "" ) );

                $cronMethod = isset( $wpie_bg_and_cron_processing[ 'method' ] ) ? $wpie_bg_and_cron_processing[ 'method' ] : "";

                if ( $cronMethod !== "external" ) {
                        return true;
                }

                $token = isset( $_GET[ 'wpie_cron_token' ] ) ? \sanitize_textarea_field( $_GET[ 'wpie_cron_token' ] ) : "";

                $siteToken = isset( $wpie_bg_and_cron_processing[ 'token' ] ) ? $wpie_bg_and_cron_processing[ 'token' ] : "";

                if ( ( string ) $siteToken !== ( string ) $token ) {
                        return false;
                }

                return true;
        }

        private function setCronMsg( $templateId ) {

                $wpie_bg_and_cron_processing = \maybe_unserialize( \get_option( "wpie_bg_and_cron_processing", "" ) );

                $cronMethod = isset( $wpie_bg_and_cron_processing[ 'method' ] ) ? $wpie_bg_and_cron_processing[ 'method' ] : "";

                if ( $cronMethod !== "external" ) {
                        return true;
                }

                $respons = [
                        "status"  => "success",
                        "plugin"  => "WP Import Export",
                        "message" => "WP Import Export : Export #" . $templateId . " Processing"
                ];

                echo json_encode( $respons );

                die();
        }

}

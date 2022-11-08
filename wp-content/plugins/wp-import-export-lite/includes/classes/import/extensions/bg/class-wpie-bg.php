<?php


namespace wpie\import\bg;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php');
}

class WPIE_BG_Import extends \wpie\import\WPIE_Import {

        private $process_lock = false;

        public function __construct() {

                add_action( 'init', array( $this, 'init_process' ), 100 );

                add_filter( 'wpie_add_import_extension_process_btn_files', array( $this, 'wpie_add_bg_process_btn' ), 10, 1 );
        }

        public function wpie_add_bg_process_btn( $files = array() ) {

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/bg/wpie_bg_btn.php';

                if ( !in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }


        public function wpie_bg_import_init() {
                $id = $this->get_bg_template_id();

                if ( $id && absint( $id ) > 0 ) {

                        parent::wpie_import_process_data( $id );
                }
                unset( $id );
        }

        public function get_bg_template_id() {

                global $wpdb;

                $id = $wpdb->get_var( "SELECT `id` FROM " . $wpdb->prefix . "wpie_template where `opration` in ('import','schedule_import') and status LIKE '%background%' and process_lock = 0 ORDER BY `id` ASC limit 0,1" );

                return $id;
        }

        public function init_process() {

                if ( !$this->isValidRequest() ) {

                        $wpie_bg_and_cron_processing = \maybe_unserialize( \get_option( "wpie_bg_and_cron_processing" ) );

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

                $this->setBgImport();
        }

        public function setBgImport() {

                global $wpdb;

                $wpieProcess = \maybe_unserialize( \get_option( "wpie_bg_process" ) );

                $wpieProcess[ 'processing' ] = isset( $wpieProcess[ 'processing' ] ) ? $wpieProcess[ 'processing' ] : [];

                $wpieProcess[ 'processing' ][ 'import' ] = !empty( $wpieProcess[ 'processing' ] ) && isset( $wpieProcess[ 'processing' ][ 'import' ] ) ? $wpieProcess[ 'processing' ][ 'import' ] : [];

                $template = $this->getTemplate( $wpieProcess[ 'processing' ][ 'import' ] );

                if ( !isset( $template->id ) ) {
                        $this->setCronMsg();
                        return;
                }
                $templateId = $template->id;

                $wpieProcess[ 'processing' ][ 'import' ][] = $templateId;

                \update_option( "wpie_bg_process", \maybe_serialize( $wpieProcess ) );

                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpie_template SET `process_lock` = 1 WHERE `id` = %d ", $templateId ) );

                parent::wpie_import_process_data( $templateId );

                $wpieProcess[ 'processing' ][ 'import' ] = array_diff( $wpieProcess[ 'processing' ][ 'import' ], [ $templateId ] );

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

                $template = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "wpie_template where `opration` in ('import','schedule_import') and `status` LIKE '%background%' and `process_lock` = 0 " . $idQuery . " ORDER BY `id` ASC limit 1" );

                if ( isset( $template->id ) && absint( $template->id ) > 0 ) {

                        return $template;
                }
                unset( $template );

                return false;
        }

        private function unlockTemplate() {

                global $wpdb;

                $wpieProcess = \maybe_unserialize( \get_option( "wpie_bg_process" ) );

                if ( !$wpieProcess || empty( $wpieProcess ) ) {
                        $wpieProcess = [];
                }

                $wpieProcess[ 'processing' ] = isset( $wpieProcess[ 'processing' ] ) ? $wpieProcess[ 'processing' ] : [];

                $processingIds = !empty( $wpieProcess[ 'processing' ] ) && isset( $wpieProcess[ 'processing' ][ 'import' ] ) ? $wpieProcess[ 'processing' ][ 'import' ] : [];

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

                $wpieProcess[ 'processing' ][ 'import' ] = array_diff( $processingIds, $updateIds );

                \update_option( "wpie_bg_process", \maybe_serialize( $wpieProcess ) );
        }

        private function updateTemplateLock( $ids = [] ) {

                $ids = !empty( $ids ) ? array_map( "absint", $ids ) : [];

                $ids = !empty( $ids ) ? implode( ",", array_unique( $ids ) ) : "";

                $idQuery = "";

                if ( !empty( $ids ) ) {
                        $idQuery = " AND `id` IN (" . $ids . ")";
                }

                global $wpdb;

                $wpdb->query( "UPDATE {$wpdb->prefix}wpie_template SET `process_lock` = 0 WHERE `process_lock` = 1 and `opration` in ('import','schedule_import') " . $idQuery );
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

        private function setCronMsg( $templateId = 0 ) {

                $wpie_bg_and_cron_processing = \maybe_unserialize( \get_option( "wpie_bg_and_cron_processing" ) );

                $cronMethod = isset( $wpie_bg_and_cron_processing[ 'method' ] ) ? $wpie_bg_and_cron_processing[ 'method' ] : "";

                if ( $cronMethod !== "external" ) {
                        return true;
                }

                if ( absint( $templateId ) > 0 ) {

                        $respons = [
                                "status"  => "success",
                                "plugin"  => "WP Import Export",
                                "message" => "WP Import Export : Import #" . $templateId . " Processing"
                        ];
                } else {
                        $respons = [
                                "status"  => "success",
                                "plugin"  => "WP Import Export",
                                "message" => "WP Import Export : No More pending schedules"
                        ];
                }

                echo json_encode( $respons );

                die();
        }

}

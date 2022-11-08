<?php

namespace wpie\core;

use wpie\Security;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-security.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-security.php');
}

class WPIE_General {

        private static $wpie_page = array( 'wpie-new-export', 'wpie-new-import', 'wpie-extensions', 'wpie-settings', 'wpie-manage-import', 'wpie-manage-export' );

        public function __construct() {

                if ( is_admin() ) {

                        add_action( 'admin_menu', array( __CLASS__, 'wpie_set_menu' ) );

                        add_action( 'init', array( __CLASS__, 'wpie_db_check' ), 1 );

                        add_action( 'admin_head', array( __CLASS__, 'wpie_hide_all_notice_to_admin_side' ), 10000 );

                        add_filter( 'admin_footer_text', array( __CLASS__, 'wpie_replace_footer_admin' ) );

                        add_filter( 'update_footer', array( __CLASS__, 'wpie_replace_footer_version' ), '1234' );

                        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'wpie_set_admin_css' ), 10 );

                        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'wpie_set_admin_js' ), 10 );

                        add_action( 'init', array( $this, 'wpie_process_file_download' ), 10 );

                        add_action( 'admin_notices', array( __CLASS__, 'wpie_admin_notices' ), 10099 );

                        add_filter( 'mod_rewrite_rules', array( __CLASS__, 'mod_rewrite_rules' ) );

                        add_action( 'admin_init', array( __CLASS__, 'update_file_security' ) );

                        add_filter( 'robots_txt', array( __CLASS__, 'update_robots_txt' ), 10, 2 );

                        add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );

                        add_action( 'shutdown', array( __CLASS__, 'flush_rewrite_rules' ) );

                        add_filter( 'plugin_row_meta', [ __CLASS__, 'plugin_row_meta' ], 10, 2 );

                        add_filter( 'plugin_action_links_' . plugin_basename( WPIE_PLUGIN_FILE ), [ __CLASS__, 'plugin_action_links' ] );

                        register_deactivation_hook( WPIE_PLUGIN_FILE, [ __CLASS__, 'deactivation' ] );
                }

                add_action( 'plugins_loaded', array( __CLASS__, 'wpie_load_textdomain' ) );

                add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );

                add_filter( 'woocommerce_order_number', [ __CLASS__, 'wpie_woocommerce_order_number' ], 9999 );
        }

        /**
         * Deactivate Plugin Action
         */
        public static function deactivation() {


                //remove schedule import / exprot
                wp_clear_scheduled_hook( "wpie_cron_schedule_import" );
                wp_clear_scheduled_hook( "wpie_cron_schedule_export" );

                global $wpdb;

                $wpdb->query( "DELETE FROM " . $wpdb->prefix . "wpie_template WHERE `opration` IN ('schedule_export_template','schedule_import_template') " );

                $wpdb->query( "UPDATE " . $wpdb->prefix . "wpie_template SET `status` = 'paused' WHERE status LIKE '%background%'" );
        }

        /**
         * Flush the rewrite rules once for upload folder security.
         */
        public static function flush_rewrite_rules() {

                if ( self::is_apache() && self::is_htaccess_writable() && get_option( 'wpie_flush_rewrite_rules', false ) === false ) {

                        flush_rewrite_rules();

                        update_option( 'wpie_flush_rewrite_rules', 1 );
                }
        }

        /**
         * Uninstall tables when MU blog is deleted.
         *
         * @param  array $tables List of tables that will be deleted by WP.
         * @return array
         */
        public static function wpmu_drop_tables( $tables = array() ) {

                global $wpdb;

                $tables[] = $wpdb->prefix . 'wpie_template';

                return $tables;
        }

        /**
         * plugin screen links
         * 
         * Add extra links as row meta on the plugin screen.
         *
         * @since  1.4.1
         * @access public
         * 
         * @param  mixed $links Plugin Row Meta.
         * @param  mixed $file  Plugin Base file.
         * 
         * @return array
         */
        public static function plugin_row_meta( $links, $file ) {

                if ( plugin_basename( WPIE_PLUGIN_FILE ) !== $file ) {
                        return $links;
                }

                $more = [
                        '<a href="' . esc_url( WPIE_DOC_URL ) . '">' . esc_html__( 'Documentation', 'wp-import-export-lite' ) . '</a>',
                        '<a href="' . esc_url( WPIE_SUPPORT_URL ) . '">' . esc_html__( 'Support', 'wp-import-export-lite' ) . '</a>',
                ];

                return array_merge( $links, $more );
        }

        /**
         * plugin action links
         * 
         * Show action links on the plugin screen.
         *
         * @since  1.4.1
         * @access public
         * 
         * @param  mixed $links Plugin Action links.
         * 
         * @return array
         */
        public static function plugin_action_links( $links = array() ) {

                $exportUrl = add_query_arg( [ 'page' => "wpie-new-export" ], admin_url( "admin.php" ) );
                $importUrl = add_query_arg( [ 'page' => "wpie-new-import" ], admin_url( "admin.php" ) );

                $plugin_links = [
                        '<a href="' . esc_url( $importUrl ) . '">' . esc_html__( 'Import', 'wp-import-export-lite' ) . '</a>',
                        '<a href="' . esc_url( $exportUrl ) . '">' . esc_html__( 'Export', 'wp-import-export-lite' ) . '</a>',
                ];

                return array_merge( $plugin_links, $links );
        }

        public function wpie_process_file_download() {

                if ( isset( $_POST[ 'wpie_download_export_id' ] ) && intval( $_POST[ 'wpie_download_export_id' ] ) != 0 ) {

                        Security::verify_request( 'wpie_new_export' );

                        $current_data = $this->get_template_data_by_id( intval( wpie_sanitize_field( $_POST[ 'wpie_download_export_id' ] ) ) );

                        $options = isset( $current_data->options ) ? maybe_unserialize( $current_data->options ) : array();

                        $filename = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                        $filedir = isset( $options[ 'fileDir' ] ) ? $options[ 'fileDir' ] : "";

                        $filePath = WPIE_UPLOAD_EXPORT_DIR . '/' . $filedir . '/' . $filename;

                        unset( $current_data, $options, $filename, $filedir );

                        $this->wpie_download_file( $filePath );
                } elseif ( isset( $_POST[ 'wpie_download_import_id' ] ) && intval( $_POST[ 'wpie_download_import_id' ] ) != 0 ) {

                        Security::verify_request( 'wpie_manage_import' );

                        $current_data = $this->get_template_data_by_id( intval( wpie_sanitize_field( $_POST[ 'wpie_download_import_id' ] ) ) );

                        $options = isset( $current_data->options ) ? maybe_unserialize( $current_data->options ) : array();

                        $activeFile = isset( $options[ 'activeFile' ] ) ? $options[ 'activeFile' ] : "";

                        $importFile = isset( $options[ 'importFile' ] ) ? $options[ 'importFile' ] : array();

                        $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                        $fileDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                        $fileName = $fileData[ 'originalName' ] ? $fileData[ 'originalName' ] : "";

                        $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/original/" . $fileName;

                        unset( $current_data, $options, $activeFile, $importFile, $fileData, $fileDir, $fileName );

                        $this->wpie_download_file( $filePath );
                } elseif ( isset( $_POST[ 'wpie_download_import_log_id' ] ) && intval( $_POST[ 'wpie_download_import_log_id' ] ) != 0 ) {

                        Security::verify_request( 'wpie_manage_import' );

                        $current_data = $this->get_template_data_by_id( intval( wpie_sanitize_field( $_POST[ 'wpie_download_import_log_id' ] ) ) );

                        $options = isset( $current_data->options ) ? maybe_unserialize( $current_data->options ) : array();

                        $activeFile = isset( $options[ 'activeFile' ] ) ? $options[ 'activeFile' ] : "";

                        $importFile = isset( $options[ 'importFile' ] ) ? $options[ 'importFile' ] : array();

                        $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                        $baseDir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                        $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $baseDir . "/log/import_log.txt";

                        unset( $current_data, $options, $activeFile, $importFile, $fileData, $baseDir );

                        $this->wpie_download_file( $filePath );
                } elseif ( isset( $_POST[ 'wpie_template_list' ] ) && !empty( $_POST[ 'wpie_template_list' ] ) ) {

                        Security::verify_request( 'wpie_settings' );

                        $templates = wpie_sanitize_field( $_POST[ 'wpie_template_list' ] );

                        if ( is_array( $templates ) && !empty( $templates ) ) {

                                $ids = implode( ',', array_map( 'absint', $templates ) );

                                global $wpdb;

                                $results = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wpie_template where `id` IN(" . $ids . ")" );

                                $results = maybe_serialize( $results );

                                $filePath = WPIE_UPLOAD_TEMP_DIR . '/' . time() . '_templates.txt';

                                if ( ( $handle = @fopen( $filePath, "w" )) !== false ) {
                                        fwrite( $handle, $results );

                                        fclose( $handle );
                                }

                                unset( $ids, $results );

                                $this->wpie_download_file( $filePath );
                        }

                        unset( $templates );
                } elseif ( isset( $_POST[ 'wpie_download_file' ] ) && !empty( $_POST[ 'wpie_download_file' ] ) ) {

                        Security::verify_request( 'wpie_settings' );

                        $current_data = $this->get_template_data_by_id( intval( wpie_sanitize_field( $_POST[ 'wpie_download_file' ] ) ) );

                        $options = isset( $current_data->options ) ? maybe_unserialize( $current_data->options ) : array();

                        $activeFile = isset( $options[ 'activeFile' ] ) ? $options[ 'activeFile' ] : "";

                        $importFile = isset( $options[ 'importFile' ] ) ? $options[ 'importFile' ] : "";

                        $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : array();

                        $file_name = $fileData[ 'fileName' ] ? $fileData[ 'fileName' ] : "";

                        $fileDir = $fileData[ 'fileDir' ] ? $fileData[ 'fileDir' ] : "";

                        $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $fileDir . "/" . $file_name;

                        unset( $current_data, $importFile, $activeFile, $fileData, $file_name, $fileDir );

                        $this->wpie_download_file( $filePath );
                }
        }

        private function wpie_download_file( $filePath ) {

                if ( file_exists( $filePath ) ) {

                        header( 'Content-Description: File Transfer' );

                        header( 'Content-Type: application/octet-stream' );

                        header( 'Content-Disposition: attachment; filename=' . basename( $filePath ) );

                        header( 'Expires: 0' );

                        header( 'Cache-Control: must-revalidate' );

                        header( 'Pragma: public' );

                        header( 'Content-Length: ' . filesize( $filePath ) );

                        if ( ob_get_length() > 0 ) {
                                @ob_clean();
                        }

                        readfile( $filePath );

                        die();
                }
        }

        private function get_template_data_by_id( $template_id = 0 ) {

                if ( $template_id != "" && $template_id > 0 ) {

                        global $wpdb;

                        $results = $wpdb->get_results( $wpdb->prepare( "SELECT `options` FROM " . $wpdb->prefix . "wpie_template where `id` = %d", $template_id ) );

                        return $results[ 0 ];
                }

                return false;
        }

        public static function is_upload_dir_writable() {
                if ( is_readable( WPIE_UPLOAD_DIR ) && wp_is_writable( WPIE_UPLOAD_DIR ) ) {
                        return 1;
                }

                chmod( WPIE_UPLOAD_DIR, 0777 );

                if ( wp_is_writable( WPIE_UPLOAD_DIR ) ) {
                        return 1;
                }

                return 0;
        }

        public static function wpie_set_admin_css() {

                $page = isset( $_GET[ 'page' ] ) ? wpie_sanitize_field( $_GET[ 'page' ] ) : "";

                wp_register_style( 'wpie-global-admin-css', WPIE_CSS_URL . '/wpie-global-admin.min.css', array(), WPIE_PLUGIN_VERSION );

                wp_enqueue_style( 'wpie-global-admin-css' );

                if ( !empty( $page ) && in_array( $page, self::$wpie_page ) ) {

                        wp_register_style( 'wpie-export-admin-css', WPIE_CSS_URL . '/wpie-export-admin.min.css', array(), WPIE_PLUGIN_VERSION );

                        wp_register_style( 'wpie-general-admin-css', WPIE_CSS_URL . '/wpie-general-admin.min.css', array(), WPIE_PLUGIN_VERSION );

                        wp_register_style( 'wpie-import-admin-css', WPIE_CSS_URL . '/wpie-import-admin.min.css', array(), WPIE_PLUGIN_VERSION );

                        wp_enqueue_style( 'fontawesome-css', WPIE_CSS_URL . '/fontawesome-all.css' );

                        wp_enqueue_style( 'bootstrap-css', WPIE_CSS_URL . '/bootstrap.css' );

                        wp_enqueue_style( 'animate-css', WPIE_CSS_URL . '/animate.css' );

                        wp_enqueue_style( 'chosen-css', WPIE_CSS_URL . '/chosen.css' );

                        wp_enqueue_style( 'tipso-css', WPIE_CSS_URL . '/tipso.css' );

                        if ( $page == 'wpie-new-export' ) {

                                wp_enqueue_style( 'wpie-export-admin-css' );

                                wp_enqueue_style( 'datatables.bootstrap5-css', WPIE_CSS_URL . '/dataTables.bootstrap5.min.css' );
                        } elseif ( $page == 'wpie-new-import' ) {

                                wp_enqueue_style( 'wpie-import-admin-css' );

                                wp_enqueue_style( 'datatables.bootstrap5-css', WPIE_CSS_URL . '/dataTables.bootstrap5.min.css' );
                        } elseif ( $page == 'wpie-extensions' || $page == 'wpie-settings' || $page == 'wpie-manage-export' || $page == 'wpie-manage-import' ) {

                                wp_enqueue_style( 'wpie-general-admin-css' );
                        }
                }

                unset( $page );
        }

        public static function wpie_set_admin_js() {

                $page = isset( $_GET[ 'page' ] ) ? wpie_sanitize_field( $_GET[ 'page' ] ) : "";

                if ( !empty( $page ) && in_array( $page, self::$wpie_page ) ) {

                        wp_register_script( 'wpie-export-admin-js', WPIE_JS_URL . '/wpie-export-admin.min.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                        wp_register_script( 'wpie-general-admin-js', WPIE_JS_URL . '/wpie-general-admin.min.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                        wp_register_script( 'wpie-import-admin-js', WPIE_JS_URL . '/wpie-import-admin.min.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                        wp_enqueue_script( 'jquery' );

                        wp_enqueue_script( 'bootstrap-js', WPIE_JS_URL . '/bootstrap.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                        wp_enqueue_script( 'bootstrap-notify-js', WPIE_JS_URL . '/bootstrap-notify.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                        wp_enqueue_script( 'chosen-js', WPIE_JS_URL . '/chosen.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                        wp_enqueue_script( 'tipso-js', WPIE_JS_URL . '/tipso.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                        if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-extensions.php' ) ) {
                                require_once(WPIE_CLASSES_DIR . '/class-wpie-extensions.php');
                        }

                        $wpie_ext = new \wpie\addons\WPIE_Extension();

                        $wpieExtData = $wpie_ext->wpie_get_activated_ext();

                        unset( $wpie_ext );

                        $isUploadDirWritable = self::is_upload_dir_writable();

                        $nonce = wp_create_nonce( "wpie-security" );

                        if ( $page == 'wpie-new-export' ) {

                                wp_enqueue_script( 'wpie-export-admin-js' );

                                $wpie_localize_script_data = array(
                                        'wpieAjaxURL'         => admin_url( 'admin-ajax.php' ),
                                        'wpieSiteURL'         => site_url(),
                                        'wpieUploadURL'       => WPIE_UPLOAD_URL,
                                        'wpieUploadDir'       => WPIE_UPLOAD_DIR,
                                        'wpiePluginURL'       => WPIE_PLUGIN_URL,
                                        'wpieImageURL'        => WPIE_IMAGES_URL,
                                        'wpieLocalizeText'    => self::wpie_load_msg(),
                                        'wpieSiteUrl' => home_url(),
                                        'wpiePluginData' => [],
                                        'isWcActive'          => class_exists( 'WooCommerce', false ),
                                        'wpieExtensions'      => $wpieExtData,
                                        'isUploadDirWritable' => $isUploadDirWritable,
                                        'wpieSecurity'        => $nonce
                                );

                                wp_localize_script( 'wpie-export-admin-js', 'wpiePluginSettings', $wpie_localize_script_data );

                                unset( $wpie_localize_script_data );

                                wp_enqueue_script( 'datatables-js', WPIE_JS_URL . '/jquery.dataTables.min.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'datatables-ellipsis-js', WPIE_JS_URL . '/ellipsis.js', array( 'jquery', 'datatables-js' ), WPIE_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'editable-js', WPIE_JS_URL . '/editable.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'dataTables.bootstrap5-js', WPIE_JS_URL . '/dataTables.bootstrap5.min.js', array( 'jquery', 'bootstrap-js' ), WPIE_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'jquery-ui-sortable' );
                        } elseif ( $page == 'wpie-new-import' ) {

                                wp_enqueue_script( 'wpie-import-admin-js' );

                                $wpie_localize_script_data = array(
                                        'wpieAjaxURL'         => admin_url( 'admin-ajax.php' ),
                                        'wpieSiteURL'         => site_url(),
                                        'wpieUploadURL'       => WPIE_UPLOAD_URL,
                                        'wpieUploadDir'       => WPIE_UPLOAD_DIR,
                                        'wpiePluginURL'       => WPIE_PLUGIN_URL,
                                        'wpieImageURL'        => WPIE_IMAGES_URL,
                                        'wpieLocalizeText'    => self::wpie_load_msg(),
                                        'wpieSiteUrl' => home_url(),
                                        'wpiePluginData' => [],
                                        'isWcActive'          => class_exists( 'WooCommerce', false ),
                                        'wpieExtensions'      => $wpieExtData,
                                        'isUploadDirWritable' => $isUploadDirWritable,
                                        'wpieSecurity'        => $nonce
                                );

                                wp_localize_script( 'wpie-import-admin-js', 'wpiePluginSettings', $wpie_localize_script_data );

                                unset( $wpie_localize_script_data );

                                wp_enqueue_script( 'datatables-js', WPIE_JS_URL . '/jquery.dataTables.min.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'datatables-ellipsis-js', WPIE_JS_URL . '/ellipsis.js', array( 'jquery', 'datatables-js' ), WPIE_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'dataTables.bootstrap5-js', WPIE_JS_URL . '/dataTables.bootstrap5.min.js', array( 'jquery', 'bootstrap-js' ), WPIE_PLUGIN_VERSION, true );

                                wp_enqueue_script( 'plupload' );

                                wp_enqueue_script( 'plupload-all' );
                        } elseif ( $page == 'wpie-extensions' || $page == 'wpie-settings' || $page == 'wpie-manage-export' || $page == 'wpie-manage-import' ) {

                                wp_enqueue_script( 'wpie-general-admin-js' );

                                $wpie_localize_script_data = array(
                                        'wpieAjaxURL'      => admin_url( 'admin-ajax.php' ),
                                        'wpieSiteURL'      => site_url(),
                                        'wpieUploadURL'    => WPIE_UPLOAD_URL,
                                        'wpieUploadDir'    => WPIE_UPLOAD_DIR,
                                        'wpiePluginURL'    => WPIE_PLUGIN_URL,
                                        'wpieImageURL'     => WPIE_IMAGES_URL,
                                        'wpieLocalizeText' => self::wpie_load_msg(),
                                        'wpieSecurity'     => $nonce
                                );

                                wp_localize_script( 'wpie-general-admin-js', 'wpiePluginSettings', $wpie_localize_script_data );

                                unset( $wpie_localize_script_data );
                        }
                        unset( $wpieExtData );
                }
                unset( $page );
        }

        public static function wpie_db_check() {

                $wpie_plugin_version = get_option( 'wpie_plugin_version', "" );

                if ( $wpie_plugin_version == "" ) {

                        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

                        global $wpdb;

                        if ( $wpdb->has_cap( 'collation' ) ) {

                                if ( !empty( $wpdb->charset ) )
                                        $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

                                if ( !empty( $wpdb->collate ) )
                                        $charset_collate .= " COLLATE $wpdb->collate";
                        }

                        update_option( 'wpie_plugin_version', WPIE_PLUGIN_VERSION );

                        update_option( 'wpie_db_version', WPIE_DB_VERSION );

                        update_option( 'wpie_install_date', current_time( 'timestamp' ) );

                        $wpie_template = $wpdb->prefix . 'wpie_template';

                        $wpie_template_table = "CREATE TABLE IF NOT EXISTS {$wpie_template}(
							
                            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                            status VARCHAR(25),
                            opration VARCHAR(100) NOT NULL, 
                            username VARCHAR(60) NOT NULL, 
                            unique_id VARCHAR(100) NOT NULL, 
                            opration_type VARCHAR(100) NOT NULL,
                            options LONGTEXT,
                            process_log VARCHAR(255),
                            process_lock INT(3),
                            create_date DATETIME NOT NULL,
                            last_update_date DATETIME NOT NULL 

                            ){$charset_collate}";

                        dbDelta( $wpie_template_table );

                        unset( $charset_collate, $wpie_template, $wpie_template_table );
                }

                unset( $wpie_plugin_version );
        }

        public static function wpie_load_textdomain() {
                load_plugin_textdomain( 'wp-import-export-lite', false, 'wp-import-export-lite/languages/' );
        }

        public static function wpie_hide_all_notice_to_admin_side() {
                if ( isset( $_GET[ 'page' ] ) && (wpie_sanitize_field( $_GET[ 'page' ] ) === 'wpie-new-export' || wpie_sanitize_field( $_GET[ 'page' ] ) == 'wpie-new-import') ) {
                        remove_all_actions( 'admin_notices', 10000 );
                        remove_all_actions( 'all_admin_notices', 10000 );
                        remove_all_actions( 'network_admin_notices', 10000 );
                        remove_all_actions( 'user_admin_notices', 10000 );
                }
        }

        public static function wpie_set_menu() {

                global $current_user;

                if ( current_user_can( 'administrator' ) || is_super_admin() ) {
                        $wpie_caps = self::wpie_user_capabilities();

                        if ( !empty( $wpie_caps ) ) {
                                foreach ( $wpie_caps as $wpie_cap => $cap_desc ) {
                                        $current_user->add_cap( $wpie_cap );
                                }
                        }
                        unset( $wpie_caps );
                }

                $menu_place = ( string ) self::get_dynamic_position( 28.81, 0.1 );

                add_menu_page( __( 'WP Import Export Dashboard', 'wp-import-export-lite' ), __( 'WP Imp Exp', 'wp-import-export-lite' ), 'wpie_new_export', 'wpie-new-export', array( __CLASS__, 'wpie_get_page' ), null, $menu_place );

                add_submenu_page( 'wpie-new-export', __( 'New Export', 'wp-import-export-lite' ), __( 'New Export', 'wp-import-export-lite' ), 'wpie_new_export', 'wpie-new-export', array( __CLASS__, 'wpie_get_page' ) );

                add_submenu_page( 'wpie-new-export', __( 'Manage Export', 'wp-import-export-lite' ), __( 'Manage Export', 'wp-import-export-lite' ), 'wpie_manage_export', 'wpie-manage-export', array( __CLASS__, 'wpie_get_page' ) );

                add_submenu_page( 'wpie-new-export', __( 'New Import', 'wp-import-export-lite' ), __( 'New Import', 'wp-import-export-lite' ), 'wpie_new_import', 'wpie-new-import', array( __CLASS__, 'wpie_get_page' ) );

                add_submenu_page( 'wpie-new-export', __( 'Manage Import', 'wp-import-export-lite' ), __( 'Manage Import', 'wp-import-export-lite' ), 'wpie_manage_import', 'wpie-manage-import', array( __CLASS__, 'wpie_get_page' ) );

                add_submenu_page( 'wpie-new-export', __( 'Settings', 'wp-import-export-lite' ), __( 'Settings', 'wp-import-export-lite' ), 'wpie_settings', 'wpie-settings', array( __CLASS__, 'wpie_get_page' ) );

                add_submenu_page( 'wpie-new-export', __( 'Extensions', 'wp-import-export-lite' ), __( 'Extensions', 'wp-import-export-lite' ), 'wpie_extensions', 'wpie-extensions', array( __CLASS__, 'wpie_get_page' ) );

                unset( $menu_place );
        }

        public static function wpie_get_page() {

                $page = isset( $_GET[ 'page' ] ) ? wpie_sanitize_field( $_GET[ 'page' ] ) : "";

                if ( !empty( $page ) && in_array( $page, self::$wpie_page ) ) {

                        if ( $page == 'wpie-new-export' && file_exists( WPIE_VIEW_DIR . '/wpie-new-export.php' ) ) {

                                require_once( WPIE_VIEW_DIR . '/wpie-new-export.php');
                        } elseif ( $page == 'wpie-new-import' && file_exists( WPIE_VIEW_DIR . '/wpie-new-import.php' ) ) {

                                require_once( WPIE_VIEW_DIR . '/wpie-new-import.php');
                        } elseif ( $page == 'wpie-manage-export' && file_exists( WPIE_VIEW_DIR . '/wpie-manage-export.php' ) ) {

                                require_once( WPIE_VIEW_DIR . '/wpie-manage-export.php');
                        } elseif ( $page == 'wpie-manage-import' && file_exists( WPIE_VIEW_DIR . '/wpie-manage-import.php' ) ) {

                                require_once( WPIE_VIEW_DIR . '/wpie-manage-import.php');
                        } elseif ( $page == 'wpie-settings' && file_exists( WPIE_VIEW_DIR . '/wpie-settings.php' ) ) {

                                require_once( WPIE_VIEW_DIR . '/wpie-settings.php');
                        } elseif ( $page == 'wpie-extensions' ) {

                                $require_page = WPIE_VIEW_DIR . '/wpie-extensions.php';

                                $include_page = WPIE_VIEW_DIR . '/wpie-extension-info.php';

                                if ( isset( $_GET[ 'wpie_ext' ] ) && !empty( wpie_sanitize_field( $_GET[ 'wpie_ext' ] ) ) ) {

                                        if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-extensions.php' ) ) {
                                                require_once(WPIE_CLASSES_DIR . '/class-wpie-extensions.php');
                                        }
                                        $wpie_ext = new \wpie\addons\WPIE_Extension();

                                        $is_valid_ext = $wpie_ext->wpie_import_extension_info( wpie_sanitize_field( $_GET[ 'wpie_ext' ] ) );

                                        if ( $is_valid_ext && file_exists( $include_page ) ) {

                                                require_once($include_page);
                                        } elseif ( file_exists( $require_page ) ) {

                                                require_once($require_page);
                                        }
                                        unset( $wpie_ext, $is_valid_ext );
                                } elseif ( file_exists( $require_page ) ) {
                                        require_once($require_page);
                                }
                                unset( $include_page );

                                unset( $require_page );
                        }
                }
                unset( $page );
        }

        private static function wpie_user_capabilities() {
                return array(
                        'wpie_new_export'    => __( 'User can export new data', 'wp-import-export-lite' ),
                        'wpie_manage_export' => __( 'User can manage export data', 'wp-import-export-lite' ),
                        'wpie_new_import'    => __( 'User can import new data', 'wp-import-export-lite' ),
                        'wpie_manage_import' => __( 'User can manage import data', 'wp-import-export-lite' ),
                        'wpie_settings'      => __( 'User can manage Settings of import and export', 'wp-import-export-lite' ),
                        'wpie_extensions'    => __( 'User can manage Extensions of import and export', 'wp-import-export-lite' ),
                        'wpie_add_shortcode' => __( 'User Add Shortcode in import field', 'wp-import-export-lite' ),
                );
        }

        private static function get_dynamic_position( $start, $increment = 0.1 ) {

                foreach ( $GLOBALS[ 'menu' ] as $key => $menu ) {
                        $menus_positions[] = $key;
                }
                if ( !in_array( $start, $menus_positions ) )
                        return $start;

                while ( in_array( $start, $menus_positions ) ) {
                        $start += $increment;
                }
                unset( $increment, $menus_positions );

                return $start;
        }

        public static function wpie_replace_footer_admin() {
                echo '';
        }

        public static function wpie_replace_footer_version() {
                return '';
        }

        private static function is_htaccess_writable() {

                require_once(ABSPATH . 'wp-admin/includes/file.php');

                $htaccess_file = get_home_path() . '.htaccess';

                if ( !file_exists( $htaccess_file ) ) {

                        return false;
                }

                if ( wp_is_writable( $htaccess_file ) ) {
                        return true;
                }

                @chmod( $htaccess_file, 0666 );

                if ( !wp_is_writable( $htaccess_file ) ) {
                        return false;
                }

                unset( $htaccess_file );

                return true;
        }

        public static function mod_rewrite_rules( $rules = "" ) {

                $newRule = "RewriteCond %{REQUEST_FILENAME} -s" . PHP_EOL;

                $newRule .= "RewriteCond %{HTTP_USER_AGENT} !facebookexternalhit/[0-9]" . PHP_EOL;

                $newRule .= "RewriteCond %{HTTP_USER_AGENT} !Twitterbot/[0-9]" . PHP_EOL;

                $newRule .= "RewriteCond %{HTTP_USER_AGENT} !Googlebot/[0-9]" . PHP_EOL;

                $upload_dir_url = str_replace( "https", "http", WPIE_UPLOAD_URL );

                $site_url = str_replace( "https", "http", site_url() );

                $newRule .= "RewriteRule " . str_replace( trailingslashit( $site_url ), '', $upload_dir_url ) . "(\/[A-Za-z0-9_@.\/&+-]+)+\.([A-Za-z0-9_@.\/&+-]+)$ [L]" . PHP_EOL;

                update_option( 'wpie_is_admin_notice_clear', 1 );

                update_option( 'wpie_flush_rewrite_rules', 1 );

                unset( $site_url, $upload_dir_url );

                return $newRule . $rules . PHP_EOL;
        }

        public static function hide_notices() {

                if ( isset( $_GET[ 'wpie-hide-notice' ] ) && isset( $_GET[ '_wpie_notice_nonce' ] ) ) {

                        if ( !wp_verify_nonce( sanitize_key( wp_unslash( $_GET[ '_wpie_notice_nonce' ] ) ), 'wpie_hide_notices_nonce' ) ) {
                                wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wp-import-export-lite' ) );
                        }

                        $hide_notice = sanitize_text_field( wp_unslash( $_GET[ 'wpie-hide-notice' ] ) );

                        update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', 1 );

                        unset( $hide_notice );
                }
        }

        public static function wpie_admin_notices() {

                if ( self::is_apache() && get_option( 'wpie_is_admin_notice_clear', false ) === false ) {

                        $notice = get_user_meta( get_current_user_id(), "dismissed_wpie_file_security_notice", true );

                        if ( intval( $notice ) != 1 ) {

                                ?>
                                <div class="wpie-message updated" >
                                        <a class="wpie-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpie-hide-notice', "wpie_file_security" ), 'wpie_hide_notices_nonce', '_wpie_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'wp-import-export-lite' ); ?></a>
                                        <p><b><?php echo __( 'WP Import Export : ', 'wp-import-export-lite' ); ?></b> <?php _e( "If your <b>.htaccess</b> file were writable, we could do this automatically, but it isn’t. So you must either make it writable or manually update your .htaccess with the mod_rewrite rules found under <b>WP Settings >> Permalinks</b>. Until then, the exported and imported files are not protected from direct access.", 'wp-import-export-lite' ); ?></p>
                                </div>
                                <?php
                        }
                }
        }

        public static function is_apache() {
                // assume apache when unknown, since most common
                if ( !isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) || empty( $_SERVER[ 'SERVER_SOFTWARE' ] ) ) {
                        return true;
                }

                return isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) && stristr( wpie_sanitize_field( $_SERVER[ 'SERVER_SOFTWARE' ] ), 'Apache' ) !== false;
        }

        public static function update_file_security() {

                $is_updated = get_option( 'wpie_is_updated_file_security', false );

                $robots_file = get_home_path() . 'robots.txt';

                if ( $is_updated === false || (!file_exists( $robots_file )) ) {

                        //update robots.txt
                        if ( is_writable( get_home_path() ) ) {

                                if ( ($fp = @fopen( $robots_file, 'a+' )) !== false ) {

                                        $filesize = filesize( $robots_file );

                                        $robotstext = $filesize > 0 ? fread( $fp, $filesize ) : "";

                                        if ( trim( $robotstext ) != "" && strpos( $robotstext, "#WP Import Export Rule" ) === false ) {

                                                $robots_content = self::update_robots_txt();

                                                fwrite( $fp, $robots_content );
                                        }
                                        fclose( $fp );

                                        update_option( 'wpie_is_updated_file_security', 1 );

                                        unset( $robotstext, $filesize );
                                }
                        }
                }

                unset( $is_updated );
        }

        public static function update_robots_txt( $robotstext = "", $public = false ) {

                if ( strpos( $robotstext, "#WP Import Export Rule" ) === false ) {

                        $robotstext .= PHP_EOL . PHP_EOL . "#WP Import Export Rule";

                        $robotstext .= PHP_EOL . "User-agent: *";

                        $robotstext .= PHP_EOL . "Disallow: /wp-content/uploads/wp-import-export-lite/";
                }
                return $robotstext;
        }

        public static function wpie_woocommerce_order_number( $order_id = 0, $order = [] ) {

                $order_number = $order_id;

                if ( absint( $order_id ) > 0 ) {

                        $new_order_number = get_post_meta( $order_id, '_wpie_order_number', true );

                        if ( !empty( $new_order_number ) ) {
                                $order_number = $new_order_number;
                        }
                        unset( $new_order_number );
                }
                return $order_number;
        }

        private static function wpie_load_msg() {
                return array(
                        "yesText"                         => __( 'Yes', 'wp-import-export-lite' ),
                        "okText"                          => __( 'Ok', 'wp-import-export-lite' ),
                        "errorText"                       => __( 'Error', 'wp-import-export-lite' ),
                        "confirmText"                     => __( 'Confirm', 'wp-import-export-lite' ),
                        "selectTemplateText"              => __( 'Select Template', 'wp-import-export-lite' ),
                        "selectSettingText"               => __( 'Select Setting', 'wp-import-export-lite' ),
                        "selectSettingloadText"           => __( 'Setting Loaded Successfully', 'wp-import-export-lite' ),
                        "wpie_ajax_not_connect_error"     => __( 'Not connect.\n Verify Network.', 'wp-import-export-lite' ),
                        "wpie_ajax_404_error"             => __( 'Requested page not found. [404]', 'wp-import-export-lite' ),
                        "wpie_ajax_internal_server_error" => __( 'Internal Server Error [500].', 'wp-import-export-lite' ),
                        "wpie_ajax_jason_parse_error"     => __( 'Requested JSON parse failed.', 'wp-import-export-lite' ),
                        "wpie_ajax_time_out_error"        => __( 'Time out error.', 'wp-import-export-lite' ),
                        "wpie_ajax_request_aborted_error" => __( 'Ajax request aborted.', 'wp-import-export-lite' ),
                        "wpie_ajax_400_error"             => __( 'Bad Request', 'wp-import-export-lite' ),
                        "wpie_ajax_uncaught_error"        => __( 'Uncaught Error', 'wp-import-export-lite' ),
                        "selectExportRuleText"            => __( 'Select Rule', 'wp-import-export-lite' ),
                        "selectElementText"               => __( 'Select Element', 'wp-import-export-lite' ),
                        "selectExportTypeText"            => __( 'Please choose export type', 'wp-import-export-lite' ),
                        "selectExportTaxTypeText"         => __( 'Please choose export taxonomy type', 'wp-import-export-lite' ),
                        "enterTemplateNameText"           => __( 'Please enter template Name', 'wp-import-export-lite' ),
                        "enterSettingNameText"            => __( 'Please enter Setting Name', 'wp-import-export-lite' ),
                        "enterCsvDelimiterText"           => __( 'Please enter CSV delimiter', 'wp-import-export-lite' ),
                        "andText"                         => __( 'AND', 'wp-import-export-lite' ),
                        "orText"                          => __( 'OR', 'wp-import-export-lite' ),
                        "saveText"                        => __( 'Save', 'wp-import-export-lite' ),
                        "closeText"                       => __( 'Close', 'wp-import-export-lite' ),
                        "wpieNoFieldsFoundText"           => __( "No fields found please choose other option", 'wp-import-export-lite' ),
                        "wpieExportFieldEditorText"       => __( "Export Field Editor", 'wp-import-export-lite' ),
                        "wpieExportEmptyFieldText"        => __( "Please Enter Field Name", 'wp-import-export-lite' ),
                        "wpieExportEmptyDataText"         => __( "There aren't any Records to export.", 'wp-import-export-lite' ),
                        "wpieExportCompletedText"         => __( "Export Completed", 'wp-import-export-lite' ),
                        "wpieExportUserExtDisableText"    => __( "Please Activate User Export Extension", 'wp-import-export-lite' ),
                        "wpieExportWCExtDisableText"      => __( "Please Activate WooCommerce Export Extension", 'wp-import-export-lite' ),
                        "wpieExportattrExtDisableText"    => __( "Please Activate Product Attributes Export Extension", 'wp-import-export-lite' ),
                        "wpieExportEmptyColumnText"       => __( "You haven't selected any columns for export.", 'wp-import-export-lite' ),
                        "wpieChooseFileText"              => __( 'Choose File', 'wp-import-export-lite' ),
                        "wpieChooseSheetText"             => __( 'Choose Excel Sheet', 'wp-import-export-lite' ),
                        "fileUploadSuccessText"           => __( 'File Uploaded Successfully', 'wp-import-export-lite' ),
                        "invalidFileExtensionText"        => __( 'Uploaded file must be CSV, ZIP, XLS, XLSX, XML, TXT, JSON', 'wp-import-export-lite' ),
                        "wpieUploadingText"               => __( 'Uploading', 'wp-import-export-lite' ),
                        "wpieUploadCompleteText"          => __( 'Upload Complete', 'wp-import-export-lite' ),
                        "wpieParingUploadFileText"        => __( 'Parsing upload file', 'wp-import-export-lite' ),
                        "wpieGetTemplatesText"            => __( 'Get Template List', 'wp-import-export-lite' ),
                        "wpieGetSettingList"              => __( 'Get Setting List', 'wp-import-export-lite' ),
                        "wpieGetSettingsText"             => __( 'Get Settings List', 'wp-import-export-lite' ),
                        "wpieGetConfigText"               => __( 'Get Configuration', 'wp-import-export-lite' ),
                        "wpieGetFieldsText"               => __( 'Get Import Fields', 'wp-import-export-lite' ),
                        "wpieGetRecordsText"              => __( 'Get Preview Recods', 'wp-import-export-lite' ),
                        "wpieChangeTemplatesText"         => __( 'Set Template', 'wp-import-export-lite' ),
                        "wpieUpdateSettingsText"          => __( 'Settings Updating', 'wp-import-export-lite' ),
                        "wpieSaveTemplatesText"           => __( 'Save Template', 'wp-import-export-lite' ),
                        "wpieSaveSettingsText"            => __( 'Save Settings', 'wp-import-export-lite' ),
                        "wpieNoRecordsFoundText"          => __( 'No Records Found. Please Try another filters', 'wp-import-export-lite' ),
                        "wpieNoRecordsText"               => __( 'No Records Found. Please Try another File or Data Format or Excel Sheet', 'wp-import-export-lite' ),
                        "wpieImportProcessingText"        => __( 'Import Processing', 'wp-import-export-lite' ),
                        "wpieImportCompleteText"          => __( 'Import Complete!', 'wp-import-export-lite' ),
                        "wpieImportPausedText"            => __( 'Import Paused', 'wp-import-export-lite' ),
                        "wpieImportStoppedText"           => __( 'Import Stopped', 'wp-import-export-lite' ),
                        "wpieImportProcessingNoticeText"  => __( 'Importing may take some time. Please do not close your browser or refresh the page until the process is complete.', 'wp-import-export-lite' ),
                        "wpieImportPartiallyText"         => __( 'WordPress Import Export partially imported your file into your WordPress installation!', 'wp-import-export-lite' ),
                        "wpieImportCompleteNoticeText"    => __( 'WordPress Import Export successfully imported your file into your WordPress installation!', 'wp-import-export-lite' ),
                        "wpieChooseValidFileText"         => __( 'Please Choose Valid File', 'wp-import-export-lite' ),
                        "wpieSetExistingFileText"         => __( 'Set Existing File', 'wp-import-export-lite' ),
                        "wpieUploadFromURLText"           => __( 'File is downloading from URL', 'wp-import-export-lite' ),
                        "wpieUploadFromFTPText"           => __( 'File is downloading from FTP/SFTP', 'wp-import-export-lite' ),
                        "wpieEmptyUsesrRole"              => __( 'Please choose user role', 'wp-import-export-lite' ),
                        "wpieEmptyTemplates"              => __( 'Please Select Templates', 'wp-import-export-lite' ),
                        "wpieEmptyActions"                => __( 'Please select any action', 'wp-import-export-lite' ),
                        "wpieSetBGProcessText"            => __( 'Set Background Process', 'wp-import-export-lite' ),
                        "wpieBgProcessingText"            => __( 'Background Process Set Successfully', 'wp-import-export-lite' ),
                        "wpieImportBGText"                => __( 'Import in Background', 'wp-import-export-lite' ),
                        "wpieImportBGNoticeText"          => __( 'plugin will automatically import data in Background. you can close your browser.', 'wp-import-export-lite' ),
                        "wpieInvalidURLText"              => __( 'Please Enter Valid URL', 'wp-import-export-lite' ),
                        "wpieInvalidHostNameText"         => __( 'Please Enter Valid Host Name', 'wp-import-export-lite' ),
                        "wpieInvalidHostUsernameText"     => __( 'Please Enter Valid Host Username', 'wp-import-export-lite' ),
                        "wpieInvalidHostPasswordText"     => __( 'Please Enter Valid Host Password', 'wp-import-export-lite' ),
                        "wpieDownloadFileText"            => __( 'Downloading File', 'wp-import-export-lite' ),
                        "wpieInvalidHostPathText"         => __( 'Please Enter Valid Host Path', 'wp-import-export-lite' ),
                        "wpieImportUserExtDisableText"    => __( "Please Activate User Import Extension", 'wp-import-export-lite' ),
                        "wpieImportWCExtDisableText"      => __( "Please Activate WooCommerce Import Extension", 'wp-import-export-lite' ),
                        "wpieImportPAExtDisableText"      => __( "Please Activate Product Attributes Import Extension", 'wp-import-export-lite' ),
                        "wpiePrepareFile"                 => __( "Prepare File", 'wp-import-export-lite' ),
                        "wpiePaused"                      => __( "Paused", 'wp-import-export-lite' ),
                        "wpieProcessing"                  => __( "Processing", 'wp-import-export-lite' ),
                        "wpieStopped"                     => __( "Stopped", 'wp-import-export-lite' ),
                        "wpieEmptyLicenseKey"             => __( "Purchase Code is empty", 'wp-import-export-lite' ),
                        "wpieInvalidLicenseKey"           => __( "Purchase Code is Invalid", 'wp-import-export-lite' ),
                        "wpieSetScheduleExportText"       => __( "Set Schedule", 'wp-import-export-lite' ),
                        "wpieFillRequiredFieldText"       => __( "Please Fill Required Fields", 'wp-import-export-lite' ),
                        "wpieEmptyTemplate"               => __( "Please Select any template", 'wp-import-export-lite' ),
                        "wpieEmptyLayout"                 => __( "Please Select any Layout", 'wp-import-export-lite' ),
                        "processingReimport"              => __( "Processing Reimport", 'wp-import-export-lite' ),
                        "ActivateWc"                      => __( "Please Activate WooCommerce Plugin", 'wp-import-export-lite' ),
                        "showMoreText"                    => __( "[Show more]", 'wp-import-export-lite' ),
                        "showLessText"                    => __( "[Show less]", 'wp-import-export-lite' ),
                        "wpieEmptyTitleField"             => __( "Title is required field for new data", 'wp-import-export-lite' ),
                        "chooseDataUpdate"                => __( "Choose which data to update", 'wp-import-export-lite' ),
                        "uploadDirWritableError"          => __( "Upload Directory YOUR_SITE/wp-content/uploads/wp-import-export-lite is not Writable. Please change permission to make writable", 'wp-import-export-lite' ),
                        "wpieInvalidLicense"              => __( "Please Activate Plugin Purchase Code from WP Imp Exp => Settings", 'wp-import-export-lite' ),
                        "wpieProFeatureOnly"              => __( "Pro Feature Only", 'wp-import-export-lite' )
                );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

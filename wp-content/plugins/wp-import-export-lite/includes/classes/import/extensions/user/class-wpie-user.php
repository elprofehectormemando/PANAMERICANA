<?php


namespace wpie\import\user;

use WP_User_Query;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-engine.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-engine.php');
}

class WPIE_User_Import extends \wpie\import\engine\WPIE_Import_Engine {

        protected $import_type = "user";
        private $login_user_id = false;

        private function get_login_user_id() {

                if ( $this->login_user_id === false ) {
                        $this->login_user_id = \get_current_user_id();
                }
                return $this->login_user_id;
        }

        public function process_import_data() {

                global $wpdb;

                if ( $this->is_update_field( "fname" ) ) {

                        $this->wpie_final_data[ 'first_name' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_first_name' ) );
                }
                if ( $this->is_update_field( "lname" ) ) {

                        $this->wpie_final_data[ 'last_name' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_last_name' ) );
                }
                $roles = [];

                if ( $this->is_update_field( "role" ) ) {

                        $roleData = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_role' ) );

                        $role = "";

                        if ( !empty( $roleData ) ) {

                                $separator = "|";

                                if ( strpos( $roleData, "|" ) === false && strpos( $roleData, "," ) !== false ) {
                                        $separator = ",";
                                }

                                $roleData = trim( $roleData, $separator );

                                $userRoles = empty( $roleData ) ? [] : explode( $separator, $roleData );

                                $roles = $this->get_valid_roles( $userRoles );

                                $role = isset( $roles[ 0 ] ) ? $roles[ 0 ] : "";

                                if ( count( $roles ) <= 1 ) {
                                        $roles = [];
                                } else {
                                        array_shift( $roles );
                                }
                        }

                        $this->wpie_final_data[ 'role' ] = apply_filters( 'wpie_import_user_role', $role );
                }
                if ( $this->is_update_field( "nickname" ) ) {

                        $this->wpie_final_data[ 'nickname' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_nickname' ) );
                }
                if ( $this->is_update_field( "desc" ) ) {

                        $this->wpie_final_data[ 'description' ] = $this->get_field_value( 'wpie_item_description' );
                }
                if ( $this->is_update_field( "login" ) ) {

                        $this->wpie_final_data[ 'user_login' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_login' ) );
                }

                $is_hashed_wp_password = false;

                if ( $this->is_update_field( "password" ) ) {

                        $this->wpie_final_data[ 'user_pass' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_pass' ) );

                        $is_hashed_wp_password = ( absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_hashed_password' ) ) ) == 1);
                }

                if ( $this->is_update_field( "nicename" ) ) {

                        $this->wpie_final_data[ 'user_nicename' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_nicename' ) );
                }
                if ( $this->is_update_field( "email" ) ) {

                        $this->wpie_final_data[ 'user_email' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_email' ) );
                }
                if ( $this->is_update_field( "registered_date" ) ) {

                        $user_registered = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_registered' ) );

                        if ( empty( trim( $user_registered ) ) || strtotime( $user_registered ) === false ) {
                                $user_registered = current_time( 'mysql' );
                        }

                        $this->wpie_final_data[ 'user_registered' ] = date( 'Y-m-d H:i:s', strtotime( $user_registered ) );
                }
                if ( $this->is_update_field( "display_name" ) ) {

                        $this->wpie_final_data[ 'display_name' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_display_name' ) );
                }
                if ( $this->is_update_field( "url" ) ) {

                        $this->wpie_final_data[ 'user_url' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_url' ) );
                }

                $this->wpie_final_data = apply_filters( 'wpie_before_user_import', $this->wpie_final_data, $this->wpie_import_option );

                $send_notifications = wpie_sanitize_field( $this->get_field_value( 'wpie_item_send_email_notifications' ) );

                if ( empty( $send_notifications ) || absint( $send_notifications ) !== 1 ) {

                        $this->remove_email_notifications();
                }

                if ( $this->is_new_item ) {

                        $this->item_id = wp_insert_user( $this->wpie_final_data );
                } else {

                        if ( $this->get_login_user_id() === $this->existing_item_id ) {

                                $this->set_log( '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . __( "Can't update current Login user", 'wp-import-export-lite' ) );

                                $this->process_log[ 'skipped' ]++;

                                $this->process_log[ 'imported' ]++;

                                return true;
                        }
                        $this->wpie_final_data[ 'ID' ] = $this->existing_item_id;

                        $this->item_id = wp_update_user( $this->wpie_final_data );
                }

                $this->process_log[ 'imported' ]++;

                if ( is_wp_error( $this->item_id ) ) {

                        $this->set_log( '<strong>' . __( 'ERROR', 'wp-import-export-lite' ) . '</strong> : ' . $this->item_id->get_error_message() );

                        $this->process_log[ 'skipped' ]++;

                        return true;
                } elseif ( $this->item_id == 0 ) {

                        $this->set_log( '<strong>' . __( 'ERROR', 'wp-import-export-lite' ) . '</strong> : ' . __( 'something wrong, ID = 0 was generated.', 'wp-import-export-lite' ) );

                        $this->process_log[ 'skipped' ]++;

                        return true;
                }
                if ( $this->is_new_item ) {
                        $this->process_log[ 'created' ]++;
                } else {
                        $this->process_log[ 'updated' ]++;
                }

                $this->item = get_user_by( "id", $this->item_id );

                $this->process_log[ 'last_records_id' ] = $this->item_id;

                $this->process_log[ 'last_records_status' ] = 'pending';

                $this->process_log[ 'last_activity' ] = date( 'Y-m-d H:i:s' );

                $wpdb->update( $wpdb->prefix . "wpie_template", array( 'last_update_date' => current_time( 'mysql' ),
                        'process_log'      => maybe_serialize( $this->process_log ) ), array(
                        'id' => $this->wpie_import_id ) );

                if ( $is_hashed_wp_password ) {

                        $wpdb->query( $wpdb->prepare(
                                        "
				UPDATE `" . $wpdb->prefix . 'users' . "`
				SET `user_pass` = %s
				WHERE `ID` = %d
				", $this->wpie_final_data[ 'user_pass' ], $this->item_id
                        ) );
                }

                if ( empty( $send_notifications ) || absint( $send_notifications ) !== 1 ) {
                        $this->add_email_notifications();
                } elseif ( $this->is_new_item ) {
                        \wp_new_user_notification( $this->item_id, null, 'both' );
                }

                unset( $send_notifications );

                do_action( 'wpie_after_user_import', $this->item_id, $this->wpie_final_data, $this->wpie_import_option );

                if ( $this->is_update_field( "cf" ) ) {

                        $this->wpie_import_cf();
                }

                if ( !empty( $roles ) ) {

                        foreach ( $roles as $role ) {
                                $this->item->add_role( $role );
                        }
                }

                return $this->item_id;
        }

        public function do_not_send_notification( $is_notify, $user, $userdata ) {

                return false;
        }

        private function remove_email_notifications() {

                remove_filter( 'after_password_reset', 'wp_password_change_notification' );
                remove_filter( 'register_new_user', 'wp_send_new_user_notifications' );
                remove_filter( 'edit_user_created_user', 'wp_send_new_user_notifications' );

                //Ultimate Member plugin Email Notifications 
                remove_all_actions( 'um_registration_complete' );

                add_filter( 'send_password_change_email', [ $this, 'do_not_send_notification' ], 99999, 3 );
                add_filter( 'send_email_change_email', [ $this, 'do_not_send_notification' ], 99999, 3 );
        }

        private function add_email_notifications() {

                remove_filter( 'send_password_change_email', [ $this, 'do_not_send_notification' ] );
                remove_filter( 'send_email_change_email', [ $this, 'do_not_send_notification' ] );

                add_action( 'after_password_reset', 'wp_password_change_notification' );
                add_action( 'register_new_user', 'wp_send_new_user_notifications' );
                add_action( 'edit_user_created_user', 'wp_send_new_user_notifications', 10, 2 );
        }

        protected function search_duplicate_item() {

                global $wpdb;

                $wpie_duplicate_indicator = empty( $this->get_field_value( 'wpie_existing_item_search_logic', true ) ) ? 'title' : wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) );

                if ( $wpie_duplicate_indicator == "id" ) {

                        $duplicate_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {
                                $user = get_user_by( 'id', absint( $duplicate_id ) );

                                if ( $user ) {
                                        $this->existing_item_id = $duplicate_id;
                                }
                                unset( $user );
                        }
                        unset( $duplicate_id );
                } elseif ( $wpie_duplicate_indicator == "email" ) {

                        $email = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_email' ) );

                        if ( !empty( $email ) ) {
                                $user = get_user_by( 'email', $email );

                                if ( $user ) {
                                        $this->existing_item_id = $user->ID;
                                }
                                unset( $user );
                        }
                        unset( $email );
                } elseif ( $wpie_duplicate_indicator == "login" ) {

                        $user_login = wpie_sanitize_field( $this->get_field_value( 'wpie_item_user_login' ) );

                        if ( !empty( $user_login ) ) {
                                $user = get_user_by( 'login', $user_login );

                                if ( $user ) {
                                        $this->existing_item_id = $user->ID;
                                }
                                unset( $user );
                        }
                        unset( $user_login );
                } elseif ( $wpie_duplicate_indicator == "cf" ) {

                        $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_key' ) );

                        $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_value' ) );

                        $user_query = array(
                                'meta_query' => array(
                                        0 => array(
                                                'key'     => $meta_key,
                                                'value'   => $meta_val,
                                                'compare' => '='
                                        )
                                )
                        );

                        $user_data = new \WP_User_Query( $user_query );

                        unset( $user_query );

                        if ( !empty( $user_data->results ) ) {
                                foreach ( $user_data->results as $user ) {
                                        $this->existing_item_id = $user->ID;
                                        break;
                                }
                        } else {
                                $user_data_found = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS " . $wpdb->users . ".ID FROM " . $wpdb->users . " INNER JOIN " . $wpdb->usermeta . " ON (" . $wpdb->users . ".ID = " . $wpdb->usermeta . ".user_id) WHERE 1=1 AND ( (" . $wpdb->usermeta . ".meta_key = %s AND " . $wpdb->usermeta . ".meta_value = %s) ) GROUP BY " . $wpdb->users . ".ID ORDER BY " . $wpdb->users . ".ID ASC LIMIT 0, 1", $meta_key, $meta_val ) );

                                if ( !empty( $user_data_found ) ) {
                                        foreach ( $user_data_found as $user ) {
                                                $this->existing_item_id = $user->ID;
                                                break;
                                        }
                                }
                                unset( $user_data_found );
                        }
                        unset( $meta_key, $meta_val, $user_data );
                }
                unset( $wpie_duplicate_indicator );
        }

        private function get_valid_roles( $userRoles = [] ) {

                if ( empty( $userRoles ) ) {
                        return [];
                }

                $roles = [];

                $wp_roles = \wp_roles();

                $site_roles = $wp_roles->get_names();

                foreach ( $userRoles as $role ) {

                        if ( empty( trim( $role ) ) ) {
                                continue;
                        }
                        $role = trim( $role );

                        $site_role = "";

                        foreach ( $site_roles as $key => $name ) {

                                if (
                                        $role === $key ||
                                        $role === $name ||
                                        strtolower( trim( $role ) ) === strtolower( trim( $key ) ) ||
                                        strtolower( trim( $role ) ) === strtolower( trim( $name ) )
                                ) {
                                        $site_role = $key;
                                        break;
                                }
                        }
                        if ( !empty( $site_role ) ) {
                                $roles[] = $site_role;
                        }
                }

                unset( $wp_roles, $site_roles );

                return $roles;
        }

}

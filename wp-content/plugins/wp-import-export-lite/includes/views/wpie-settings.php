<?php
if ( !defined( 'ABSPATH' ) )
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );

global $wp_roles;

$user_roles = $wp_roles->get_names();

if ( isset( $user_roles[ 'administrator' ] ) ) {
        unset( $user_roles[ 'administrator' ] );
}
$templates = array();

if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-common-action.php' ) ) {

        require_once(WPIE_CLASSES_DIR . '/class-wpie-common-action.php');

        $cmm_act = new WPIE_Common_Actions();

        $templates = $cmm_act->wpie_get_templates();

        unset( $cmm_act );
}
$delete_on_uninstall = get_option( "wpie_delete_on_uninstall", 0 );

$wpie_bg_and_cron_processing = get_option( "wpie_bg_and_cron_processing" );

$token = "";

if ( $wpie_bg_and_cron_processing && !empty( $wpie_bg_and_cron_processing ) ) {
        $wpie_bg_and_cron_processing = maybe_unserialize( $wpie_bg_and_cron_processing );

        $cron_method = isset( $wpie_bg_and_cron_processing[ 'method' ] ) ? $wpie_bg_and_cron_processing[ 'method' ] : "";

        $token = isset( $wpie_bg_and_cron_processing[ 'token' ] ) ? ( string ) $wpie_bg_and_cron_processing[ 'token' ] : "";
}

if ( !is_string( $token ) || trim( $token ) === "" ) {

        $cron_method = "wp";

        $token = time();

        $cron_data = maybe_serialize( [ "method" => $cron_method, "token" => $token ] );

        update_option( "wpie_bg_and_cron_processing", $cron_data );
}

$cron_url = add_query_arg( [ 'wpie_cron_token' => $token ], site_url( 'wp-load.php' ) );

?>

<div class="wpie_main_container">
        <div class="wpie_content_header">
                <div class="wpie_content_header_inner_wrapper">
                        <div class="wpie_content_header_title"><?php esc_html_e( 'Settings', 'wp-import-export-lite' ); ?></div>
                        <a class="wpie_btn wpie_btn_primary ms-4" href="https://1.envato.market/1krom" target="_blank">
                            <?php esc_html_e( 'Upgrade Pro', 'wp-import-export-lite' ); ?>
                        </a>
                </div>
        </div>
        <div class="wpie_content_wrapper">
                <div class="wpie_section_wrapper">
                        <div class="wpie_content_data_header">
                                <div class="wpie_content_title"><?php esc_html_e( 'Manage Templates', 'wp-import-export-lite' ); ?></div>
                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                        </div>
                        <div class="wpie_section_content">
                                <div class="wpie_setting_element_wrapper">
                                        <div class="wpie_setting_element_data">
                                                <div class="wpie_setting_element">
                                                        <form class="wpie_template_export" name="wpie_template_export" method="post" action="">
                                                                <input type="hidden" class="wpieSecurity" name="wpieSecurity" value="<?php echo wp_create_nonce( "wpie-security" ); ?>">
                                                                <select class="wpie_content_data_select wpie_template_list" name="wpie_template_list[]" multiple="multiple" data-placeholder="<?php esc_html_e( 'Choose Templates', 'wp-import-export-lite' ); ?>">
                                                                        <?php
                                                                        if ( !empty( $templates ) ) {
                                                                                foreach ( $templates as $data ) {
                                                                                        $id = isset( $data->id ) ? absint( $data->id ) : 0;

                                                                                        $options = isset( $data->options ) ? maybe_unserialize( $data->options ) : array();

                                                                                        $name = isset( $options[ 'wpie_template_name' ] ) ? $options[ 'wpie_template_name' ] : "";
                                                                                        if ( empty( $name ) ) {
                                                                                                $name = isset( $options[ 'template_name' ] ) ? $options[ 'template_name' ] : "";
                                                                                        }
                                                                                        if ( $id > 0 && !empty( $name ) ) {

                                                                                                ?>
                                                                                                <option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $name ); ?></option>
                                                                                                <?php
                                                                                        }
                                                                                }
                                                                        }

                                                                        ?>
                                                                </select>
                                                        </form>
                                                </div>
                                        </div>
                                        <div class="wpie_setting_element_btn">
                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_template_delete_btn">
                                                        <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Delete', 'wp-import-export-lite' ); ?>
                                                </div>
                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_template_export_btn">
                                                        <i class="fas fa-file-export wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Export', 'wp-import-export-lite' ); ?>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_setting_element_wrapper">
                                        <div class="wpie_setting_element_data">
                                                <div class="wpie_setting_element">
                                                        <input type="file" class="wpie_template_file" name="wpie_template_file"/>
                                                </div>
                                        </div>
                                        <div class="wpie_setting_element_btn">
                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_template_import_btn">
                                                        <i class="fas fa-download wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Import', 'wp-import-export-lite' ); ?>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                if ( current_user_can( 'administrator' ) || is_super_admin() ) {

                        ?>
                        <div class="wpie_section_wrapper">
                                <div class="wpie_content_data_header">
                                        <div class="wpie_content_title"><?php esc_html_e( 'Plugin Access Permission', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                                </div>
                                <div class="wpie_section_content">
                                        <div class="wpie_setting_element_wrapper">
                                                <form class="wpie_user_role_frm">
                                                        <div class="wpie_setting_element_only_data">
                                                                <div class="wpie_setting_element">
                                                                        <select class="wpie_content_data_select wpie_role_list" name="wpie_user_role" data-placeholder="<?php esc_attr_e( 'Choose Role', 'wp-import-export-lite' ); ?>">
                                                                                <option value=""><?php esc_html_e( 'Choose Role', 'wp-import-export-lite' ); ?></option>                                   
                                                                                <?php
                                                                                if ( !empty( $user_roles ) ) {
                                                                                        foreach ( $user_roles as $key => $name ) {

                                                                                                ?>
                                                                                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $name ); ?></option>
                                                                                                <?php
                                                                                        }
                                                                                }

                                                                                ?>
                                                                        </select>
                                                                </div>
                                                                <div class="wpie_import_cap_wrapper">
                                                                        <div class="wpie_import_cap_container">
                                                                                <input type="checkbox" class="wpie_checkbox wpie_user_cap wpie_new_export" id="wpie_cap_new_export" name="wpie_cap_new_export" value="1"/>
                                                                                <label for="wpie_cap_new_export" class="wpie_checkbox_label"><?php esc_html_e( 'New Export', 'wp-import-export-lite' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_import_cap_container">
                                                                                <input type="checkbox" class="wpie_checkbox wpie_manage_export wpie_user_cap" id="wpie_cap_manage_export" name="wpie_cap_manage_export" value="1"/>
                                                                                <label for="wpie_cap_manage_export" class="wpie_checkbox_label"><?php esc_html_e( 'Manage Export', 'wp-import-export-lite' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_import_cap_container">
                                                                                <input type="checkbox" class="wpie_checkbox wpie_new_import wpie_user_cap" id="wpie_cap_new_import" name="wpie_cap_new_import" value="1"/>
                                                                                <label for="wpie_cap_new_import" class="wpie_checkbox_label"><?php esc_html_e( 'New Import', 'wp-import-export-lite' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_import_cap_container">
                                                                                <input type="checkbox" class="wpie_checkbox wpie_manage_import wpie_user_cap" id="wpie_cap_manage_import" name="wpie_cap_manage_import" value="1"/>
                                                                                <label for="wpie_cap_manage_import" class="wpie_checkbox_label"><?php esc_html_e( 'Manage Import', 'wp-import-export-lite' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_import_cap_container">
                                                                                <input type="checkbox" class="wpie_checkbox wpie_settings wpie_user_cap" wpie_user_cap id="wpie_cap_settings" name="wpie_cap_settings" value="1"/>
                                                                                <label for="wpie_cap_settings" class="wpie_checkbox_label"><?php esc_html_e( 'Settings', 'wp-import-export-lite' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_import_cap_container">
                                                                                <input type="checkbox" class="wpie_checkbox wpie_extensions wpie_user_cap" id="wpie_cap_ext" name="wpie_cap_ext" value="1"/>
                                                                                <label for="wpie_cap_ext" class="wpie_checkbox_label"><?php esc_html_e( 'Manage Extensions', 'wp-import-export-lite' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_import_cap_container">
                                                                                <input type="checkbox" class="wpie_checkbox wpie_add_shortcode wpie_user_cap" id="wpie_cap_add_shortcode" name="wpie_cap_add_shortcode" value="1"/>
                                                                                <label for="wpie_cap_add_shortcode" class="wpie_checkbox_label"><?php esc_html_e( 'Add Shortcode', 'wp-import-export-lite' ); ?></label>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </form>
                                                <div class="wpie_setting_element_btn">
                                                        <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_save_cap_btn">
                                                                <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Save', 'wp-import-export-lite' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                <?php } ?>
                <div class="wpie_section_wrapper">
                        <div class="wpie_content_data_header">
                                <div class="wpie_content_title"><?php esc_html_e( 'Advance Options', 'wp-import-export-lite' ); ?></div>
                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                        </div>
                        <div class="wpie_section_content">
                                <div class="wpie_setting_element_wrapper">
                                        <div class="wpie_import_cap_container">
                                                <input type="checkbox" class="wpie_checkbox wpie_delete_data_on_unistall" <?php
                                                if ( $delete_on_uninstall == 1 ) {

                                                        ?>checked="checked"<?php } ?> id="wpie_delete_data_on_unistall" name="wpie_delete_data_on_unistall" value="1"/>
                                                <label for="wpie_delete_data_on_unistall" class="wpie_checkbox_label"><?php esc_html_e( 'Delete All Data on plugin uninstall', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_setting_element_btn">
                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_advanced_options_save">
                                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Save', 'wp-import-export-lite' ); ?>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <div class="wpie_section_wrapper">
                        <div class="wpie_content_data_header">
                                <div class="wpie_content_title"><?php esc_html_e( 'Background & Cron Processing', 'wp-import-export-lite' ); ?></div>
                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                        </div>
                        <div class="wpie_section_content">
                                <div class="wpie_setting_element_wrapper">
                                        <div class="wpie_doc_hint_container"><a class="wpie_doc_hint_url" href="<?php echo esc_url( 'https://plugins.vjinfotech.com/wordpress-import-export/article-categories/background-cron-processing/' ); ?>" target="_blank"><?php esc_html_e( 'Click Here', 'wp-import-export-lite' ); ?></a> <?php esc_html_e( 'For More Details', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_radio_input_wrapper wpie_radio_wrapper">
                                                <input type="radio" class="wpie_radio wpie_bg_and_cron_processing" <?php checked( $cron_method, "wp" ) ?> name="wpie_bg_and_cron_processing" id="wpie_bg_and_cron_processing_wp" value="wp"/>
                                                <label for="wpie_bg_and_cron_processing_wp" class="wpie_radio_label"><?php esc_html_e( 'Use WordPress Default Cron & processing', 'wp-import-export-lite' ); ?></label>                                                       
                                        </div>
                                        <div class="wpie_field_mapping_radio_input_wrapper wpie_radio_wrapper">
                                                <input type="radio" class="wpie_radio wpie_bg_and_cron_processing" <?php checked( $cron_method, "external" ) ?> name="wpie_bg_and_cron_processing" id="wpie_bg_and_cron_processing_external" value="external"/>
                                                <label for="wpie_bg_and_cron_processing_external" class="wpie_radio_label"><?php esc_html_e( 'Use Web Host / External Cron & Processing', 'wp-import-export-lite' ); ?></label>
                                                <div class="wpie_radio_container">
                                                        <div class="wpie_cron_url_wrapper">
                                                                <div class="wpie_cron_url_label"><?php esc_html_e( 'Use this single link for ALL schedule and background processing.', 'wp-import-export-lite' ); ?></div> 
                                                                <div class="wpie_cron_url_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_cron_url" readonly="" disabled="disabled" value="<?php echo esc_attr( $cron_url ); ?>"/>
                                                                        <input type="hidden" class="wpie_cron_token" value="<?php echo esc_attr( $token ); ?>"/>
                                                                </div> 
                                                        </div>                                                      
                                                </div>
                                        </div>
                                        <div class="wpie_setting_element_btn">
                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_bg_and_cron_processing_btn">
                                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Save', 'wp-import-export-lite' ); ?>
                                                </div>
                                        </div>
                                        <!--                                        <div class="wpie_setting_element_wrapper">
                                                                                        <div class="wpie_import_cap_container">
                                                                                                <input type="radio" class="wpie_checkbox wpie_bg_and_cron_processing" id="wpie_bg_and_cron_processing_wp" name="wpie_bg_and_cron_processing" value="wp"/>
                                                                                                <label for="wpie_bg_and_cron_processing_wp" class="wpie_radio_label"><?php esc_html_e( 'Delete All Data on plugin uninstall', 'wp-import-export-lite' ); ?></label>
                                                                                        </div>
                                                                                        <div class="wpie_setting_element_btn">
                                                                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_advanced_options_save">
                                                                                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Save', 'wp-import-export-lite' ); ?>
                                                                                                </div>
                                                                                        </div>
                                                                                </div>-->
                                </div>
                        </div>
                </div>
        </div>
        <div class="modal fade wpie_error_model" tabindex="-1" role="dialog"  aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered " role="document">
                        <div class="modal-content wpie_error">
                                <div class="modal-header">
                                        <h5 class="modal-title"><?php esc_html_e( 'ERROR', 'wp-import-export-lite' ); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                        <div class="wpie_error_content"></div>
                                </div>
                                <div class="modal-footer">
                                        <div class="wpie_btn wpie_btn_red wpie_btn_radius " data-bs-dismiss="modal">
                                                <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Ok', 'wp-import-export-lite' ); ?>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
        <div class="wpie_doc_wrapper">
                <div class="wpie_doc_container">
                        <a class="wpie_doc_url" href="<?php echo esc_url( WPIE_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e( 'Support', 'wp-import-export-lite' ); ?></a>
                        <div class="wpie_doc_url_delim">|</div>
                        <a class="wpie_doc_url" href="<?php echo esc_url( WPIE_DOC_URL ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'wp-import-export-lite' ); ?></a>
                </div>
        </div>
        <div class="wpie_loader wpie_hidden">
                <div></div>
                <div></div>
        </div>
        <?php
        unset( $user_roles, $delete_on_uninstall, $templates );
        
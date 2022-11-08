<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-extensions.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-extensions.php');
}

$wpie_ext = new \wpie\addons\WPIE_Extension();

$wpie_ext_data = isset( $_GET[ 'wpie_ext' ] ) ? wpie_sanitize_field( $_GET[ 'wpie_ext' ] ) : "";

$wpie_import_ext = $wpie_ext->wpie_get_import_extension();

$ext_data = isset( $wpie_import_ext[ $wpie_ext_data ] ) ? $wpie_import_ext[ $wpie_ext_data ] : array();
?>
<div class="wpie_main_container">
        <div class="wpie_content_header">
                <div class="wpie_content_header_inner_wrapper">
                        <div class="wpie_content_header_title"><?php echo isset( $ext_data[ 'name' ] ) ? esc_html( $ext_data[ 'name' ] ) : ""; ?></div>
                </div>
        </div>
        <div class="wpie_content_wrapper">
            <?php
            $settings = isset( $ext_data[ 'settings' ] ) ? $ext_data[ 'settings' ] : "";

            if ( ! empty( $settings ) && file_exists( $settings ) ) {
                    ?>
                        <div class="wpie_section_wrapper">
                                <div class="wpie_content_data_header  wpie_section_wrapper_selected">
                                        <div class="wpie_content_title"><?php esc_html_e( 'Settings', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                                </div>
                                <div class="wpie_section_content wpie_show">
                                        <form class="wpie_ext_settings_frm">
                                                <input type="hidden" name="wpie_ext" value="<?php echo esc_attr( $wpie_ext_data ); ?>"/>
                                                <div class="wpie_content_data_wrapper">
                                                    <?php
                                                    include($settings);
                                                    ?>
                                                        <div class="wpie_ext_save_wrapper">
                                                                <div class="wpie_btn wpie_btn_primary wpie_ext_save_data">
                                                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Save', 'wp-import-export-lite' ); ?>
                                                                </div>
                                                        </div>
                                                </div>
                                        </form>

                                </div>
                        </div>
                        <?php
                }
                ?>
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
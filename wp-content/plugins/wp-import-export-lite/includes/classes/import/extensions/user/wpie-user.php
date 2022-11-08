<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

?>
<div class="wpie_upload_outer_container" >
        <input type="hidden" value="" class="wpie_upload_final_file" />
        <div  class="wpie_existing_file_upload_container">
                <div class="wpie_element_full_wrapper">
                        <div class="wpie_element_title"><?php esc_html_e( 'File URL', 'wp-import-export-lite' ); ?></div>
                        <div class="wpie_element_data">
                                <input class="wpie_content_data_input wpie_file_upload_url" type="text" name="" value="" placeholder="http://example.com/sample.csv">
                        </div>
                </div>
                <div class="wpie_download_btn_wrapper">
                        <div class="wpie_btn wpie_btn_primary wpie_url_upload_btn">
                                <i class="fas fa-download wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Download', 'wp-import-export-lite' ); ?>
                        </div>
                </div>
        </div>
        <div class="wpie_file_list_wrapper"></div>
        <div class="wpie_excel_sheets_wrapper"></div>
</div>
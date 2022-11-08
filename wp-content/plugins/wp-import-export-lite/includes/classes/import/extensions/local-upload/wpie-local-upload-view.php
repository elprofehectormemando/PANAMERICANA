<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

?>

<div class="wpie_upload_outer_container">
        <div id="wpie_upload_container" class="wpie_upload_container" >
                <div id="wpie_upload_drag_drop" class="wpie_upload_drag_drop">
                        <div class="wpie_upload_file_label"><?php esc_html_e( 'Drop file here', 'wp-import-export-lite' ); ?></div>
                        <div class="wpie_upload_file_label_small"><?php esc_html_e( 'OR', 'wp-import-export-lite' ); ?></div>
                        <div class="wpie_upload_file_btn">
                                <input id="plupload_browse_button" type="button" value="<?php esc_attr_e( 'Select Files', 'wp-import-export-lite' ); ?>" class="wpie_btn wpie_btn_primary wpie_btn_radius wpie_plupload_browse_button" />
                        </div>
                </div>
                <input type="hidden" value="" class="wpie_upload_drag_drop_data" wpie_status="processing"/>
        </div>
        <div class="wpie_uploaded_file_list_wrapper">
                <div class="wpie_local_uploaded_filename_wrapper">
                        <div class="wpie_local_uploaded_filename_label"><?php esc_html_e( 'Uploading', 'wp-import-export-lite' ); ?></div>
                        <div class="wpie_local_uploaded_file_sep">-</div>
                        <div class="wpie_local_uploaded_filename"></div>
                </div>
                <div class="progress wpie_import_upload_process">
                        <div class="progress-bar progress-bar-striped progress-bar-animated wpie_import_upload_process_per" role="progressbar" style="" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>

        </div>
        <div class="wpie_file_list_wrapper"></div>
        <div class="wpie_excel_sheets_wrapper"></div>
        <div class="wpie_text_format_list_wrapper">
                <div class="wpie_text_format_list_header"><?php esc_html_e( 'Data Format', 'wp-import-export-lite' ); ?></div>
                <div class="wpie_text_format_list_container">
                        <div class="wpie_text_format_list_element" >
                                <input class="wpie_text_format_list  wpie_radio wpie_text_format_list_csv"  type="radio" id="wpie_text_format_list_csv" value="csv" name="wpie_text_format_list" checked="checked">
                                <label class="wpie_text_format_list_data wpie_radio_label"  for="wpie_text_format_list_csv"><?php esc_html_e( 'CSV', 'wp-import-export-lite' ); ?></label>
                        </div>
                        <div class="wpie_text_format_list_element" >
                                <input class="wpie_text_format_list  wpie_radio wpie_text_format_list_json"  type="radio" id="wpie_text_format_list_json" value="json" name="wpie_text_format_list">
                                <label class="wpie_text_format_list_data wpie_radio_label"  for="wpie_text_format_list_json"><?php esc_html_e( 'JSON', 'wp-import-export-lite' ); ?></label>
                        </div>
                        <div class="wpie_text_format_list_element" >
                                <input class="wpie_text_format_list  wpie_radio wpie_text_format_list_xml"  type="radio" id="wpie_text_format_list_xml" value="xml" name="wpie_text_format_list">
                                <label class="wpie_text_format_list_data wpie_radio_label"  for="wpie_text_format_list_xml"><?php esc_html_e( 'XML', 'wp-import-export-lite' ); ?></label>
                        </div>
                </div>
        </div>
</div>
<?php
global $wpdb;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php' ) ) {
        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php');
}
$wpie_export = new \wpie\export\WPIE_Export();

$export_type = $wpie_export->get_export_type();

$wpie_taxonomies_list = $wpie_export->wpie_get_taxonomies();

$attribute_taxonomies = null;
if ( class_exists( "WooCommerce" ) ) {
        $attribute_taxonomies = $wpie_export->get_attribute_list();
}
unset( $wpie_export );

$advance_options_files = apply_filters( 'wpie_export_advance_option_files', array() );

$extension_html_files = apply_filters( 'wpie_add_export_extension_files', array() );

$extension_process_btn = apply_filters( 'wpie_add_export_extension_process_btn', array() );

$wpie_remote_data = apply_filters( 'wpie_get_export_remote_locations', array() );

?>
<div class="wpie_main_container wpie_export_init_step">
        <div class="wpie_content_header">
                <div class="wpie_content_header_inner_wrapper">
                        <div class="wpie_content_header_title"><?php esc_html_e( 'New Export', 'wp-import-export-lite' ); ?></div>
                        <a class="wpie_btn wpie_btn_primary ms-4" href="https://1.envato.market/1krom" target="_blank">
                            <?php esc_html_e( 'Upgrade Pro', 'wp-import-export-lite' ); ?>
                        </a>
                        <div class="wpie_total_records_wrapper">
                                <div class="wpie_total_record_text"><?php esc_html_e( 'Total Records Found', 'wp-import-export-lite' ); ?></div>
                                <div class="wpie_total_records_outer"><span class="wpie_total_records wpie_total_records_container"></span></div>
                        </div>
                        <div class="wpie_fixed_header_button">
                                <div class="wpie_btn wpie_btn_primary wpie_export_preview_btn">
                                        <i class="fas fa-eye wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Preview', 'wp-import-export-lite' ); ?>
                                </div>
                                <div class="wpie_btn wpie_btn_primary wpie_migrate_export_data_btn">
                                        <i class="fas fa-exchange-alt wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Export With Settings For Import', 'wp-import-export-lite' ); ?>
                                </div>
                                <div class="wpie_btn wpie_btn_primary wpie_export_data_btn">
                                        <i class="fas fa-file-export wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Export', 'wp-import-export-lite' ); ?>
                                </div>
                        </div>
                </div>
        </div>
        <div class="wpie_content_wrapper">
                <form class="wpie_export_frm" method="post" action="#">
                        <input type="hidden" name="wpie_total_filter_records" value="0" class="wpie_total_filter_records">
                        <input type="hidden" name="fields_data" value="" class="wpie_export_fields_data">
                        <input type="hidden" name="wpie_filter_rule" value="" class="wpie_filter_rule">
                        <input type="hidden" name="wpie_export_condition" value="" class="wpie_export_condition">
                        <div class="wpie_content_data_choose">
                                <div class="wpie_section_wrapper wpie_choose_export_container">
                                        <div class="wpie_content_data_header wpie_section_wrapper_selected">
                                                <div class="wpie_content_title"><?php esc_html_e( 'Choose what to export', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                                        </div>
                                        <div class="wpie_section_content" style="display: block;">
                                                <div class="wpie_content_data_wrapper">
                                                        <select class="wpie_content_data_select wpie_export_type_select" name="wpie_export_type">
                                                                <option value=""><?php esc_html_e( 'Select Export Type', 'wp-import-export-lite' ); ?></option>
                                                                <optgroup label="<?php esc_html_e( 'Free Export Types', 'wp-import-export-lite' ); ?>" >
                                                                <?php if ( !empty( $export_type ) ) {

                                                                        ?>                       
                                                                            <?php
                                                                            foreach ( $export_type as $key => $label ) {
                                                                                    if ( in_array( $key, [ 'product', 'product_reviews', 'product_attributes', 'shop_order', 'shop_coupon', 'shop_customer' ] ) ) {
                                                                                            continue;
                                                                                    }

                                                                                ?>
                                                                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
                                                                        <?php } ?>
                                                                <?php } ?>
                                                                </optgroup>
                                                                <optgroup label="<?php esc_html_e( 'Premium Export Types', 'wp-import-export-lite' ); ?>" >
                                                                        <option value="product"><?php esc_html_e( 'WooCommerce Products', 'wp-import-export-lite' ); ?></option>
                                                                        <option value="product_reviews"><?php esc_html_e( 'Product Reviews', 'wp-import-export-lite' ); ?></option>
                                                                        <option value="product_attributes"><?php esc_html_e( 'Product Attributes', 'wp-import-export-lite' ); ?></option>
                                                                        <option value="shop_order"><?php esc_html_e( 'WooCommerce Orders', 'wp-import-export-lite' ); ?></option>
                                                                        <option value="shop_coupon"><?php esc_html_e( 'WooCommerce Coupons', 'wp-import-export-lite' ); ?></option>
                                                                        <option value="shop_coupon"><?php esc_html_e( 'WooCommerce Customers', 'wp-import-export-lite' ); ?></option>
                                                                </optgroup>
                                                        </select>
                                                </div>
                                                <div class="wpie_content_data_wrapper wpie_taxonomies_types_wrapper wpie_sub_type_wrapper">
                                                        <select class="wpie_content_data_select wpie_taxonomies_types_select" name="wpie_taxonomy_type">
                                                                <option value=""><?php esc_html_e( 'Select Taxonomy', 'wp-import-export-lite' ); ?></option>
                                                                <?php if ( !empty( $wpie_taxonomies_list ) ) {

                                                                        ?>                       
                                                                        <?php foreach ( $wpie_taxonomies_list as $slug => $name ) {

                                                                                ?>
                                                                                <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></option>
                                                                        <?php } ?>
                                                                <?php } ?>
                                                        </select>
                                                </div>
                                                <div class="wpie_content_data_wrapper wpie_attribute_taxonomies_wrapper wpie_sub_type_wrapper">
                                                        <select class="wpie_content_data_select wpie_attribute_taxonomies_select" data-placeholder="<?php esc_html_e( 'All Attributes', 'wp-import-export-lite' ); ?>" name="wpie_attribute_taxonomy[]"  multiple="multiple">
                                                                <?php if ( !empty( $attribute_taxonomies ) ) {

                                                                        ?>                       
                                                                        <?php foreach ( $attribute_taxonomies as $attribute ) {

                                                                                ?>
                                                                                <option value="<?php echo isset( $attribute->attribute_name ) ? esc_attr( $attribute->attribute_name ) : ""; ?>" ><?php echo isset( $attribute->attribute_label ) ? esc_html( $attribute->attribute_label ) : ""; ?></option>
                                                                        <?php } ?>
                                                                <?php } ?>
                                                        </select>
                                                        <div class="wpie_export_default_hint"><?php esc_html_e( 'Default : All Attributes.', 'wp-import-export-lite' ); ?></div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div class="wpie_filter_and_option_header"><?php esc_html_e( 'Apply Filters & Options', 'wp-import-export-lite' ); ?></div>
                        <div class="wpie_content_data">

                                <div class="wpie_section_wrapper wpie_filter_section_wrapper">
                                        <div class="wpie_content_data_header">
                                                <div class="wpie_content_title"><?php esc_html_e( 'Add filtering options', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                                        </div>
                                        <div class="wpie_section_content wpie_field_selection_wrapper">
                                                <div class="wpie_content_data_wrapper">
                                                        <div class="wpie_content_data_rule_header_wrapper">
                                                                <div class="wpie_content_data_rule_header"><?php esc_html_e( 'Element', 'wp-import-export-lite' ); ?></div>
                                                                <div class="wpie_content_data_rule_header"><?php esc_html_e( 'Rule', 'wp-import-export-lite' ); ?></div>
                                                                <div class="wpie_content_data_rule_header"><?php esc_html_e( 'Value', 'wp-import-export-lite' ); ?></div>
                                                                <div class="wpie_content_data_rule_btn_header"></div>
                                                        </div>
                                                        <div class="wpie_content_data_rule_wrapper ">
                                                                <div class="wpie_content_data_rule">
                                                                        <select class="wpie_content_data_select wpie_content_data_rule_fields">
                                                                                <option value=""><?php esc_html_e( 'Select Element', 'wp-import-export-lite' ); ?></option>
                                                                        </select>
                                                                </div>
                                                                <div class="wpie_content_data_rule wpie_content_data_rule_condition">
                                                                        <select class="wpie_content_data_select wpie_content_data_rule_select">
                                                                                <option value=""><?php esc_html_e( 'Select Rule', 'wp-import-export-lite' ); ?></option>
                                                                        </select>
                                                                </div>
                                                                <div class="wpie_content_data_rule">
                                                                        <input type="text" class="wpie_content_data_input wpie_content_data_rule_value" value=""/>
                                                                        <div class="wpie_value_hints_container">
                                                                                <div class="wpie_value_hints">
                                                                                        <?php esc_html_e( 'Dynamic date allowed', 'wp-import-export-lite' ); ?>
                                                                                </div>
                                                                                <div class="wpie_value_hints">
                                                                                        <?php esc_html_e( 'Example :', 'wp-import-export-lite' ); ?> yesterday, today, tomorrow...
                                                                                </div>
                                                                                <div class="wpie_value_hints">
                                                                                        <?php esc_html_e( 'For more click', 'wp-import-export-lite' ); ?> <a target="_blank" href="<?php echo esc_url( 'https://www.php.net/manual/en/datetime.formats.relative.php' ); ?>"><?php esc_html_e( 'here', 'wp-import-export-lite' ); ?> </a>
                                                                                </div>                                        
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_content_data_rule_btn_wrapper"> 
                                                                        <a class="wpie_icon_btn  wpie_save_add_rule_btn">
                                                                                <i class="fas fa-plus wpie_icon_btn_icon " aria-hidden="true"></i>
                                                                        </a>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_content_added_data_rule_wrapper">
                                                                <table class="wpie_content_added_data_rule table table-bordered">

                                                                </table>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_section_wrapper">
                                        <div class="wpie_content_data_header">
                                                <div class="wpie_content_title"><?php esc_html_e( 'Choose Fields', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                                        </div>
                                        <div class="wpie_section_content">
                                                <div class="wpie_content_data_wrapper">
                                                        <div class="wpie_export_fields_hint"><?php esc_html_e( 'Use click on text for edit field. Use Drag and Drop for change any position', 'wp-import-export-lite' ); ?></div>
                                                        <div class="wpie_field_selection"></div>
                                                        <div class="wpie_fields_selection_btn_wrapper">
                                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_fields_add_new" >
                                                                        <i class="fas fa-plus wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Add', 'wp-import-export-lite' ); ?>
                                                                </div>
                                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_add_bulk_fields">
                                                                        <i class="fas fa-plus wpie_general_btn_icon " aria-hidden="true"></i></i><?php esc_html_e( 'Add Bulk', 'wp-import-export-lite' ); ?>
                                                                </div>
                                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_fields_add_all">
                                                                        <i class="fas fa-plus wpie_general_btn_icon " aria-hidden="true"></i></i><?php esc_html_e( 'Add All', 'wp-import-export-lite' ); ?>
                                                                </div>
                                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_fields_remove_all">
                                                                        <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Remove All', 'wp-import-export-lite' ); ?>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>

                                <div class="wpie_section_wrapper">
                                        <div class="wpie_content_data_header">
                                                <div class="wpie_content_title"><?php esc_html_e( 'Advanced Options', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                                        </div>
                                        <div class="wpie_section_content">
                                                <div class="wpie_content_data_wrapper">
                                                        <table class="wpie_content_data_tbl table table-bordered">
                                                                <tr>
                                                                        <td >
                                                                                <div class="wpie_options_data">
                                                                                        <div class="wpie_options_data_title"><?php esc_html_e( 'Export File Type', 'wp-import-export-lite' ); ?></div>
                                                                                        <div class="wpie_options_data_content">
                                                                                                <select class="wpie_content_data_select wpie_export_file_type" name="wpie_export_file_type">
                                                                                                        <option value=""><?php esc_html_e( 'Choose Export file type', 'wp-import-export-lite' ); ?></option>
                                                                                                        <option value="csv"><?php esc_html_e( 'CSV', 'wp-import-export-lite' ); ?></option>
                                                                                                        <option value="xls"><?php esc_html_e( 'XLS', 'wp-import-export-lite' ); ?></option>
                                                                                                        <option value="xlsx"><?php esc_html_e( 'XLSX', 'wp-import-export-lite' ); ?></option>
                                                                                                        <option value="xml"><?php esc_html_e( 'XML', 'wp-import-export-lite' ); ?></option>
                                                                                                        <option value="ods"><?php esc_html_e( 'ODS', 'wp-import-export-lite' ); ?></option>
                                                                                                        <option value="json"><?php esc_html_e( 'JSON', 'wp-import-export-lite' ); ?></option>
                                                                                                </select>
                                                                                                <div class="wpie_export_default_hint"><?php esc_html_e( 'Default : CSV', 'wp-import-export-lite' ); ?></div>
                                                                                        </div>
                                                                                </div>
                                                                        </td>
                                                                        <td>
                                                                                <div class="wpie_options_data wpie_csv_field_separator_wrapper">
                                                                                        <div class="wpie_options_data_title"><?php esc_html_e( 'Field Separator', 'wp-import-export-lite' ); ?></div>
                                                                                        <div class="wpie_options_data_content">
                                                                                                <input type="text" class="wpie_content_data_input wpie_csv_field_separator" value="," name="wpie_csv_field_separator"/>
                                                                                                <div class="wpie_export_default_hint"><?php esc_html_e( 'Default : , (Comma)', 'wp-import-export-lite' ); ?></div>
                                                                                        </div>
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                                <tr>
                                                                        <td>
                                                                                <div class="wpie_options_data">
                                                                                        <div class="wpie_options_data_title"><?php esc_html_e( 'Export File Name', 'wp-import-export-lite' ); ?></div>
                                                                                        <div class="wpie_options_data_content">
                                                                                                <input type="text" class="wpie_content_data_input wpie_export_file_name" value="" name="wpie_export_file_name"/>
                                                                                                <div class="wpie_export_default_hint"><?php esc_html_e( 'Default : Auto Generated', 'wp-import-export-lite' ); ?></div>
                                                                                        </div>
                                                                                </div>
                                                                        </td>
                                                                        <td>
                                                                                <div class="wpie_options_data">
                                                                                        <div class="wpie_options_data_title"><?php esc_html_e( 'Records Per iteration', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "WP Import Export must be able to process this many records in less than your server's timeout settings. If your export fails before completion, to troubleshoot you should lower this number.", "wp-import-export-lite" ); ?>"></i></div>
                                                                                        <div class="wpie_options_data_content">
                                                                                                <input type="text" class="wpie_content_data_input wpie_records_per_iteration" value="50" name="wpie_records_per_iteration"/>
                                                                                                <div class="wpie_export_default_hint"><?php esc_html_e( 'Default : 50', 'wp-import-export-lite' ); ?></div>
                                                                                        </div>
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                                <tr>
                                                                        <td>
                                                                                <div class="wpie_options_data">
                                                                                        <div class="wpie_options_data_title"><?php esc_html_e( 'File path for extra copy in WordPress upload directory', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php echo esc_attr( __( "Enter relative path to", "wp-import-export-lite" ) . " " . WPIE_SITE_UPLOAD_DIR . " " . __( "Enter only path that not include file name. it's useful when you sync any export data with import. Path folders must be exist", "wp-import-export-lite" ) ); ?>"></i></div>
                                                                                        <div class="wpie_options_data_content">
                                                                                                <input type="text" class="wpie_content_data_input extra_copy_path" value="" name="extra_copy_path"/>
                                                                                                <div class="wpie_export_default_hint"><?php esc_html_e( 'Default : empty', 'wp-import-export-lite' ); ?></div>
                                                                                        </div>
                                                                                </div>
                                                                        </td>
                                                                        <td>
                                                                                <div class="wpie_options_data">
                                                                                        <div class="wpie_options_data_title"><?php esc_html_e( 'Include BOM', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The BOM will help some programs like Microsoft Excel read your export file if it includes non-English characters.", "wp-import-export-lite" ); ?>"></i></div>
                                                                                        <div class="wpie_options_data_content">
                                                                                                <input type="checkbox" class="wpie_export_include_bom_chk wpie_checkbox wpie_export_include_bom" id="wpie_export_include_bom" name="wpie_export_include_bom" value="1"/>
                                                                                                <label for="wpie_export_include_bom" class="wpie_options_data_title_email wpie_checkbox_label"><?php esc_html_e( 'Include BOM in export file', 'wp-import-export-lite' ); ?></label>

                                                                                        </div>
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                                <tr class="wpie_skip_empty_nodes_wrapper">
                                                                        <td colspan="2">
                                                                                <div class="wpie_options_data">
                                                                                        <div class="wpie_options_data_content">
                                                                                                <input type="checkbox" class="wpie_export_include_bom_chk wpie_checkbox wpie_skip_empty_nodes" id="wpie_skip_empty_nodes" name="wpie_skip_empty_nodes" value="1" checked="checked"/>
                                                                                                <label for="wpie_skip_empty_nodes" class="wpie_options_data_title_email wpie_checkbox_label"><?php esc_html_e( 'Do not add Empty nodes in xml file', 'wp-import-export-lite' ); ?></label>
                                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Plugin will not add empty value nodes", "wp-import-export-lite" ); ?>"></i>
                                                                                        </div>
                                                                                </div>
                                                                        </td>

                                                                </tr>
                                                                <?php
                                                                if ( !empty( $advance_options_files ) ) {

                                                                        $temp = 0;

                                                                        foreach ( $advance_options_files as $adv_options ) {

                                                                                if ( $temp % 2 == 0 ) {

                                                                                        ?>
                                                                                        <tr class="wpie_advance_options_row">
                                                                                                <?php
                                                                                        }
                                                                                        if ( file_exists( $adv_options ) ) {
                                                                                                include $adv_options;
                                                                                        }
                                                                                        if ( $temp % 2 == 0 ) {

                                                                                                ?>
                                                                                        </tr>
                                                                                        <?php
                                                                                }

                                                                                $temp++;
                                                                        }
                                                                }

                                                                ?>

                                                        </table>
                                                </div>
                                        </div>
                                </div>
                                <?php
                                if ( !empty( $extension_html_files ) ) {
                                        foreach ( $extension_html_files as $ext_html_file ) {
                                                if ( file_exists( $ext_html_file ) ) {
                                                        include $ext_html_file;
                                                }
                                        }
                                }

                                ?>
                        </div>
                        <div class="wpie_export_sidebar">
                                <div class="wpie_section_wrapper">
                                        <div class="wpie_content_data_header wpie_section_wrapper_selected">
                                                <div class="wpie_content_title"><?php esc_html_e( 'Load Settings', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                                        </div>
                                        <div class="wpie_section_content" style="display: block;">
                                                <div class="wpie_load_template_wrapper">
                                                        <div class="wpie_load_template_label"><?php esc_html_e( 'From Saved Settings', 'wp-import-export-lite' ); ?></div>
                                                        <div class="wpie_content_data_wrapper wpie_template_list_wrapper">
                                                                <select class="wpie_content_data_select wpie_template_list_select" name="wpie_template_list">
                                                                        <option value=""><?php esc_html_e( 'Select Setting', 'wp-import-export-lite' ); ?></option>
                                                                </select>
                                                        </div>
                                                        <div class="wpie_update_template_btn_wrapper"> 
                                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_update_template_btn">
                                                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Update', 'wp-import-export-lite' ); ?>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_load_template_wrapper">
                                                        <div class="wpie_load_template_label"><?php esc_html_e( 'From Exports', 'wp-import-export-lite' ); ?></div>
                                                        <div class="wpie_content_data_wrapper wpie_export_settings_list_wrapper">
                                                                <select class="wpie_content_data_select wpie_export_settings_list_select" name="wpie_export_settings_list">
                                                                        <option value=""><?php esc_html_e( 'Select Setting', 'wp-import-export-lite' ); ?></option>
                                                                </select>
                                                        </div>                                                      
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_section_wrapper">
                                        <div class="wpie_content_data_header wpie_section_wrapper_selected">
                                                <div class="wpie_content_title"><?php esc_html_e( 'Save Setting', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                                        </div>
                                        <div class="wpie_section_content" style="display: block;">
                                                <div class="wpie_load_template_label"><?php esc_html_e( 'Setting Name', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_content_data_wrapper ">
                                                        <input type="text" class="wpie_content_data_input wpie_export_template_name" value="" name="wpie_template_name"/>
                                                </div>
                                                <div class="wpie_save_template_btn_wrapper"> 
                                                        <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_save_template_btn">
                                                                <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Save', 'wp-import-export-lite' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </form>
        </div>
        <form class="wpie_file_download_action" method="post" action="#">
                <input type="hidden" class="wpie_download_export_id" name="wpie_download_export_id" value="0">
                <input type="hidden" class="wpieSecurity" name="wpieSecurity" value="<?php echo wp_create_nonce( "wpie-security" );?>">
        </form>
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
<!-- Modal -->
<div class="modal fade wpie_field_editor_model" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title"><?php esc_html_e( 'Export Field Editor', 'wp-import-export-lite' ); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                                <div class="wpie_export_field_editor_wrapper">
                                        <div class="wpie_export_field_editor_container">
                                                <div class="wpie_export_field_editor_title"><?php esc_html_e( 'Field Name', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_export_field_editor_data_wrapper"><input type="text" class="wpie_content_data_input wpie_field_editor_data" value=""/></div>
                                        </div>
                                        <div class="wpie_export_field_editor_container">
                                                <div class="wpie_export_field_editor_title"><?php esc_html_e( 'Field value', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_export_field_editor_data_wrapper wpie_content_data_wrapper">
                                                        <select class="wpie_content_data_select  wpie_content_data_field_list">
                                                        </select>
                                                </div>
                                        </div>
                                        <div class="wpie_export_field_editor_container wpie_field_editor_date_field_wrapper">
                                                <div class="wpie_export_field_editor_title"><?php esc_html_e( 'Date Format', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_export_field_editor_data_wrapper wpie_content_data_wrapper">
                                                        <select class="wpie_content_data_select  wpie_field_editor_date_field">
                                                                <option value="unix"><?php esc_html_e( 'UNIX timestamp - PHP time()', 'wp-import-export-lite' ); ?></option>
                                                                <option value="php" selected="selected"><?php esc_html_e( 'Natural Language PHP date()', 'wp-import-export-lite' ); ?></option>
                                                        </select>
                                                        <div class="wpie_field_editor_date_field_format_wrapper">
                                                                <input type="text" class="wpie_content_data_input wpie_field_editor_date_field_format" value="" placeholder="<?php esc_attr_e( 'Y-m-d', 'wp-import-export-lite' ); ?>"/>
                                                                <div class="wpie_export_default_hint"><?php esc_html_e( 'Default : Site Date Format', 'wp-import-export-lite' ); ?></div>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="wpie_export_field_editor_container">
                                                <div class="wpie_export_field_editor_other_data">

                                                        <div class="wpie_export_php_fun_wrapper">
                                                                <input type="checkbox" class="wpie_checkbox wpie_export_php_fun" id="wpie_export_php_fun" name="wpie_export_php_fun" value="1"/>
                                                                <label for="wpie_export_php_fun" class="wpie_checkbox_label"><?php esc_html_e( 'Export the value returned by a PHP function', 'wp-import-export-lite' ); ?></label>
                                                        </div>
                                                        <div class="wpie_export_php_fun_inner_wrapper">
                                                                <span>&lt;?php </span>
                                                                <span><input type="text" class="wpie_content_data_small_input wpie_export_php_fun_data" id="wpie_export_php_fun_data" name="wpie_export_php_fun_data" value=""/></span>
                                                                <span> ( $value ); ?&gt;</span>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_export_cancel_field_btn" data-bs-dismiss="modal">
                                        <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Cancel', 'wp-import-export-lite' ); ?>
                                </div>
                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_export_save_field_btn">
                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Save', 'wp-import-export-lite' ); ?>
                                </div>
                        </div>
                </div>
        </div>
</div>
<div class="modal fade wpie_bulk_fields_model" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title"><?php esc_html_e( 'Add Fields', 'wp-import-export-lite' ); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                                <div class="wpie_export_field_editor_wrapper">
                                        <div class="wpie_export_field_editor_container">
                                                <div class="wpie_export_field_editor_title"><?php esc_html_e( 'Select Fields', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_export_fields_hint"><?php esc_html_e( 'Use Ctrl + Click to Select Multiple Fields', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_export_field_editor_data_wrapper wpie_content_data_wrapper">
                                                        <select class="wpie_content_data_select wpie_bulk_fields" multiple="multiple">
                                                        </select>
                                                </div>
                                        </div>                   
                                </div>
                        </div>
                        <div class="modal-footer">
                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_cancel_bulk_field_btn" data-bs-dismiss="modal">
                                        <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Cancel', 'wp-import-export-lite' ); ?>
                                </div>
                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_add_bulk_field_btn">
                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Add', 'wp-import-export-lite' ); ?>
                                </div>
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
<div class="modal fade wpie_strict_error_model" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content wpie_error">
                        <div class="modal-header">
                                <h5 class="modal-title"><?php esc_html_e( 'Permission Required', 'wp-import-export-lite' ); ?></h5>                            
                        </div>
                        <div class="modal-body">
                                <div class="wpie_strict_error_content"></div>
                        </div>                        
                </div>
        </div>
</div>
<div class="modal fade wpie_preview_model" tabindex="-1"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title"><?php esc_html_e( 'Preview', 'wp-import-export-lite' ); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                                <div class="wpie_preview_wrapper">
                                        <table class="wpie_preview table table-bordered" cellspacing="0"></table>
                                </div>
                        </div>
                </div>
        </div>
</div>
<div class="modal fade wpie_export_popup_wrapper" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title wpie_export_proccess_title" ><?php esc_html_e( 'Export In Process', 'wp-import-export-lite' ); ?></h5>
                        </div>
                        <div class="modal-body">
                                <div class="wpie_process_bar_inner_wrapper">
                                        <div class="wpie_export_notice"><?php esc_html_e( 'Exporting may take some time. Please do not close your browser or refresh the page until the process is complete.', 'wp-import-export-lite' ); ?></div>
                                        <div class="progress wpie_export_process">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated wpie_export_process_per" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
                                        </div>
                                        <div class="wpie_export_time_elapsed"><div class="wpie_export_time_elapsed_label"><?php esc_html_e( 'Time Elapsed', 'wp-import-export-lite' ); ?></div><div class="wpie_export_time_elapsed_value">00:00:00</div></div>
                                        <div class="wpie_export_total_records_wrapper">
                                                <div class="wpie_export_total_records">
                                                        <div class="wpie_export_total_records_label"><?php esc_html_e( 'Exported', 'wp-import-export-lite' ); ?></div>
                                                        <div class="wpie_export_total_records_value">0</div>
                                                        <div class="wpie_export_total_records_label"><?php esc_html_e( 'of', 'wp-import-export-lite' ); ?></div>
                                                        <span class="wpie_total_records wpie_export_total_records_count"></span></div>
                                        </div>
                                </div>
                                <?php if ( !empty( $wpie_remote_data ) ) {

                                        ?>
                                        <div class="wpie_remote_export_wrapper">
                                                <div class="wpie_remote_export_title"><?php esc_html_e( 'Send Exported Data To', 'wp-import-export-lite' ); ?></div>
                                                <table class="wpie_remote_export_table table table-borderedtable table-bordered">
                                                        <?php foreach ( $wpie_remote_data as $remote_key => $remote_data ) {

                                                                ?>
                                                                <tr>
                                                                        <td>
                                                                                <input type="checkbox" class="wpie_checkbox" id="wpie_remote_export_dropbox" name="wpie_remote_exported_data[]" value="<?php echo esc_attr( $remote_key ); ?>"/>
                                                                                <label for="wpie_remote_export_dropbox" class="wpie_checkbox_label"><?php echo isset( $remote_data[ 'label' ] ) ? esc_html( $remote_data[ 'label' ] ) : ""; ?></label>
                                                                        </td>
                                                                        <td>
                                                                                <div class="wpie_content_data_wrapper">
                                                                                        <select class="wpie_content_data_select" name="wpie_export_type" multiple="multiple">
                                                                                                <?php $remote_options = isset( $remote_data[ 'data' ] ) ? $remote_data[ 'data' ] : array(); ?>
                                                                                                <?php if ( !empty( $remote_options ) ) {

                                                                                                        ?>                       
                                                                                                        <?php foreach ( $remote_options as $option_key => $option_data ) {

                                                                                                                ?>
                                                                                                                <option value="<?php echo esc_attr( $option_key ); ?>"><?php echo isset( $option_data[ 'wpie_export_ext_label' ] ) ? esc_html( $option_data[ 'wpie_export_ext_label' ] ) : ""; ?></option>
                                                                                                        <?php } ?>
                                                                                                <?php } ?>
                                                                                                <?php unset( $remote_options ); ?>
                                                                                        </select>
                                                                                </div>
                                                                        </td>
                                                                </tr>
                                                        <?php } ?>
                                                </table>
                                                <div class="wpie_send_remote_data_wrapper">
                                                        <div class="wpie_btn wpie_btn_primary wpie_send_remote_data">
                                                                <i class="fas fa-play wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Send', 'wp-import-export-lite' ); ?>
                                                        </div>
                                                </div>
                                        </div>
                                <?php } ?>
                        </div>
                        <div class="modal-footer">
                                <div class="wpie_export_process_option_btn_wrapper">
                                        <div class="wpie_btn wpie_btn_primary wpie_export_process_pause_btn wpie_export_process_btn">
                                                <i class="fas fa-pause wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Pause', 'wp-import-export-lite' ); ?>
                                        </div>
                                        <div class="wpie_btn wpie_btn_primary wpie_export_process_stop_btn wpie_export_process_btn">
                                                <i class="fas fa-stop wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Stop', 'wp-import-export-lite' ); ?>
                                        </div>
                                        <div class="wpie_btn wpie_btn_primary wpie_export_process_resume_btn wpie_export_process_btn">
                                                <i class="fas fa-play wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Resume', 'wp-import-export-lite' ); ?>
                                        </div>
                                        <?php
                                        if ( !empty( $extension_process_btn ) ) {
                                                foreach ( $extension_process_btn as $ext_p_btn ) {
                                                        if ( file_exists( $ext_p_btn ) ) {
                                                                include $ext_p_btn;
                                                        }
                                                }
                                        }

                                        ?>
                                </div>
                                <div class="wpie_export_process_btn_wrapper ">
                                        <div class="wpie_btn wpie_btn_primary wpie_export_process_close_btn wpie_export_process_btn">
                                                <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Close', 'wp-import-export-lite' ); ?>
                                        </div>
                                        <a class="wpie_btn wpie_btn_primary wpie_export_process_btn wpie_export_manage_export_btn" href="<?php echo esc_url( add_query_arg( [ 'page' => 'wpie-manage-export' ], admin_url( "admin.php" ) ) ); ?>">
                                                <i class="fas fa-cogs wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Manage Export', 'wp-import-export-lite' ); ?>
                                        </a>
                                        <div class="wpie_btn wpie_btn_primary wpie_export_download_btn wpie_export_process_btn">
                                                <i class="fas fa-download wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Download', 'wp-import-export-lite' ); ?>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>
<div class="modal fade wpie_process_action" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title wpie_export_proccess_title" ><?php esc_html_e( 'Please Wait', 'wp-import-export-lite' ); ?></h5>
                        </div>
                        <div class="modal-body">
                                <div class="wpie_process_action_msg"><?php esc_html_e( 'Pause Exporting may take some time. Please do not close your browser or refresh the page until the process is complete.', 'wp-import-export-lite' ); ?></div>
                        </div>
                </div>
        </div>
</div>
<div class="modal fade wpie_processing_data" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title wpie_import_proccess_title" ><?php esc_html_e( 'Please Wait until process is complete', 'wp-import-export-lite' ); ?></h5>
                        </div>
                        <div class="modal-body">
                                <div class="wpie_task_list"></div>
                        </div>
                </div>
        </div>
</div>
<?php
unset( $export_type, $wpie_taxonomies_list, $advance_options, $extension_html_files, $extension_process_btn, $wpie_remote_data );

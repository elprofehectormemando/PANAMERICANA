<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

add_filter( 'wpie_import_mapping_fields', "wpie_import_comment_mapping_fields", 10, 2 );

if ( !function_exists( "wpie_import_comment_mapping_fields" ) ) {

        function wpie_import_comment_mapping_fields( $sections = array(), $wpie_import_type = "" ) {

                global $wp_version;

                $uniqid = uniqid();

                $post_types = [];

                if ( \class_exists( '\WooCommerce' ) && $wpie_import_type === "product_reviews" ) {

                        $import_title = __( 'Product Reviews', 'wp-import-export-lite' );

                        $parent_type = __( 'Product', 'wp-import-export-lite' );
                } else {

                        $parent_type  = __( 'Post', 'wp-import-export-lite' );
                        $import_title = __( 'Comment', 'wp-import-export-lite' );

                        $post_types = get_post_types( array( '_builtin' => true ), 'objects' ) + get_post_types( array( '_builtin' => false, 'show_ui' => true ), 'objects' ) + get_post_types( array( '_builtin' => false, 'show_ui' => false ), 'objects' );

                        $hidden_posts = [
                                'attachment',
                                'revision',
                                'nav_menu_item',
                                'shop_webhook',
                                'import_users',
                                'wp-types-group',
                                'wp-types-user-group',
                                'wp-types-term-group',
                                'acf-field',
                                'acf-field-group',
                                'custom_css',
                                'customize_changeset',
                                'oembed_cache',
                                'wp_block',
                                'user_request',
                                'scheduled-action',
                                'product_variation',
                                'shop_order_refund'
                        ];
                        
                        $exclude_types =[];

                        if ( \class_exists( '\WooCommerce' ) ) {
                                $exclude_types = array_merge( [ "product", "shop_order", "shop_coupon" ], $hidden_posts );
                        }
                        foreach ( $post_types as $key => $ct ) {
                                if ( in_array( $key, $exclude_types ) ) {
                                        unset( $post_types[ $key ] );
                                }
                        }

                        unset( $exclude_types );
                }

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper wpie_comment_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title wpie_active" ><?php echo esc_html( __( 'Search Parent', 'wp-import-export-lite' ) . " " . $parent_type ); ?> <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data  wpie_field_mapping_other_option_outer_wrapper wpie_show" >
                                <?php if ( \class_exists( '\WooCommerce' ) && $wpie_import_type === "product_reviews" ) { ?>
                                        <input type="hidden" name="wpie_comment_parent_include_post_types[]" value="product" />
                                <?php } else { ?>
                                        <div class="wpie_field_mapping_container_element">
                                                <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Includes only these post types', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_content_data_wrapper">
                                                        <select class="wpie_content_data_select" name="wpie_comment_parent_include_post_types[]" multiple="multiple">
                                                                <?php if ( !empty( $post_types ) ) { ?>
                                                                        <?php foreach ( $post_types as $key => $value ) { ?>
                                                                                <option value="<?php echo esc_attr( $key ); ?>" selected="selected"><?php echo (isset( $value->labels ) && isset( $value->labels->name )) ? esc_html( $value->labels->name ) : ""; ?></option>
                                                                        <?php } ?>
                                                                <?php } ?>
                                                        </select>
                                                </div>
                                        </div>
                                <?php } ?>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Parent Post', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Post can be matched by Slug, ID, or Title", "wp-import-export-lite" ); ?>"></i></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_parent_post" name="wpie_item_comment_parent_post" value=""/>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $post_fields = ob_get_clean();
                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper wpie_comment_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title" ><?php echo esc_html( $import_title . " " . __( 'Data', 'wp-import-export-lite' ) ); ?> <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data  wpie_field_mapping_other_option_outer_wrapper">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Author', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_author" name="wpie_item_comment_author" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Author Email', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_author_email" name="wpie_item_comment_author_email" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Author URL', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_author_url" name="wpie_item_comment_author_url" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Author IP', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_author_ip wpie_item_comment_author_IP" name="wpie_item_comment_author_ip" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Date', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_date" name="wpie_item_comment_date" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Date GMT', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_date_gmt" name="wpie_item_comment_date_gmt" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Content', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <textarea class="wpie_content_data_textarea wpie_item_comment_content" name="wpie_item_comment_content" ></textarea>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Karma', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_karma" name="wpie_item_comment_karma" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Approved', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_approved" name="wpie_item_comment_approved" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Agent', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_agent" name="wpie_item_comment_agent" value=""/>
                                        </div>
                                </div>
                                <?php if ( $wpie_import_type === "product_reviews" ) { ?>
                                        <input type="hidden" name="wpie_item_comment_type" value="review" />
                                <?php } else { ?>
                                        <div class="wpie_field_mapping_container_element">
                                                <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Type', 'wp-import-export-lite' ); ?></div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_comment_type" name="wpie_item_comment_type" value=""/>
                                                </div>
                                        </div>
                                <?php } ?>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comment Parent', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Parent Comment can be matched by ID or Content", "wp-import-export-lite" ); ?>"></i></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_comment_parent" name="wpie_item_comment_parent" value=""/>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $general_fields = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php echo esc_html( $import_title . " " . __( 'Meta', 'wp-import-export-lite' ) ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_cf_wrapper">
                                        <div class="wpie_field_mapping_radio_input_wrapper wpie_cf_notice_wrapper">
                                                <input type="checkbox" id="wpie_item_not_add_empty" name="wpie_item_not_add_empty" checked="checked" value="1" class="wpie_checkbox wpie_item_not_add_empty">
                                                <label class="wpie_checkbox_label" for="wpie_item_not_add_empty"><?php esc_html_e( "Don't add Empty value fields in database.", 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "it's highly recommended. If custom field value is empty then it skip perticular field and not add to database. it's save memory and increase import speed", "wp-import-export-lite" ); ?>"></i></label>
                                        </div>
                                        <table class="wpie_cf_table">
                                                <thead>
                                                        <tr>
                                                                <th><?php esc_html_e( 'Name', 'wp-import-export-lite' ); ?></th>
                                                                <th><?php esc_html_e( 'Value', 'wp-import-export-lite' ); ?></th>
                                                                <th><?php esc_html_e( 'Options', 'wp-import-export-lite' ); ?></th>
                                                                <th></th>
                                                        </tr>
                                                </thead>
                                                <tbody class="wpie_cf_option_outer_wrapper">
                                                        <tr class="wpie_cf_option_wrapper wpie_data_row" wpie_row_id="<?php echo esc_attr( $uniqid ); ?>">
                                                                <td class="wpie_item_cf_name_wrapper">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_cf_name" value="" name="wpie_item_cf[<?php echo esc_attr( $uniqid ); ?>][name]"/>
                                                                </td>
                                                                <td class="wpie_item_cf_value_wrapper">
                                                                        <div class="wpie_cf_normal_data">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_cf_value" value="" name="wpie_item_cf[<?php echo esc_attr( $uniqid ); ?>][value]"/>
                                                                        </div>
                                                                        <div class="wpie_btn wpie_btn_primary wpie_cf_serialized_data_btn">
                                                                                <i class="fas fa-hand-point-up wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Click to specify', 'wp-import-export-lite' ); ?>
                                                                        </div>
                                                                        <div class="wpie_cf_child_data"></div>
                                                                </td>
                                                                <td class="wpie_item_cf_option_wrapper">
                                                                        <select class="wpie_content_data_select wpie_item_cf_option" name="wpie_item_cf[<?php echo esc_attr( $uniqid ); ?>][option]" >
                                                                                <option value="normal"><?php esc_html_e( 'Normal Data', 'wp-import-export-lite' ); ?></option>
                                                                                <option value="serialized"><?php esc_html_e( 'Serialized Data', 'wp-import-export-lite' ); ?></option>
                                                                        </select>
                                                                </td>
                                                                <td>
                                                                        <div class="wpie_remove_cf_btn"><i class="fas fa-trash wpie_trash_general_btn_icon " aria-hidden="true"></i></div>
                                                                </td>
                                                        </tr>
                                                </tbody>
                                                <tfoot>
                                                        <tr>
                                                                <th colspan="4">
                                                                        <div class="wpie_btn wpie_btn_primary wpie_cf_add_btn">
                                                                                <i class="fas fa-plus wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Add New', 'wp-import-export-lite' ); ?>
                                                                        </div> 
                                                                        <div class="wpie_btn wpie_btn_primary wpie_cf_close_btn">
                                                                                <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Close', 'wp-import-export-lite' ); ?>
                                                                        </div> 
                                                                </th>
                                                </tfoot>
                                        </table>
                                </div>
                        </div>
                </div>
                <?php
                $cf_section = ob_get_clean();

                $sections = array_replace( $sections, array(
                        '100_post_fields'            => $post_fields,
                        '200_general_fields_section' => $general_fields,
                        '300_cf_section'             => $cf_section
                        )
                );

                unset( $post_fields, $general_fields, $cf_section, $post_types );

                return apply_filters( "wpie_pre_comment_field_mapping_section", $sections, $wpie_import_type );
        }

}

add_filter( 'wpie_import_search_existing_item', "wpie_import_comment_search_existing_item", 10, 2 );

if ( !function_exists( "wpie_import_comment_search_existing_item" ) ) {

        function wpie_import_comment_search_existing_item( $sections = "", $wpie_import_type = "" ) {

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_element">
                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Search Existing Item on your site based on...', 'wp-import-export-lite' ); ?></div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic" checked="checked"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_content" value="content"/>
                                <label for="wpie_existing_item_search_logic_content" class="wpie_radio_label"><?php esc_html_e( 'Content', 'wp-import-export-lite' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic wpie_existing_item_search_logic_content_date" name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_content_date" value="content_date"/>
                                <label for="wpie_existing_item_search_logic_content_date" class="wpie_radio_label"><?php esc_html_e( 'Content + Date', 'wp-import-export-lite' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_cf" value="cf"/>
                                <label for="wpie_existing_item_search_logic_cf" class="wpie_radio_label"><?php esc_html_e( 'Comment Meta', 'wp-import-export-lite' ); ?></label>
                                <div class="wpie_radio_container">
                                        <table class="wpie_search_based_on_cf_table">
                                                <thead>
                                                        <tr>
                                                                <th><?php esc_html_e( 'Name', 'wp-import-export-lite' ); ?></th>
                                                                <th><?php esc_html_e( 'Value', 'wp-import-export-lite' ); ?></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                                <td><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_cf_key" name="wpie_existing_item_search_logic_cf_key" value=""/></td>
                                                                <td><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_cf_value" name="wpie_existing_item_search_logic_cf_value" value=""/></td>
                                                        </tr>
                                                </tbody>
                                        </table>
                                </div>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_id" value="id"/>
                                <label for="wpie_existing_item_search_logic_id" class="wpie_radio_label"><?php esc_html_e( 'Comment ID', 'wp-import-export-lite' ); ?></label>
                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_id" name="wpie_existing_item_search_logic_id" value=""/></div>
                        </div>
                </div>
                <?php
                return ob_get_clean();
        }

}

add_filter( 'wpie_import_update_existing_item_fields', "wpie_import_comment_update_existing_item_fields", 10, 2 );

if ( !function_exists( "wpie_import_comment_update_existing_item_fields" ) ) {

        function wpie_import_comment_update_existing_item_fields( $sections = "", $wpie_import_type = "" ) {

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_element">
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_item_update wpie_item_update_all" name="wpie_item_update" id="wpie_item_update_all" value="all"/>
                                <label for="wpie_item_update_all" class="wpie_radio_label"><?php esc_html_e( 'Update all data', 'wp-import-export-lite' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_item_update wpie_item_update_specific" name="wpie_item_update" id="wpie_item_update_specific" value="specific"  checked="checked"/>
                                <label for="wpie_item_update_specific" class="wpie_radio_label"><?php esc_html_e( 'Choose which data to update', 'wp-import-export-lite' ); ?></label>
                                <div class="wpie_radio_container">
                                        <div class="wpie_update_item_all_action"><?php esc_html_e( 'Check/Uncheck All', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_post_id"name="is_update_item_post_id" id="is_update_item_post_id" value="1"/>
                                                <label for="is_update_item_post_id" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Post Id', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_author" name="is_update_item_author" id="is_update_item_author" value="1"/>
                                                <label for="is_update_item_author" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Author', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_author_email" name="is_update_item_author_email" id="is_update_item_author_email" value="1"/>
                                                <label for="is_update_item_author_email" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Author Email', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_author_url" name="is_update_item_author_url" id="is_update_item_author_url" value="1"/>
                                                <label for="is_update_item_author_url" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Author URL', 'wp-import-export-lite' ); ?></label>
                                        </div>

                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_author_ip" name="is_update_item_author_ip" id="is_update_item_author_ip" value="1"/>
                                                <label for="is_update_item_author_ip" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Author IP', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_date" name="is_update_item_date" id="is_update_item_date" value="1"/>
                                                <label for="is_update_item_date" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Date', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_date_gmt" name="is_update_item_date_gmt" id="is_update_item_date_gmt" value="1"/>
                                                <label for="is_update_item_date_gmt" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Date GMT', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_content" name="is_update_item_content" id="is_update_item_content" value="1"/>
                                                <label for="is_update_item_content" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Content', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_karma" name="is_update_item_karma" id="is_update_item_karma" value="1"/>
                                                <label for="is_update_item_karma" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Karma', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_approved" name="is_update_item_approved" id="is_update_item_approved" value="1"/>
                                                <label for="is_update_item_approved" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Approved', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_agent" name="is_update_item_agent" id="is_update_item_agent" value="1"/>
                                                <label for="is_update_item_agent" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Agent', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_type" name="is_update_item_type" id="is_update_item_type" value="1"/>
                                                <label for="is_update_item_type" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Type', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_parent" name="is_update_item_parent" id="is_update_item_parent" value="1"/>
                                                <label for="is_update_item_parent" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Parent', 'wp-import-export-lite' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_cf" name="is_update_item_cf" id="is_update_item_cf" value="1"/>
                                                <label for="is_update_item_cf" class="wpie_checkbox_label"><?php esc_html_e( 'Comment Meta', 'wp-import-export-lite' ); ?></label>
                                                <div class="wpie_checkbox_container">
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_append" checked="checked" name="wpie_item_update_cf" id="wpie_item_update_cf_append" value="append"/>
                                                                <label for="wpie_item_update_cf_append" class="wpie_radio_label"><?php esc_html_e( 'Update all Comment Meta and keep meta if not found in file', 'wp-import-export-lite' ); ?></label>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_all" name="wpie_item_update_cf" id="wpie_item_update_cf_all" value="all"/>
                                                                <label for="wpie_item_update_cf_all" class="wpie_radio_label"><?php esc_html_e( 'Update all Comment Meta and Remove meta if not found in file', 'wp-import-export-lite' ); ?></label>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_includes" name="wpie_item_update_cf" id="wpie_item_update_cf_includes" value="includes"/>
                                                                <label for="wpie_item_update_cf_includes" class="wpie_radio_label"><?php esc_html_e( "Update only these Comment Meta, leave the rest alone", 'wp-import-export-lite' ); ?></label>
                                                                <div class="wpie_radio_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_update_cf_includes_data" name="wpie_item_update_cf_includes_data" value=""/>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_excludes" name="wpie_item_update_cf" id="wpie_item_update_cf_excludes" value="excludes"/>
                                                                <label for="wpie_item_update_cf_excludes" class="wpie_radio_label"><?php esc_html_e( "Leave these fields alone, update all other Comment Meta", 'wp-import-export-lite' ); ?></label>
                                                                <div class="wpie_radio_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_update_cf_excludes_data" name="wpie_item_update_cf_excludes_data" value=""/>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>

                <?php
                return ob_get_clean();
        }

}        
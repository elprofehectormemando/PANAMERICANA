<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}
if ( !function_exists( "wpie_import_get_yoast_seo_taxonomy_tab" ) ) {

        function wpie_import_get_yoast_seo_taxonomy_tab( $wpie_import_type = "" ) {


                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title">
                            <?php esc_html_e( 'Yoast SEO', 'wp-import-export-lite' ); ?>
                                <div class="wpie_layout_header_icon_wrapper">
                                        <i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i>
                                        <i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i>
                                </div>
                        </div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Focus Keywords', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_focuskw" name="wpie_item_wpseo_focuskw" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'SEO Title', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_title" name="wpie_item_wpseo_title" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Meta Description', 'wp-import-export-lite' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_desc" name="wpie_item_wpseo_desc" value=""/>
                                        </div>
                                </div>                                                                
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Keyphrase Synonyms', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Multiple values separated by || Example value1||value2||value3", "wp-import-export-lite" ); ?>"></i></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_keywordsynonyms" name="wpie_item_wpseo_keywordsynonyms" value=""/>
                                        </div>
                                </div>                                                                
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Related keyphrase', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Multiple values separated by || Example value1||value2||value3", "wp-import-export-lite" ); ?>"></i></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_focuskeywords" name="wpie_item_wpseo_focuskeywords" value=""/>
                                        </div>
                                </div>                                                               
                                <div class="wpie_inner_section">
                                        <div class="wpie_inner_section_label"><?php esc_html_e( 'Facebook Options', 'wp-import-export-lite' ); ?><i class="fas fa-chevron-down wpie_inner_section_icon" aria-hidden="true"></i></div>
                                        <div class="wpie_inner_section_container">
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Title', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If you don't want to use the post title for sharing the post on Facebook but instead want another title there, import it here.", "wp-import-export-lite" ); ?>"></i></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_opengraph-title" name="wpie_item_wpseo_opengraph-title" value=""/>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Description', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If you don't want to use the meta description for sharing the post on Facebook but want another description there, write it here.", "wp-import-export-lite" ); ?>"></i></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_opengraph-description" name="wpie_item_wpseo_opengraph-description" value=""/>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Image', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If you want to override the image used on Facebook for this post, import one here. The recommended image size for Facebook is 1200 x 628px.", "wp-import-export-lite" ); ?>"></i></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_opengraph-image wpie_item_wpseo_opengraph-image_url" name="wpie_item_wpseo_opengraph-image" checked="checked" id="wpie_item_wpseo_opengraph-image_url" value="url"/>
                                                                        <label for="wpie_item_wpseo_opengraph-image_url" class="wpie_radio_label"><?php esc_html_e( 'Download images hosted elsewhere', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Image URL with http:// or https://", "wp-import-export-lite" ); ?>"></i></label>
                                                                        <div class="wpie_radio_container">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_opengraph-image_url_data" name="wpie_item_wpseo_opengraph-image_url_data" value=""/>                                                                               
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_opengraph-image wpie_item_wpseo_opengraph-image_media_library" name="wpie_item_wpseo_opengraph-image" id="wpie_item_wpseo_opengraph-image_media_library" value="media_library"/>
                                                                        <label for="wpie_item_wpseo_opengraph-image_media_library" class="wpie_radio_label"><?php esc_html_e( 'Use images currently in Media Library', 'wp-import-export-lite' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_opengraph-image_media_library_data" name="wpie_item_wpseo_opengraph-image_media_library_data" value=""/>                                                                               
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_opengraph-image wpie_item_wpseo_opengraph-image_directory" name="wpie_item_wpseo_opengraph-image"  id="wpie_item_wpseo_opengraph-image_directory" value="directory"/>
                                                                        <label for="wpie_item_wpseo_opengraph-image_directory" class="wpie_radio_label"><?php echo esc_html( __( 'Use images currently uploaded in', 'wp-import-export-lite' ) . " " . WPIE_UPLOAD_TEMP_DIR ); ?> </label>
                                                                        <div class="wpie_radio_container">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_opengraph-image_directory_data" name="wpie_item_wpseo_opengraph-image_directory_data" value=""/>                                                                               
                                                                        </div>
                                                                </div>                                                                                                
                                                        </div>
                                                </div>                                                
                                        </div>
                                </div>                               
                                <div class="wpie_inner_section">
                                        <div class="wpie_inner_section_label"><?php esc_html_e( 'Twitter Options', 'wp-import-export-lite' ); ?><i class="fas fa-chevron-down wpie_inner_section_icon" aria-hidden="true"></i></div>
                                        <div class="wpie_inner_section_container">
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Title', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If you don't want to use the post title for sharing the post on Twitter but instead want another title there, import it here.", "wp-import-export-lite" ); ?>"></i></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_twitter-title" name="wpie_item_wpseo_twitter-title" value=""/>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Description', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If you don't want to use the meta description for sharing the post on Twitter but want another description there, write it here.", "wp-import-export-lite" ); ?>"></i></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_twitter-description" name="wpie_item_wpseo_twitter-description" value=""/>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Image', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If you want to override the image used on Twitter for this post, import one here. The recommended image size for Twitter is 1024 x 512px.", "wp-import-export-lite" ); ?>"></i></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_twitter-image wpie_item_wpseo_twitter-image_url" name="wpie_item_wpseo_twitter-image" checked="checked" id="wpie_item_wpseo_twitter-image_url" value="url"/>
                                                                        <label for="wpie_item_wpseo_twitter-image_url" class="wpie_radio_label"><?php esc_html_e( 'Download images hosted elsewhere', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Image URL with http:// or https://", "wp-import-export-lite" ); ?>"></i></label>
                                                                        <div class="wpie_radio_container">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_twitter-image_url_data" name="wpie_item_wpseo_twitter-image_url_data" value=""/>                                                                               
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_twitter-image wpie_item_wpseo_twitter-image_media_library" name="wpie_item_wpseo_twitter-image" id="wpie_item_wpseo_twitter-image_media_library" value="media_library"/>
                                                                        <label for="wpie_item_wpseo_twitter-image_media_library" class="wpie_radio_label"><?php esc_html_e( 'Use images currently in Media Library', 'wp-import-export-lite' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_twitter-image_media_library_data" name="wpie_item_wpseo_twitter-image_media_library_data" value=""/>                                                                               
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_twitter-image wpie_item_wpseo_twitter-image_directory" name="wpie_item_wpseo_twitter-image"  id="wpie_item_wpseo_twitter-image_directory" value="directory"/>
                                                                        <label for="wpie_item_wpseo_twitter-image_directory" class="wpie_radio_label"><?php echo esc_html( __( 'Use images currently uploaded in', 'wp-import-export-lite' ) . " " . WPIE_UPLOAD_TEMP_DIR ); ?> </label>
                                                                        <div class="wpie_radio_container">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_twitter-image_directory_data" name="wpie_item_wpseo_twitter-image_directory_data" value=""/>                                                                               
                                                                        </div>
                                                                </div>                                                                                                
                                                        </div>
                                                </div>                                                
                                        </div>
                                </div>                               
                                <div class="wpie_inner_section">
                                        <div class="wpie_inner_section_label"><?php esc_html_e( 'Advanced Options', 'wp-import-export-lite' ); ?><i class="fas fa-chevron-down wpie_inner_section_icon" aria-hidden="true"></i></div>
                                        <div class="wpie_inner_section_container">
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Meta Robots Index', 'wp-import-export-lite' ); ?></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_noindex wpie_item_wpseo_noindex_index" name="wpie_item_wpseo_noindex" id="wpie_item_wpseo_noindex_index" value="index" checked="checked" />
                                                                        <label for="wpie_item_wpseo_noindex_index" class="wpie_radio_label"><?php esc_html_e( 'Index', 'wp-import-export-lite' ); ?></label>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_noindex wpie_item_wpseo_noindex_noindex" name="wpie_item_wpseo_noindex"  id="wpie_item_wpseo_noindex_noindex" value="noindex"/>
                                                                        <label for="wpie_item_wpseo_noindex_noindex" class="wpie_radio_label"><?php echo esc_html_e( 'No Index', 'wp-import-export-lite' ); ?></label>
                                                                </div>                                                              
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_noindex wpie_item_wpseo_noindex_as_specified" name="wpie_item_wpseo_noindex" id="wpie_item_wpseo_noindex_as_specified" value="as_specified"/>
                                                                        <label for="wpie_item_wpseo_noindex_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'wp-import-export-lite' ); ?></label>
                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_noindex_as_specified_data" name="wpie_item_wpseo_noindex_as_specified_data" value=""/>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('noindex', 'index').", "wp-import-export-lite" ); ?>"></i>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>                                              
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'This article is cornerstone content', 'wp-import-export-lite' ); ?></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_is_cornerstone wpie_item_wpseo_is_cornerstone_no" name="wpie_item_wpseo_is_cornerstone" id="wpie_item_wpseo_is_cornerstone_no" value="" checked="checked" />
                                                                        <label for="wpie_item_wpseo_is_cornerstone_no" class="wpie_radio_label"><?php esc_html_e( 'No', 'wp-import-export-lite' ); ?></label>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_is_cornerstone wpie_item_wpseo_is_cornerstone_yes" name="wpie_item_wpseo_is_cornerstone"  id="wpie_item_wpseo_is_cornerstone_yes" value="1"/>
                                                                        <label for="wpie_item_wpseo_is_cornerstone_yes" class="wpie_radio_label"><?php echo esc_html_e( 'Yes', 'wp-import-export-lite' ); ?></label>
                                                                </div>                                                              
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpseo_is_cornerstone wpie_item_wpseo_is_cornerstone_as_specified" name="wpie_item_wpseo_is_cornerstone" id="wpie_item_wpseo_is_cornerstone_as_specified" value="as_specified"/>
                                                                        <label for="wpie_item_wpseo_is_cornerstone_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'wp-import-export-lite' ); ?></label>
                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_is_cornerstone_as_specified_data" name="wpie_item_wpseo_is_cornerstone_as_specified_data" value=""/>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('', '1'). Leave blank for 'Default'", "wp-import-export-lite" ); ?>"></i>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Breadcrumbs Title', 'wp-import-export-lite' ); ?></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_bctitle" name="wpie_item_wpseo_bctitle" value=""/>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_container_element">
                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Canonical URL', 'wp-import-export-lite' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The canonical URL that this page should point to, leave empty to default to permalink. Cross domain canonical supported too.", "wp-import-export-lite" ); ?>"></i></div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="text" class="wpie_content_data_input wpie_item_wpseo_canonical" name="wpie_item_wpseo_canonical" value=""/>
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
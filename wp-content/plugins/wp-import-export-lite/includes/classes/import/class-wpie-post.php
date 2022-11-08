<?php


namespace wpie\import\post;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-engine.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-engine.php');
}

class WPIE_Post extends \wpie\import\engine\WPIE_Import_Engine {

        protected $import_type = "post";

        public function process_import_data() {

                global $wpdb;

                if ( $this->is_update_field( "post_type" ) ) {

                        $this->wpie_final_data[ 'post_type' ] = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_import_type', true ) ) ) );
                }

                if ( $this->is_update_field( "post_status" ) ) {

                        $this->wpie_final_data[ 'post_status' ] = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_status', false, true ) ) ) );
                }
                if ( $this->is_update_field( "title" ) ) {

                        $this->wpie_final_data[ 'post_title' ] = $this->get_field_value( 'wpie_item_title' );

                        if ( !empty( $this->addons ) ) {

                                foreach ( $this->addons as $addon ) {

                                        if ( method_exists( $addon, "get_item_title" ) ) {
                                                $addon->get_item_title( $this->wpie_final_data[ 'post_title' ] );

                                                if ( !empty( $this->addon_log ) ) {

                                                        $this->set_log( $this->addon_log );

                                                        $this->addon_log = array();
                                                }

                                                if ( $this->addon_error === true ) {

                                                        break;
                                                }
                                        }
                                }
                        }
                }

                if ( $this->addon_error === true ) {

                        $this->remove_current_item();

                        return true;
                }

                if ( $this->is_update_field( "author" ) ) {

                        $wpie_user_id = 0;

                        $wpie_post_author = wpie_sanitize_field( $this->get_field_value( 'wpie_item_author' ) );

                        $wpie_user = get_user_by( 'login', $wpie_post_author ) or $wpie_user = get_user_by( 'slug', $wpie_post_author ) or $wpie_user = get_user_by( 'email', $wpie_post_author ) or ctype_digit( $wpie_post_author ) and $wpie_user = get_user_by( 'id', $wpie_post_author );

                        if ( $wpie_user ) {
                                $wpie_user_id = $wpie_user->ID;
                        }

                        if ( $wpie_user_id == 0 ) {

                                if ( !empty( $this->import_username ) ) {

                                        $user = get_user_by( "login", $this->import_username );

                                        if ( isset( $user->ID ) ) {
                                                $wpie_user_id = $user->ID;
                                        }
                                        unset( $user );
                                }

                                if ( $wpie_user_id == 0 ) {

                                        $current_user = wp_get_current_user();

                                        if ( isset( $current_user->ID ) ) {
                                                $wpie_user_id = $current_user->ID;
                                        }
                                        unset( $current_user );
                                }
                        }

                        $this->wpie_final_data[ 'post_author' ] = $wpie_user_id;

                        unset( $wpie_user_id, $wpie_post_author, $wpie_user );
                }

                if ( $this->is_update_field( "slug" ) ) {

                        $this->wpie_final_data[ 'post_name' ] = sanitize_title_with_dashes( $this->get_field_value( 'wpie_item_slug' ), '', 'save' );
                }

                if ( $this->is_update_field( "content" ) ) {

                        $post_content = $this->get_field_value( 'wpie_item_content' );

                        if ( intval( $this->get_field_value( 'wpie_item_import_img_tags' ) ) === 1 ) {
                                $post_content = $this->import_image_tags( $post_content );
                        }

                        $this->wpie_final_data[ 'post_content' ] = $post_content;

                        unset( $post_content );
                }

                if ( $this->is_update_field( "excerpt" ) ) {

                        $this->wpie_final_data[ 'post_excerpt' ] = $this->get_field_value( 'wpie_item_excerpt' );
                }

                if ( $this->is_update_field( "dates" ) ) {

                        $post_date_option = wpie_sanitize_field( $this->get_field_value( 'wpie_item_date' ) );

                        $post_date = "";

                        if ( $post_date_option === "as_specified" ) {
                                $post_date = wpie_sanitize_field( $this->get_field_value( 'wpie_item_date_as_specified_data' ) );
                        } elseif ( $post_date_option === "now" ) {
                                $post_date = current_time( 'mysql' );
                        } elseif ( !empty( $post_date_option ) ) {
                                $post_date = $post_date_option;
                        }

                        if ( empty( $post_date ) || strtotime( $post_date ) === false ) {
                                $post_date = current_time( 'mysql' );
                        }

                        $post_date = $this->get_date( $post_date );

                        $this->wpie_final_data[ 'post_date' ] = $post_date;

                        $this->wpie_final_data[ 'post_date_gmt' ] = get_gmt_from_date( $post_date );

                        unset( $post_date_option, $post_date );
                }

                if ( $this->is_update_field( "order" ) ) {

                        $this->wpie_final_data[ 'menu_order' ] = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_order' ) ) );
                }
                if ( $this->is_update_field( "parent" ) ) {

                        $post_parent_option = $this->get_field_value( 'wpie_item_parent', true );

                        if ( $post_parent_option == "as_specified" ) {

                                $post_parent = $this->get_field_value( 'wpie_item_parent_as_specified_data' );
                        } else {
                                $post_parent = $this->get_field_value( 'wpie_item_parent_data' );
                        }

                        if ( !empty( $post_parent ) ) {

                                $parent_id = $this->find_post( $post_parent );

                                if ( (!is_wp_error( $parent_id )) && absint( $parent_id ) > 0 ) {
                                        $this->wpie_final_data[ 'post_parent' ] = absint( $parent_id );
                                }

                                unset( $parent_id );
                        }

                        unset( $post_parent_option, $post_parent );
                }

                if ( $this->is_update_field( "comment_status" ) ) {

                        $this->wpie_final_data[ 'comment_status' ] = strtolower( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_item_comment_status', false, true ) ) ) ) === "closed" ? "closed" : "open";
                }
                if ( $this->is_update_field( "ping_status" ) ) {

                        $this->wpie_final_data[ 'ping_status' ] = strtolower( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_item_ping_status', false, true ) ) ) ) === "closed" ? "closed" : "open";
                }
                if ( $this->is_update_field( "post_password" ) ) {

                        $this->wpie_final_data[ 'post_password' ] = $this->get_field_value( 'wpie_item_post_password' );
                }

                $page_template = wpie_sanitize_field( $this->get_field_value( 'wpie_item_template', false, true ) );

                if ( !empty( $page_template ) ) {

                        $this->wpie_final_data[ 'page_template' ] = $page_template;
                }

                unset( $page_template );

                if ( !$this->is_new_item ) {
                        $this->wpie_final_data[ 'ID' ] = $this->existing_item_id;
                }

                $this->wpie_final_data = apply_filters( 'wpie_before_post_import', $this->wpie_final_data, $this->wpie_import_option, $this->wpie_import_record );

                if ( $this->is_new_item || !isset( $this->wpie_final_data[ 'ID' ] ) ) {

                        $this->item_id = wp_insert_post( $this->wpie_final_data, true );
                } else {

                        $this->item_id = wp_update_post( $this->wpie_final_data, false );
                }

                $this->process_log[ 'imported' ]++;

                if ( is_wp_error( $this->item_id ) ) {

                        $this->set_log( "<strong>" . __( 'ERROR', 'wp-import-export-lite' ) . '</strong> : ' . $this->item_id->get_error_message() );

                        $this->process_log[ 'skipped' ]++;

                        return true;
                } elseif ( $this->item_id == 0 ) {

                        $this->set_log( "<strong>" . __( 'ERROR', 'wp-import-export-lite' ) . '</strong> : ' . __( 'something wrong, ID = 0 was generated.', 'wp-import-export-lite' ) );

                        $this->process_log[ 'skipped' ]++;

                        return true;
                }

                if ( $this->is_new_item ) {

                        do_action( 'wpie_insert_post', $this->item_id );

                        $this->process_log[ 'created' ]++;
                } else {

                        do_action( 'wpie_update_post', $this->item_id );

                        $this->process_log[ 'updated' ]++;
                }

                if ( $this->backup_service !== false && $this->is_new_item ) {
                        $this->backup_service->create_backup( $this->item_id, true );
                }

                $this->item = get_post( $this->item_id );

                $this->process_log[ 'last_records_id' ] = $this->item_id;

                $this->process_log[ 'last_records_status' ] = 'pending';

                $this->process_log[ 'last_activity' ] = date( 'Y-m-d H:i:s' );

                $wpdb->update( $wpdb->prefix . "wpie_template", array( 'last_update_date' => current_time( 'mysql' ),
                        'process_log'      => maybe_serialize( $this->process_log ) ), array(
                        'id' => $this->wpie_import_id ) );

                do_action( 'wpie_after_post_import', $this->item_id, $this->wpie_final_data, $this->wpie_import_option );

                $this->wpie_final_data[ 'post_format' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_post_format', false, true ) );

                if ( empty( $this->wpie_final_data[ 'post_format' ] ) ) {

                        $this->wpie_final_data[ 'post_format' ] = "standard";
                }

                set_post_format( $this->item, $this->wpie_final_data[ 'post_format' ] );

                if ( $this->is_update_field( "taxonomies" ) ) {

                        $this->prepare_taxonomies();
                }

                if ( $this->is_update_field( "cf" ) ) {

                        $this->wpie_import_cf();
                }
                if ( $this->is_update_field( "images" ) ) {

                        $this->wpie_import_images();
                }
                if ( $this->is_update_field( "attachments" ) ) {

                        $this->wpie_import_attachments();
                }

                return $this->item_id;
        }

        protected function search_duplicate_item() {

                global $wpdb;

                $wpie_duplicate_indicator = empty( $this->get_field_value( 'wpie_existing_item_search_logic', true ) ) ? 'title' : wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) );

                if ( $wpie_duplicate_indicator == "id" ) {

                        $duplicate_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_id' ) ) );

                        if ( $duplicate_id > 0 ) {
                                $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 1", $duplicate_id ) );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $this->existing_item_id = absint( $duplicate_id );
                                }
                                unset( $_post );
                        }
                        unset( $duplicate_id );
                } elseif ( $wpie_duplicate_indicator == "title" || $wpie_duplicate_indicator == "content" ) {

                        $wpie_field = 'post_' . $wpie_duplicate_indicator;

                        $temp_field = 'wpie_item_' . $wpie_duplicate_indicator;

                        $wpie_field_data = $this->get_field_value( $temp_field );

                        if ( !empty( $wpie_field_data ) ) {

                                $_post = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT ID FROM " . $wpdb->posts . "
                                                        WHERE
                                                            post_type = %s
                                                            AND ID != 0
                                                            AND  `" . $wpie_field . "` IN ( %s,%s,%s )
                                                        LIMIT 1
                                ", wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) ), html_entity_decode( $wpie_field_data ), htmlentities( $wpie_field_data ), $wpie_field_data
                                        )
                                );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $this->existing_item_id = absint( $_post );
                                }

                                unset( $_post );
                        }
                        unset( $wpie_field, $wpie_field_data, $temp_field );
                } elseif ( $wpie_duplicate_indicator == "slug" ) {

                        $wpie_field_data = $this->get_field_value( "wpie_existing_item_search_logic_slug" );

                        if ( !empty( $wpie_field_data ) ) {

                                $_post = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "SELECT ID FROM " . $wpdb->posts . "
                                                        WHERE
                                                            post_type = %s
                                                            AND ID != 0
                                                            AND  `post_name` IN ( %s,%s,%s )
                                                        LIMIT 1
                                ", wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) ), html_entity_decode( $wpie_field_data ), htmlentities( $wpie_field_data ), $wpie_field_data
                                        )
                                );

                                if ( $_post && absint( $_post ) > 0 ) {
                                        $this->existing_item_id = absint( $_post );
                                }

                                unset( $_post );
                        }
                        unset( $wpie_field_data );
                } elseif ( $wpie_duplicate_indicator == "cf" || $wpie_duplicate_indicator == "sku" ) {

                        if ( $wpie_duplicate_indicator == "cf" ) {

                                $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_key' ) );

                                $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_value' ) );
                        } else {
                                $meta_key = "_sku";
                                $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_sku' ) );
                        }


                        $post_types = wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) );

                        if ( $post_types == "product" ) {

                                if ( strpos( trim( strtolower( $meta_key ) ), "sku" ) !== false ) {
                                        $meta_key = "_sku";
                                }

                                $post_types = [ "product", "product_variation" ];
                        } else {
                                $post_types = [ $post_types ];
                        }

                        $sql_post_type = implode( "','", $post_types );

                        $id = $wpdb->get_var(
                                $wpdb->prepare(
                                        "
                                                SELECT posts.ID
                                                FROM {$wpdb->posts} as posts
                                                INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                AND posts.post_status NOT IN ('trash','auto-draft' )
                                                AND postmeta.meta_key = %s                                               
                                                AND postmeta.meta_value = %s
                                                ORDER BY posts.ID ASC
                                                LIMIT 0, 1
                                        ",
                                        $meta_key,
                                        $meta_val
                                )
                        );

                        if ( absint( $id ) > 0 ) {
                                $this->existing_item_id = $id;
                        }

                        if ( $this->existing_item_id === 0 ) {

                                $id = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "
                                                        SELECT posts.ID
                                                        FROM {$wpdb->posts} as posts
                                                        INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                        WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                        AND postmeta.meta_key = %s                                               
                                                        AND postmeta.meta_value = %s
                                                        ORDER BY posts.ID ASC
                                                        LIMIT 0, 1
                                                ",
                                                $meta_key,
                                                $meta_val
                                        )
                                );

                                if ( absint( $id ) > 0 ) {
                                        $this->existing_item_id = $id;
                                }
                        }
                        if ( $this->existing_item_id === 0 ) {

                                $id = $wpdb->get_var(
                                        $wpdb->prepare(
                                                "
                                                        SELECT posts.ID
                                                        FROM {$wpdb->posts} as posts
                                                        INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                        WHERE posts.post_type IN ( '{$sql_post_type}' )
                                                        AND postmeta.meta_key IN ( %s,%s,%s )                                               
                                                        AND postmeta.meta_value IN( %s,%s,%s,%s )
                                                        ORDER BY posts.ID ASC
                                                        LIMIT 0, 1
                                                ",
                                                $meta_key,
                                                trim( $meta_key ),
                                                wpie_sanitize_field( $meta_key ),
                                                $meta_val,
                                                trim( $meta_val ),
                                                wpie_sanitize_field( $meta_val ),
                                                preg_replace( '%[ \\t\\n]%', '', $meta_val )
                                        )
                                );

                                if ( absint( $id ) > 0 ) {
                                        $this->existing_item_id = $id;
                                }
                        }

                        unset( $meta_key, $meta_val, $post_types, $sql_post_type, $id );
                }
                unset( $wpie_duplicate_indicator );
        }

        private function prepare_taxonomies() {

                if ( file_exists( ABSPATH . 'wp-admin/includes/taxonomy.php' ) ) {
                        require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
                }

                $object_taxonomies = get_object_taxonomies( $this->item, "names" );

                $tax_includes = array();

                $tax_excludes = array();

                $update_policy = $this->get_field_value( 'wpie_item_update', true );

                if ( $update_policy != "all" ) {
                        $handle_tax = $this->get_field_value( 'wpie_item_update_taxonomies', true );
                } else {
                        $handle_tax = "all";
                }

                if ( !$this->is_new_item ) {

                        if ( $handle_tax == 'includes' ) {

                                $includes = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_taxonomies_includes_data' ) );

                                if ( !empty( $includes ) ) {
                                        $tax_includes = explode( ",", $includes );
                                }
                                unset( $includes );
                        } elseif ( $handle_tax == 'excludes' ) {

                                $excludes = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_taxonomies_excludes_data' ) );

                                if ( !empty( $excludes ) ) {
                                        $tax_excludes = explode( ",", $excludes );
                                }
                                unset( $excludes );
                        }
                }

                $set_taxonomy = wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_taxonomy', true ) );

                $item_taxonomy = $this->get_field_value( 'wpie_item_taxonomy' );

                $taxonomy_delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_taxonomy_delim' ) );

                $taxonomy_hierarchical_delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_taxonomy_hierarchical_delim' ) );

                $taxonomy_child_only = wpie_sanitize_field( $this->get_field_value( 'wpie_item_taxonomy_child_only' ) );

                $post_type = wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) );

                if ( !empty( $object_taxonomies ) ) {

                        foreach ( $object_taxonomies as $taxonomy ) {

                                if ( !empty( $tax_includes ) && !in_array( $taxonomy, $tax_includes ) ) {
                                        continue;
                                }
                                if ( !empty( $tax_excludes ) && in_array( $taxonomy, $tax_excludes ) ) {
                                        continue;
                                }

                                if ( strpos( $taxonomy, "pa_" ) === 0 && $post_type == "product" ) {
                                        continue;
                                }

                                $terms = array();

                                if ( isset( $set_taxonomy[ $taxonomy ] ) && absint( $set_taxonomy[ $taxonomy ] ) == 1 ) {

                                        $tax = isset( $item_taxonomy[ $taxonomy ] ) ? $this->get_field( $item_taxonomy[ $taxonomy ] ) : "";

                                        $tax_delim = isset( $taxonomy_delim[ $taxonomy ] ) && !empty( $this->get_field( $taxonomy_delim[ $taxonomy ] ) ) ? $this->get_field( $taxonomy_delim[ $taxonomy ] ) : ",";

                                        $_child_only = isset( $taxonomy_child_only[ $taxonomy ] ) ? absint( $taxonomy_child_only[ $taxonomy ] ) : 0;

                                        $tax_data = explode( $tax_delim, $tax );

                                        if ( !empty( $tax_data ) && !empty( $tax_data ) ) {

                                                foreach ( $tax_data as $term_data ) {

                                                        if ( isset( $taxonomy_hierarchical_delim[ $taxonomy ] ) ) {

                                                                $hierarchical_delim = empty( $this->get_field( $taxonomy_hierarchical_delim[ $taxonomy ] ) ) ? ">" : $this->get_field( $taxonomy_hierarchical_delim[ $taxonomy ] );

                                                                $term_data = explode( $hierarchical_delim, html_entity_decode( $term_data ) );

                                                                unset( $hierarchical_delim );
                                                        }

                                                        $parent = null;

                                                        $grand_parent = null;

                                                        if ( !empty( $term_data ) ) {

                                                                if ( is_array( $term_data ) ) {

                                                                        $_temp_term_data = array();

                                                                        foreach ( $term_data as $_term ) {

                                                                                $_temp_parent_id = null;

                                                                                if ( !empty( $parent ) ) {

                                                                                        $_parent_data = $this->is_term_exists( $parent, $taxonomy, $grand_parent );

                                                                                        if ( !is_array( $_parent_data ) ) {
                                                                                                $_parent_data = wp_insert_term( $parent, $taxonomy, array(
                                                                                                        'parent' => $grand_parent ) );
                                                                                        }

                                                                                        if ( is_array( $_parent_data ) && isset( $_parent_data[ 'term_id' ] ) && absint( $_parent_data[ 'term_id' ] ) > 0 ) {
                                                                                                $_temp_parent_id = absint( $_parent_data[ 'term_id' ] );
                                                                                        }
                                                                                }

                                                                                if ( $_child_only == 1 ) {
                                                                                        $_temp_term_data = array(
                                                                                                "term"   => $_term,
                                                                                                "parent" => $_temp_parent_id
                                                                                        );
                                                                                } else {
                                                                                        $terms[] = array(
                                                                                                "term"   => $_term,
                                                                                                "parent" => $_temp_parent_id
                                                                                        );
                                                                                }

                                                                                $grand_parent = $_temp_parent_id;

                                                                                $parent = $_term;
                                                                        }

                                                                        if ( $_child_only == 1 && !empty( $_temp_term_data ) ) {
                                                                                $terms[] = $_temp_term_data;
                                                                        }
                                                                        unset( $_temp_term_data );
                                                                } else {
                                                                        $terms[] = array(
                                                                                "term"   => $term_data,
                                                                                "parent" => null
                                                                        );
                                                                }
                                                        }

                                                        unset( $parent );
                                                }
                                        }

                                        unset( $tax, $tax_delim, $tax_data );
                                }

                                if ( !empty( $terms ) ) {

                                        $appned = true;

                                        if ( $this->is_new_item || (!$this->is_new_item && $handle_tax == "all" ) ) {
                                                $appned = false;
                                        }

                                        $this->set_taxonomies( $terms, $taxonomy, $appned );

                                        unset( $appned );
                                }

                                unset( $terms );
                        }
                }

                unset( $object_taxonomies, $tax_includes, $tax_excludes, $handle_tax, $set_taxonomy, $item_taxonomy, $taxonomy_delim, $taxonomy_hierarchical_delim, $taxonomy_child_only, $post_type );
        }

        private function set_taxonomies( $terms = array(), $taxonomy = "", $append = true ) {

                if ( is_array( $terms ) && !empty( $terms ) ) {

                        $term_list = array();

                        foreach ( $terms as $term ) {

                                $_term = isset( $term[ 'term' ] ) ? $term[ 'term' ] : "";

                                if ( empty( $_term ) || empty( $taxonomy ) ) {
                                        continue;
                                }

                                $parent = isset( $term[ 'parent' ] ) && !empty( $term[ 'parent' ] ) ? absint( $term[ 'parent' ] ) : null;

                                $term_data = $this->is_term_exists( $_term, $taxonomy, $parent );

                                $termId = false;
                                if ( !is_array( $term_data ) ) {

                                        $term_data = wp_insert_term( $_term, $taxonomy, array(
                                                'parent' => $parent ) );

                                        if ( !is_wp_error( $term_data ) ) {
                                                $termId = isset( $term_data[ 'term_id' ] ) ? absint( $term_data[ 'term_id' ] ) : false;

                                                do_action( 'wpie_add_new_post_term', $termId );
                                        } else {
                                                $this->set_log( "<strong>" . __( 'ERROR', 'wp-import-export-lite' ) . '</strong> : ' . $term_data->get_error_message() );
                                        }
                                } elseif ( isset( $term_data[ 'term_id' ] ) && absint( $term_data[ 'term_id' ] ) > 0 ) {

                                        $termId = absint( $term_data[ 'term_id' ] );
                                }

                                if ( $termId !== false && absint( $termId ) > 0 ) {
                                        $term_list[] = apply_filters( 'wpie_add_post_term', $termId );
                                }
                                unset( $_term, $parent, $term_data );
                        }


                        if ( !empty( $term_list ) ) {
                                wp_set_object_terms( $this->item_id, $term_list, $taxonomy, $append );
                        }

                        unset( $term_list );
                }
        }

        private function find_post( $post = "" ) {

                if ( empty( $post ) ) {
                        return 0;
                }

                global $wpdb;

                $post_id = 0;

                if ( is_numeric( $post ) && absint( $post ) > 0 ) {

                        $_post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d LIMIT 1", absint( $post ) ) );

                        if ( $_post && absint( $_post ) > 0 ) {
                                $post_id = absint( $post );
                        }
                }

                if ( $post_id === 0 ) {

                        $_post = $wpdb->get_var(
                                $wpdb->prepare(
                                        "SELECT ID FROM " . $wpdb->posts . "
                                                WHERE
                                                    post_type = %s
                                                    AND ID != 0
                                                    AND ( `post_title` IN ( %s,%s,%s ) OR  `post_content` IN ( %s,%s,%s ))
                                                LIMIT 1
                                ", wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) ), html_entity_decode( $post ), htmlentities( $post ), $post, html_entity_decode( $post ), htmlentities( $post ), $post
                                )
                        );

                        if ( $_post && absint( $_post ) > 0 ) {
                                $post_id = absint( $_post );
                        }
                }
                return $post_id;
        }

        public function __destruct() {
                parent::__destruct();
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

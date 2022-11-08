<?php


namespace wpie\export\post;

use WP_User_Query;
use WP_Query;
use wpie\export\media;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-engine.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-engine.php');
}

class WPIE_Post extends \wpie\export\engine\WPIE_Export_Engine {

        protected $item_where = array();
        protected $item_join  = array();
        private $user_where   = "";
        private $user_join    = array();

        protected function get_fields( $exclude_meta = array() ) {

                $standard_fields = array(
                        "title"     => __( "Standerd", 'wp-import-export-lite' ),
                        "isDefault" => true,
                        "data"      => array(
                                array(
                                        'name'      => 'ID',
                                        'type'      => 'id',
                                        'isDefault' => true
                                ),
                                array(
                                        'name'      => 'Title',
                                        'type'      => 'title',
                                        'isDefault' => true
                                ),
                                array(
                                        'name'      => 'Content',
                                        'type'      => 'content',
                                        'isDefault' => true
                                ),
                                array(
                                        'name'      => 'Excerpt',
                                        'type'      => 'excerpt',
                                        'isDefault' => true
                                ),
                                array(
                                        'name'      => 'Date',
                                        'type'      => 'date',
                                        'isDate'    => true,
                                        'isDefault' => true
                                ),
                                array(
                                        'name'      => 'Post Type',
                                        'type'      => 'post_type',
                                        'isDefault' => true
                                ),
                                array(
                                        'name'      => 'Permalink',
                                        'type'      => 'permalink',
                                        'isDefault' => true
                                )
                        )
                );
                $image_fields    = array(
                        "title"      => __( "Images", 'wp-import-export-lite' ),
                        "isFiltered" => false,
                        "data"       => array(
                                array(
                                        'name' => 'Image URL',
                                        'type' => 'image_url',
                                ),
                                array(
                                        'name' => 'Images Filename',
                                        'type' => 'image_filename',
                                ),
                                array(
                                        'name' => 'Images Path',
                                        'type' => 'image_path',
                                ),
                                array(
                                        'name' => 'Images ID',
                                        'type' => 'image_id',
                                ),
                                array(
                                        'name' => 'Images Title',
                                        'type' => 'image_title',
                                ),
                                array(
                                        'name' => 'Images Caption',
                                        'type' => 'image_caption',
                                ),
                                array(
                                        'name' => 'Images Description',
                                        'type' => 'image_description',
                                ),
                                array(
                                        'name' => 'Images Alt Text',
                                        'type' => 'image_alt',
                                ),
                        )
                );

                $attachment_fields = array(
                        "title"      => __( "Attachments", 'wp-import-export-lite' ),
                        "isFiltered" => false,
                        "data"       => array(
                                array(
                                        'name' => 'Attachment URL',
                                        'type' => 'attachment_url',
                                ),
                                array(
                                        'name' => 'Attachments Filename',
                                        'type' => 'attachment_filename',
                                ),
                                array(
                                        'name' => 'Attachments Path',
                                        'type' => 'attachment_path',
                                ),
                                array(
                                        'name' => 'Attachments ID',
                                        'type' => 'attachment_id',
                                ),
                                array(
                                        'name' => 'Attachments Title',
                                        'type' => 'attachment_title',
                                ),
                                array(
                                        'name' => 'Attachments Caption',
                                        'type' => 'attachment_caption',
                                ),
                                array(
                                        'name' => 'Attachments Description',
                                        'type' => 'attachment_description',
                                ),
                                array(
                                        'name' => 'Attachments Alt Text',
                                        'type' => 'attachment_alt',
                                ),
                        )
                );

                $author_fields = array(
                        "title"      => __( "Author", 'wp-import-export-lite' ),
                        "isExported" => false,
                        "data"       => array(
                                array(
                                        'name' => 'User ID',
                                        'type' => 'user_ID',
                                ),
                                array(
                                        'name' => 'User Login',
                                        'type' => 'user_login',
                                ),
                                array(
                                        'name' => 'Nicename',
                                        'type' => 'user_nicename',
                                ),
                                array(
                                        'name' => 'Email',
                                        'type' => 'user_email',
                                ),
                                array(
                                        'name'   => 'Date Registered (Y-m-d H:i:s)',
                                        'type'   => 'user_registered',
                                        "isDate" => true,
                                ),
                                array(
                                        'name' => 'Display Name',
                                        'type' => 'display_name',
                                ),
                                array(
                                        'name' => 'First Name',
                                        'type' => 'wpie_cf_first_name',
                                ),
                                array(
                                        'name' => 'Last Name',
                                        'type' => 'wpie_cf_last_name',
                                ),
                                array(
                                        'name' => 'Nickname',
                                        'type' => 'nickname',
                                ),
                                array(
                                        'name' => 'User Description',
                                        'type' => 'description',
                                ),
                                array(
                                        'name'         => 'User Role',
                                        'type'         => 'wp_capabilities',
                                        'isCapability' => true
                                ),
                        )
                );

                $other_fields = array(
                        "title" => __( "Other", 'wp-import-export-lite' ),
                        "data"  => array(
                                array(
                                        'name' => 'Status',
                                        'type' => 'status',
                                ),
                                array(
                                        'name' => 'Author ID',
                                        'type' => 'author_id'
                                ),
                                array(
                                        'name' => 'Author Username',
                                        'type' => 'author_username'
                                ),
                                array(
                                        'name' => 'Author Email',
                                        'type' => 'author_email'
                                ),
                                array(
                                        'name' => 'Author First Name',
                                        'type' => 'wpie_cf_first_name',
                                ),
                                array(
                                        'name' => 'Author Last Name',
                                        'type' => 'wpie_cf_last_name',
                                ),
                                array(
                                        'name' => 'Slug',
                                        'type' => 'slug',
                                ),
                                array(
                                        'name' => 'Format',
                                        'type' => 'format',
                                ),
                                array(
                                        'name' => 'Post Password',
                                        'type' => 'post_password',
                                ),
                                array(
                                        'name' => 'Template',
                                        'type' => 'template',
                                ),
                                array(
                                        'name' => 'Parent',
                                        'type' => 'parent',
                                ),
                                array(
                                        'name' => 'Parent Slug',
                                        'type' => 'parent_slug',
                                ),
                                array(
                                        'name' => 'Order',
                                        'type' => 'order',
                                ),
                                array(
                                        'name' => 'Comment Status',
                                        'type' => 'comment_status',
                                ),
                                array(
                                        'name' => 'Ping Status',
                                        'type' => 'ping_status',
                                ),
                                array(
                                        'name'   => 'Post Modified Date',
                                        'type'   => 'post_modified',
                                        "isDate" => true,
                                )
                        )
                );

                $excludes = apply_filters( 'wpie_exclude_post_taxonomy_fields', [] );

                $taxonomy_data = $this->get_taxonomies_by_post_type( $this->export_type, "wpie_tax", false, $excludes );

                $taxonomy = array(
                        "title" => __( "Taxonomy", 'wp-import-export-lite' ),
                        "data"  => $taxonomy_data
                );

                unset( $taxonomy_data );

                $metas = $this->get_meta_keys( $this->export_type );

                $export_fields = array(
                        "standard"   => $standard_fields,
                        "meta"       => $metas,
                        "taxonomy"   => $taxonomy,
                        "image"      => $image_fields,
                        "attachment" => $attachment_fields,
                        "author"     => $author_fields,
                        "other"      => $other_fields,
                );

                $addon_class = apply_filters( 'wpie_prepare_post_fields', array(), $this->export_type );

                if ( !empty( $addon_class ) ) {

                        foreach ( $addon_class as $addon ) {

                                if ( class_exists( $addon ) ) {

                                        $addon_data = new $addon();

                                        if ( method_exists( $addon_data, "pre_process_fields" ) ) {

                                                $addon_data->pre_process_fields( $export_fields, $this->export_type, $this->export_taxonomy_type );
                                        }

                                        unset( $addon_data );
                                }
                        }
                }

                $meta_data = array();

                if ( !empty( $export_fields[ "meta" ] ) ) {
                        foreach ( $export_fields[ "meta" ] as $key ) {

                                if ( empty( trim( $key ) ) ) {
                                        continue;
                                }
                                $meta_data[] = apply_filters( "wpie_pre_item_meta", array(
                                        'name'    => $key,
                                        'type'    => "wpie_cf",
                                        'metaKey' => $key
                                        ), $key );
                        }
                }

                unset( $metas );

                $meta_fields = array(
                        "title" => __( "Custom Fields", 'wp-import-export-lite' ),
                        "data"  => $meta_data
                );

                $export_fields[ 'meta' ] = $meta_fields;

                unset( $standard_fields, $meta_fields, $taxonomy, $image_fields, $attachment_fields, $author_fields, $other_fields, $addon_class, $meta_data );

                return apply_filters( "wpie_export_fields", $export_fields, $this->export_type );
        }

        protected function get_meta_keys( $export_type = array() ) {

                global $wpdb;

                $post_type = implode( "','", $export_type );

                $sql   = "SELECT DISTINCT $wpdb->postmeta.meta_key
			FROM $wpdb->postmeta,$wpdb->posts
                        WHERE $wpdb->postmeta.post_id = $wpdb->posts.ID
                        AND $wpdb->posts.post_type IN ('$post_type')
			AND $wpdb->postmeta.meta_key NOT LIKE %s
			AND $wpdb->postmeta.meta_key NOT LIKE %s
			ORDER BY $wpdb->postmeta.meta_key
			LIMIT %d";
                $limit = apply_filters( 'wpie_postmeta_form_limit', 1000 );

                $keys = $wpdb->get_col( $wpdb->prepare( $sql, '_edit%', '_oembed_%', $limit ) );

                unset( $post_type, $sql, $limit );

                return $keys;
        }

        protected function parse_rule( $filter = array() ) {

                if ( isset( $filter[ 'element' ] ) ) {

                        $filter[ 'element' ] = $this->get_valid_element( $filter[ 'element' ] );

                        $filter[ 'condition' ] = isset( $filter[ 'condition' ] ) ? $filter[ 'condition' ] : "";

                        $filter[ 'value' ] = isset( $filter[ 'value' ] ) ? $filter[ 'value' ] : "";

                        $filter[ 'clause' ] = isset( $filter[ 'clause' ] ) ? $filter[ 'clause' ] : "";

                        global $wpdb;

                        switch ( $filter[ 'element' ] ) {
                                case 'ID':
                                case 'id':
                                case 'post_parent':
                                case 'post_author':
                                        $this->item_where[]  = " {$wpdb->posts}.{$filter[ 'element' ]} " . $this->add_filter_rule( $filter, true, false );
                                        break;
                                case 'post_status':
                                case 'post_title':
                                case 'post_content':
                                case 'post_excerpt':
                                case 'guid':
                                case 'post_name':
                                case 'menu_order':
                                case 'post_password':
                                        $this->item_where[]  = " {$wpdb->posts}.{$filter[ 'element' ]} " . $this->add_filter_rule( $filter, false, false );
                                        break;
                                case 'post_date':
                                case 'post_modified':
                                        $filter[ 'value' ]   = $this->add_date_filter_rule( $filter );
                                        $this->item_where[]  = " {$wpdb->posts}.{$filter[ 'element' ]} " . $this->add_filter_rule( $filter, false, false );
                                        break;
                                case 'user_ID':
                                        $filter[ 'element' ] = 'post_author';
                                        $this->item_where[]  = " {$wpdb->posts}.{$filter[ 'element' ]} " . $this->add_filter_rule( $filter, true, false );
                                        break;
                                case 'user_login':
                                case 'user_nicename':
                                case 'user_email':
                                case 'user_registered':
                                case 'display_name':
                                case 'first_name':
                                case 'last_name':
                                case 'nickname':
                                case 'description':
                                case 'wp_capabilities':

                                        $this->user_where = " AND (";

                                        $this->user_join = array();

                                        $meta_query = false;

                                        switch ( $filter[ 'element' ] ) {
                                                case 'wp_capabilities':

                                                        $meta_query = true;

                                                        $cap_key = $wpdb->prefix . 'capabilities';

                                                        $this->user_join[] = " INNER JOIN {$wpdb->usermeta} ON ({$wpdb->usermeta}.user_id = {$wpdb->users}.ID) ";

                                                        $this->user_where .= "{$wpdb->usermeta}.meta_key = '$cap_key' AND {$wpdb->usermeta}.meta_value " . $this->add_filter_rule( $filter, false, false );

                                                        unset( $cap_key );

                                                        break;
                                                case 'user_registered':
                                                        $filter[ 'value' ] = $this->add_date_filter_rule( $filter );
                                                        $this->user_where  .= "{$wpdb->users}.{$filter[ 'element' ]} " . $this->add_filter_rule( $filter, false, false );
                                                        break;
                                                case 'user_login':
                                                case 'user_nicename':
                                                case 'user_email':
                                                case 'display_name':
                                                case 'description':
                                                        $this->user_where  .= "{$wpdb->users}.{$filter[ 'element' ]} " . $this->add_filter_rule( $filter, false, false );
                                                        break;
                                                default:

                                                        if ( strpos( $filter[ 'element' ], "wpie_cf_" ) === 0 || $filter[ 'element' ] === "nickname" ) {

                                                                $meta_key = str_replace( "wpie_cf_", "", $filter[ 'element' ] );

                                                                if ( $filter[ 'clause' ] == 'is_empty' ) {

                                                                        $this->user_join[] = " LEFT JOIN {$wpdb->usermeta} ON ({$wpdb->usermeta}.user_id = {$wpdb->users}.ID AND {$wpdb->usermeta}.meta_key = '$meta_key') ";

                                                                        $this->user_where .= "{$wpdb->usermeta}.umeta_id " . $this->add_filter_rule( $filter, false, false );
                                                                } else {
                                                                        $this->user_join[] = " INNER JOIN {$wpdb->usermeta} ON ({$wpdb->usermeta}.user_id = {$wpdb->users}.ID) ";

                                                                        $this->user_where .= "{$wpdb->usermeta}.meta_key = '$meta_key' AND {$wpdb->usermeta}.meta_value " . $this->add_filter_rule( $filter, false, false );
                                                                }
                                                                unset( $meta_key );
                                                        }
                                                        break;
                                        }

                                        $this->user_where .= $meta_query ? " ) GROUP BY {$wpdb->users}.ID" : ")";

                                        unset( $meta_query );

                                        add_action( 'pre_user_query', array( $this, 'pre_user_query' ), 10, 1 );

                                        $user_query = new \WP_User_Query( array( 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids' ) );

                                        remove_action( 'pre_user_query', array( $this, 'pre_user_query' ) );

                                        $user_list = array();

                                        if ( !empty( $user_query->results ) ) {
                                                foreach ( $user_query->results as $userID ) {
                                                        $user_list[] = $userID;
                                                }
                                        }

                                        if ( !empty( $user_list ) ) {

                                                $users_str = implode( ",", $user_list );

                                                $user_data_where = "{$wpdb->posts}.post_author IN ($users_str)";

                                                if ( !empty( $filter[ 'clause' ] ) ) {

                                                        $user_data_where .= " " . $filter[ 'clause' ] . " ";
                                                }

                                                $this->item_where[] = $user_data_where;

                                                unset( $users_str, $user_data_where );
                                        }

                                        unset( $user_list );

                                        break;
                                case 'wpie_tax':

                                        if ( !empty( $filter[ 'value' ] ) ) {

                                                $tx_name = isset( $filter[ 'taxName' ] ) ? $filter[ 'taxName' ] : "category";

                                                $terms = array();

                                                $txs = explode( ",", $filter[ 'value' ] );

                                                if ( !empty( $txs ) ) {
                                                        foreach ( $txs as $tx ) {
                                                                if ( is_numeric( $tx ) ) {
                                                                        $terms[] = $tx;
                                                                } else {
                                                                        $term = $this->is_term_exists( $tx, $tx_name );

                                                                        if ( is_array( $term ) && isset( $term[ 'term_taxonomy_id' ] ) ) {
                                                                                $terms[] = $term[ 'term_taxonomy_id' ];
                                                                        }
                                                                        unset( $term );
                                                                }
                                                        }
                                                }

                                                unset( $txs, $tx_name );

                                                if ( !empty( $terms ) ) {

                                                        $terms_str = implode( ",", $terms );

                                                        switch ( $filter[ 'condition' ] ) {
                                                                case 'in':

                                                                        $table_alias = 'tr' . time() . uniqid();

                                                                        $this->item_join[] = " LEFT JOIN {$wpdb->term_relationships} AS $table_alias ON ({$wpdb->posts}.ID = $table_alias.object_id)";

                                                                        $term_tx_data = "$table_alias.term_taxonomy_id IN ($terms_str)";

                                                                        if ( !empty( $filter[ 'clause' ] ) ) {
                                                                                $term_tx_data .= " " . $filter[ 'clause' ] . " ";
                                                                        }

                                                                        $this->item_where[] = $term_tx_data;

                                                                        unset( $table_alias, $term_tx_data );

                                                                        break;
                                                                case 'not_in':

                                                                        $term_not_in_tx_data = "{$wpdb->posts}.ID NOT IN (
                                                                            SELECT object_id
                                                                            FROM {$wpdb->term_relationships}
                                                                            WHERE term_taxonomy_id IN ($terms_str)
                                                                          )";

                                                                        if ( !empty( $filter[ 'clause' ] ) ) {
                                                                                $term_not_in_tx_data .= " " . $filter[ 'clause' ] . " ";
                                                                        }

                                                                        $this->item_where[] = $term_not_in_tx_data;

                                                                        unset( $term_not_in_tx_data );

                                                                        break;
                                                                default:

                                                                        break;
                                                        }

                                                        unset( $terms_str );
                                                }

                                                unset( $terms );
                                        }
                                        break;
                                case 'wpie_cf':

                                        $meta_key = isset( $filter[ 'metaKey' ] ) ? $filter[ 'metaKey' ] : "";

                                        $table_alias = (count( $this->item_join ) > 0) ? 'meta' . count( $this->item_join ) : 'meta';

                                        if ( $filter[ 'condition' ] == 'is_empty' ) {

                                                $this->item_join[] = " LEFT JOIN {$wpdb->postmeta} AS $table_alias ON ($table_alias.post_id = {$wpdb->posts}.ID AND $table_alias.meta_key = '$meta_key') ";

                                                $this->item_where[] = "$table_alias.meta_id " . $this->add_filter_rule( $filter, false, false );
                                        } else {

                                                if ( in_array( $meta_key, array( '_completed_date' ) ) ) {
                                                        $filter[ 'value' ] = $this->add_date_filter_rule( $filter );
                                                }

                                                $this->item_join[] = " INNER JOIN {$wpdb->postmeta} AS $table_alias ON ({$wpdb->posts}.ID = $table_alias.post_id) ";

                                                $this->item_where[] = "$table_alias.meta_key = '$meta_key' AND $table_alias.meta_value " . $this->add_filter_rule( $filter, false, $table_alias );
                                        }
                                        unset( $meta_key, $table_alias );
                                        break;
                                default:

                                        break;
                        }

                        $filter_data = apply_filters( 'wpie_apply_post_filter', [], $this->export_type, $filter );

                        if ( !empty( $filter_data ) ) {
                                $item_where = isset( $filter_data[ 'item_where' ] ) && !empty( $filter_data[ 'item_where' ] ) ? $filter_data[ 'item_where' ] : [];
                                if ( !empty( $item_where ) ) {
                                        $this->item_where = array_merge( $this->item_where, $item_where );
                                }
                                $item_join = isset( $filter_data[ 'item_join' ] ) && !empty( $filter_data[ 'item_join' ] ) ? $filter_data[ 'item_join' ] : [];
                                if ( !empty( $item_join ) ) {
                                        $this->item_join = array_merge( $this->item_join, $item_join );
                                }

                                unset( $item_where, $item_join );
                        }
                }
                unset( $filter );
        }

        protected function process_export() {

                $query = array(
                        'post_type'      => $this->export_type,
                        'post_status'    => array_keys( get_post_stati() ),
                        'orderby'        => "ID",
                        'order'          => 'ASC',
                        'fields'         => 'ids',
                        'offset'         => isset( $this->process_log[ 'exported' ] ) ? absint( $this->process_log[ 'exported' ] ) : 0,
                        'posts_per_page' => isset( $this->template_options[ 'wpie_records_per_iteration' ] ) ? absint( $this->template_options[ 'wpie_records_per_iteration' ] ) : 50
                );

                if ( $this->opration === "count" || $this->opration === "ids" ) {
                        $query[ 'offset' ]         = 0;
                        $query[ 'posts_per_page' ] = -1;
                }

                $query = apply_filters( 'wpie_pre_execute_post_query', $query );

                add_filter( 'posts_where', array( $this, 'posts_where' ), 10, 1 );

                add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 1 );

                add_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 1 );

                $post_result = new \WP_Query( $query );

                unset( $query );

                remove_filter( 'posts_where', array( $this, 'posts_where' ), 10, 1 );

                remove_filter( 'posts_join', array( $this, 'posts_join' ), 10, 1 );

                remove_filter( 'posts_groupby', array( $this, 'posts_groupby' ), 10, 1 );

                wp_reset_postdata();

                if ( $this->opration === "count" ) {
                        return count( $post_result->posts );
                } elseif ( $this->opration === "ids" ) {
                        return $post_result->posts;
                }

                $post_data = $post_result->posts;

                unset( $post_result );

                $this->process_items( $post_data );

                return true;
        }

        protected function process_items( $post_data = array() ) {

                $fields_data = (isset( $this->template_options[ 'fields_data' ] ) && trim( $this->template_options[ 'fields_data' ] ) != "") ? explode( "~||~", wp_unslash( $this->template_options[ 'fields_data' ] ) ) : [];

                $site_date_format = get_option( 'date_format' );

                $users = array();

                $media = array();

                if ( $this->addons && is_array( $this->addons ) ) {

                        foreach ( $this->addons as $addon ) {

                                if ( method_exists( $addon, "init_export_process" ) ) {

                                        $addon->init_export_process( $post_data, $this->template_options, $this->export_id );
                                }
                        }
                }

                if ( $post_data ) {

                        $temp_field_count = 0;

                        global $wpie_export_id;

                        $wpie_export_id = 0;

                        foreach ( $post_data as $post_id ) {

                                $wpie_export_id = $post_id;

                                $item = get_post( $post_id );

                                $this->export_data = array();

                                $this->has_multiple_rows = false;

                                if ( !empty( $fields_data ) ) {

                                        foreach ( $fields_data as $field ) {

                                                if ( empty( $field ) ) {
                                                        continue;
                                                }
                                                $temp_field_count++;

                                                $new_field = explode( "|~|", $field );

                                                $field_label = isset( $new_field[ 0 ] ) ? $new_field[ 0 ] : "";

                                                $field_option = isset( $new_field[ 1 ] ) ? json_decode( wpie_sanitize_field( $new_field[ 1 ] ), true ) : "";

                                                $field_type = isset( $field_option[ 'type' ] ) ? wpie_sanitize_field( $field_option[ 'type' ] ) : "";

                                                $is_php = isset( $field_option[ 'isPhp' ] ) ? wpie_sanitize_field( $field_option[ 'isPhp' ] ) == 1 : false;

                                                $php_func = isset( $field_option[ 'phpFun' ] ) ? wpie_sanitize_field( $field_option[ 'phpFun' ] ) : "";

                                                $date_type = isset( $field_option[ 'dateType' ] ) ? wpie_sanitize_field( $field_option[ 'dateType' ] ) : "";

                                                $date_format = isset( $field_option[ 'dateFormat' ] ) ? wpie_sanitize_field( $field_option[ 'dateFormat' ] ) : "";

                                                $field_name = strtolower( preg_replace( "/[^a-zA-Z0-9]+/", "", $field_type . $field_label ) ) . $this->get_unique_str() . "_" . $temp_field_count;

                                                if ( $this->process_log[ 'exported' ] == 0 ) {

                                                        $this->export_labels[ $field_name ] = $field_label;

                                                        if ( $this->addons && is_array( $this->addons ) ) {

                                                                foreach ( $this->addons as $addon ) {

                                                                        if ( method_exists( $addon, "change_export_labels" ) ) {

                                                                                $addon->change_export_labels( $this->export_labels, $field_type, $field_name, $field_label, $field_option );
                                                                        }
                                                                }
                                                        }
                                                }
                                                if ( $this->addons && is_array( $this->addons ) ) {

                                                        foreach ( $this->addons as $addon ) {

                                                                if ( method_exists( $addon, "pre_process_data" ) ) {

                                                                        $addon->pre_process_data( $this->export_labels, $field_type, $field_name, $field_label );
                                                                }
                                                        }
                                                }

                                                switch ( $field_type ) {

                                                        case 'id':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_id', $this->apply_user_function( $item->ID, $is_php, $php_func ), $item );
                                                                break;
                                                        case 'permalink':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_permalink', $this->apply_user_function( get_permalink( $item ), $is_php, $php_func ), $item );
                                                                break;

                                                        case 'post_type':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_type', $this->apply_user_function( $item->post_type, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'title':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_title', $this->apply_user_function( $item->post_title, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'content':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_content', $this->apply_user_function( $item->post_content, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'date':

                                                                $post_date = $this->get_date_field( $date_type, get_post_time( 'U', true, $item->ID ), $date_format );

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_date', $this->apply_user_function( $post_date, $is_php, $php_func ), $item );

                                                                unset( $post_date );

                                                                break;

                                                        case 'post_modified':

                                                                $post_modified_date = $this->get_date_field( $date_type, get_post_modified_time( 'U', true, $item->ID ), $date_format );

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_modified_time', $this->apply_user_function( $post_modified_date, $is_php, $php_func ), $item );

                                                                unset( $post_modified_date );

                                                                break;

                                                        case 'parent':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_parent', $this->apply_user_function( $item->post_parent, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'parent_slug':

                                                                $parent_slug = '';

                                                                if ( $item->post_parent != 0 ) {

                                                                        $wpie_parent_posts = get_post_ancestors( $item->ID );

                                                                        if ( !empty( $wpie_parent_posts ) ) {

                                                                                $wpie_slugs = array();

                                                                                foreach ( $wpie_parent_posts as $wpie_parent_post ) {

                                                                                        $the_post = get_post( $wpie_parent_post );

                                                                                        if ( $the_post ) {
                                                                                                $wpie_slugs[] = $the_post->post_name;
                                                                                        }

                                                                                        unset( $the_post );
                                                                                }

                                                                                $parent_slug = implode( "/", array_reverse( $wpie_slugs ) );

                                                                                unset( $wpie_slugs );
                                                                        } else {

                                                                                $the_post = get_post( $item->post_parent );

                                                                                if ( $the_post ) {
                                                                                        $parent_slug = $the_post->post_name;
                                                                                }

                                                                                unset( $the_post );
                                                                        }

                                                                        unset( $wpie_parent_posts );
                                                                } else {
                                                                        $parent_slug = $item->post_parent;
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_parent_slug', $this->apply_user_function( $parent_slug, $is_php, $php_func ), $item );

                                                                unset( $parent_slug );

                                                                break;

                                                        case 'comment_status':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_comment_status', $this->apply_user_function( $item->comment_status, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'ping_status':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_ping_status', $this->apply_user_function( $item->ping_status, $is_php, $php_func ), $item );
                                                                break;
                                                        case 'post_password':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_password', $this->apply_user_function( $item->post_password, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'template':

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_template', $this->apply_user_function( get_post_meta( $item->ID, '_wp_page_template', true ), $is_php, $php_func ), $item );
                                                                break;

                                                        case 'order':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_menu_order', $this->apply_user_function( $item->menu_order, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'status':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_status', $this->apply_user_function( $item->post_status, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'format':

                                                                $postFormat = get_post_format( $item->ID );

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_format', $this->apply_user_function( ($postFormat === false ? "" : $postFormat ), $is_php, $php_func ), $item );
                                                                break;

                                                        case 'author_id':
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_author_id', $this->apply_user_function( $item->post_author, $is_php, $php_func ), $item );
                                                                break;

                                                        case 'author_username':

                                                                if ( !isset( $users[ $item->post_author ] ) ) {
                                                                        $users[ $item->post_author ] = get_user_by( 'id', $item->post_author );
                                                                }

                                                                $user_data = $users[ $item->post_author ];

                                                                if ( is_object( $user_data ) ) {
                                                                        $username = isset( $user_data->user_login ) ? $user_data->user_login : "";
                                                                } else {
                                                                        $username = '';
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_author', $this->apply_user_function( $username, $is_php, $php_func ), $item );

                                                                unset( $user_data, $username );

                                                                break;
                                                        case 'author_email':

                                                                if ( !isset( $users[ $item->post_author ] ) ) {
                                                                        $users[ $item->post_author ] = get_user_by( 'id', $item->post_author );
                                                                }

                                                                $user_data = $users[ $item->post_author ];

                                                                if ( is_object( $user_data ) ) {
                                                                        $user_email = isset( $user_data->user_email ) ? $user_data->user_email : "";
                                                                } else {
                                                                        $user_email = '';
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_author_email', $this->apply_user_function( $user_email, $is_php, $php_func ), $item );

                                                                unset( $user_data, $user_email );

                                                                break;
                                                        case 'wpie_cf_first_name':

                                                                if ( !isset( $users[ $item->post_author ] ) ) {
                                                                        $users[ $item->post_author ] = get_user_by( 'id', $item->post_author );
                                                                }

                                                                $user_data = $users[ $item->post_author ];

                                                                if ( is_object( $user_data ) ) {
                                                                        $user_fname = isset( $user_data->first_name ) ? $user_data->first_name : "";
                                                                } else {
                                                                        $user_fname = '';
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_author_first_name', $this->apply_user_function( $user_fname, $is_php, $php_func ), $item );

                                                                unset( $user_data, $user_fname );

                                                                break;
                                                        case 'wpie_cf_last_name':

                                                                if ( !isset( $users[ $item->post_author ] ) ) {
                                                                        $users[ $item->post_author ] = get_user_by( 'id', $item->post_author );
                                                                }

                                                                $user_data = $users[ $item->post_author ];

                                                                if ( is_object( $user_data ) ) {
                                                                        $user_lname = isset( $user_data->last_name ) ? $user_data->last_name : "";
                                                                } else {
                                                                        $user_lname = '';
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_author_last_name', $this->apply_user_function( $user_lname, $is_php, $php_func ), $item );

                                                                unset( $user_data, $user_lname );

                                                                break;

                                                        case 'slug':

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_slug', $this->apply_user_function( $item->post_name, $is_php, $php_func ), $item );

                                                                break;

                                                        case 'excerpt':
                                                        case 'post_excerpt':

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_excerpt', $this->apply_user_function( $item->post_excerpt, $is_php, $php_func ), $item );

                                                                break;
                                                        case 'wpie_cf':

                                                                $meta_value = "";

                                                                $meta_key = isset( $field_option[ 'metaKey' ] ) ? wpie_sanitize_field( $field_option[ 'metaKey' ] ) : "";

                                                                if ( $meta_key != "" ) {

                                                                        $post_meta_value = get_post_meta( $item->ID, $meta_key );

                                                                        if ( !empty( $post_meta_value ) && is_array( $post_meta_value ) ) {

                                                                                foreach ( $post_meta_value as $mkey => $mvalue ) {
                                                                                        if ( empty( $meta_value ) ) {
                                                                                                $meta_value = maybe_serialize( $mvalue );
                                                                                        } else {
                                                                                                $meta_value = $meta_value . "||" . maybe_serialize( $mvalue );
                                                                                        }
                                                                                }
                                                                        }
                                                                        unset( $post_meta_value );
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_meta', $this->apply_user_function( $meta_value, $is_php, $php_func ), $meta_key, $item );

                                                                unset( $meta_value, $meta_key );

                                                                break;
                                                        case 'wpie_tax':

                                                                $tax_name = isset( $field_option[ 'taxName' ] ) ? wpie_sanitize_field( $field_option[ 'taxName' ] ) : "";

                                                                $tax_value = array();

                                                                if ( $tax_name != "" ) {

                                                                        $_post_id = $item->ID;
                                                                        if ( $this->addons && is_array( $this->addons ) ) {
                                                                                foreach ( $this->addons as $addon ) {
                                                                                        if ( method_exists( $addon, "process_item_taxonomy_id" ) ) {
                                                                                                $_post_id = $addon->process_item_taxonomy_id( $_post_id, $field_type, $field_name, $field_option, $item );
                                                                                        }
                                                                                }
                                                                        }

                                                                        $tax_value = $this->get_hierarchy_by_post_id( $_post_id, $tax_name );
                                                                }

                                                                if ( !empty( $tax_value ) ) {
                                                                        $tax_value = implode( ",", $tax_value );
                                                                }

                                                                $tax_value = $tax_value === false ? "" : $tax_value;

                                                                $this->export_data[ $field_name ] = $tax_value;

                                                                if ( $this->addons && is_array( $this->addons ) ) {
                                                                        foreach ( $this->addons as $addon ) {
                                                                                if ( method_exists( $addon, "process_item_taxonomy" ) ) {
                                                                                        $addon->process_item_taxonomy( $this->export_data, $field_type, $field_name, $field_option, $item );
                                                                                }
                                                                        }
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_term', $this->apply_user_function( $this->export_data[ $field_name ], $is_php, $php_func ), $tax_name, $item );

                                                                unset( $tax_name, $tax_value );

                                                                break;

                                                        case 'attachments':
                                                        case 'attachment_id':
                                                        case 'attachment_url':
                                                        case 'attachment_filename':
                                                        case 'attachment_path':
                                                        case 'attachment_title':
                                                        case 'attachment_caption':
                                                        case 'attachment_description':
                                                        case 'attachment_alt':

                                                                if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-media.php' ) ) {

                                                                        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-media.php');
                                                                }

                                                                $wpie_media = new \wpie\export\media\WPIE_Media();

                                                                if ( !(isset( $media[ $item->ID ] )) ) {

                                                                        $media[ $item->ID ] = $wpie_media->get_media( $item->ID );
                                                                }

                                                                $attach_value = "";

                                                                if ( !empty( $media[ $item->ID ] ) ) {

                                                                        $attch = $wpie_media->get_attch( $media[ $item->ID ], $field_type, 'post' );

                                                                        if ( !empty( $attch ) ) {
                                                                                $attach_value = implode( "||", $attch );
                                                                        }
                                                                        unset( $attch );
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_attach_' . $field_type, $this->apply_user_function( $attach_value, $is_php, $php_func ), $item );

                                                                unset( $wpie_media, $attach_value );

                                                                break;
                                                        // Media Images
                                                        case 'media':
                                                        case 'image_id':
                                                        case 'image_url':
                                                        case 'image_filename':
                                                        case 'image_path':
                                                        case 'image_title':
                                                        case 'image_caption':
                                                        case 'image_description':
                                                        case 'image_alt':

                                                                if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-media.php' ) ) {

                                                                        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-media.php');
                                                                }

                                                                $wpie_media = new \wpie\export\media\WPIE_Media();

                                                                if ( !(isset( $media[ $item->ID ] )) ) {

                                                                        $media[ $item->ID ] = $wpie_media->get_media( $item->ID );
                                                                }

                                                                $image_media = "";

                                                                if ( isset( $media[ $item->ID ] ) ) {

                                                                        $image_data = $wpie_media->get_images( $item->ID, $media[ $item->ID ], $field_type, 'post' );

                                                                        if ( !empty( $image_data ) ) {
                                                                                $image_media = implode( "||", $image_data );
                                                                        }

                                                                        unset( $image_data );
                                                                }

                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_image_' . $field_type, $this->apply_user_function( $image_media, $is_php, $php_func ), $item );

                                                                unset( $wpie_media, $image_media );

                                                                break;

                                                        default:
                                                                $this->export_data[ $field_name ] = apply_filters( 'wpie_export_post_field', $this->apply_user_function( "", $is_php, $php_func ), $item );
                                                                break;
                                                }


                                                if ( $this->addons && is_array( $this->addons ) ) {
                                                        foreach ( $this->addons as $addon ) {
                                                                if ( method_exists( $addon, "process_addon_data" ) ) {
                                                                        $addon->process_addon_data( $this->export_data, $field_type, $field_name, $field_option, $item, $site_date_format );
                                                                }
                                                        }
                                                }

                                                unset( $new_field, $field_label, $field_option, $field_type, $is_php, $php_func, $date_type, $date_format, $field_name );
                                        }

                                        if ( $this->addons && is_array( $this->addons ) ) {

                                                foreach ( $this->addons as $addon ) {

                                                        if ( method_exists( $addon, "finalyze_export_process" ) ) {

                                                                $addon->finalyze_export_process( $this->export_data, $this->has_multiple_rows );
                                                        }
                                                }
                                        }

                                        $this->process_data();
                                }

                                unset( $item );
                        }

                        $wpie_export_id = 0;
                }

                unset( $fields_data, $site_date_format, $users, $media );
        }

        public function posts_where( $data = "" ) {

                global $wpdb;

                $this->item_where = apply_filters( 'wpie_export_posts_where', $this->item_where, $this->item_join );

                if ( !empty( $this->item_where ) ) {

                        $post_where = preg_replace( array( '/OR$/', '/OR $/', '/AND$/', '/AND $/' ), " ", preg_replace( array( '%^ OR %', '%^ AND %' ), ' ', implode( ' ', $this->item_where ) ) );

                        $data .= " AND ( " . $post_where . ")";

                        unset( $post_where );
                }
                $data .= " AND ({$wpdb->posts}.post_status != 'auto-draft' AND {$wpdb->posts}.post_title != 'Auto Draft') ";

                return $data;
        }

        public function posts_join( $data = "" ) {

                $this->item_join = apply_filters( 'wpie_export_posts_join', $this->item_join );

                if ( !empty( $this->item_join ) ) {

                        $data .= implode( ' ', array_unique( $this->item_join ) );
                }

                return $data;
        }

        public function posts_groupby( $groupby = "" ) {

                global $wpdb;

                $groupby = "{$wpdb->posts}.ID";

                return $groupby;
        }

        private function get_valid_element( $element = "" ) {

                switch ( $element ) {
                        case 'id':
                                $return_data = strtoupper( $element );
                                break;
                        case 'parent':
                        case 'author':
                        case 'status':
                        case 'title':
                        case 'content':
                        case 'date':
                        case 'excerpt':
                                $return_data = 'post_' . $element;
                                break;
                        case 'permalink':
                                $return_data = 'guid';
                                break;
                        case 'slug':
                                $return_data = 'post_name';
                                break;
                        case 'order':
                                $return_data = 'menu_order';
                                break;
                        case 'template':
                                $return_data = 'wpie_cf__wp_page_template';
                                break;
                        case 'format':
                                $return_data = 'wpie_tax_post_format';
                                break;
                        default:
                                $return_data = $element;
                                break;
                }

                return $return_data;
        }

        private function check_children_assign( $parent, $taxonomy, $term_ids = array() ) {

                $is_latest_child = true;

                $children = get_term_children( $parent, $taxonomy );

                if ( !is_wp_error( $children ) && is_array( $children ) && !empty( $children ) ) {

                        foreach ( $children as $child ) {

                                if ( in_array( $child, $term_ids ) ) {

                                        $is_latest_child = false;

                                        break;
                                }
                        }
                }
                unset( $children );

                return $is_latest_child;
        }

        public function get_hierarchy_by_taxonomy_id( $tax_ids = array(), $tax_name = "" ) {

                if ( is_array( $tax_ids ) && !empty( $tax_ids ) ) {

                        global $wp_version;

                        if ( version_compare( $wp_version, '4.5.0', '<' ) ) {

                                $taxonomies = get_terms( $tax_name, array( 'include' => $tax_ids, "hide_empty" => false ) );
                        } else {
                                $taxonomies = get_terms( array( 'taxonomy' => $tax_name, 'include' => $tax_ids, "hide_empty" => false ) );
                        }

                        return $this->get_hierarchy_by_taxonomy( $taxonomies );
                }
                return false;
        }

        private function get_hierarchy_by_post_id( $_post_id = 0, $tax_name = "" ) {

                $taxonomies = get_the_terms( $_post_id, $tax_name );

                return $this->get_hierarchy_by_taxonomy( $taxonomies, $tax_name );
        }

        private function get_hierarchy_by_taxonomy( $taxonomy = array(), $tax_name = "" ) {

                $hierarchy_groups = array();

                if ( !is_wp_error( $taxonomy ) && !empty( $taxonomy ) ) {

                        $tax_ids = array();

                        foreach ( $taxonomy as $tax_list ) {
                                $tax_ids[] = $tax_list->term_id;
                        }

                        foreach ( $taxonomy as $tax_list ) {

                                if ( $this->check_children_assign( $tax_list->term_id, $tax_name, $tax_ids ) ) {

                                        $ancestors = get_ancestors( $tax_list->term_id, $tax_name );

                                        if ( count( $ancestors ) > 0 ) {

                                                $hierarchy = array();

                                                for ( $i = count( $ancestors ) - 1; $i >= 0; $i-- ) {

                                                        $term = get_term_by( 'id', $ancestors[ $i ], $tax_name );

                                                        if ( $term ) {
                                                                $hierarchy[] = $term->name;
                                                        }
                                                        unset( $term );
                                                }

                                                $hierarchy[] = $tax_list->name;

                                                $hierarchy_groups[] = implode( '>', $hierarchy );

                                                unset( $hierarchy );
                                        } else {

                                                $hierarchy_groups[] = $tax_list->name;
                                        }
                                        unset( $ancestors );
                                }
                        }

                        unset( $tax_ids );
                }

                if ( !empty( $hierarchy_groups ) ) {

                        return $hierarchy_groups;
                }

                return false;
        }

        public function pre_user_query( $obj ) {

                $obj->query_where .= $this->user_where;

                if ( !empty( $this->user_join ) ) {
                        $obj->query_from .= implode( ' ', array_unique( $this->user_join ) );
                }
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

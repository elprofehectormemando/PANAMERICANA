<?php


namespace wpie\import\images;

use wpie\import\Downloader\Manager as Downloader;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

if ( file_exists( ABSPATH . 'wp-admin/includes/image.php' ) ) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
}
if ( file_exists( ABSPATH . 'wp-admin/includes/media.php' ) ) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
}
if ( file_exists( ABSPATH . 'wp-admin/includes/file.php' ) ) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
}

class WPIE_Images extends \wpie\import\base\WPIE_Import_Base {

        private $target_dir;
        private $images        = [];
        private $attach        = [];
        private $gallary       = [];
        private $image_options = [];

        public function __construct( $item_id = 0, $is_new_item = true, $wpie_import_option = array(), $wpie_import_record = array(), $import_type = "post" ) {

                $this->item_id = $item_id;

                $this->is_new_item = $is_new_item;

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $this->import_type = $import_type;
        }

        public function prepare_images() {

                if ( $this->item_id ) {

                        $wp_uploads = wp_upload_dir();

                        $this->target_dir = $wp_uploads[ 'path' ];

                        $this->prepare_image_option();

                        if ( !$this->is_new_item && trim( strtolower( wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_images' ) ) ) ) === "all" ) {
                                $this->prepare_old_attch();
                        }

                        $image_option = trim( strtolower( wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_option', true ) ) ) );

                        $this->process_images( $image_option );

                        if ( !$this->is_new_item && wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_images' ) ) == "all" ) {
                                $this->remove_old_attch( "images" );
                        }

                        $this->set_gallary_images();

                        unset( $wp_uploads, $image_option );
                }

                return array( "as_draft" => $this->as_draft, "import_log" => $this->import_log );
        }

        private function is_search_existing() {

                return absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_search_existing_images', true ) ) ) === 1;
        }

        private function process_images( $method = "media_library" ) {

                $this->images = [];

                $method = ("download_images" === $method) ? "url" : (("local_images" === $method) ? "local" : $method);

                $image_data = $this->get_field_value( 'wpie_item_image_' . $method );

                if ( empty( $image_data ) ) {
                        return true;
                }

                $data = explode( "\n", $image_data );

                if ( (!isset( $data[ 1 ] )) || ( isset( $data[ 1 ] ) && empty( $data[ 1 ] )) ) {

                        $delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_' . $method . '_delim' ) );

                        $data = explode( empty( $delim ) ? "|" : $delim, $image_data );

                        unset( $delim );
                }

                if ( empty( $data ) || !is_array( $data ) ) {
                        return true;
                }

                foreach ( $data as $index => $image ) {

                        if ( empty( $image ) ) {
                                continue;
                        }
                        $attch_id = null;

                        $existing_image = $this->get_existing_image( $image );

                        if ( $existing_image !== false && absint( $existing_image ) > 0 ) {
                                $attch_id = $existing_image;
                        }

                        if ( empty( $attch_id ) && (($method === "media_library") || ($method !== "media_library" && $this->is_search_existing() ) ) ) {

                                $media_id = $this->wpie_get_image_from_gallery( $image );

                                if ( $media_id !== false ) {
                                        $attch_id = absint( $media_id );
                                }
                        }

                        if ( empty( $attch_id ) ) {

                                $newExt  = isset( $this->image_options[ 'new_ext' ][ $index ] ) ? $this->image_options[ 'new_ext' ][ $index ] : null;
                                $newName = isset( $this->image_options[ 'new_name' ][ $index ] ) ? $this->image_options[ 'new_name' ][ $index ] : null;

                                $temp_id = false;

                                if ( $method === "local" ) {
                                        $temp_id = $this->wpie_get_image_from_local( $image, $newName, $newExt );
                                } elseif ( $method === "url" ) {
                                        $temp_id = $this->wpie_get_image_from_url( $image, $newName, $newExt );
                                }
                                if ( $temp_id && (!empty( $temp_id )) && absint( $temp_id ) > 0 ) {
                                        $attch_id = absint( $temp_id );
                                } else {
                                        $this->set_as_draft();
                                }
                        }

                        if ( !empty( $attch_id ) ) {

                                $this->images[] = absint( $attch_id );
                                $this->wpie_set_image_meta( absint( $attch_id ), $index );
                        }
                }

                $this->images = apply_filters( 'wpie_import_images_ids', $this->images, $this->item_id );
        }

        private function prepare_image_option() {

                $this->prepare_image_meta();

                $this->image_options = [];

                $this->image_options[ 'new_name' ] = [];

                if ( absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_rename', true ) ) ) === 1 ) {

                        $nameList = [];

                        $new_names = wpie_sanitize_field( $this->get_field_value( 'wpie_item_image_new_name' ) );

                        if ( !empty( $new_names ) ) {

                                $data = explode( "\n", $new_names );

                                if ( (!isset( $data[ 1 ] )) || ( isset( $data[ 1 ] ) && empty( $data[ 1 ] )) ) {
                                        $delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_image_rename_delim' ) );

                                        $nameList = explode( $delim != "" ? $delim : ",", $new_names );

                                        unset( $delim );
                                } else {
                                        $nameList = $data;
                                }
                        }

                        $this->image_options[ 'new_name' ] = $nameList;

                        unset( $new_names );
                }

                $this->image_options[ 'new_ext' ] = [];

                if ( absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_change_ext', true ) ) ) === 1 ) {

                        $extList = [];

                        $new_ext = wpie_sanitize_field( $this->get_field_value( 'wpie_item_new_ext' ) );

                        if ( !empty( $new_ext ) ) {

                                $data = explode( "\n", $new_ext );

                                if ( (!isset( $data[ 1 ] )) || ( isset( $data[ 1 ] ) && empty( $data[ 1 ] )) ) {
                                        $delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_image_new_ext_delim' ) );

                                        $extList = explode( $delim != "" ? $delim : ",", $new_ext );

                                        unset( $delim );
                                } else {
                                        $extList = $data;
                                }
                        }

                        $this->image_options[ 'new_ext' ] = $extList;

                        unset( $new_names );
                }
        }

        private function get_image_meta_values( $field = "" ) {

                $meta = [];

                if ( absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_image_' . $field, true ) ) ) !== 1 ) {
                        return $meta;
                }

                $value = wpie_sanitize_textarea( $this->get_field_value( 'wpie_item_image_' . $field ) );

                if ( empty( $value ) ) {
                        return $meta;
                }

                $data = explode( "\n", $value );

                if ( (!isset( $data[ 1 ] )) || ( isset( $data[ 1 ] ) && empty( $data[ 1 ] )) ) {
                        $delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_image_' . $field . '_delim' ) );

                        $meta = explode( $delim != "" ? $delim : ",", $value );

                        unset( $delim );
                } else {
                        $meta = $data;
                }
                unset( $value, $data );

                return $meta;
        }

        private function prepare_image_meta() {

                $this->image_meta = [
                        'title'   => $this->get_image_meta_values( "title" ),
                        'caption' => $this->get_image_meta_values( "caption" ),
                        'alt'     => $this->get_image_meta_values( "alt" ),
                        'desc'    => $this->get_image_meta_values( "description" )
                ];
        }

        private function wpie_set_image_meta( $attch_id = 0, $index = 0 ) {

                $update_attch_meta = array();

                if ( isset( $this->image_meta[ 'title' ][ $index ] ) ) {

                        $update_attch_meta[ 'post_title' ] = $this->image_meta[ 'title' ][ $index ];
                }
                if ( isset( $this->image_meta[ 'caption' ][ $index ] ) ) {

                        $update_attch_meta[ 'post_excerpt' ] = $this->image_meta[ 'caption' ][ $index ];
                }
                if ( isset( $this->image_meta[ 'alt' ][ $index ] ) ) {

                        update_post_meta( $attch_id, '_wp_attachment_image_alt', $this->image_meta[ 'alt' ][ $index ] );
                }
                if ( isset( $this->image_meta[ 'desc' ][ $index ] ) ) {

                        $update_attch_meta[ 'post_content' ] = $this->image_meta[ 'desc' ][ $index ];
                }

                if ( !empty( $update_attch_meta ) ) {

                        global $wpdb;

                        $wpdb->update( $wpdb->posts, $update_attch_meta, array( 'ID' => $attch_id ) );
                }

                unset( $update_attch_meta );
        }

        private function wpie_get_image_from_gallery( $image_name = "" ) {

                if ( empty( $image_name ) ) {
                        return false;
                }
                global $wpdb;

                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = '_wpie_source_url' AND meta.meta_value = %s LIMIT 0,1;", esc_url_raw( $image_name ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = %s AND (meta.meta_value = %s OR meta.meta_value LIKE %s) LIMIT 0,1;", '_wp_attached_file', basename( $image_name ), "%/" . basename( $image_name ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = %s AND (meta.meta_value = %s OR meta.meta_value LIKE %s) LIMIT 0,1;", '_wp_attached_file', sanitize_file_name( basename( $image_name ) ), "%/" . sanitize_file_name( basename( $image_name ) ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $wp_filetype = wp_check_filetype( basename( $image_name ) );

                if ( isset( $wp_filetype[ 'type' ] ) && !empty( $wp_filetype[ 'type' ] ) ) {
                        $name  = pathinfo( $image_name, PATHINFO_FILENAME );
                        $attch = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'attachment' AND post_mime_type LIKE %s;", $name, "image%" ) );
                        if ( $attch && absint( $attch ) > 0 ) {
                                return $attch;
                        }
                }
                return false;
        }

        private function wpie_get_image_from_local( $filename = "", $newName = null, $newExt = null ) {

                if ( (!wp_is_writable( $this->target_dir )) || empty( $filename ) ) {
                        return false;
                }

                $file = WPIE_UPLOAD_TEMP_DIR . "/" . $filename;

                if ( !file_exists( $file ) ) {
                        $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $filename . " " . __( 'Image Not Exist', 'wp-import-export-lite' );
                        return false;
                }
                if ( !$this->is_valid_image( $file ) ) {
                        $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $filename . " " . __( 'File is not valid Image', 'wp-import-export-lite' );
                        return false;
                }


                if ( !empty( $newName ) ) {
                        $filename = sanitize_file_name( pathinfo( $newName, PATHINFO_FILENAME ) . '.' . pathinfo( $filename, PATHINFO_EXTENSION ) );
                }

                if ( !empty( $newExt ) ) {
                        $filename = sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) . '.' . ltrim( $newExt, "." ) );
                }


                $upload_file = wp_upload_bits( $filename, null, file_get_contents( $file ) );

                unset( $file );

                if ( !$upload_file[ 'error' ] ) {

                        $wp_filetype = wp_check_filetype( $filename, null );

                        $attachment = array(
                                'post_mime_type' => $wp_filetype[ 'type' ],
                                'post_parent'    => $this->item_id,
                                'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                                'post_author'    => $this->get_post_user()
                        );

                        $attachment_id = wp_insert_attachment( $attachment, $upload_file[ 'file' ], $this->item_id );

                        if ( !is_wp_error( $attachment_id ) && absint( $attachment_id ) > 0 ) {

                                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file[ 'file' ] );

                                wp_update_attachment_metadata( $attachment_id, $attachment_data );

                                unset( $attachment_data );
                        }

                        unset( $attachment, $wp_filetype, $upload_file, $filename );

                        return $attachment_id;
                }

                $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Image Upload failed', 'wp-import-export-lite' );

                unset( $upload_file, $filename );
        }

        private function is_valid_image( $file = "" ) {

                if ( empty( $file ) ) {
                        return false;
                }

                if ( !is_readable( $file ) ) {
                        return false;
                }

                if ( !preg_match( '/[^\?]+\.(jpg|jpeg|jpe|gif|png|bmp|tif|tiff|ico|heic|webp|svg)\b/i', strtolower( trim( $file ) ) ) ) {
                        return false;
                }

                $filesize = filesize( $file );

                if ( $filesize === 0 || $filesize === false ) {
                        return false;
                }

                return true;
        }

        private function get_post_user() {

                $user_id = 0;
                if ( !empty( $this->item_id ) ) {
                        $post = get_post( $this->item_id );
                        if ( $post && isset( $post->post_author ) && !empty( $post->post_author ) ) {
                                $user_id = $post->post_author;
                        }
                        unset( $post );
                }
                if ( $user_id === 0 ) {
                        if ( !empty( $this->import_username ) ) {

                                $user = get_user_by( "login", $this->import_username );

                                if ( $user && isset( $user->ID ) ) {
                                        $user_id = $user->ID;
                                }
                                unset( $user );
                        }
                }
                if ( $user_id === 0 ) {

                        $current_user = wp_get_current_user();

                        if ( $current_user && isset( $current_user->ID ) ) {
                                $user_id = $current_user->ID;
                        }
                        unset( $current_user );
                }
                return $user_id;
        }

        private function wpie_get_image_from_url( $file_url = "", $newName = null, $newExt = null ) {

                if ( empty( $file_url ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File URL is empty', 'wp-import-export-lite' ) );
                }

                if ( is_readable( WPIE_IMPORT_CLASSES_DIR . '/downloader/manager.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/downloader/manager.php');
                }

                $download_manager = new Downloader();

                $fileName = $download_manager->get_filename( $file_url, "image" );

                if ( \is_wp_error( $fileName ) ) {
                        $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $fileName->get_error_message();
                        return false;
                }

                $newFileName = $fileName;

                if ( !empty( $newName ) ) {
                        $newFileName = sanitize_file_name( pathinfo( $newName, PATHINFO_FILENAME ) . '.' . pathinfo( $fileName, PATHINFO_EXTENSION ) );
                }

                if ( !empty( $newExt ) ) {
                        $newFileName = sanitize_file_name( pathinfo( $newFileName, PATHINFO_FILENAME ) . '.' . ltrim( $newExt, "." ) );
                }

                if ( $newFileName !== $fileName ) {
                        $fileName = $newFileName;

                        $download_manager->setFilename( $fileName );
                }


                if ( $this->is_search_existing() ) {
                        $media_id = $this->wpie_get_image_from_gallery( $fileName );

                        if ( $media_id !== false && absint( $media_id ) > 0 ) {
                                unset( $download_manager );
                                return \absint( $media_id );
                        }
                }

                $file = $download_manager->download();

                if ( \is_wp_error( $file ) ) {
                        $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $file->get_error_message();
                        return false;
                }

                unset( $download_manager );

                $id = $this->media_handle_upload( [ 'name' => $fileName, 'tmp_name' => $file, 'url' => $file_url ], $this->item_id );

                // If error storing permanently, unlink.
                if ( \is_wp_error( $id ) ) {
                        if ( \file_exists( $file ) ) {
                                \unlink( $file );
                        }
                        $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $id->get_error_message();
                        return false;
                }
                // Store the original attachment source in meta.
                \update_post_meta( $id, '_wpie_source_url', esc_url_raw( $file_url ) );

                $author_id = \absint( $this->get_post_user() );

                if ( $author_id > 0 ) {
                        global $wpdb;
                        $wpdb->update( $wpdb->posts, [ "post_author" => $author_id ], [ 'ID' => $id ] );
                }
                return $id;
        }

        private function prepare_old_attch() {
                $this->attach = get_posts(
                        [
                                'post_parent' => $this->item_id,
                                'post_type'   => 'attachment',
                                'numberposts' => -1,
                                'post_status' => null,
                                "fields"      => "ids"
                        ]
                );
        }

        private function remove_old_attch( $type = 'images' ) {

                if ( $type === 'images' && has_post_thumbnail( $this->item_id ) ) {
                        delete_post_thumbnail( $this->item_id );
                }

                $ids = array();

                if ( !empty( $this->attach ) ) {

                        $keep_images = absint( (wpie_sanitize_field( $this->get_field_value( 'wpie_item_keep_images', true ) ) ) === 1 );

                        $attachments = array_diff( $this->attach, $this->images );

                        if ( !empty( $attachments ) ) {

                                foreach ( $attachments as $attach ) {

                                        if ( ($type === 'files' && !wp_attachment_is_image( $attach )) || ($type === 'images' && wp_attachment_is_image( $attach )) ) {

                                                if ( $keep_images === false ) {
                                                        wp_delete_attachment( $attach, true );
                                                } else {
                                                        $ids[] = $attach;
                                                }
                                        }
                                }
                        }
                        unset( $attachments, $keep_images );

                        global $wpdb;

                        if ( !empty( $ids ) ) {

                                $ids_string = implode( ',', array_map( "absint", $ids ) );

                                $wpdb->query( "UPDATE $wpdb->posts SET post_parent = 0 WHERE post_type = 'attachment' AND ID IN ( $ids_string )" );

                                unset( $ids_string );

                                foreach ( $ids as $att_id ) {
                                        clean_attachment_cache( $att_id );
                                }
                        }
                }

                return $ids;
        }

        private function set_as_draft() {
                if ( !$this->as_draft && absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_unsuccess_set_draft' ) ) ) === 1 ) {
                        $this->as_draft = true;
                }
        }

        private function set_gallary_images() {

                $this->gallary = $this->images;

                if ( !empty( $this->images ) && absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_first_imaege_is_featured' ) ) ) === 1 ) {

                        $image_id = array_shift( $this->gallary );

                        $this->set_thumbnail( $image_id );

                        unset( $image_id );
                }
                update_post_meta( $this->item_id, '_product_image_gallery', (empty( $this->gallary ) ? "" : implode( ",", $this->gallary ) ) );
        }

        private function set_thumbnail( $thumbnail_id ) {

                if ( $this->import_type === "taxonomy" ) {
                        update_term_meta( $this->item_id, "thumbnail_id", $thumbnail_id );
                } else {
                        update_post_meta( $this->item_id, '_thumbnail_id', $thumbnail_id );
                }
        }

        private function get_existing_image( $image = "" ) {

                if ( empty( $this->attach ) || empty( $image ) ) {
                        return false;
                }

                $attach_id = 0;

                $name = sanitize_file_name( basename( $image ) );

                foreach ( $this->attach as $id ) {

                        $file = get_post_meta( $id, "_wp_attached_file", true );

                        if ( !empty( $file ) && $name === sanitize_file_name( basename( $file ) ) || get_the_title( $id ) == pathinfo( $name, PATHINFO_FILENAME ) ) {
                                $attach_id = $id;
                                break;
                        }
                }
                if ( $attach_id !== 0 ) {
                        return $attach_id;
                }
                return false;
        }

        public function relink_content_images( $content = "" ) {

                if ( empty( $content ) ) {
                        return $content;
                }

                if ( strpos( trim( strtolower( $content ) ), "<img" ) === false ) {
                        return $content;
                }

                // search for images in <img> tags
                $tag_images = [];
                $matches    = [];

                if ( preg_match_all( '%<img\s[^>]*src=(?(?=")"([^"]*)"|(?(?=\')\'([^\']*)\'|([^\s>]*)))%is', $content, $matches, PREG_PATTERN_ORDER ) ) {
                        $tag_images = array_unique( array_merge( array_filter( $matches[ 1 ] ), array_filter( $matches[ 2 ] ), array_filter( $matches[ 3 ] ) ) );
                }

                if ( preg_match_all( '%<img\s[^>]*srcset=(?(?=")"([^"]*)"|(?(?=\')\'([^\']*)\'|([^\s>]*)))%is', $content, $matches, PREG_PATTERN_ORDER ) ) {

                        $srcset_images = array_unique( array_merge( array_filter( $matches[ 1 ] ), array_filter( $matches[ 2 ] ), array_filter( $matches[ 3 ] ) ) );

                        if ( !empty( $srcset_images ) ) {

                                foreach ( $srcset_images as $srcset_image ) {

                                        if ( empty( $srcset_image ) ) {
                                                continue;
                                        }

                                        $srcset = array_filter( explode( ",", $srcset_image ) );

                                        foreach ( $srcset as $srcset_img ) {

                                                if ( empty( $srcset_img ) ) {
                                                        continue;
                                                }
                                                $srcset_image_parts = explode( " ", $srcset_img );

                                                foreach ( $srcset_image_parts as $srcset_image_part ) {

                                                        if ( empty( $srcset_image_part ) ) {
                                                                continue;
                                                        }
                                                        if ( !empty( filter_var( $srcset_image_part, FILTER_VALIDATE_URL ) ) ) {
                                                                $tag_images[] = trim( $srcset_image_part );
                                                        }
                                                }
                                        }
                                }
                        }
                }

                if ( empty( $tag_images ) ) {
                        return $content;
                }

                $full_size_images = apply_filters( 'wpie_import_content_images_get_full_size', true, $this->item_id );

                foreach ( $tag_images as $img_url ) {

                        if ( empty( $img_url ) || !preg_match( '%^(http|ftp)s?://%i', $img_url ) ) {
                                continue;
                        }

                        $original_img_url = $img_url;

                        // Trying to get image full size.
                        if ( $full_size_images ) {

                                $full_size = preg_replace( '%-\d{2,4}x\d{2,4}%', '', $img_url );

                                if ( $full_size != $img_url ) {

                                        // check if full size image exists
                                        $image_headers = get_headers( $full_size, true );

                                        if ( $image_headers !== false && isset( $image_headers[ 'Content-Type' ] ) && !empty( $image_headers[ 'Content-Type' ] ) ) {

                                                $content_type = is_array( $image_headers[ 'Content-Type' ] ) ? end( $image_headers[ 'Content-Type' ] ) : $image_headers[ 'Content-Type' ];

                                                if ( strpos( $content_type, 'image' ) !== false ) {
                                                        $img_url = $full_size;
                                                }
                                        }

                                        unset( $image_headers );
                                }

                                unset( $full_size );
                        }

                        $attch_id = $this->wpie_get_image_from_url( $img_url );

                        if ( $attch_id && absint( $attch_id ) > 0 ) {

                                $attach_url = wp_get_attachment_url( absint( $attch_id ) );

                                $content = str_replace( $original_img_url, $attach_url, $content );

                                unset( $attach_url );
                        }

                        unset( $attch_id, $original_img_url );
                }

                return $content;
        }

        private function media_handle_upload( $file_array, $post_id = 0, $desc = null, $post_data = array() ) {

                $oFileUrl = isset( $file_array[ 'url' ] ) ? $file_array[ 'url' ] : "";

                $oFileName = isset( $file_array[ 'name' ] ) ? $file_array[ 'name' ] : "";

                $time = $this->getMediaTime( $oFileUrl, $post_id );

                $uploads = wp_upload_dir( $time );

                $name = \wp_unique_filename( $uploads[ 'path' ], $oFileName );

                // Move the file to the uploads dir.
                $newFile = $uploads[ 'path' ] . "/" . $name;

                if ( !copy( $file_array[ 'tmp_name' ], $newFile ) ) {
                        return new \WP_Error( 'wpie_import_error', sprintf( __( "File Download Error : Can't copy File", 'wp-import-export-lite' ), $oFileUrl ) );
                }

                unlink( $file_array[ 'tmp_name' ] );

                $fileType = wp_check_filetype( $newFile );

                $file = [
                        'file' => $newFile,
                        'url'  => $uploads[ 'url' ] . "/" . $name,
                        'type' => empty( $fileType[ 'type' ] ) ? '' : $fileType[ 'type' ]
                ];

                $ext  = pathinfo( $oFileName, PATHINFO_EXTENSION );
                $name = wp_basename( $oFileName, ".$ext" );

                $url     = $file[ 'url' ];
                $type    = $file[ 'type' ];
                $file    = $file[ 'file' ];
                $title   = sanitize_text_field( $name );
                $content = '';
                $excerpt = '';

                if ( 0 === strpos( $type, 'image/' ) ) {
                        $image_meta = wp_read_image_metadata( $file );

                        if ( $image_meta ) {
                                if ( trim( $image_meta[ 'title' ] ) && !is_numeric( sanitize_title( $image_meta[ 'title' ] ) ) ) {
                                        $title = $image_meta[ 'title' ];
                                }

                                if ( trim( $image_meta[ 'caption' ] ) ) {
                                        $excerpt = $image_meta[ 'caption' ];
                                }
                        }
                }

                // Construct the attachment array.
                $attachment = array_merge(
                        array(
                                'post_mime_type' => $type,
                                'guid'           => $url,
                                'post_parent'    => $post_id,
                                'post_title'     => $title,
                                'post_content'   => $content,
                                'post_excerpt'   => $excerpt,
                        ),
                        $post_data
                );

                // This should never be set as it would then overwrite an existing attachment.
                unset( $attachment[ 'ID' ] );

                // Save the data.
                $attachment_id = wp_insert_attachment( $attachment, $file, $post_id, true );

                if ( !is_wp_error( $attachment_id ) ) {
                        // Set a custom header with the attachment_id.
                        // Used by the browser/client to resume creating image sub-sizes after a PHP fatal error.
                        if ( !headers_sent() ) {
                                header( 'X-WP-Upload-Attachment-ID: ' . $attachment_id );
                        }

                        // The image sub-sizes are created during wp_generate_attachment_metadata().
                        // This is generally slow and may cause timeouts or out of memory errors.
                        wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );
                }

                return $attachment_id;
        }

        private function getMediaTime( $url, $post_id ) {

                $time = "";

                if ( strpos( $url, "wp-content/uploads" ) !== false ) {
                        $time = mb_substr( $url, strpos( $url, "/wp-content/uploads/" ) + 20, 7 );
                }

                if ( empty( $time ) ) {

                        $post = get_post( $post_id );

                        if ( $post && substr( $post->post_date, 0, 4 ) > 0 ) {
                                $time = $post->post_date;
                        } else {
                                $time = current_time( 'mysql' );
                        }
                }
                return $time;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

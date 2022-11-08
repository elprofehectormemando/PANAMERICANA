<?php


namespace wpie\import;

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

class WPIE_Attachments extends \wpie\import\base\WPIE_Import_Base {

        private $target_dir;
        private $attachments         = [];
        private $attach              = [];
        private $gallary             = [];
        private $attachments_options = [];

        public function __construct( $item_id = 0, $is_new_item = true, $wpie_import_option = array(), $wpie_import_record = array(), $import_type = "post" ) {

                $this->item_id = $item_id;

                $this->is_new_item = $is_new_item;

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $this->import_type = $import_type;
        }

        public function prepare_attachments() {

                if ( $this->item_id ) {

                        $wp_uploads = \wp_upload_dir();

                        $this->target_dir = isset( $wp_uploads[ 'path' ] ) ? $wp_uploads[ 'path' ] : "";

                        if ( !$this->is_new_item && trim( strtolower( wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_attachments' ) ) ) ) === "all" ) {
                                $this->prepare_old_attch();
                        }

                        $attachment_option = trim( strtolower( wpie_sanitize_field( $this->get_field_value( 'wpie_item_attachment_option', true ) ) ) );

                        $this->process_attach( $attachment_option );

                        if ( !$this->is_new_item && wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_attachments' ) ) == "all" ) {
                                $this->remove_old_attch( "files" );
                        }

                        unset( $wp_uploads, $attachment_option );
                }

                return array( "as_draft" => $this->as_draft, "import_log" => $this->import_log );
        }

        private function is_search_existing() {

                return absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_attachement_search_for_existing', true ) ) ) === 1;
        }

        private function process_attach( $method = "media_library" ) {

                $this->attachments = [];

                $attach_data = $this->get_field_value( 'wpie_item_attachments' );

                if ( empty( $attach_data ) ) {
                        return true;
                }

                $data = explode( "\n", $attach_data );

                if ( (!isset( $data[ 1 ] )) || ( isset( $data[ 1 ] ) && empty( $data[ 1 ] )) ) {

                        $delim = wpie_sanitize_field( $this->get_field_value( 'wpie_item_attachments_delim' ) );

                        $data = explode( empty( $delim ) ? "||" : $delim, $attach_data );

                        unset( $delim );
                }

                if ( empty( $data ) || !is_array( $data ) ) {
                        return true;
                }

                foreach ( $data as $index => $attach ) {

                        if ( empty( $attach ) ) {
                                continue;
                        }
                        $attch_id = null;

                        $existing_attach = $this->get_existing_attach( $attach );

                        if ( $existing_attach !== false && absint( $existing_attach ) > 0 ) {
                                $attch_id = $existing_attach;
                        }

                        if ( empty( $attch_id ) ) {

                                $media_id = $this->wpie_get_attach_from_media_library( $attach );

                                if ( $media_id !== false ) {
                                        $attch_id = absint( $media_id );
                                }
                        }

                        if ( empty( $attch_id ) ) {

                                $temp_id = $this->wpie_get_attach_from_url( $attach );

                                if ( $temp_id && absint( $temp_id ) > 0 ) {
                                        $attch_id = absint( $temp_id );
                                }
                        }

                        if ( !empty( $attch_id ) ) {

                                $this->attachments[] = absint( $attch_id );
                        }
                }

                $this->attachments = apply_filters( 'wpie_import_attachments_ids', $this->attachments, $this->item_id );
        }

        private function wpie_get_attach_from_media_library( $attach_name = "" ) {

                if ( empty( $attach_name ) ) {
                        return false;
                }
                global $wpdb;

                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = '_wpie_source_url' AND meta.meta_value = %s LIMIT 0,1;", esc_url_raw( $attach_name ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = %s AND (meta.meta_value = %s OR meta.meta_value LIKE %s) LIMIT 0,1;", '_wp_attached_file', basename( $attach_name ), "%/" . basename( $attach_name ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $attachment = $wpdb->get_var( $wpdb->prepare( "SELECT post.ID FROM {$wpdb->posts} post INNER JOIN {$wpdb->postmeta} meta ON post.ID = meta.post_id WHERE post.post_type = 'attachment' AND meta.meta_key = %s AND (meta.meta_value = %s OR meta.meta_value LIKE %s) LIMIT 0,1;", '_wp_attached_file', sanitize_file_name( basename( $attach_name ) ), "%/" . sanitize_file_name( basename( $attach_name ) ) ) );

                if ( $attachment && absint( $attachment ) > 0 ) {
                        return $attachment;
                }

                $wp_filetype = wp_check_filetype( basename( $attach_name ) );

                if ( isset( $wp_filetype[ 'type' ] ) && !empty( $wp_filetype[ 'type' ] ) ) {
                        $name  = pathinfo( $attach_name, PATHINFO_FILENAME );
                        $attch = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'attachment' AND post_mime_type LIKE %s;", $name, "image%" ) );
                        if ( $attch && absint( $attch ) > 0 ) {
                                return $attch;
                        }
                }
                return false;
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

        private function wpie_get_attach_from_url( $file_url = "" ) {

                if ( empty( $file_url ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File URL is empty', 'wp-import-export-lite' ) );
                }

                if ( is_readable( WPIE_IMPORT_CLASSES_DIR . '/downloader/manager.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/downloader/manager.php');
                }

                $download_manager = new Downloader();

                $fileName = $download_manager->get_filename( $file_url, "media" );

                if ( \is_wp_error( $fileName ) ) {
                        $this->import_log[] = '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $fileName->get_error_message();
                        return false;
                }

                if ( $this->is_search_existing() ) {

                        $media_id = $this->wpie_get_attach_from_media_library( $fileName );

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

                $this->attach = get_children(
                        [
                                'post_parent' => $this->item_id,
                                'post_type'   => 'attachment',
                                'numberposts' => -1,
                                'post_status' => null,
                                "fields"      => "ids"
                        ]
                );
        }

        private function remove_old_attch( $type = 'files' ) {

                $ids = array();

                if ( !empty( $this->attach ) ) {


                        $attachments = array_diff( $this->attach, $this->attachments );

                        if ( !empty( $attachments ) ) {

                                foreach ( $attachments as $attach ) {

                                        if ( wp_attachment_is_image( $attach ) ) {
                                                continue;
                                        }
                                        wp_delete_attachment( $attach, true );
                                }
                        }
                }

                return $ids;
        }

        private function get_existing_attach( $attach = "" ) {

                if ( empty( $this->attach ) || empty( $attach ) ) {
                        return false;
                }

                $attach_id = 0;

                $name = sanitize_file_name( basename( $attach ) );

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

        private function media_handle_upload( $file_array, $post_id = 0, $desc = null, $post_data = array() ) {

                $oFileUrl = isset( $file_array[ 'url' ] ) ? $file_array[ 'url' ] : "";

                $oFileName = isset( $file_array[ 'name' ] ) ? $file_array[ 'name' ] : "";

                $time = $this->getMediaTime( $oFileUrl, $post_id );

                $uploads = wp_upload_dir( $time );

                $name = $oFileName;

                // Move the file to the uploads dir.
                $newFile = $uploads[ 'path' ] . "/" . $name;

                if ( !copy( $file_array[ 'tmp_name' ], $newFile ) ) {
                        return new \WP_Error( 'wpie_import_error', sprintf( __( "File Download Error : Can't copy File", 'wp-import-export-lite' ), $url ) );
                }


                unlink( $file_array[ 'tmp_name' ] );

                $fileType = wp_check_filetype( $newFile );

                $file = [
                        'file' => $newFile,
                        'url'  => $uploads[ 'url' ] . "/" . $name,
                        'type' => empty( $fileType[ 'type' ] ) ? '' : $fileType[ 'type' ]
                ];

                $ext  = pathinfo( $name, PATHINFO_EXTENSION );
                $name = wp_basename( $name, ".$ext" );

                $url     = $file[ 'url' ];
                $type    = $file[ 'type' ];
                $file    = $file[ 'file' ];
                $title   = sanitize_text_field( $name );
                $content = '';
                $excerpt = '';

                if ( preg_match( '#^audio#', $type ) ) {
                        $meta = wp_read_audio_metadata( $file );

                        if ( !empty( $meta[ 'title' ] ) ) {
                                $title = $meta[ 'title' ];
                        }

                        if ( !empty( $title ) ) {

                                if ( !empty( $meta[ 'album' ] ) && !empty( $meta[ 'artist' ] ) ) {
                                        /* translators: 1: Audio track title, 2: Album title, 3: Artist name. */
                                        $content .= sprintf( __( '"%1$s" from %2$s by %3$s.' ), $title, $meta[ 'album' ], $meta[ 'artist' ] );
                                } elseif ( !empty( $meta[ 'album' ] ) ) {
                                        /* translators: 1: Audio track title, 2: Album title. */
                                        $content .= sprintf( __( '"%1$s" from %2$s.' ), $title, $meta[ 'album' ] );
                                } elseif ( !empty( $meta[ 'artist' ] ) ) {
                                        /* translators: 1: Audio track title, 2: Artist name. */
                                        $content .= sprintf( __( '"%1$s" by %2$s.' ), $title, $meta[ 'artist' ] );
                                } else {
                                        /* translators: %s: Audio track title. */
                                        $content .= sprintf( __( '"%s".' ), $title );
                                }
                        } elseif ( !empty( $meta[ 'album' ] ) ) {

                                if ( !empty( $meta[ 'artist' ] ) ) {
                                        /* translators: 1: Audio album title, 2: Artist name. */
                                        $content .= sprintf( __( '%1$s by %2$s.' ), $meta[ 'album' ], $meta[ 'artist' ] );
                                } else {
                                        $content .= $meta[ 'album' ] . '.';
                                }
                        } elseif ( !empty( $meta[ 'artist' ] ) ) {

                                $content .= $meta[ 'artist' ] . '.';
                        }

                        if ( !empty( $meta[ 'year' ] ) ) {
                                /* translators: Audio file track information. %d: Year of audio track release. */
                                $content .= ' ' . sprintf( __( 'Released: %d.' ), $meta[ 'year' ] );
                        }

                        if ( !empty( $meta[ 'track_number' ] ) ) {
                                $track_number = explode( '/', $meta[ 'track_number' ] );

                                if ( isset( $track_number[ 1 ] ) ) {
                                        /* translators: Audio file track information. 1: Audio track number, 2: Total audio tracks. */
                                        $content .= ' ' . sprintf( __( 'Track %1$s of %2$s.' ), number_format_i18n( $track_number[ 0 ] ), number_format_i18n( $track_number[ 1 ] ) );
                                } else {
                                        /* translators: Audio file track information. %s: Audio track number. */
                                        $content .= ' ' . sprintf( __( 'Track %s.' ), number_format_i18n( $track_number[ 0 ] ) );
                                }
                        }

                        if ( !empty( $meta[ 'genre' ] ) ) {
                                /* translators: Audio file genre information. %s: Audio genre name. */
                                $content .= ' ' . sprintf( __( 'Genre: %s.' ), $meta[ 'genre' ] );
                        }

                        // Use image exif/iptc data for title and caption defaults if possible.
                } elseif ( 0 === strpos( $type, 'image/' ) ) {
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

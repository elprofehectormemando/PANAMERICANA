<?php


namespace wpie\import\seo;

use wpie\import\Downloader\Manager as Downloader;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Images {

        public static $item_id = 0;

        public static function download_images( $method = "media_library", $data = "", $item_id = 0 ) {

                self::$item_id = $item_id;

                $image_id = 0;

                if ( $method === "directory" ) {
                        $image_id = self::wpie_get_image_from_local( $data );
                } elseif ( $method === "url" ) {
                        $image_id = self::wpie_get_image_from_url( $data );
                } elseif ( $method === "media_library" ) {
                        $image_id = self::wpie_get_image_from_gallery( $data );
                }
                return $image_id;
        }

        public static function wpie_get_image_from_gallery( $image_name = "" ) {

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
                        $name = pathinfo( $image_name, PATHINFO_FILENAME );
                        $attch = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_title = %s AND post_type = 'attachment' AND post_mime_type LIKE %s;", $name, "image%" ) );
                        if ( $attch && absint( $attch ) > 0 ) {
                                return $attch;
                        }
                }
                return false;
        }

        public static function wpie_get_image_from_local( $filename = "" ) {

                if ( empty( $filename ) ) {
                        return false;
                }

                $file = WPIE_UPLOAD_TEMP_DIR . "/" . $filename;

                if ( !file_exists( $file ) ) {

                        return new \WP_Error( 'imageDownloadError', '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $filename . " " . __( 'Image Not Exist', 'wp-import-export-lite' ) );
                }
                if ( !self::is_valid_image( $file ) ) {
                        return new \WP_Error( 'imageDownloadError', '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $filename . " " . __( 'File is not valid Image', 'wp-import-export-lite' ) );
                }

                $upload_file = wp_upload_bits( $filename, null, file_get_contents( $file ) );

                unset( $file, $change_ext );

                if ( !$upload_file[ 'error' ] ) {

                        $wp_filetype = wp_check_filetype( $filename, null );

                        $attachment = array(
                                'post_mime_type' => $wp_filetype[ 'type' ],
                                'post_parent' => self::$item_id,
                                'post_title' => preg_replace( '/\.[^.]+$/', '', $filename ),
                                'post_content' => '',
                                'post_status' => 'inherit',
                        );

                        $attachment_id = wp_insert_attachment( $attachment, $upload_file[ 'file' ], self::$item_id );

                        if ( !is_wp_error( $attachment_id ) && absint( $attachment_id ) > 0 ) {

                                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file[ 'file' ] );

                                wp_update_attachment_metadata( $attachment_id, $attachment_data );

                                unset( $attachment_data );
                        }

                        unset( $attachment, $wp_filetype, $upload_file, $filename );

                        return $attachment_id;
                }

                unset( $upload_file, $filename );

                return new \WP_Error( 'imageDownloadError', '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . __( 'Image Upload failed', 'wp-import-export-lite' ) );
        }

        public static function is_valid_image( $file = "" ) {

                if ( empty( $file ) ) {
                        return false;
                }

                if ( !is_readable( $file ) ) {
                        return false;
                }

                if ( !preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png|bmp|tif|tiff|ico|svg)\b/i', strtolower( trim( $file ) ) ) ) {
                        return false;
                }

                $filesize = filesize( $file );

                if ( $filesize === 0 || $filesize === false ) {
                        return false;
                }

                return true;
        }

        public static function wpie_get_image_from_url( $file_url = "" ) {

                if ( empty( $file_url ) ) {
                        return new \WP_Error( 'wpie_import_error', __( 'File URL is empty', 'wp-import-export-lite' ) );
                }

                if ( is_readable( WPIE_IMPORT_CLASSES_DIR . '/downloader/manager.php' ) ) {
                        require_once(WPIE_IMPORT_CLASSES_DIR . '/downloader/manager.php');
                }

                $download_manager = new Downloader();

                $fileName = $download_manager->get_filename( $file_url, "image" );

                if ( \is_wp_error( $fileName ) ) {
                        return new \WP_Error( 'imageDownloadError', '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $fileName->get_error_message() );
                }

                $newFileName = $fileName;

                if ( $newFileName !== $fileName ) {
                        $fileName = $newFileName;

                        $download_manager->setFilename( $fileName );
                }



                $media_id = self::wpie_get_image_from_gallery( $fileName );

                if ( $media_id !== false && absint( $media_id ) > 0 ) {
                        unset( $download_manager );
                        return \absint( $media_id );
                }


                $file = $download_manager->download();

                if ( \is_wp_error( $file ) ) {
                        return new \WP_Error( 'imageDownloadError', '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $file->get_error_message() );
                }

                unset( $download_manager );

                $id = \media_handle_sideload( [ 'name' => $fileName, 'tmp_name' => $file ], self::$item_id );

                // If error storing permanently, unlink.
                if ( \is_wp_error( $id ) ) {
                        if ( \file_exists( $file ) ) {
                                \unlink( $file );
                        }
                        return new \WP_Error( 'imageDownloadError', '<strong>' . __( 'Warning', 'wp-import-export-lite' ) . '</strong> : ' . $id->get_error_message() );
                }
                // Store the original attachment source in meta.
                \update_post_meta( $id, '_wpie_source_url', esc_url_raw( $file_url ) );

                return $id;
        }

}

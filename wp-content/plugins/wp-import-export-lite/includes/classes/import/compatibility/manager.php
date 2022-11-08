<?php

namespace wpie\import\Compatibility;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class Manager {

        private $template;

        public function __construct( $template ) {
                $this->template = $template;

                $this->add_compatibility();
        }

        private function add_compatibility() {

                $import_type = (isset( $this->template->opration_type ) && trim( $this->template->opration_type ) != "") ? $this->template->opration_type : "post";

                if ( defined( 'HOUZEZ_THEME_VERSION' ) && $import_type === 'property' ) {
                        add_action( 'wpie_after_completed_item_import', [ $this, 'add_houzez_theme_images' ], 10, 4 );
                        add_action( 'wpie_after_post_import', [ $this, 'add_gallery_images' ], 10, 4 );
                }
        }

        public function add_gallery_images( $item_id = 0, $wpie_import_record = [], $wpie_final_data = [], $wpie_import_option = [] ) {

                $gallery = get_post_meta( $item_id, 'fave_property_images' );

                if ( is_array( $gallery ) && ! empty( $gallery ) ) {
                        $gallery = implode( ',', $gallery );
                } else {
                        $gallery = "";
                }

                update_post_meta( $item_id, '_product_image_gallery', $gallery );

                unset( $gallery );
        }

        public function add_houzez_theme_images( $item_id = 0, $wpie_import_record = [], $wpie_final_data = [], $wpie_import_option = [] ) {

                delete_post_meta( $item_id, 'fave_property_images' );

                $gallery = get_post_meta( $item_id, '_product_image_gallery', true );

                if ( ! empty( $gallery ) ) {

                        $gallery = explode( ',', $gallery );

                        foreach ( $gallery as $image ) {
                                add_post_meta( $item_id, 'fave_property_images', intval( $image ) );
                        }
                }

                unset( $gallery );
        }

}

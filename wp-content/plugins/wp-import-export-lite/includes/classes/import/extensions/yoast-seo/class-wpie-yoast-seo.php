<?php


namespace wpie\import\seo;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Yoast_SEO_Import extends \wpie\import\base\WPIE_Import_Base {

        private $import_data_type = "";
        private $seo_meta         = [];

        public function __construct( $wpie_import_option = [], $import_type = "" ) {

                $this->wpie_import_option = $wpie_import_option;

                $this->import_type = $import_type;
        }

        public function before_item_import( $wpie_import_record = [], &$existing_item_id = 0, &$is_new_item = true, &$is_search_duplicates = true ) {

                $this->wpie_import_record = $wpie_import_record;
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = false ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                if ( !$this->is_update_field( 'yoast_seo' ) ) {

                        return false;
                }

                $this->seo_meta = [];

                $this->import_data_type = wpie_sanitize_field( $this->get_field_value( 'wpie_import_type', true ) );

                $this->set_general_fields();

                $this->set_facebook_fields();

                $this->set_twitter_fields();

                $this->set_advanced_fields();

                $this->set_schema_fields();

                $this->save();
        }

        public function get_all_fields() {

                $general_fields  = $this->get_general_fields();
                $facebook_fields = $this->get_facebook_fields();
                $twitter_fields  = $this->get_twitter_fields();
                $advanced_fields = $this->get_advanced_fields();
                $schema_fields   = $this->get_schema_fields();

                $fields = array_merge( $general_fields, $facebook_fields, $twitter_fields, $advanced_fields, $schema_fields );

                unset( $general_fields, $facebook_fields, $twitter_fields, $advanced_fields, $schema_fields );

                return $fields;
        }

        private function set_general_fields() {

                $fields = $this->get_general_fields();

                foreach ( $fields as $field ) {

                        $val = $this->get_field_value( 'wpie_item_' . $field );

                        switch ( $field ) {

                                case '_yoast_wpseo_focuskeywords':
                                case 'wpseo_focuskeywords':

                                        if ( $this->isJSON( wp_unslash( $val ) ) ) {
                                                $val = wp_unslash( $val );
                                        } else {
                                                $valData = explode( "||", $val );

                                                $finalData = [];
                                                foreach ( $valData as $_data ) {
                                                        $finalData[] = [ "keyword" => $_data, "score" => "" ];
                                                }
                                                $val = json_encode( $finalData );
                                        }
                                        break;
                                case '_yoast_wpseo_keywordsynonyms':
                                case 'wpseo_keywordsynonyms':

                                        if ( $this->isJSON( wp_unslash( $val ) ) ) {
                                                $val = wp_unslash( $val );
                                        } else {
                                                $valData = explode( "||", $val );

                                                $val = json_encode( $valData );
                                        }
                                        break;
                        }

                        $this->update_seo_meta( $field, $val );
                }
        }

        private function get_general_fields() {

                if ( $this->import_type === "taxonomy" ) {
                        $fields = [ 'wpseo_focuskw', 'wpseo_title', 'wpseo_desc', 'wpseo_keywordsynonyms', 'wpseo_focuskeywords' ];
                } else {
                        $fields = [ '_yoast_wpseo_focuskw', '_yoast_wpseo_title', '_yoast_wpseo_metadesc', '_yoast_wpseo_keywordsynonyms', '_yoast_wpseo_focuskeywords' ];
                }

                return $fields;
        }

        private function set_facebook_fields() {

                $fields = $this->get_facebook_fields();

                foreach ( $fields as $field ) {

                        $val = $this->get_field_value( 'wpie_item_' . $field );

                        switch ( $field ) {

                                case '_yoast_wpseo_opengraph-image':

                                        if ( strpos( $val, 'url' ) !== false ) {
                                                $data   = $this->get_field_value( 'wpie_item__yoast_wpseo_opengraph-image_url_data' );
                                                $method = "url";
                                        } elseif ( strpos( $val, 'media' ) !== false ) {
                                                $data   = $this->get_field_value( 'wpie_item__yoast_wpseo_opengraph-image_media_library_data' );
                                                $method = "media_library";
                                        } else {
                                                $data   = $this->get_field_value( 'wpie_item__yoast_wpseo_opengraph-image_directory_data' );
                                                $method = "directory";
                                        }
                                        $val = $this->downloadImage( $method, $data );

                                        if ( $val === false || is_wp_error( $val ) ) {
                                                $val = null;
                                        }
                                        $this->update_seo_meta( '_yoast_wpseo_opengraph-image-id', $val );

                                        if ( !empty( $val ) ) {
                                                $val = \wp_get_attachment_url( $val );
                                        }
                                        break;
                                case 'wpseo_opengraph-image':

                                        if ( strpos( $val, 'url' ) !== false ) {
                                                $data   = $this->get_field_value( 'wpie_item_wpseo_opengraph-image_url_data' );
                                                $method = "url";
                                        } elseif ( strpos( $val, 'media' ) !== false ) {
                                                $data   = $this->get_field_value( 'wpie_item_wpseo_opengraph-image_media_library_data' );
                                                $method = "media_library";
                                        } else {
                                                $data   = $this->get_field_value( 'wpie_item_wpseo_opengraph-image_directory_data' );
                                                $method = "directory";
                                        }
                                        $val = $this->downloadImage( $method, $data );

                                        if ( $val === false || is_wp_error( $val ) ) {
                                                $val = null;
                                        }
                                        $this->update_seo_meta( 'wpseo_opengraph-image-id', $val );

                                        if ( !empty( $val ) ) {
                                                $val = \wp_get_attachment_url( $val );
                                        }
                                        break;
                        }

                        $this->update_seo_meta( $field, $val );
                }
        }

        private function get_facebook_fields() {

                if ( $this->import_type === "taxonomy" ) {
                        $fields = [ 'wpseo_opengraph-title', 'wpseo_opengraph-description', 'wpseo_opengraph-image' ];
                } else {
                        $fields = [ '_yoast_wpseo_opengraph-title', '_yoast_wpseo_opengraph-description', '_yoast_wpseo_opengraph-image' ];
                }

                return $fields;
        }

        private function set_twitter_fields() {

                $fields = $this->get_twitter_fields();

                foreach ( $fields as $field ) {

                        $val = $this->get_field_value( 'wpie_item_' . $field );

                        switch ( $field ) {

                                case '_yoast_wpseo_twitter-image':

                                        if ( strpos( $val, 'url' ) !== false ) {
                                                $data   = $this->get_field_value( 'wpie_item__yoast_wpseo_twitter-image_url_data' );
                                                $method = "url";
                                        } elseif ( strpos( $val, 'media' ) !== false ) {
                                                $data   = $this->get_field_value( 'wpie_item__yoast_wpseo_twitter-image_media_library_data' );
                                                $method = "media_library";
                                        } else {
                                                $data   = $this->get_field_value( 'wpie_item__yoast_wpseo_twitter-image_directory_data' );
                                                $method = "directory";
                                        }
                                        $val = $this->downloadImage( $method, $data );

                                        if ( $val === false || is_wp_error( $val ) ) {
                                                $val = null;
                                        }
                                        $this->update_seo_meta( '_yoast_wpseo_twitter-image-id', $val );

                                        if ( !empty( $val ) ) {
                                                $val = \wp_get_attachment_url( $val );
                                        }
                                        break;

                                case 'wpseo_twitter-image':

                                        if ( strpos( $val, 'url' ) !== false ) {
                                                $data   = $this->get_field_value( 'wpie_item_wpseo_twitter-image_url_data' );
                                                $method = "url";
                                        } elseif ( strpos( $val, 'media' ) !== false ) {
                                                $data   = $this->get_field_value( 'wpie_item_wpseo_twitter-image_media_library_data' );
                                                $method = "media_library";
                                        } else {
                                                $data   = $this->get_field_value( 'wpie_item_wpseo_twitter-image_directory_data' );
                                                $method = "directory";
                                        }
                                        $val = $this->downloadImage( $method, $data );

                                        if ( $val === false || is_wp_error( $val ) ) {
                                                $val = null;
                                        }
                                        $this->update_seo_meta( 'wpseo_twitter-image-id', $val );

                                        if ( !empty( $val ) ) {
                                                $val = \wp_get_attachment_url( $val );
                                        }
                                        break;
                        }

                        $this->update_seo_meta( $field, $val );
                }
        }

        private function get_twitter_fields() {

                if ( $this->import_type === "taxonomy" ) {
                        $fields = [ 'wpseo_twitter-title', 'wpseo_twitter-description', 'wpseo_twitter-image' ];
                } else {
                        $fields = [ '_yoast_wpseo_twitter-title', '_yoast_wpseo_twitter-description', '_yoast_wpseo_twitter-image' ];
                }

                return $fields;
        }

        private function set_advanced_fields() {

                $fields = $this->get_advanced_fields();

                foreach ( $fields as $field ) {

                        $val = $this->get_field_value( 'wpie_item_' . $field );

                        if ( $val === "as_specified" ) {
                                $val = $this->get_field_value( 'wpie_item_' . $field . '_as_specified_data' );
                        }

                        $this->update_seo_meta( $field, $val );
                }
        }

        private function get_advanced_fields() {

                if ( $this->import_type === "taxonomy" ) {
                        $fields = [ 'wpseo_noindex', 'wpseo_is_cornerstone', 'wpseo_bctitle', 'wpseo_canonical' ];
                } else {
                        $fields = [
                                '_yoast_wpseo_meta-robots-noindex', '_yoast_wpseo_meta-robots-nofollow', '_yoast_wpseo_meta-robots-adv',
                                '_yoast_wpseo_is_cornerstone', '_yoast_wpseo_bctitle', '_yoast_wpseo_canonical'
                        ];
                }

                return $fields;
        }

        private function set_schema_fields() {

                $fields = $this->get_schema_fields();

                if ( empty( $fields ) ) {
                        return;
                }

                foreach ( $fields as $field ) {

                        $val = $this->get_field_value( 'wpie_item_' . $field );

                        if ( $val === "as_specified" ) {
                                $val = $this->get_field_value( 'wpie_item_' . $field . '_as_specified_data' );
                        }

                        $this->update_seo_meta( $field, $val );
                }
        }

        private function get_schema_fields() {

                if ( $this->import_type === "taxonomy" ) {
                        return;
                }

                $fields = [ '_yoast_wpseo_schema_page_type' ];

                if ( $this->import_type === "post" ) {
                        $fields[] = '_yoast_wpseo_schema_article_type';
                }

                return $fields;
        }

        private function downloadImage( $method, $data ) {

                if ( empty( $data ) ) {
                        return "";
                }

                if ( file_exists( __DIR__ . '/class-images.php' ) ) {
                        require_once(__DIR__ . '/class-images.php');
                }

                return WPIE_Images::download_images( $method, $data, $this->item_id );
        }

        private function update_seo_meta( $key, $val ) {

                if ( $this->import_type === "taxonomy" ) {
                        $this->seo_meta[ $key ] = $val;
                } else {
                        $this->update_meta( $key, $val );
                }
        }

        private function save() {

                if ( $this->import_type === "taxonomy" ) {

                        $taxonomy = \wpie_sanitize_field( $this->get_field_value( 'wpie_taxonomy_type', true ) );

                        \WPSEO_Taxonomy_Meta::set_values( $this->item_id, $taxonomy, array_merge( \WPSEO_Taxonomy_Meta::$defaults_per_term, $this->seo_meta ) );
                }
        }

        private function isJSON( $string ) {
                return is_string( $string ) && is_array( json_decode( $string, true ) ) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

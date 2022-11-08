<?php


namespace wpie\export\yoast_seo;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php');
}

class WPIE_Yoast_SEO_Taxonomy_Export extends \wpie\export\base\WPIE_Export_Base {

        public function __construct() {
                
        }

        public function pre_process_fields( &$export_fields = array(), $export_type = array(), $export_taxonomy_type = "" ) {

                $fields = array(
                        "title"      => __( "Yoast SEO", 'wp-import-export-lite' ),
                        "isFiltered" => false,
                        "data"       => array(
                                array(
                                        'name'    => 'Focus Keywords',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_focuskw'
                                ),
                                array(
                                        'name'    => 'SEO Title',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_title'
                                ),
                                array(
                                        'name'    => 'Meta Description',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_desc'
                                ),
                                array(
                                        'name'    => 'Keyphrase Synonyms',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_keywordsynonyms'
                                ),
                                array(
                                        'name'    => 'Related keyphrase',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_focuskeywords'
                                ),
                                array(
                                        'name'    => 'Facebook Title',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_opengraph-title'
                                ),
                                array(
                                        'name'    => 'Facebook Description',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_opengraph-description'
                                ),
                                array(
                                        'name'    => 'Facebook Image',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_opengraph-image'
                                ),
                                array(
                                        'name'    => 'Twitter Title',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_twitter-title'
                                ),
                                array(
                                        'name'    => 'Twitter Description',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_twitter-description'
                                ),
                                array(
                                        'name'    => 'Twitter Image',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_twitter-image'
                                ),
                                array(
                                        'name'    => 'Meta Robots Index',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_noindex'
                                ),
                                array(
                                        'name'    => 'is cornerstone content',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_is_cornerstone'
                                ),
                                array(
                                        'name'    => 'Breadcrumbs Title',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_bctitle'
                                ),
                                array(
                                        'name'    => 'Canonical URL',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_canonical'
                                ),
                                array(
                                        'name'    => 'SEO Score',
                                        'type'    => 'yoast_seo',
                                        'metaKey' => 'wpseo_content_score'
                                )
                        )
                );

                $export_fields[ 'yoast_seo' ] = $fields;
        }

        public function process_addon_data( &$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null, $site_date_format = "" ) {

                if ( $field_type === "yoast_seo" ) {

                        $is_php = isset( $field_option[ 'isPhp' ] ) ? wpie_sanitize_field( $field_option[ 'isPhp' ] ) == 1 : false;

                        $php_func = isset( $field_option[ 'phpFun' ] ) ? wpie_sanitize_field( $field_option[ 'phpFun' ] ) : "";

                        $metaKey = isset( $field_option[ 'metaKey' ] ) ? wpie_sanitize_field( $field_option[ 'metaKey' ] ) : "";

                        $item_id = isset( $item->term_id ) ? $item->term_id : 0;

                        $taxonomy = isset( $item->taxonomy ) ? $item->taxonomy : "category";

                        $termMeta = substr( $metaKey, 6 );

                        $data = \WPSEO_Taxonomy_Meta::get_term_meta( $item, $taxonomy, $termMeta );

                        $export_data[ $field_name ] = apply_filters( 'wpie_export_yoast_field', $this->apply_user_function( (( empty( $data ) || $data === false) ? "" : $data ), $is_php, $php_func ), $item );

                        unset( $is_php, $php_func );
                }
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

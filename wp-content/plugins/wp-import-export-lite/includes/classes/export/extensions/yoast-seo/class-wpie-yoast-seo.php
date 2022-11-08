<?php


namespace wpie\export\yoast_seo;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php');
}

class WPIE_Yoast_SEO_Export extends \wpie\export\base\WPIE_Export_Base {

        public function __construct() {
                
        }

        public function pre_process_fields( &$export_fields = array(), $export_type = array(), $export_taxonomy_type="" ) {

                $fields = array(
                        "title" => __( "Yoast SEO", 'wp-import-export-lite' ),
                        "isFiltered" => false,
                        "data" => array(
                                array(
                                        'name' => 'Focus Keywords',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_focuskw'
                                ),
                                array(
                                        'name' => 'SEO Title',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_title'
                                ),
                                array(
                                        'name' => 'Meta Description',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_metadesc'
                                ),
                                array(
                                        'name' => 'Keyphrase Synonyms',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_keywordsynonyms'
                                ),
                                array(
                                        'name' => 'Related keyphrase',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_focuskeywords'
                                ),
                                array(
                                        'name' => 'Facebook Title',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_opengraph-title'
                                ),
                                array(
                                        'name' => 'Facebook Description',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_opengraph-description'
                                ),
                                array(
                                        'name' => 'Facebook Image',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_opengraph-image'
                                ),
                                array(
                                        'name' => 'Twitter Title',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_twitter-title'
                                ),
                                array(
                                        'name' => 'Twitter Description',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_twitter-description'
                                ),
                                array(
                                        'name' => 'Twitter Image',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_twitter-image'
                                ),
                                array(
                                        'name' => 'Meta Robots Index',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_meta-robots-noindex'
                                ),
                                array(
                                        'name' => 'Meta Robots Nofollow',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_meta-robots-nofollow'
                                ),
                                array(
                                        'name' => 'Meta Robots Advanced',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_meta-robots-adv'
                                ),
                                array(
                                        'name' => 'is cornerstone content',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_is_cornerstone'
                                ),
                                array(
                                        'name' => 'Breadcrumbs Title',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_bctitle'
                                ),
                                array(
                                        'name' => 'Canonical URL',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_canonical'
                                ),
                                array(
                                        'name' => 'SEO Score',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_content_score'
                                ),
                                array(
                                        'name' => 'Schema Page type',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_yoast_wpseo_schema_page_type'
                                )
                        )
                );
                if ( in_array( "post", $export_type ) ) {
                        $fields[ 'data' ][] = array(
                                'name' => 'Schema Article type',
                                'type' => 'wpie_cf',
                                'metaKey' => '_yoast_wpseo_schema_article_type'
                        );
                }

                $export_fields[ 'yoast_seo' ] = $fields;

                $meta_fields = $this->seo_metas( $export_type );

                $export_fields[ 'meta' ] = array_diff( $export_fields[ 'meta' ], $meta_fields );
        }

        private function seo_metas( $export_type ) {

                $fields = [
                        //general fields
                        '_yoast_wpseo_focuskw', '_yoast_wpseo_title', '_yoast_wpseo_metadesc',
                        '_yoast_wpseo_keywordsynonyms', '_yoast_wpseo_focuskeywords',
                        //Social Fields
                        '_yoast_wpseo_opengraph-title', '_yoast_wpseo_opengraph-description', '_yoast_wpseo_opengraph-image',
                        '_yoast_wpseo_twitter-title', '_yoast_wpseo_twitter-description', '_yoast_wpseo_twitter-image',
                        //Advanced Fields
                        '_yoast_wpseo_meta-robots-noindex', '_yoast_wpseo_meta-robots-nofollow', '_yoast_wpseo_meta-robots-adv',
                        '_yoast_wpseo_is_cornerstone', '_yoast_wpseo_bctitle', '_yoast_wpseo_canonical',
                        //Schema Fields
                        '_yoast_wpseo_schema_page_type',
                        //Other Fields
                        '_yoast_wpseo_content_score'
                ];

                if ( in_array( "post", $export_type ) ) {
                        $fields[] = '_yoast_wpseo_schema_article_type';
                }

                return $fields;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

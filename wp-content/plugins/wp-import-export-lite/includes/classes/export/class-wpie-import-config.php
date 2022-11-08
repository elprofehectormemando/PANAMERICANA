<?php


namespace wpie\export;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

class WPIE_Import_Config {

        private static $fields      = [];
        private static $options     = [];
        private static $export_type = "";

        public static function generate( $options = [] ) {

                if ( empty( $options ) ) {
                        return;
                }

                self::$options = $options;

                self::$export_type = isset( $options[ 'wpie_export_type' ] ) ? $options[ 'wpie_export_type' ] : "post";

                self::map_fields();

                self::save();
        }

        private static function map_fields() {

                self::$fields[ 'is_exported' ] = 1;

                self::set_extra_fields();

                self::set_fields();
        }

        private static function set_extra_fields() {

                if ( self::$export_type === "product_attributes" ) {
                        self::set_attribute_fields();
                } elseif ( self::$export_type === "taxonomies" ) {
                        self::set_taxonomy_fields();
                } elseif ( self::$export_type === "product" ) {
                        self::set_product_fields();
                } elseif ( self::$export_type === "shop_order" ) {
                        self::set_shop_order_fields();
                } elseif ( self::$export_type === "comments" || self::$export_type === "product_reviews" ) {
                        self::set_comment_fields();
                } elseif ( self::$export_type === "shop_customer" ) {
                        self::set_customer_fields();
                }
        }

        private static function set_attribute_fields() {

                self::$fields[ "wpie_existing_item_search_logic" ]      = "slug";
                self::$fields[ "wpie_existing_item_search_logic_slug" ] = "{slug[1]}";
        }

        private static function set_taxonomy_fields() {

                self::$fields[ "wpie_existing_item_search_logic" ] = "slug";
        }

        private static function set_product_fields() {

                self::$fields[ "wpie_item_variation_import_method" ]                     = "match_unique_field";
                self::$fields[ "wpie_item_product_variation_field_parent" ]              = "{id[1]}";
                self::$fields[ "wpie_item_product_variation_match_unique_field_parent" ] = "{parent[1]}";
        }

        private static function set_shop_order_fields() {

                /* billing fields */
                self::$fields[ "wpie_item_order_number" ]                 = "{orderid[1]}";
                self::$fields[ "wpie_item_order_billing_source" ]         = "existing";
                self::$fields[ "wpie_item_order_billing_match_by" ]       = "email";
                self::$fields[ "wpie_item_order_billing_match_by_email" ] = "{_customer_user_email[1]}";
                self::$fields[ "wpie_item_order_billing_no_match_guest" ] = "1";
                self::$fields[ "wpie_item_guest_billing_first_name" ]     = "{_billing_first_name[1]}";
                self::$fields[ "wpie_item_guest_billing_last_name" ]      = "{_billing_last_name[1]}";
                self::$fields[ "wpie_item_guest_billing_address_1" ]      = "{_billing_address_1[1]}";
                self::$fields[ "wpie_item_guest_billing_address_2" ]      = "{_billing_address_2[1]}";
                self::$fields[ "wpie_item_guest_billing_city" ]           = "{_billing_city[1]}";
                self::$fields[ "wpie_item_guest_billing_postcode" ]       = "{_billing_postcode[1]}";
                self::$fields[ "wpie_item_guest_billing_country" ]        = "{_billing_country[1]}";
                self::$fields[ "wpie_item_guest_billing_state" ]          = "{_billing_state[1]}";
                self::$fields[ "wpie_item_guest_billing_email" ]          = "{_billing_email[1]}";
                self::$fields[ "wpie_item_guest_billing_phone" ]          = "{_billing_phone[1]}";
                self::$fields[ "wpie_item_guest_billing_company" ]        = "{_billing_company[1]}";

                /* shipping fields */
                self::$fields[ "wpie_item_order_shipping_source" ]           = "guest";
                self::$fields[ "wpie_item_order_shipping_no_match_billing" ] = "1";
                self::$fields[ "wpie_item_shipping_first_name" ]             = "{_shipping_first_name[1]}";
                self::$fields[ "wpie_item_shipping_last_name" ]              = "{_shipping_last_name[1]}";
                self::$fields[ "wpie_item_shipping_address_1" ]              = "{_shipping_address_1[1]}";
                self::$fields[ "wpie_item_shipping_address_2" ]              = "{_shipping_address_2[1]}";
                self::$fields[ "wpie_item_shipping_city" ]                   = "{_shipping_city[1]}";
                self::$fields[ "wpie_item_shipping_postcode" ]               = "{_shipping_postcode[1]}";
                self::$fields[ "wpie_item_shipping_country" ]                = "{_shipping_country[1]}";
                self::$fields[ "wpie_item_shipping_state" ]                  = "{_shipping_state[1]}";
                self::$fields[ "wpie_item_shipping_email" ]                  = "";
                self::$fields[ "wpie_item_shipping_phone" ]                  = "";
                self::$fields[ "wpie_item_shipping_company" ]                = "{_shipping_company[1]}";
                self::$fields[ "wpie_item_order_customer_provided_note" ]    = "{customernote[1]}";

                /* payment fields */
                self::$fields[ "wpie_item_order_payment_method" ]                   = "as_specified";
                self::$fields[ "wpie_item_order_payment_method_as_specified_data" ] = "{paymentmethodtitle[1]}";
                self::$fields[ "wpie_item_order_transaction_id" ]                   = "{transactionid[1]}";

                /* Order Items List Start */

                /* Product Item */
                self::$fields[ "wpie_item_order_item_product_name" ]           = "{productname1[1]}";
                self::$fields[ "wpie_item_order_item_product_price" ]          = "{itemcost1[1]}";
                self::$fields[ "wpie_item_order_item_product_quantity" ]       = "{quantity1[1]}";
                self::$fields[ "wpie_item_order_item_product_sku" ]            = "{sku1[1]}";
                self::$fields[ "wpie_item_order_item_is_variation" ]           = "{isvariation1[1]}";
                self::$fields[ "wpie_item_order_item_original_product_title" ] = "{originalproducttitle1[1]}";
                self::$fields[ "wpie_item_order_item_variation_attributes" ]   = "{variationattributes1[1]}";
                self::$fields[ "wpie_item_order_item_meta" ]                   = "{itemmeta[1]}";
                self::$fields[ "wpie_item_order_item_product_delim" ]          = "|";

                $item_count = isset( self::$options[ 'wpie_order_item_count' ] ) ? intval( self::$options[ 'wpie_order_item_count' ] ) : 0;

                if ( $item_count > 1 ) {

                        for ( $i = 2; $i <= $item_count; $i++ ) {

                                self::$fields[ "wpie_item_order_item_product_name" ]           .= "|{productname" . $i . "[1]}";
                                self::$fields[ "wpie_item_order_item_product_price" ]          .= "|{itemcost" . $i . "[1]}";
                                self::$fields[ "wpie_item_order_item_product_quantity" ]       .= "|{quantity" . $i . "[1]}";
                                self::$fields[ "wpie_item_order_item_product_sku" ]            .= "|{sku" . $i . "[1]}";
                                self::$fields[ "wpie_item_order_item_is_variation" ]           .= "|{isvariation" . $i . "[1]}";
                                self::$fields[ "wpie_item_order_item_original_product_title" ] .= "|{originalproducttitle" . $i . "[1]}";
                                self::$fields[ "wpie_item_order_item_variation_attributes" ]   .= "|{variationattributes" . $i . "[1]}";
                                self::$fields[ "wpie_item_order_item_meta" ]                   .= "|{itemmeta" . $i . "[1]}";
                        }
                }

                /* Fee Item */
                self::$fields[ "wpie_item_order_item_fee" ]        = "{feename[1]}";
                self::$fields[ "wpie_item_order_item_fee_amount" ] = "{feeamountpersurcharge[1]}";
                self::$fields[ "wpie_item_order_item_fees_delim" ] = "|";

                /* Coupons Item */
                self::$fields[ "wpie_item_order_item_coupon" ]            = "{couponsused[1]}";
                self::$fields[ "wpie_item_order_item_coupon_amount" ]     = "{discountamountpercoupon[1]}";
                self::$fields[ "wpie_item_order_item_coupon_amount_tax" ] = "";
                self::$fields[ "wpie_item_order_item_coupon_delim" ]      = "|";

                /* Shipping Item */
                self::$fields[ "wpie_item_order_item_shipping_name" ]        = "{shippingname[1]}";
                self::$fields[ "wpie_item_order_item_shipping_amount" ]      = "{shippingcost[1]}";
                self::$fields[ "wpie_item_order_item_shipping_method" ]      = "{shippingmethod[1]}";
                self::$fields[ "wpie_item_order_item_shipping_meta" ]        = "{shippingmeta[1]}";
                self::$fields[ "wpie_item_order_item_shipping_costs_delim" ] = "|";

                /* Taxes Item */
                self::$fields[ "wpie_item_order_item_tax_rate_amount" ]         = "{amountpertax[1]}";
                self::$fields[ "wpie_item_order_item_tax_shipping_tax_amount" ] = "{shippingtaxes[1]}";
                self::$fields[ "wpie_item_order_item_tax_rate" ]                = "{ratecodepertax[1]}";
                self::$fields[ "wpie_item_order_item_shipping_costs_delim" ]    = "|";

                /* Order Items List End */

                /* Refunds */
                self::$fields[ "wpie_item_order_item_refund_amount" ]          = "{refundamounts[1]}";
                self::$fields[ "wpie_item_order_item_refund_reason" ]          = "{refundreason[1]}";
                self::$fields[ "wpie_item_order_item_refund_date" ]            = "{refunddate[1]}";
                self::$fields[ "wpie_item_order_item_refund_issued_match_by" ] = "existing";
                self::$fields[ "wpie_item_order_item_refund_issued_by" ]       = "email";
                self::$fields[ "wpie_item_refund_customer_email" ]             = "{refundauthoremail[1]}";

                /* Order Total */
                self::$fields[ "wpie_item_order_total" ]              = "manually";
                self::$fields[ "wpie_item_order_total_as_specified" ] = "{ordertotal[1]}";

                /* Order Notes */
                self::$fields[ "wpie_item_import_order_note_content" ]    = "{notecontent[1]}";
                self::$fields[ "wpie_item_import_order_note_date" ]       = "{notedate[1]}";
                self::$fields[ "wpie_item_import_order_note_visibility" ] = "{notevisibility[1]}";
                self::$fields[ "wpie_item_import_order_note_username" ]   = "{noteusername[1]}";
                self::$fields[ "wpie_item_import_order_note_email" ]      = "{noteuseremail[1]}";
                self::$fields[ "wpie_item_import_order_note_delim" ]      = "|";

                /* Handle Existing Items */
                self::$fields[ "wpie_existing_item_search_logic" ]          = "cf";
                self::$fields[ "wpie_existing_item_search_logic_cf_key" ]   = "_order_key";
                self::$fields[ "wpie_existing_item_search_logic_cf_value" ] = "{orderkey[1]}";
        }

        private static function set_comment_fields() {

                self::$fields[ "wpie_item_comment_parent_post" ] = "{parentposttitle[1]}";
        }

        private static function set_customer_fields() {

                self::$fields[ "wpie_item_customer_shipping_source" ] = "import";
        }

        private static function get_filename() {

                $type = isset( self::$options[ 'wpie_export_file_type' ] ) && !empty( self::$options[ 'wpie_export_file_type' ] ) ? self::$options[ 'wpie_export_file_type' ] : "csv";

                $fileName = isset( self::$options[ 'fileName' ] ) ? self::$options[ 'fileName' ] : "";

                if ( $type != "csv" && $fileName != "" ) {
                        $fileName = str_replace( ".csv", "." . $type, $fileName );
                }

                return $fileName;
        }

        private static function save() {

                $config = [
                        "import_type"     => self::$export_type,
                        "site_url"        => \site_url(),
                        "import_sub_type" => isset( self::$options[ 'wpie_taxonomy_type' ] ) ? self::$options[ 'wpie_taxonomy_type' ] : "",
                        "fields"          => self::$fields,
                        "fileName"        => self::get_filename(),
                ];

                $fileDir = isset( self::$options[ 'fileDir' ] ) ? self::$options[ 'fileDir' ] : "";

                $filePath = WPIE_UPLOAD_EXPORT_DIR . "/" . $fileDir . "/" . "config.json";

                file_put_contents( $filePath, json_encode( $config ) );

                unset( $config, $fileDir, $filePath );
        }

        private static function get_acf_field_data( $field_id = "" ) {

                if ( empty( $field_id ) ) {
                        return;
                }

                $field = \acf_get_field( $field_id );

                if ( !is_array( $field ) || empty( $field ) ) {
                        return;
                }

                $data = array();

                $type = isset( $field[ 'type' ] ) ? $field[ 'type' ] : "";

                $new_name = isset( $field[ 'label' ] ) && !empty( $field[ 'label' ] ) ? strtolower( str_replace( ' ', '_', preg_replace( '/[^a-z0-9_]/i', '', $field[ 'label' ] ) ) ) : "";

                switch ( $type ) {
                        case "select":
                        case "checkbox":
                        case "radio":
                        case "button_group":
                        case "true_false":
                        case "taxonomy":
                        case 'repeater':
                        case 'flexible_content':
                        case 'clone':
                        case 'group':
                                $data = array(
                                        "value_option" => "custom",
                                        "custom_value" => "{" . $new_name . "[1]}",
                                        "type"         => $type
                                );
                                break;
                        case "image":
                        case "file":
                        case "gallery":
                                $data = array(
                                        "value"                => "{" . $new_name . "[1]}",
                                        "search_through_media" => "1",
                                        "use_upload_dir"       => "",
                                        "delim"                => ",",
                                        "type"                 => $type
                                );
                                break;
                        case "link":
                                $data = array(
                                        "value" => [
                                                "url"    => "{" . $new_name . "url[1]}",
                                                "title"  => "{" . $new_name . "title[1]}",
                                                "target" => "{" . $new_name . "target[1]}",
                                        ],
                                        "type"  => $type
                                );
                                break;
                        case "google_map":
                                $data = array(
                                        "value" => [
                                                "address" => "{" . $new_name . "address[1]}",
                                                "lat"     => "{" . $new_name . "lat[1]}",
                                                "lng"     => "{" . $new_name . "lng[1]}",
                                        ],
                                        "type"  => $type
                                );
                                break;
                        case "post_object":
                        case "page_link":
                        case "relationship":
                        case "user":
                                $data = array(
                                        "value" => "{" . $new_name . "[1]}",
                                        "delim" => ",",
                                        "type"  => $type
                                );
                                break;
                        default :
                                $data = array(
                                        "value" => "{" . $new_name . "[1]}",
                                        "type"  => $type
                                );

                                break;
                }

                return $data;
        }

        private static function get_product_cf_fields() {

                return [ "_sku", "_regular_price", "_sale_price", "_sale_price_dates_from",
                        "_sale_price_dates_to", "_virtual", "_downloadable",
                        "_tax_status", "_tax_class", "_downloadable_files",
                        "_downloadable_file_name", "_download_limit", "_download_expiry",
                        "_manage_stock", "_stock", "_stock_status", "_backorders",
                        "_sold_individually", "_weight", "_length", "_width",
                        "_height", "_upsell_ids", "_crosssell_ids", "_purchase_note",
                        "_featured", "_visibility", "_variation_description",
                        "_product_url", "_button_text"
                ];
        }

        private static function get_user_fields() {

                return [ 'first_name', 'last_name', 'nickname', 'description' ];
        }

        private static function get_customer_fields() {

                return [
                        'billing_first_name', 'billing_last_name', 'billing_company',
                        'billing_address_1', 'billing_address_2', 'billing_city',
                        'billing_postcode', 'billing_country', 'billing_state',
                        'billing_email', 'billing_phone',
                        'shipping_first_name', 'shipping_last_name', 'shipping_company',
                        'shipping_address_1', 'shipping_address_2', 'shipping_city',
                        'shipping_postcode', 'shipping_country', 'shipping_state'
                ];
        }

        private static function get_order_cf_fields() {

                return [ '_billing_first_name', '_billing_last_name', '_billing_company',
                        '_billing_address_1', '_billing_address_2', '_billing_city',
                        '_billing_postcode', '_billing_country', '_billing_state',
                        '_billing_email', '_customer_user_email', '_billing_phone',
                        '_shipping_first_name', '_shipping_last_name', '_shipping_company',
                        '_shipping_address_1', '_shipping_address_2', '_shipping_city',
                        '_shipping_postcode', '_shipping_country', '_shipping_state',
                        '_payment_method', '_transaction_id', '_payment_method_title', '_order_total',
                        '_customer_user'
                ];
        }

        private static function set_fields() {

                $fields = (isset( self::$options[ 'fields_data' ] ) && trim( self::$options[ 'fields_data' ] ) != "") ? explode( "~||~", wpie_sanitize_field( wp_unslash( self::$options[ 'fields_data' ] ) ) ) : [];

                if ( !empty( $fields ) ) {

                        $data = [];

                        foreach ( $fields as $fieldData ) {

                                if ( empty( $fieldData ) ) {
                                        continue;
                                }

                                $field = explode( "|~|", $fieldData );

                                $label = isset( $field[ 0 ] ) ? wpie_sanitize_field( $field[ 0 ] ) : "";

                                $options = isset( $field[ 1 ] ) ? json_decode( wpie_sanitize_field( $field[ 1 ] ), true ) : "";

                                $type = isset( $options[ 'type' ] ) ? wpie_sanitize_field( $options[ 'type' ] ) : "";

                                $value = "{" . strtolower( preg_replace( '/[^a-z0-9_]/i', '', $label ) ) . "[1]}";

                                $key = $type;

                                if ( $key == "wc-product" ) {
                                        $type = "wpie_cf";
                                }

                                if ( in_array( $value, $data ) ) {

                                        $tempval = $value;

                                        $count = 1;

                                        while ( in_array( $tempval, $data ) ) {
                                                $tempval = "{" . strtolower( preg_replace( '/[^a-z0-9_]/i', '', $label ) ) . "_" . $count . "[1]}";
                                                $count++;
                                        }

                                        $value = $tempval;

                                        unset( $tempval, $count );
                                }


                                if ( $type == "wpie_cf" ) {

                                        $is_acf = isset( $options[ 'is_acf' ] ) ? intval( wpie_sanitize_field( $options[ 'is_acf' ] ) ) : 0;

                                        if ( $is_acf === 1 ) {
                                                continue;
                                        }

                                        $key = isset( $options[ 'metaKey' ] ) ? wpie_sanitize_field( $options[ 'metaKey' ] ) : "";

                                        if ( strpos( $key, "_yoast" ) === 0 ) {

                                                if ( in_array( $key, [ "_yoast_wpseo_opengraph-image", "_yoast_wpseo_twitter-image" ] ) ) {
                                                        self::$fields[ "wpie_item_" . $key . "_url_data" ] = $value;
                                                        $value                                             = "url";
                                                }

                                                self::$fields[ "wpie_item_" . $key ] = $value;
                                                continue;
                                        }
                                        if ( isset( $data[ $key ] ) ) {

                                                $tempkey = $key;

                                                $count = 0;

                                                while ( isset( $data[ $tempkey ] ) ) {
                                                        $tempkey = $key . "_" . $count;
                                                        $count++;
                                                }

                                                $key = $tempkey;

                                                unset( $tempkey, $count );
                                        }

                                        if ( self::$export_type === "shop_order" && in_array( $key, self::get_order_cf_fields() ) ) {
                                                continue;
                                        } elseif ( self::$export_type === "product" && in_array( $key, self::get_product_cf_fields() ) ) {

                                                self::$fields[ "wpie_item_meta" . $key ] = $value;

                                                if ( $key === "_downloadable_files" ) {
                                                        self::$fields[ "wpie_item_downloadable_files_delim" ]     = ",";
                                                        self::$fields[ "wpie_item_downloadable_file_name_delim" ] = ",";
                                                }
                                        } elseif ( ( in_array( self::$export_type, [ "shop_customer", "users" ] ) && in_array( $key, self::get_user_fields() )) || ( self::$export_type == "shop_customer" && in_array( $key, self::get_customer_fields() )) ) {
                                                self::$fields[ "wpie_item_" . $key ] = $value;
                                        } else {

                                                $_uniqueid = uniqid();

                                                self::$fields[ "wpie_item_cf" ][ $_uniqueid ] = [ "name" => $key, "value" => $value ];
                                        }
                                } elseif ( $type == "wpie-acf" ) {

                                        $acf_key = isset( $options[ 'acfKey' ] ) && !empty( $options[ 'acfKey' ] ) ? $options[ 'acfKey' ] : "";

                                        if ( !empty( $acf_key ) ) {

                                                $acf = [ $acf_key => self::get_acf_field_data( $acf_key ) ];

                                                if ( !empty( $acf ) ) {

                                                        if ( !isset( self::$fields[ 'acf' ] ) ) {
                                                                self::$fields[ 'acf' ] = [];
                                                        }
                                                        self::$fields[ 'acf' ] = array_replace( self::$fields[ 'acf' ], $acf );
                                                }
                                                unset( $acf );
                                        }

                                        unset( $acf_key );

                                        continue;
                                } elseif ( $type == "wpie_tax" ) {

                                        $key = isset( $options[ 'taxName' ] ) ? wpie_sanitize_field( $options[ 'taxName' ] ) : "";

                                        if ( in_array( $key, [ "product_type", "product_shipping_class" ] ) ) {

                                                self::$fields[ "wpie_item_" . $key ] = $value;

                                                if ( $key == "product_shipping_class" ) {
                                                        self::$fields[ "wpie_item_product_shipping_class_logic" ] = "as_specified";
                                                }
                                        } else {
                                                self::$fields[ "wpie_item_set_taxonomy" ][ $key ] = 1;
                                                self::$fields[ "wpie_item_taxonomy" ][ $key ]     = $value;
                                                if ( isset( $options[ 'hierarchical' ] ) && $options[ 'hierarchical' ] == 1 ) {
                                                        self::$fields[ "wpie_item_taxonomy_hierarchical_delim" ][ $key ] = ">";
                                                }
                                        }
                                } elseif ( $type == "wc-product-attr" ) {

                                        $attr_label = isset( $options[ 'name' ] ) ? $options[ 'name' ] : "";

                                        $attr_name = !empty( $attr_label ) ? strtolower( preg_replace( '/[^a-z0-9_]/i', '', $attr_label ) ) : "";

                                        $temp_attr_name = "{attributename" . $attr_name . "[1]}";

                                        if ( isset( self::$fields[ "wpie_attr_slug" ] ) && is_array( self::$fields[ "wpie_attr_slug" ] ) && !empty( self::$fields[ "wpie_attr_slug" ] ) && in_array( $temp_attr_name, self::$fields[ "wpie_attr_slug" ] ) ) {

                                                $attr_count = 0;

                                                while ( in_array( $temp_attr_name, self::$fields[ "wpie_attr_slug" ] ) ) {
                                                        $attr_count++;
                                                        $temp_attr_name = "{attributename" . $attr_name . "_" . $attr_count . "[1]}";
                                                }

                                                $attr_name = $attr_name . "_" . $attr_count;

                                                unset( $attr_count );
                                        }

                                        self::$fields[ "wpie_product_attr_name" ][]        = $attr_label;
                                        self::$fields[ "wpie_attr_slug" ][]                = "{attributename" . $attr_name . "[1]}";
                                        self::$fields[ "wpie_product_attr_value" ][]       = "{attributevalue" . $attr_name . "[1]}";
                                        self::$fields[ "wpie_attr_in_variations" ][]       = "{attributeinvariations" . $attr_name . "[1]}";
                                        self::$fields[ "wpie_attr_is_visible" ][]          = "{attributeisvisible" . $attr_name . "[1]}";
                                        self::$fields[ "wpie_attr_is_taxonomy" ][]         = "{attributeistaxonomy" . $attr_name . "[1]}";
                                        self::$fields[ "wpie_attr_is_auto_create_term" ][] = "yes";
                                        self::$fields[ "wpie_attr_position" ][]            = "{attributeposition" . $attr_name . "[1]}";

                                        unset( $attr_name, $temp_attr_name );
                                        continue;
                                } elseif ( $type == "wc-order" ) {

                                        $order_field_type = isset( $options[ 'field_type' ] ) ? wpie_sanitize_field( $options[ 'field_type' ] ) : "";

                                        $order_field_key = isset( $options[ 'field_key' ] ) ? wpie_sanitize_field( $options[ 'field_key' ] ) : "";

                                        if ( $order_field_type == "coupons" && in_array( $key, [ "_cart_discount" ] ) ) {
                                                continue;
                                        }
                                } elseif ( $type == "yoast_seo" ) {

                                        $key = isset( $options[ 'metaKey' ] ) ? wpie_sanitize_field( $options[ 'metaKey' ] ) : "";

                                        if ( in_array( $key, [ "wpseo_twitter-image", "wpseo_opengraph-image" ] ) ) {
                                                self::$fields[ "wpie_item_" . $key . "_url_data" ] = $value;
                                                $value                                             = "url";
                                        }
                                        self::$fields[ "wpie_item_" . $key ] = $value;
                                } else {

                                        switch ( $key ) {
                                                case "author_email":
                                                        self::$fields[ "wpie_item_author" ]                          = $value;
                                                        break;
                                                case "term_parent_slug":
                                                        self::$fields[ "wpie_item_term_parent" ]                     = $value;
                                                        break;
                                                case "parent":
                                                        self::$fields[ "wpie_item_parent" ]                          = "{parentslug[1]}";
                                                        self::$fields[ "wpie_item_parent_id" ]                       = "{parent[1]}";
                                                        break;
                                                case "comment_parent_content":
                                                        self::$fields[ "wpie_item_comment_parent" ]                  = $value;
                                                        break;
                                                case "comment_parent":
                                                        self::$fields[ "wpie_item_" . $key . "_id" ]                 = $value;
                                                        break;
                                                case "user_pass":
                                                        self::$fields[ "wpie_item_set_hashed_password" ]             = 1;
                                                        break;
                                                case "polylang_translation_title":
                                                        self::$fields[ "wpie_item_polylang_default_item" ]           = "title";
                                                        self::$fields[ "wpie_item_polylang_translation_title_data" ] = $value;
                                                        break;

                                                case "image_title":
                                                case "image_caption":
                                                case "image_description":
                                                case "image_alt":
                                                        self::$fields[ "wpie_item_set_" . $key ] = 1;
                                                        break;
                                        }
                                        self::$fields[ "wpie_item_" . $key ] = $value;
                                }

                                unset( $field, $label, $options, $type, $value, $key );
                        }
                }
        }

}

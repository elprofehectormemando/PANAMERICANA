<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

if (!function_exists("get_term_meta")) {

    function get_term_meta($term_id = 0, $key = '', $single = false) {
        return get_metadata('term', $term_id, $key, $single);
    }

}
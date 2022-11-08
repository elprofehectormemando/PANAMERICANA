<?php

/**
 * The Updates routine for version 1.5.0
 *
 * @since      1.5.0
 * @package    wpie
 * @subpackage wpie\Core
 * @author     VJinfotech <support@vjinfotech.com>
 */

/**
 * Delete previous notices.
 */
function wpie_1_5_0_update() {

    global $wpdb;

    $wpdb->query("ALTER TABLE " . $wpdb->prefix . "wpie_template ADD `username` VARCHAR(60) NOT NULL AFTER opration, ADD `unique_id` VARCHAR(100) NOT NULL AFTER opration");

    $wpdb->query("UPDATE {$wpdb->prefix}wpie_template SET unique_id = MD5(`id`) WHERE unique_id = ''");
}

wpie_1_5_0_update();

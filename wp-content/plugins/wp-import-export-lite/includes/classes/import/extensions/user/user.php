<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

class WPIE_User_Import_Extension {

    public function __construct() {

        add_filter('wpie_import_engine_init', array($this, "wpie_import_engine_init"), 10, 3);

        add_filter('wpie_import_mapping_fields_file', array($this, "wpie_import_mapping_fields_file"), 10, 2);
    }

    public function wpie_import_mapping_fields_file($fileName = "", $import_type = "") {

        if ($import_type == "users" || $import_type == "shop_customer") {

            $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/user/wpie-user-fields.php';
        }

        return $fileName;
    }

    public function wpie_import_engine_init($import_engine = "", $wpie_import_type = "", $template_data = "") {

        if ($wpie_import_type == "users" || $wpie_import_type == "shop_customer") {

            $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/user/class-wpie-user.php';

            if (file_exists($fileName)) {

                require_once($fileName);
            }
            unset($fileName);

            $import_engine = '\wpie\import\user\WPIE_User_Import';
        }

        return $import_engine;
    }

}

new WPIE_User_Import_Extension();

<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

class WPIE_User_Export_Extension {

    public function __construct() {
        add_filter('wpie_export_engine_init', array($this, 'wpie_export_engine_init'), 10, 3);
    }

    public function wpie_export_engine_init($export_engine = "", $export_type = "", $template_data = "") {

        if ($export_type == "users" || $export_type == "shop_customer") {

            $fileName = WPIE_EXPORT_CLASSES_DIR . "/extensions/user/class-wpie-user.php";

            if (file_exists($fileName)) {

                require_once($fileName);
            }

            unset($fileName);

            $export_engine = '\wpie\export\user\WPIE_User_Export';
        }
        unset($template_data);

        unset($export_type);

        return $export_engine;
    }

}

new WPIE_User_Export_Extension();

<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

class WPIE_BG_Extension {

    public function __construct() {

        $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/bg/class-wpie-bg.php';

        if (file_exists($fileName)) {

            require_once($fileName);

            $bg_export = new \wpie\export\bg\WPIE_BG();

            $bg_export->init();

            unset($bg_export);
        }
    }

}

new WPIE_BG_Extension();

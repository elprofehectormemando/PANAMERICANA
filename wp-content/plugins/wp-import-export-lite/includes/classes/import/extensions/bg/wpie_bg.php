<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

class WPIE_BG_Import_Extension {

    public function __construct() {

       $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/bg/class-wpie-bg.php';

        if (file_exists($fileName)) {

            require_once($fileName);

            new \wpie\import\bg\WPIE_BG_Import();
        }
        unset($fileName);
    }
}

new WPIE_BG_Import_Extension();

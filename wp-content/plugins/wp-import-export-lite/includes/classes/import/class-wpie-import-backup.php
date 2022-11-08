<?php

namespace wpie\import\backup;

use WP_Error;
use wpie\export\WPIE_Export;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

class WPIE_Import_Backup {

    private $backup_dir;
    private $filename = "backup.json";
    private $backup = array();
    private $export;
    private $item_template;
    private $import_type;
    private $import_taxonomy_type;

    public function __construct() {
        
    }

    public function init_backup_services($import_type = "", $wpie_taxonomy_type = "", $backup_dir = "") {

        $is_dir = $this->set_dir($backup_dir);

        if (is_wp_error($is_dir)) {
            return $is_dir;
        }

        unset($is_dir);

        if (file_exists($this->backup_dir . "/" . $this->filename)) {

            $this->backup = json_decode(file_get_contents($this->backup_dir . "/" . $this->filename), true);
        }

        $this->import_type = $import_type;

        $this->import_taxonomy_type = $wpie_taxonomy_type;

        $this->init_export();

        return true;
    }

    private function init_export() {

        if (file_exists(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php')) {
            require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php');
        }

        $this->export = new \wpie\export\WPIE_Export();

        $fields = $this->export->prepare_fields($this->import_type, $this->import_taxonomy_type);

        $item_fields = "";

        if ($fields) {

            foreach ($fields as $field_data) {

                if (isset($field_data['isExported']) && $field_data['isExported'] == false) {
                    continue;
                }

                if (isset($field_data['data']) && is_array($field_data['data'])) {

                    foreach ($field_data['data'] as $_field) {

                        if (isset($_field['isExported']) && $_field['isExported'] == false) {
                            continue;
                        }
                        $label = isset($_field['name']) ? $_field['name'] : "";

                        $value = json_encode($_field);

                        $item_fields .= $label . "|~|" . $value . "|~|" . $value . "~||~";

                        unset($label, $value);
                    }
                }
            }
        }

        $this->item_template["fields_data"] = $item_fields;

        $this->item_template["wpie_export_type"] = $this->import_type;

        $this->item_template["wpie_taxonomy_type"] = $this->import_taxonomy_type;

        $this->item_template["backup_dir"] = $this->backup_dir;

        if ($this->backup) {
            $this->item_template["count"] = count($this->backup);
        } else {
            $this->item_template["count"] = 0;
        }
        unset($item_fields, $fields);
    }

    private function set_dir($backup_dir = "") {

        if ($backup_dir && wp_is_writable($backup_dir)) {
            $this->backup_dir = $backup_dir;
        } else {
            return new \WP_Error('wpie_import_error', __('Backup Directory is not writable', 'wp-import-export-lite'));
        }
        return true;
    }

    public function create_backup($item_id = 0, $is_new_item = false) {

        if (!isset($this->backup[$item_id])) {

            $this->backup[$item_id] = array("is_new_item" => $is_new_item);

            $is_success = file_put_contents($this->backup_dir . "/" . $this->filename, json_encode($this->backup));

            if ($is_success === false) {
                return new \WP_Error('wpie_import_error', __('Fail To Generate Log', 'wp-import-export-lite'));
            }
            unset($is_success);

            if (!$is_new_item) {
                $this->generate_backup($item_id);
            }
        }

        return true;
    }

    private function generate_backup($item_id = 0) {

        $this->item_template["id"] = $item_id;

        $this->export->init_export($this->import_type, "import_backup", $this->item_template);

        $this->item_template["count"] ++;
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}

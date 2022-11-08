<?php

namespace wpie\import\log;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

class WPIE_Import_Log {

    private $log_dir;
    private $log_fp;
    private $filename = "import_log.txt";

    public function __construct() {
        
    }

    public function init_log_services($log_dir = "") {

        $is_dir = $this->set_dir($log_dir);

        if (is_wp_error($is_dir)) {
            return $is_dir;
        }

        unset($is_dir);

        $this->log_fp = fopen($this->log_dir . "/" . $this->filename, 'a');

        return true;
    }

    private function set_dir($log_dir = "") {

        if ($log_dir && wp_is_writable($log_dir)) {
            $this->log_dir = $log_dir;
        } else {
            return new \WP_Error('wpie_import_error', __('Log Directory is not writable', 'wp-import-export-lite'));
        }
        return true;
    }

    public function add_log($log = "") {
        if ($this->log_fp) {
            fwrite($this->log_fp, $log . PHP_EOL);
        }
    }

    public function finalyze_process() {
        if ($this->log_fp) {
            fclose($this->log_fp);
            unset($this->log_fp);
        }
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}

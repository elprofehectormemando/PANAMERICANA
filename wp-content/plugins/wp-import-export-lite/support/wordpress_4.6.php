<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

if (!class_exists("WP_Term_Query")) {

    class WP_Term_Query {

        private $query;

        public function __construct($query = '') {
            $this->query = $query;
        }

        public function get_terms() {
            global $wp_version;

            if (version_compare($wp_version, '4.5.0', '<')) {

                return get_terms($this->query['taxonomy'], $this->query);
            } else {
                return get_terms($this->query);
            }
        }

        public function __destruct() {
            foreach ($this as $key => $value) {
                unset($this->$key);
            }
        }

    }

}

if (!function_exists("wpie_unzip_file")) {

    function wpie_unzip_file($file, $to) {

        global $wp_filesystem;

        if (!$wp_filesystem || !is_object($wp_filesystem))
            return new WP_Error('fs_unavailable', __('Could not access filesystem.', 'wp-import-export-lite'));

        // Unzip can use a lot of memory, but not this much hopefully
        /** This filter is documented in wp-admin/admin.php */
        @ini_set('memory_limit', apply_filters('admin_memory_limit', WP_MAX_MEMORY_LIMIT));

        $needed_dirs = array();
        $to = trailingslashit($to);

        // Determine any parent dir's needed (of the upgrade directory)
        if (!$wp_filesystem->is_dir($to)) { //Only do parents if no children exist
            $path = preg_split('![/\\\]!', untrailingslashit($to));
            for ($i = count($path); $i >= 0; $i--) {
                if (empty($path[$i]))
                    continue;

                $dir = implode('/', array_slice($path, 0, $i + 1));
                if (preg_match('!^[a-z]:$!i', $dir)) // Skip it if it looks like a Windows Drive letter.
                    continue;

                if (!$wp_filesystem->is_dir($dir))
                    $needed_dirs[] = $dir;
                else
                    break; // A folder exists, therefor, we dont need the check the levels below this
            }
        }

        /**
         * Filter whether to use ZipArchive to unzip archives.
         *
         * @since 3.0.0
         *
         * @param bool $ziparchive Whether to use ZipArchive. Default true.
         */
        if (class_exists('ZipArchive', false) && apply_filters('unzip_file_use_ziparchive', true)) {
            $result = wpie_unzip_file_ziparchive($file, $to, $needed_dirs);
            if (true === $result) {
                return $result;
            } elseif (is_wp_error($result)) {
                if ('incompatible_archive' != $result->get_error_code())
                    return $result;
            }
        }

        // Fall through to PclZip if ZipArchive is not available, or encountered an error opening the file.
        return _unzip_file_pclzip($file, $to, $needed_dirs);
    }

}

if (!function_exists("wpie_unzip_file_ziparchive")) {

    function wpie_unzip_file_ziparchive($file, $to, $needed_dirs = array()) {
        global $wp_filesystem;

        $z = new ZipArchive();

        $zopen = $z->open($file, ZIPARCHIVE::CHECKCONS);
        if (true !== $zopen)
            return new WP_Error('incompatible_archive', __('Incompatible Archive.', 'wp-import-export-lite'), array('ziparchive_error' => $zopen));

        $uncompressed_size = 0;

        for ($i = 0; $i < $z->numFiles; $i++) {
            if (!$info = $z->statIndex($i))
                return new WP_Error('stat_failed_ziparchive', __('Could not retrieve file from archive.', 'wp-import-export-lite'));

            if ('__MACOSX/' === substr($info['name'], 0, 9)) // Skip the OS X-created __MACOSX directory
                continue;

            $uncompressed_size += $info['size'];

            if ('/' === substr($info['name'], -1)) {
                // Directory.
                $needed_dirs[] = $to . untrailingslashit($info['name']);
            } elseif ('.' !== $dirname = dirname($info['name'])) {
                // Path to a file.
                $needed_dirs[] = $to . untrailingslashit($dirname);
            }
        }

        /*
         * disk_free_space() could return false. Assume that any falsey value is an error.
         * A disk that has zero free bytes has bigger problems.
         * Require we have enough space to unzip the file and copy its contents, with a 10% buffer.
         */
        if (defined('DOING_CRON') && DOING_CRON) {
            $available_space = @disk_free_space(WP_CONTENT_DIR);
            if ($available_space && ( $uncompressed_size * 2.1 ) > $available_space)
                return new WP_Error('disk_full_unzip_file', __('Could not copy files. You may have run out of disk space.', 'wp-import-export-lite'), compact('uncompressed_size', 'available_space'));
        }

        $needed_dirs = array_unique($needed_dirs);
        foreach ($needed_dirs as $dir) {
            // Check the parent folders of the folders all exist within the creation array.
            if (untrailingslashit($to) == $dir) // Skip over the working directory, We know this exists (or will exist)
                continue;
            if (strpos($dir, $to) === false) // If the directory is not within the working directory, Skip it
                continue;

            $parent_folder = dirname($dir);
            while (!empty($parent_folder) && untrailingslashit($to) != $parent_folder && !in_array($parent_folder, $needed_dirs)) {
                $needed_dirs[] = $parent_folder;
                $parent_folder = dirname($parent_folder);
            }
        }
        asort($needed_dirs);

        // Create those directories if need be:
        foreach ($needed_dirs as $_dir) {
            // Only check to see if the Dir exists upon creation failure. Less I/O this way.
            if (!$wp_filesystem->mkdir($_dir, FS_CHMOD_DIR) && !$wp_filesystem->is_dir($_dir)) {
                return new WP_Error('mkdir_failed_ziparchive', __('Could not create directory.', 'wp-import-export-lite'), substr($_dir, strlen($to)));
            }
        }
        unset($needed_dirs);

        for ($i = 0; $i < $z->numFiles; $i++) {
            if (!$info = $z->statIndex($i))
                return new WP_Error('stat_failed_ziparchive', __('Could not retrieve file from archive.', 'wp-import-export-lite'));

            if ('/' == substr($info['name'], -1)) // directory
                continue;

            if ('__MACOSX/' === substr($info['name'], 0, 9)) // Don't extract the OS X-created __MACOSX directory files
                continue;

            $contents = $z->getFromIndex($i);
            if (false === $contents)
                return new WP_Error('extract_failed_ziparchive', __('Could not extract file from archive.', 'wp-import-export-lite'), $info['name']);

            if (!$wp_filesystem->put_contents($to . $info['name'], $contents, FS_CHMOD_FILE))
                return new WP_Error('copy_failed_ziparchive', __('Could not copy file.', 'wp-import-export-lite'), $info['name']);
        }

        $z->close();

        return true;
    }

}
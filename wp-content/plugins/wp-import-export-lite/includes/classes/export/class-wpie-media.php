<?php

namespace wpie\export\media;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'wp-import-export-lite'));
}

class WPIE_Media {

    public function __construct() {
        
    }

    public function get_media($media_id = 0) {

        if (empty($media_id)) {
            return;
        }

        $wpie_attachments = get_posts(array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_parent' => $media_id,
        ));

        return $wpie_attachments;
    }

    public function get_images($parent = 0, $attch = array(), $field_type = "", $type = "post") {

        $image_data = array();

        $featured_image_id = $this->get_meta($parent, '_thumbnail_id', true, $type);

        if (empty($featured_image_id)) {

            $featured_image_id = $this->get_meta($parent, 'thumbnail_id', true, $type);
        }

        if (!empty($featured_image_id)) {
            $image_data[$featured_image_id] = get_post($featured_image_id);
        }

        unset($featured_image_id);

        $image_gallery = $this->get_meta($parent, '_product_image_gallery', true, $type);

        if (!empty($image_gallery)) {

            $image_gallery_data = explode(',', $image_gallery);

            if (!empty($image_gallery_data) && is_array($image_gallery_data)) {

                foreach ($image_gallery_data as $gallary_data) {

                    if (!empty($gallary_data) && !in_array($gallary_data, $image_data)) {

                        $wpie_image = get_post($gallary_data);

                        if ($wpie_image) {
                            $image_data[$gallary_data] = $wpie_image;
                        }
                        unset($wpie_image);
                    }
                }
            }
            unset($image_gallery_data);
        }
        unset($image_gallery);


        if (!empty($attch)) {

            foreach ($attch as $wpie_image) {

                if (!in_array($wpie_image, $image_data) && wp_attachment_is_image($wpie_image->ID)) {

                    $image_data[$wpie_image->ID] = $wpie_image;
                }
            }
        }
        unset($attch);

        $images = array();

        if (!empty($image_data)) {

            $is_empty = true;

            foreach ($image_data as $wpie_attach) {

                $_value = $this->get_field_value(str_replace("image_", "", $field_type), $wpie_attach, $type);

                $images[] = $_value;

                if ($_value) {
                    $is_empty = false;
                }
            }

            if ($is_empty) {
                $images = array();
            }

            unset($is_empty);
        }

        return $images;
    }

    public function get_attch($attachments = null, $field_type = "", $type = "post") {

        if (empty($attachments)) {
            return;
        }

        $media_attch = array();

        if ($attachments && is_array($attachments)) {
            foreach ($attachments as $wpie_attch) {
                if (!wp_attachment_is_image($wpie_attch->ID)) {
                    $media_attch[] = $wpie_attch;
                }
            }
        }

        $attach_data = array();

        if (!empty($media_attch)) {

            $is_empty = true;

            foreach ($media_attch as $attach) {

                $_value = $this->get_field_value(str_replace("attachment_", "", $field_type), $attach, $type);

                $attach_data[] = $_value;

                if ($_value) {
                    $is_empty = false;
                }
            }

            if ($is_empty) {
                $attach_data = array();
            }

            unset($is_empty);
        }

        unset($media_attch);

        return $attach_data;
    }

    private function get_field_value($field_type = 'url', $attachment = false, $type = "post") {

        if (empty($attachment))
            return false;

        switch ($field_type) {
            case 'media':
            case 'attachments':
            case 'url':
                return wp_get_attachment_url($attachment->ID);
                break;
            case 'filename':
                return basename(wp_get_attachment_url($attachment->ID));
                break;
            case 'path':
                return get_attached_file($attachment->ID);
                break;
            case 'id':
                return $attachment->ID;
                break;
            case 'title':
                return $attachment->post_title;
                break;
            case 'caption':
                return $attachment->post_excerpt;
                break;
            case 'description':
                return $attachment->post_content;
                break;
            case 'alt':
                return $this->get_meta($attachment->ID, '_wp_attachment_image_alt', true, $type);
                break;

            default:
                return false;
                break;
        }

        return false;
    }

    private function get_meta($data_id = 0, $meta_key = "", $single = true, $type = "post") {

        if ($type == "texonomy") {

            return get_term_meta($data_id, $meta_key, $single);
        } elseif ($type == "user") {

            return get_user_meta($data_id, $meta_key, $single);
        } else {

            return get_post_meta($data_id, $meta_key, $single);
        }
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}

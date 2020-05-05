<?php

namespace YellowTree\GeoipDetect;

/**
 * This API is experimental and WILL change in future versions. Beware!
 */
class Logger {
    const CATEGORY_CRON = 'cron';
    const CATEGORY_UPDATE = 'update';
    
    // Other errors to log: lookup, API

    public static function logIfError($str, $category = '', $data = array()) {
        if (is_wp_error($str)) {
            $str = $str->get_error_message();
        }

        if (is_string($str)) {
            self::log($str, $category, $data);
        }
    }

    public static function log($str, $category = '', $data = array()) {
        $str = sanitize_text_field($str);

        // For now, only log the last error
        $time = geoip_detect_format_localtime();

        $str = '[' . $time . '] ' . $str;
        update_option('geoip-detect-logger-last-error' . $category, $str);
    }

    public static function get_last_error_msg($category = '') {
        return get_option('geoip-detect-logger-last-error' . $category);
    }
    public static function reset_last_error_msg($category = '') {
        update_option('geoip-detect-logger-last-error' . $category, false);
    }


}


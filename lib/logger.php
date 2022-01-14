<?php

namespace YellowTree\GeoipDetect;

/**
 * This API is experimental and WILL change in future versions. Beware!
 */
class Logger {
    const CATEGORY_CRON = 'cron';
    const CATEGORY_UPDATE = 'update';
    
    protected static $ignoreErrorCodes = [
        'http_304',
    ];
    protected static $ignoreErrorMessages = [
        'It has not changed since the last update.'
    ];

    public static function init() {
        $translated = [];
        foreach (self::$ignoreErrorMessages as $msg) {
            $tr = __($msg, 'geoip-detect');
            if ($tr !== $msg) {
                $translated[] = $tr;
            }
        }
        self::$ignoreErrorMessages = array_merge(self::$ignoreErrorMessages, $translated);
    }

    // Other errors to log: lookup, API

    public static function logIfError($str, $category = '', $data = []) {
        if (is_wp_error($str)) {
            $code = $str->get_error_code();
            if ($code && in_array($code, self::$ignoreErrorCodes)) {
                return;
            }

            $str = $str->get_error_message();
        }

        if (is_string($str)) {
            if (in_array($str, self::$ignoreErrorMessages)) {
                return;
            }
            self::log($str, $category, $data);
        }
    }

    public static function log($str, $category = '', $data = []) {
        $str = sanitize_text_field($str);

        // For now, only log the last error
        $time = geoip_detect_format_localtime();

        $str = '[' . $time . '] ' . $str;

        // ToDo implement $data

        update_option('geoip-detect-logger-last-error' . $category, $str);
    }

    public static function get_last_error_msg($category = '') {
        return get_option('geoip-detect-logger-last-error' . $category);
    }
    public static function reset_last_error_msg($category = '') {
        update_option('geoip-detect-logger-last-error' . $category, false);
    }


}
Logger::init();

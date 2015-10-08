<?php

/**
 * Calling the API via AJAX
 * ========================
 * 
 * These function make it possible to query the geo-data corresponding to the current visitor via AJAX.
 * This can be useful for Site Cache: If the variable content/behavior is injected via JS only, the HTML still can be cached.
 * 
 * WARNING: We cannot completely prevent others from using this functionality, though, as JS requests can be faked.
 * To make this harder, we check the referer (so simply embeding the JS in another site won't work).
 */

function geoip_detect_ajax_get_info_from_current_ip() {
	// Enabled in preferences? If not, do as if the plugin doesn't even exist.
	if (!get_option('geoip-detect-ajax_enabled'))
		return;
	
	if (!defined( 'DOING_AJAX' ))	
		_geoip_detect_ajax_error('This method is for AJAX only.');
	
	// Referer check
	
	$referer = wp_get_referer();
	$site_url = get_site_url();
	if (strpos($referer, $site_url) !== 0)
		_geoip_detect_ajax_error('Incorrect referer.'); // Ajax only works if coming from the same site. No CORS even if headers are enabled.
	
	// Do not cache this response!
	if (!headers_sent()) {
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');
	}
	
	$locales = null;
	if (isset($_REQUEST['locales']))
		$locales = $_REQUEST['locales'];
	
	$data = _geoip_detect_ajax_get_data($locales);
	
	if ($data['extra']['error'])
		http_response_code(400);
	
	echo json_encode($data);
	exit;
}

add_action(        'wp_ajax_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );
add_action( 'wp_ajax_nopriv_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );

function _geoip_detect_ajax_error($error) {
	http_response_code(412);

	$data = array('extra' => array('error' => $error));
	echo json_encode($data);

	exit;
}

function _geoip_detect_ajax_get_data($locales, $options = array()) {
	$info = geoip_detect2_get_info_from_current_ip($locales, $options);
	$data = $info->jsonSerialize();
	
	// Fill in properties that are possible, but not existing (eg, for this data source)
	// TODO: Hard code default array
	
	// Add the 'name' field
	$locales = apply_filters('geoip_detect2_locales', $locales);
	foreach ($data as &$prop) {
		if (isset($prop['names']) && is_array($prop['names'])) {
			$prop['name'] = _geoip_detect_ajax_get_name($prop['names'], $locales);
		}
	}
	
	return $data;
}

function _geoip_detect_ajax_get_name($names, $locales)
{
	foreach ($locales as $locale) {
		if (isset($names[$locale])) {
			return $names[$locale];
		}
	}
	// Nothing found ...
	return '';
}


function _geoip_detect_register_javascript() {
	wp_enqueue_script('geoip-detect-js', GEOIP_DETECT_PLUGIN_URI . 'js/example_usage.js', array('jquery'), GEOIP_DETECT_VERSION, true);

	$data = array();
	$data['ajaxurl'] = admin_url('/admin-ajax.php');
	wp_localize_script('geoip-detect-js', 'geoip_detect', $data);
}

add_action('wp_enqueue_scripts', '_geoip_detect_register_javascript');

if (!function_exists('http_response_code')) {
        function http_response_code($code = NULL) {
            if ($code !== NULL) {
                switch ($code) {
                    case 100: $text = 'Continue'; break;
                    case 101: $text = 'Switching Protocols'; break;
                    case 200: $text = 'OK'; break;
                    case 201: $text = 'Created'; break;
                    case 202: $text = 'Accepted'; break;
                    case 203: $text = 'Non-Authoritative Information'; break;
                    case 204: $text = 'No Content'; break;
                    case 205: $text = 'Reset Content'; break;
                    case 206: $text = 'Partial Content'; break;
                    case 300: $text = 'Multiple Choices'; break;
                    case 301: $text = 'Moved Permanently'; break;
                    case 302: $text = 'Moved Temporarily'; break;
                    case 303: $text = 'See Other'; break;
                    case 304: $text = 'Not Modified'; break;
                    case 305: $text = 'Use Proxy'; break;
                    case 400: $text = 'Bad Request'; break;
                    case 401: $text = 'Unauthorized'; break;
                    case 402: $text = 'Payment Required'; break;
                    case 403: $text = 'Forbidden'; break;
                    case 404: $text = 'Not Found'; break;
                    case 405: $text = 'Method Not Allowed'; break;
                    case 406: $text = 'Not Acceptable'; break;
                    case 407: $text = 'Proxy Authentication Required'; break;
                    case 408: $text = 'Request Time-out'; break;
                    case 409: $text = 'Conflict'; break;
                    case 410: $text = 'Gone'; break;
                    case 411: $text = 'Length Required'; break;
                    case 412: $text = 'Precondition Failed'; break;
                    case 413: $text = 'Request Entity Too Large'; break;
                    case 414: $text = 'Request-URI Too Large'; break;
                    case 415: $text = 'Unsupported Media Type'; break;
                    case 500: $text = 'Internal Server Error'; break;
                    case 501: $text = 'Not Implemented'; break;
                    case 502: $text = 'Bad Gateway'; break;
                    case 503: $text = 'Service Unavailable'; break;
                    case 504: $text = 'Gateway Time-out'; break;
                    case 505: $text = 'HTTP Version not supported'; break;
                    default:
                        exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
                }

                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
                header($protocol . ' ' . $code . ' ' . $text);
                $GLOBALS['http_response_code'] = $code;
            } else {
                $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
            }
            return $code;
        }
    }
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
		die('This method is for AJAX only.');
	
	// Referer check
	
	$referer = wp_get_referer();
	$site_url = get_site_url();
	if (strpos($referer, $site_url) !== 0)
		die('Incorrect referer.'); // Ajax only works if coming from the same site. No CORS even if headers are enabled.
	
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
		http_send_status(401);
	
	echo json_encode($data);
	exit;
}

add_action(        'wp_ajax_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );
add_action( 'wp_ajax_nopriv_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );

function _geoip_detect_ajax_get_data($locales, $options = array()) {
	$info = geoip_detect2_get_info_from_current_ip($locales, $options);
	$data = $info->jsonSerialize();
	
	// Fill in properties that are possible, but not existing (eg, for this data source)
	// TODO: Hard code default array? Reflection API from Maxmind API and cache in a transient?	
	
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
	return '';
}



add_action('wp_enqueue_scripts', function() {
	wp_enqueue_script('geoip-detect-js', GEOIP_DETECT_PLUGIN_URI . 'js/example_usage.js', array('jquery'), GEOIP_DETECT_VERSION, true);
	
	$data = array();
	$data['ajaxurl'] = admin_url('/admin-ajax.php'); 
	wp_localize_script('geoip-detect-js', 'geoip_detect', $data);
});

if (!function_exists('http_send_status')) {
	// Polyfill in this function if PHP < 7.0
	
	function http_send_status($status) {
		//...
		return true;
	}
}
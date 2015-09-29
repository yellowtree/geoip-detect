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
	
	$info = geoip_detect2_get_info_from_current_ip($locales);
	$data = $info->jsonSerialize();
	
	echo json_encode($data);
	exit;
}

add_action(        'wp_ajax_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );
add_action( 'wp_ajax_nopriv_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );

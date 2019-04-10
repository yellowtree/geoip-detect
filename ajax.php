<?php

/*
Copyright 2013-2019 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (wp-geoip-detect| |posteo.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

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
	// Do not cache this response!
	if (!headers_sent()) {
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/json');
	}

	// Enabled in preferences? If not, do as if the plugin doesn't even exist.
	if (!get_option('geoip-detect-ajax_enabled')) {
        _geoip_detect_ajax_error('AJAX must be enabled in the options of the plugin.');
    }
	
	if (!defined( 'DOING_AJAX' ))	
		_geoip_detect_ajax_error('This method is for AJAX only.');
	
	// Referer check
	
    $referer = _geoip_detect_get_domain_name($_SERVER['HTTP_REFERER']);
    if (!$referer) {
        _geoip_detect_ajax_error('This AJAX call does not work when called directly. Do an AJAX call via JS instead.');
	}
	$allowed_domains = [ _geoip_detect_get_domain_name(get_site_url()) ];
	$allowed_domains = apply_filters('geoip_detect2_ajax_allowed_domains', $allowed_domains);
	if (!in_array($referer, $allowed_domains)) {
		_geoip_detect_ajax_error('Incorrect referer.'); // Ajax only works if coming from the same site. No CORS even if headers are enabled.
    }
	
	$options = apply_filters('geoip_detect2_ajax_options', []);

	// Do the API call:
	$data = _geoip_detect_ajax_get_data($options);
	
	$data = apply_filters('geoip_detect2_ajax_record_data', $data, isset($data['traits']['ip_address']) ? $data['traits']['ip_adress'] : '' ) ;
	
	if ($data['extra']['error'])
		http_response_code(500);
	
	echo json_encode($data);
	exit;
}

add_action(        'wp_ajax_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );
add_action( 'wp_ajax_nopriv_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );


function _geoip_detect_get_domain_name($url) {
	$result = parse_url($url);
	return $result['host'];
}

function _geoip_detect_ajax_error($error) {
	http_response_code(412);

	$data = array('extra' => array('error' => $error));
	echo json_encode($data);

	exit;
}


function _geoip_detect_ajax_get_data($options = array()) {
	$info = geoip_detect2_get_info_from_current_ip(['en'], $options);
	$data = $info->jsonSerialize();

	// For privacy reasons, do not emit the nb of credits left (Maxmind Precision)
	unset($data['maxmind']);
	
	return $data;
}

/**
 * Call this function if you want to register the JS script only for specific pages
 */
function geoip_detect2_enqueue_javascript() {
	wp_enqueue_script('geoip-detect-js');
}

function _geoip_detect_parcel_get_dist_js($handle) {
	$urlFile = GEOIP_PLUGIN_DIR . '/js/dist/parcel.json';
	if (!is_readable($urlFile)) return false;

	$json = file_get_contents($urlFile);
	$urls = json_decode($json, true);

	if (isset($urls[$handle]))
		return '/js/dist' .$urls[$handle];
	return false;
}

function _geoip_detect_register_javascript() {
	$file_uri = _geoip_detect_parcel_get_dist_js('frontendJS');
	if (!$file_uri) {
		if (WP_DEBUG) {
			trigger_error('Warning by the geoip-detect-Plugin: the file frontend.js could not be found, JS API will not work.', E_USER_NOTICE);
			die();
		}
		return;
	}

	wp_register_script('geoip-detect-js', GEOIP_DETECT_PLUGIN_URI . $file_uri, array('jquery'), GEOIP_DETECT_VERSION, true);
	$data = [
		'ajaxurl' => admin_url('/admin-ajax.php'),
		'default_locales' => apply_filters('geoip_detect2_locales', null),
	];
	$data = apply_filters('geoip_detect2_ajax_localize_script_data', $data);
	wp_localize_script('geoip-detect-js', 'geoip_detect', [ 'options' => $data ] );
	
	if (get_option('geoip-detect-ajax_enqueue_js')) {
		geoip_detect2_enqueue_javascript();
	}
}

add_action('wp_enqueue_scripts', '_geoip_detect_register_javascript');

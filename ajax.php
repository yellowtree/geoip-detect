<?php

/*
Copyright 2013-2023 Yellow Tree, Siegen, Germany
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
	// Enabled in preferences? If not, do as if the plugin doesn't even exist.
	if (!get_option('geoip-detect-ajax_enabled')) {
        return;
    }

	// Do not cache this response!
	if (!headers_sent()) {
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/json');
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
	
	$data = apply_filters('geoip_detect2_ajax_record_data', $data, isset($data['traits']['ip_address']) ? $data['traits']['ip_address'] : '' ) ;

	_geoip_detect_disable_pagecache();
	wp_send_json($data, !empty($data['extra']['error']) ? 500 : 200 );
}

add_action(        'wp_ajax_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );
add_action( 'wp_ajax_nopriv_geoip_detect2_get_info_from_current_ip', 'geoip_detect_ajax_get_info_from_current_ip' );


function _geoip_detect_get_domain_name($url) {
	$result = parse_url($url);
	return $result['host'];
}

function _geoip_detect_ajax_error($error) {
	$data = [ 'extra' => [ 'error' => $error ] ];
	$data['is_empty'] = true;
	_geoip_detect_disable_pagecache();
	wp_send_json($data, 412);
}


function _geoip_detect_ajax_get_data($options = []) {
	$info = geoip_detect2_get_info_from_current_ip(['en'], $options);
	$data = $info->jsonSerialize();

	// For privacy reasons, do not emit the nb of credits left (Maxmind Precision)
	unset($data['maxmind']);

	if (isset($data['subdivisions']) && is_array($data['subdivisions'])) {
		$data['most_specific_subdivision'] = end($data['subdivisions']);
	}

	return $data;
}

/**
 * Call this function if you want to register the JS script only for specific pages
 */
function _geoip_detect2_enqueue_javascript() {
	if (did_action('wp_enqueue_scripts')) {
		wp_enqueue_script('geoip-detect-js');
	} else {
		add_action('wp_enqueue_scripts', function() {
			wp_enqueue_script('geoip-detect-js');
		});
	}
	return true;
}

function _geoip_detect2_get_variant() {
	if (defined('GEOIP_DETECT_JS_VARIANT')) {
		if (file_exists(GEOIP_PLUGIN_DIR . 'js/dist/frontend_' . $variant . '.js')) {
			return $variant;
		}
	}
	return 'full';
}

function _geoip_detect_register_javascript() {
	// What about CORS usage?
	// if (!get_option('geoip-detect-ajax_enabled')) {
	// 	return;
	// }
	$variant = _geoip_detect2_get_variant();

	wp_register_script('geoip-detect-js', GEOIP_DETECT_PLUGIN_URI . 'js/dist/frontend_' . $variant . '.js', [], GEOIP_DETECT_VERSION, true);
	$data = [
		'ajaxurl' => admin_url('/admin-ajax.php'),
		'default_locales' => apply_filters('geoip_detect2_locales', null),
		'do_body_classes' => (bool) get_option('geoip-detect-ajax_set_css_country'),
		'do_shortcodes' => (bool) get_option('geoip-detect-ajax_shortcodes'),
		'cookie_name' => 'geoip-detect-result', /* If you don't want to use the cookie cache (localstorage), empty this value via the filter */
		'cookie_duration_in_days' => 1, /* If you set this to 0, then the cookie will expire when the window closes. */
	];
	$data = apply_filters('geoip_detect2_ajax_localize_script_data', $data);
	wp_localize_script('geoip-detect-js', 'geoip_detect', [ 'options' => $data ] );

	if (get_option('geoip-detect-ajax_enqueue_js') && !is_admin()) {
		geoip_detect2_enqueue_javascript('option');
	}
}

add_action('wp_enqueue_scripts', '_geoip_detect_register_javascript');

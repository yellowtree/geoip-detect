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
 * Get the client ip
 * IPv4 or IPv6-Adress of the client. This takes reverse proxies into account, if they are configured on the options page.
 * 
 * [geoip_detect2_get_client_ip]
 * 
 * @param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
 * 
 * @since 2.5.2 
 */
function geoip_detect2_shortcode_client_ip($attr) {
	if (geoip_detect2_shortcode_is_ajax_mode($attr)) {
		return geoip_detect2_shortcode([
			'property' => 'traits.ipAddress',
			'ajax' => isset($attr['ajax']) ? $attr['ajax'] : null,
		]);
	} else {
		$client_ip = geoip_detect2_get_client_ip();
		$client_ip = geoip_detect_normalize_ip($client_ip);
	
		return $client_ip;
	}
}
add_shortcode('geoip_detect2_get_client_ip', 'geoip_detect2_shortcode_client_ip');

function geoip_detect2_shortcode_get_external_ip_adress($attr) {
	$external_ip = geoip_detect2_get_external_ip_adress();
	$external_ip = geoip_detect_normalize_ip($external_ip);

	return $external_ip;
}
add_shortcode('geoip_detect2_get_external_ip_adress', 'geoip_detect2_shortcode_get_external_ip_adress');

function geoip_detect2_shortcode_get_current_source_description() {
	$return = geoip_detect2_get_current_source_description();

	return $return;
}
add_shortcode('geoip_detect2_get_current_source_description', 'geoip_detect2_shortcode_get_current_source_description');


/**
 * Just in case somebody really wants to use this shortcode outside of cf7
 */
function geoip_detect_shortcode_user_info() {
    return geoip_detect2_shortcode_user_info_wpcf7('', 'geoip_detect2_user_info', true);
}
add_shortcode('geoip_detect2_user_info', 'geoip_detect_shortcode_user_info');

function geoip_detect2_shortcode_enqueue_javascript() {
	geoip_detect2_enqueue_javascript('user_shortcode');
	return '';
}
add_shortcode('geoip_detect2_enqueue_javascript', 'geoip_detect2_shortcode_enqueue_javascript');

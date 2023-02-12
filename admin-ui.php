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

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
use YellowTree\GeoipDetect\Logger;

function geoip_detect_menu() {
	if (!function_exists('add_submenu_page')) {
		require_once ABSPATH . '/wp-admin/admin.php';
	}
	
	add_submenu_page('tools.php', __('Geolocation IP Detection Lookup', 'geoip-detect'), __('Geolocation Lookup', 'geoip-detect'), 'activate_plugins', GEOIP_PLUGIN_BASENAME, 'geoip_detect_lookup_page');
	add_options_page(__('Geolocation IP Detection', 'geoip-detect'), __('Geolocation IP Detection', 'geoip-detect'), 'manage_options', GEOIP_PLUGIN_BASENAME, 'geoip_detect_option_page');
}
add_action('admin_menu', 'geoip_detect_menu');

function geoip_detect_add_settings_link( $links ) {
	$link = '<a href="tools.php?page=' . GEOIP_PLUGIN_BASENAME . '">' . __('Lookup', 'geoip-detect') . '</a>';
	array_push( $links, $link );

	$link = '<a href="options-general.php?page=' . GEOIP_PLUGIN_BASENAME . '">' . __('Options', 'geoip-detect') . '</a>';
	array_push( $links, $link );

	return $links;
}
add_filter( "plugin_action_links_" . GEOIP_PLUGIN_BASENAME, 'geoip_detect_add_settings_link' );


// ------------- Admin GUI --------------------

function geoip_detect_verify_nonce($action) {
	$nonce = isset($_POST['_wpnonce']) ? sanitize_text_field($_POST['_wpnonce']) : '';
	return wp_verify_nonce( $nonce, 'geoip_detect_' . $action );
}

function geoip_detect_lookup_page()
{
	if (!is_admin())
		return;

	$ip_lookup_result = false;
	$message = '';
	$action = isset($_POST['action']) ? sanitize_key($_POST['action']) : '';
	$ip = isset($_POST['ip']) ? sanitize_text_field($_POST['ip']) : '';
	$current_ip = geoip_detect2_get_client_ip();

	if (geoip_detect_verify_nonce($action)) {
		switch($action) {
			case 'clear_cache':
				$registry = DataSourceRegistry::getInstance();
				$ret = $registry->clearCache();
				if ($ret === true) {
					$message = __('The cache has been emptied successfully.', 'geoip-detect');
				} else {
					$message = $ret;
				}
				break;
			case 'lookup':
				if ($ip)
				{
					$request_ip = geoip_detect_is_ip($ip) ? $ip : '';
					$request_skipCache = !empty($_POST['skip_cache']);
					$request_skipLocalCache = !empty($_POST['skip_local_cache']);
					$options = [ 'skipCache' => $request_skipCache, 'skipLocalCache' => $request_skipLocalCache ];

					$request_locales = null;
					if (!empty($_POST['locales'])) {
						$request_locales = explode(',', sanitize_text_field($_POST['locales']));
					}

					$ip_lookup_result = geoip_detect_lookup_page_timed_lookup($request_ip, $request_locales, $options, $current_ip, $ip_lookup_duration);
					geoip_detect_lookup_page_timed_lookup($request_ip, $request_locales, $options, $current_ip, $ip_lookup_2nd_duration);
				}
				break;
		}
	}

	include_once(GEOIP_PLUGIN_DIR . '/views/lookup.php');
}

function geoip_detect_lookup_page_timed_lookup($request_ip, $request_locales, $options, $current_ip, &$ip_lookup_duration) {
	$start = microtime(true);
	if ($request_ip === $current_ip) {
		$ip_lookup_result = geoip_detect2_get_info_from_current_ip($request_locales, $options);
	} else {
		$ip_lookup_result = geoip_detect2_get_info_from_ip($request_ip, $request_locales, $options);
	}
	$end = microtime(true);
	$ip_lookup_duration = $end - $start;

	return $ip_lookup_result;
}

function geoip_detect_sanitize_option($opt_name, $opt_value, &$message = '') {
	$opt_value = sanitize_text_field($opt_value);
	switch($opt_name) {
		case 'external_ip':
			if ($opt_value) {
				if (!geoip_detect_is_ip($opt_value)) {
					$message .= sprintf(__('The external IP "%s" is not a valid IP.', 'geoip-detect'), esc_html($opt_value));
					return false;
				} else {
					if (!geoip_detect_is_public_ip($opt_value)) {
						$message .= sprintf(__('Warning: The external IP "%s" is not a public internet IP, so it will probably not work.', 'geoip-detect'), esc_html($opt_value));
					}
					$opt_value = (string) $opt_value;
				}
			}

		case 'trusted_proxy_ips':
			$opt_value = geoip_detect_sanitize_ip_list($opt_value);
	}

	return $opt_value;

}

function geoip_detect_option_page() {
	if (!is_admin() || !current_user_can('manage_options'))
		return;


	if (isset($_GET['geoip_detect_part'])) {
		switch ($_GET['geoip_detect_part']) {
			case 'client-ip':
				return geoip_detect_option_client_ip_page();
				break;
		}
	}

	if (isset($_GET['geoip_detect_dismiss_log_notice'])) {
		$category = sanitize_key($_GET['geoip_detect_dismiss_log_notice']);
		if ($category == 'cron') {
			Logger::reset_last_error_msg($category);
		}
	}

	$last_cron_error_msg = Logger::get_last_error_msg('cron');

	$registry = DataSourceRegistry::getInstance();
	$sources = $registry->getAllSources();

	$messages = [];

	$numeric_options = [ 'set_css_country', 'has_reverse_proxy', 'disable_pagecache', 'ajax_enabled', 'ajax_enqueue_js', 'ajax_set_css_country', 'ajax_shortcodes', 'dynamic_reverse_proxies' ];
	$text_options = [ 'external_ip', 'trusted_proxy_ips', 'dynamic_reverse_proxy_type' ];
	$option_names = array_merge($numeric_options, $text_options);

	$action = isset($_POST['action']) ? sanitize_key($_POST['action']) : '';

	if (geoip_detect_verify_nonce($action)) {
		switch($action)
		{
			case 'update':
				$registry->setCurrentSource('auto');

				$s = new \YellowTree\GeoipDetect\DataSources\Auto\AutoDataSource();
				$ret = $s->maxmindUpdate();

				$c = new \YellowTree\GeoipDetect\Lib\RetrieveCcpaBlacklist();
				$ret2 = $c->doUpdate();

				if ($ret === true && $ret2 === true) {
					$messages[] = __('Updated successfully.', 'geoip-detect');
				} else {
					if ($ret !== true) {
						Logger::log($ret , Logger::CATEGORY_UPDATE);
						$messages[] = __('File was not updated', 'geoip-detect') .': '. $ret;
					 } 
					 
					 if ($ret2 !== true) {
						Logger::log($ret2, Logger::CATEGORY_UPDATE);
					
						$messages[] = __('Privacy Exclusions could not be updated', 'geoip-detect') .': '. $ret2;
					 }
				}

				break;

			case 'choose':
				$sourceId = sanitize_text_field($_POST['options']['source']);
				$registry->setCurrentSource($sourceId);
				break;


			case 'options-source':
				foreach ($sources as $s) {
					$ret = $s->saveParameters($_POST);
					if (is_string($ret) && $ret) {
						$messages[] = $ret;
					}
				}

				break;

			case 'options':
				// Empty IP Cache
				delete_transient('geoip_detect_external_ip');

				foreach ($option_names as $opt_name) {
					$m = '';
					if (in_array($opt_name, $numeric_options))
						$opt_value = isset($_POST['options'][$opt_name]) ? (int) $_POST['options'][$opt_name] : 0;
					else {
						$opt_value = geoip_detect_sanitize_option($opt_name, @$_POST['options'][$opt_name], $m);
					}
					if ($m) {
						$messages[] = $m;
					}

					if ($opt_value !== false) {
						update_option('geoip-detect-' . $opt_name, $opt_value);
					}
				}

				do_action('geoip_detect2_options_changed');

				break;
		}
	}

	if ($messages) {
		$message = implode('<br />', $messages);
	} else {
		$message = '';
	}

    $currentSource = $registry->getCurrentSource();

	$wp_options = [];
	foreach ($option_names as $opt_name) {
		$wp_options[$opt_name] = get_option('geoip-detect-'. $opt_name);
	}

	$ipv6_supported = GEOIP_DETECT_IPV6_SUPPORTED;

	include_once(GEOIP_PLUGIN_DIR . '/views/options.php');
}

function geoip_detect_option_client_ip_page() {
	if (!is_admin() || !current_user_can('manage_options'))
		return;

	$last_update = get_option('geoip_detect2_dynamic-reverse-proxies_last_updated');

	$successMessage = '';
	$errorMessage = '';
	$action = isset($_POST['action']) ? sanitize_key($_POST['action']) : '';
	if (geoip_detect_verify_nonce($action)) {
		switch($action)
		{
			case 'reload-proxies':
				$manager = \YellowTree\GeoipDetect\DynamicReverseProxies\getDataManager();
				if ($manager)  {
					$success = $manager->reload(true);
					if ($success) {
						$successMessage = 'Dynamic Reverse Proxy list was reloaded successfully.';
					} else {
						$errorMessage = 'There was an error reloading the dynamic reverse proxy list.';
					}
				} else {
					$errorMessage = 'No DataManager found.';
				}
				break;
		}
	}

	include_once(GEOIP_PLUGIN_DIR . '/views/client-ip.php');
}

function _geoip_detect_improve_data_for_lookup($data, $shorten_attributes = false) {
	if ($shorten_attributes) {
		$short = [
			'city',
			'subdivisions',
			'country',
			'location'	
		];
		$short = array_combine($short, $short);
		$data = array_intersect_key($data, $short);

		unset($data['city']['geoname_id']);
		unset($data['country']['geoname_id']);
		unset($data['country']['is_in_european_union']);
		unset($data['location']['accuracy_radius']);
		if (!empty($data['subdivisions'])) {
			foreach ($data['subdivisions'] as $i => $s) {
				unset($data['subdivisions'][$i]['geoname_id']);
			}
		}
	}

	// Logical order
	$order = [
		'is_empty',
		'city',
		'most_specific_subdivision',
		'subdivisions',
		'postal',
		'country',
		'registered_country',
		'represented_country',
		'continent',
		'location',
		'traits',
		'maxmind',
		'extra'
	];

	uksort($data, function($a, $b) use ($order) {
		$a_found = array_search($a, $order);
		$b_found = array_search($b, $order);

		if ($a_found === false) $a_found = 1000;
		if ($b_found === false) $b_found = 1000;
		return $a_found - $b_found;
	});

	return $data;
}
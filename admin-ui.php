<?php

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

function geoip_detect_menu() {
	require_once ABSPATH . '/wp-admin/admin.php';
	add_submenu_page('tools.php', __('GeoIP Detection Lookup', 'geoip-detect'), __('GeoIP Lookup', 'geoip-detect'), 'activate_plugins', GEOIP_PLUGIN_BASENAME, 'geoip_detect_lookup_page');
	add_options_page(__('GeoIP Detection', 'geoip-detect'), __('GeoIP Detection', 'geoip-detect'), 'manage_options', GEOIP_PLUGIN_BASENAME, 'geoip_detect_option_page');
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

function geoip_detect_lookup_page()
{
	$ip_lookup_result = false;
	$message = '';

	switch(@$_POST['action']) {
		case 'lookup':
			if (isset($_POST['ip']))
			{
				$request_ip = $_POST['ip'];
				$request_skipCache = !empty($_POST['skip_cache']);
				$options = array('skipCache' => $request_skipCache);
				
				$request_locales = null;
				if (!empty($_POST['locales']))
					$request_locales = explode(',', $_POST['locales']);
				
				$start = microtime(true);
				$ip_lookup_result = geoip_detect2_get_info_from_ip($request_ip, $request_locales, $options);
				$end = microtime(true);
				$ip_lookup_duration = $end - $start;
			}
			break;
	}
	
	include_once(GEOIP_PLUGIN_DIR . '/views/lookup.php');
}

function geoip_detect_option_page() {
	if (!current_user_can('manage_options'))
		return;
	
	$registry = DataSourceRegistry::getInstance();
	$sources = $registry->getAllSources();
	$currentSource = $registry->getCurrentSource();
	
	$message = '';
	
	$numeric_options = array('set_css_country', 'has_reverse_proxy', 'disable_pagecache');
	$text_options = array('external_ip');
	$option_names = array_merge($numeric_options, $text_options);
	
	switch(@$_POST['action'])
	{
		case 'update':
			$registry->setCurrentSource('auto');
	
			$s = new \YellowTree\GeoipDetect\DataSources\Auto\AutoDataSource();
			$ret = $s->maxmindUpdate();

			if ($ret === true)
				$message .= __('Updated successfully.', 'geoip-detect');
			else
				$message .= __('Update failed.', 'geoip-detect') .' '. $ret;
	
			break;

		case 'choose':
			$registry->setCurrentSource($_POST['options']['source']);
			$currentSource = $registry->getCurrentSource();
			break;
	
			
		case 'options-source':			
			$messages = array();
			foreach ($sources as $s) {
				$ret = $s->saveParameters($_POST);
				if (is_string($ret) && $ret) {
					$messages[] = $ret;
				}
			}
			if ($messages)
				$message .= implode('<br />', $messages);
			
			break;
			
		case 'options':	
			// Empty IP Cache
			delete_transient('geoip_detect_external_ip');
			
			if (!empty($_POST['options']['external_ip'])) {
				if (!geoip_detect_is_ip($_POST['options']['external_ip'])) {
					$message .= 'The external IP "' . esc_html($_POST['options']['external_ip']) . '" is not a valid IP.';
					unset($_POST['options']['external_ip']);
				} else if (!geoip_detect_is_public_ip($_POST['options']['external_ip'])) {
					$message .= 'Warning: The external IP "' . esc_html($_POST['options']['external_ip']) . '" is not a public internet IP, so it will probably not work.';
				}
			}
			
			
			foreach ($option_names as $opt_name) {
				if (in_array($opt_name, $numeric_options))
					$opt_value = isset($_POST['options'][$opt_name]) ? (int) $_POST['options'][$opt_name] : 0;
				else
					$opt_value = isset($_POST['options'][$opt_name]) ? $_POST['options'][$opt_name] : '';
				update_option('geoip-detect-' . $opt_name, $opt_value);
			}
			break;
	}

	$wp_options = array();
	foreach ($option_names as $opt_name) {
		$wp_options[$opt_name] = get_option('geoip-detect-'. $opt_name);
	}
	
	$ipv6_supported = GEOIP_DETECT_IPV6_SUPPORTED;
	
	include_once(GEOIP_PLUGIN_DIR . '/views/options.php');
}

<?php

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
				
				$request_locales = null;
				if (!empty($_POST['locales']))
					$request_locales = explode(',', $_POST['locales']);
				
				$start = microtime(true);
				$ip_lookup_result = geoip_detect2_get_info_from_ip($request_ip, $request_locales, $request_skipCache);
				$end = microtime(true);
				$ip_lookup_duration = $end - $start;
			}
			break;
	}
	
	include_once(GEOIP_PLUGIN_DIR . '/views/lookup.php');
}

function geoip_detect_option_page() {
	$message = '';
	
	$numeric_options = array('set_css_country', 'has_reverse_proxy');
	$text_options = array('source', 'manual_file');
	$option_names = array_merge($numeric_options, $text_options);
	
	switch(@$_POST['action'])
	{
		case 'update':
			update_option('geoip-detect-source', 'auto');
			update_option('geoip-detect-ui-has-chosen-source', true);
	
			$ret = geoip_detect_update();
			if ($ret === true)
				$message .= __('Updated successfully.', 'geoip-detect');
			else
				$message .= __('Update failed.', 'geoip-detect') .' '. $ret;
	
			break;
	

	
		case 'options':
			foreach ($option_names as $opt_name) {
				if (in_array($opt_name, $numeric_options))
					$opt_value = isset($_POST['options'][$opt_name]) ? (int) $_POST['options'][$opt_name] : 0;
				else
					$opt_value = isset($_POST['options'][$opt_name]) ? $_POST['options'][$opt_name] : '';
				update_option('geoip-detect-' . $opt_name, $opt_value);
			}
	
			// Validate manual file name
			if (!empty($_POST['options']['manual_file'])) {
				//$validated_filename = geoip_detect_validate_filename($_POST['options']['manual_file']);
				//update_option('geoip-detect-manual_file_validated', $validated_filename);
	
				if (empty($validated_filename)) {
					$message .= __('The manual datafile has not been found or is not a mmdb-File. ', 'geoip-detect');
				}
			}
	
	
			break;
	}
	
	
	$options = array();
	foreach ($option_names as $opt_name) {
		$options[$opt_name] = get_option('geoip-detect-'. $opt_name);
	}
	
	include_once(GEOIP_PLUGIN_DIR . '/views/options.php');
}
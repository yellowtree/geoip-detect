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
	$last_update = 0;
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

		case 'lookup':
			if (isset($_POST['ip']))
			{
				$ip = $_POST['ip'];
				$start = microtime(true);
				$ip_lookup_result = geoip_detect2_get_info_from_ip($ip);
				$end = microtime(true);
				$ip_lookup_duration = $end - $start;
			}
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

	$data_file = geoip_detect_get_abs_db_filename();
	$last_update_db = 0;
	$last_update = 0;

	if (file_exists($data_file))
	{
		$last_update = filemtime($data_file);

		$reader = geoip_detect2_get_reader();
		if (method_exists($reader, 'metadata')) {
			$metadata = $reader->metadata();
			$last_update_db = @$metadata->buildEpoch;
		}
	}
	else
	{
		if ($options['source'] == 'auto')
			$message .= __('No GeoIP Database found. Click on the button "Update now" or follow the installation instructions.', 'geoip-detect');
		elseif ($options['source'] == 'manual')
		$message .= __('No GeoIP Database found. Please enter a valid file path below.', 'geoip-detect');
	}




	$next_cron_update = 0;
	if ($options['source'] == 'auto') {
		$next_cron_update = wp_next_scheduled( 'geoipdetectupdate' );
	}

	include_once(GEOIP_PLUGIN_DIR . '/views/lookup.php');
}

function geoip_detect_option_page() {
	include_once(GEOIP_PLUGIN_DIR . '/views/options.php');
}
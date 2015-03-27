<?php
/*
Plugin Name:     GeoIP Detection
Plugin URI:      http://www.yellowtree.de
Description:     Retrieving Geo-Information using the Maxmind GeoIP (Lite) Database.
Author:          Yellow Tree (Benjamin Pick)
Author URI:      http://www.yellowtree.de
Version:         2.3.1
License:         GPLv3 or later
License URI:     http://www.gnu.org/licenses/gpl-3.0.html
Text Domain:     geoip-detect
Domain Path:     /languages
GitHub Plugin URI: https://github.com/yellowtree/wp-geoip-detect
GitHub Branch:   geoipv2
Requires WP:     3.5
Requires PHP:    5.3.1
*/

define('GEOIP_DETECT_VERSION', '2.3.1');

/*
Copyright 2013-2015 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (b.pick@yellowtree.de)

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

define('GEOIP_REQUIRED_PHP_VERSION', '5.3.1');
define('GEOIP_REQUIRED_WP_VERSION', '3.5');

define('GEOIP_PLUGIN_FILE', __FILE__);
define('GEOIP_PLUGIN_DIR', dirname(GEOIP_PLUGIN_FILE));
define('GEOIP_PLUGIN_BASENAME', plugin_basename(GEOIP_PLUGIN_FILE));

// Do PHP & WP Version check
require_once(GEOIP_PLUGIN_DIR . '/check_requirements.php');
if (!geoip_detect_version_check()) 
	return; // Do nothing except emitting the admin notice

require_once(GEOIP_PLUGIN_DIR . '/vendor/autoload.php');
require_once(GEOIP_PLUGIN_DIR . '/init.php');

require_once(GEOIP_PLUGIN_DIR . '/geoip-detect-lib.php');

require_once(GEOIP_PLUGIN_DIR . '/upgrade-plugin.php');
require_once(GEOIP_PLUGIN_DIR . '/api.php');
require_once(GEOIP_PLUGIN_DIR . '/legacy-api.php');
require_once(GEOIP_PLUGIN_DIR . '/filter.php');
require_once(GEOIP_PLUGIN_DIR . '/shortcode.php');

@include_once(GEOIP_PLUGIN_DIR . '/updater.php');
if (!defined('GEOIP_DETECT_UPDATER_INCLUDED'))
	define('GEOIP_DETECT_UPDATER_INCLUDED', false);

require_once('data-sources/registry.php');
require_once('data-sources/abstract.php');
@include_once('data-sources/hostinfo.php');
//@include_once('data-sources/auto.php');
//@include_once('data-sources/manual.php');

// You can define these constants if you like.

//define('GEOIP_DETECT_IP_CACHE_TIME', 2 * HOUR_IN_SECONDS);


// ------------- Admin GUI --------------------

function geoip_detect_plugin_page()
{
	if (function_exists('geoip_detect_set_cron_schedule'))
		geoip_detect_set_cron_schedule();
	
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
				$ip_lookup_result = geoip_detect2_get_info_from_ip($ip);
			}
			break;

		case 'options':
			update_option('geoip-detect-ui-has-chosen-source', true);
			
			foreach ($option_names as $opt_name) {
				if (in_array($opt_name, $numeric_options))
					$opt_value = isset($_POST['options'][$opt_name]) ? (int) $_POST['options'][$opt_name] : 0;
				else
					$opt_value = isset($_POST['options'][$opt_name]) ? $_POST['options'][$opt_name] : '';
				update_option('geoip-detect-' . $opt_name, $opt_value);
			}
			
			// Validate manual file name
			if (!empty($_POST['options']['manual_file'])) {
				$validated_filename = geoip_detect_validate_filename($_POST['options']['manual_file']);
				update_option('geoip-detect-manual_file_validated', $validated_filename);
				
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
	
	include_once(GEOIP_PLUGIN_DIR . '/views/plugin_page.php');	
}

function geoip_detect_menu() {
	require_once ABSPATH . '/wp-admin/admin.php';
	add_submenu_page('tools.php', __('GeoIP Detection', 'geoip-detect'), __('GeoIP Detection', 'geoip-detect'), 'activate_plugins', __FILE__, 'geoip_detect_plugin_page');
}
add_action('admin_menu', 'geoip_detect_menu');

function geoip_detect_add_settings_link( $links ) {
	$settings_link = '<a href="tools.php?page=' . GEOIP_PLUGIN_BASENAME . '">' . __('Plugin page', 'geoip-detect') . '</a>';
	array_push( $links, $settings_link );
	return $links;
}
add_filter( "plugin_action_links_" . GEOIP_PLUGIN_BASENAME, 'geoip_detect_add_settings_link' );



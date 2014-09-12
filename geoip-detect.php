<?php
/*
Plugin Name: GeoIP Detection
Plugin URI: http://www.yellowtree.de
Description: Retrieving Geo-Information using the Maxmind GeoIP (Lite) Database.
Author: YellowTree (Benjamin Pick)
Author URI: http://www.yellowtree.de
Version: 1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: geoip-detect
Domain Path: /languages
*/
/*
Copyright 2013 YellowTree, Siegen, Germany
Author: Benjamin Pick (b.pick@yellowtree.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('geoiprecord') && !class_exists('geoiprecord')) {
	require_once(dirname(__FILE__) . '/vendor/geoip/geoip/src/geoipcity.inc');
}

require_once(dirname(__FILE__) . '/api.php');
require_once(dirname(__FILE__) . '/filter.php');

require_once(dirname(__FILE__) . '/updater.php');

require_once(dirname(__FILE__) . '/shortcode.php');


$custom_filename = get_option('geoip-detect-data_filename');
if(!empty($custom_filename)){
	define('GEOIP_DETECT_DATA_FILENAME', $custom_filename);
} else {
	define('GEOIP_DETECT_DATA_FILENAME', 'GeoLiteCity.dat');
}


function geoip_detect_get_abs_db_filename()
{
	$data_filename = dirname(__FILE__) . '/' . GEOIP_DETECT_DATA_FILENAME;
	if (!file_exists($data_filename))
		$data_filename = '';
	
	$data_filename = apply_filters('geoip_detect_get_abs_db_filename', $data_filename);
	
	if (!$data_filename && (WP_DEBUG || defined('WP_TESTS_TITLE')))
		trigger_error(__('No GeoIP Database file found. Please refer to the installation instructions in readme.txt.', 'geoip-detect'), E_USER_NOTICE);

	return $data_filename;
}



// ------------- Admin GUI --------------------

function geoip_detect_plugin_page()
{
	geoip_detect_set_cron_schedule();
	
	$ip_lookup_result = false;
	$last_update = 0;
	$message = '';
	
	switch(@$_POST['action'])
	{
		case 'update':
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
				$ip_lookup_result = geoip_detect_get_info_from_ip($ip);
			}
			break;

		case 'options':
			$opt_value = isset($_POST['options']['set_css_country']) ? (int) $_POST['options']['set_css_country'] : 0;
			update_option('geoip-detect-set_css_country', $opt_value);
			$opt_value = isset($_POST['options']['data_filename']) ? $_POST['options']['data_filename'] : 'GeoLiteCity.dat';
			update_option('geoip-detect-data_filename', $opt_value);
			break;
	}
	
	$data_file = geoip_detect_get_abs_db_filename();
	if (file_exists($data_file))
	{
		$last_update = filemtime($data_file);
	}
	else 
	{
		$message .= __('No GeoIP Database found. Click on the button "Update now" or follow the installation instructions.', 'geoip-detect');
		$last_update = 0;
	}
	if(empty($custom_filename)){
		$next_cron_update = wp_next_scheduled( 'geoipdetectupdate' );
	}
	
	$options = array();
	$options['set_css_country'] = (int) get_option('geoip-detect-set_css_country');
	$options['data_filename'] = get_option('geoip-detect-data_filename');

	include_once(dirname(__FILE__) . '/views/plugin_page.php');	
}

function geoip_detect_menu() {
	require_once ABSPATH . '/wp-admin/admin.php';
	add_submenu_page('tools.php', __('GeoIP Detection', 'geoip-detect'), __('GeoIP Detection', 'geoip-detect'), 'activate_plugins', __FILE__, 'geoip_detect_plugin_page');
}
add_action('admin_menu', 'geoip_detect_menu');

function geoip_detect_add_settings_link( $links ) {
	$settings_link = '<a href="tools.php?page=geoip-detect/geoip-detect.php">' . __('Plugin page', 'geoip-detect') . '</a>';
	array_push( $links, $settings_link );
	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'geoip_detect_add_settings_link' );



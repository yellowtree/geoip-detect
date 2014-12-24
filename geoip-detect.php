<?php
/*
Plugin Name: GeoIP Detection
Plugin URI: http://www.yellowtree.de
Description: Retrieving Geo-Information using the Maxmind GeoIP (Lite) Database.
Author: YellowTree (Benjamin Pick)
Author URI: http://www.yellowtree.de
Version: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: geoip-detect
Domain Path: /languages
*/
/*
Copyright 2013-2014 YellowTree, Siegen, Germany
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

define('GEOIP_PLUGIN_FILE', __FILE__);

require_once(dirname(__FILE__) . '/vendor/autoload.php');

require_once(dirname(__FILE__) . '/api.php');
require_once(dirname(__FILE__) . '/legacy-api.php');
require_once(dirname(__FILE__) . '/filter.php');

require_once(dirname(__FILE__) . '/updater.php');

require_once(dirname(__FILE__) . '/shortcode.php');


define('GEOIP_DETECT_DATA_FILENAME', 'GeoLite2-City.mmdb');
define('GEOIP_REQUIRED_PHP_VERSION', '5.3.1');
define('GEOIP_REQUIRED_WP_VERSION', '3.5');


function geoip_detect_defines() {
	if (!defined('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED'))
		define('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED', false);
	if (!defined('GEOIP_DETECT_IP_CACHE_TIME'))
		define('GEOIP_DETECT_IP_CACHE_TIME', 2 * HOUR_IN_SECONDS);
}
add_action('plugins_loaded', 'geoip_detect_defines');

register_activation_hook( __FILE__, 'geoip_detect_version_check' );

function geoip_detect_version_check() {
   global $wp_version;
   
    if ( version_compare( PHP_VERSION, GEOIP_REQUIRED_PHP_VERSION, '<' ) ) {
        $flag = 'PHP';
    	$min = GEOIP_REQUIRED_PHP_VERSION;
    }
    elseif ( version_compare( $wp_version, GEOIP_REQUIRED_WP_VERSION, '<' ) ) {
        $flag = 'WordPress';
   		$min = GEOIP_REQUIRED_WP_VERSION;
    }
    else
        return;
        
    deactivate_plugins( basename( __FILE__ ) );
    wp_die('<p>The <strong>GeoIP Detect</strong> plugin requires '.$flag.'  version '.$min.' or greater.</p><p>You can try to install an 1.x version of this plugin.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
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


function geoip_detect_is_ip($ip) {
	return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false;
}

function geoip_detect_is_ip_in_range($ip, $range_start, $range_end) {
	$long_ip = ip2long($ip);
	if ($long_ip === false) // Not IPv4
		return false;
	if($long_ip >= ip2long($range_start) && $long_ip <= ip2long($range_end))
		return true;
	return false;
}

/**
 * Check if IP is in RFC private IP range
 * (for local development)
 * @param string $ip	IP (IPv4 or IPv6)
 * @return boolean TRUE if private
 */
function geoip_detect_is_public_ip($ip) {
	if (geoip_detect_is_ip_in_range($ip, '127.0.0.0', '127.255.255.255'))
		return false;

	$flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6  // IP can be v4 or v6
		| FILTER_FLAG_NO_PRIV_RANGE // It may not be in the RFC private range
		|  FILTER_FLAG_NO_RES_RANGE; // It may not be in the RFC reserved range
	$is_public = filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
	
	return $is_public;
}



// ------------- Admin GUI --------------------

function geoip_detect_plugin_page()
{
	geoip_detect_set_cron_schedule();
	
	$ip_lookup_result = false;
	$last_update = 0;
	$message = '';
	
	$option_names = array('set_css_country', 'has_reverse_proxy');
	
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
				$ip_lookup_result = geoip_detect2_get_info_from_ip($ip);
			}
			break;

		case 'options':
			foreach ($option_names as $opt_name) {
				$opt_value = isset($_POST['options'][$opt_name]) ? (int) $_POST['options'][$opt_name] : 0;
				update_option('geoip-detect-' . $opt_name, $opt_value);
			}
			
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
	$next_cron_update = wp_next_scheduled( 'geoipdetectupdate' );
	
	$options = array();
	
	foreach ($option_names as $opt_name) {
		$options[$opt_name] = (int) get_option('geoip-detect-'. $opt_name);
	}

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



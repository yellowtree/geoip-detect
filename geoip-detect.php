<?php
/*
Plugin Name:     Geolocation IP Detection
Plugin URI:      http://www.yellowtree.de
Description:     Provides geographic information detected by an IP adress.
Author:          Yellow Tree (Benjamin Pick)
Author URI:      http://www.yellowtree.de
Version:         5.3.0
License:         GPLv3 or later
License URI:     http://www.gnu.org/licenses/gpl-3.0.html
Text Domain:     geoip-detect
Domain Path:     /languages
GitHub Plugin URI: https://github.com/yellowtree/geoip-detect
GitHub Branch:   master
Requires WP:     5.4
Requires PHP:    7.2.5
*/

define('GEOIP_DETECT_VERSION', '5.3.0');

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

define('GEOIP_REQUIRED_PHP_VERSION', '7.2.5');

// In theory, it should still run in 3.5 . But officially supported is only WP 5.4 and above.
define('GEOIP_REQUIRED_WP_VERSION', '3.5');

// We need this requirement because WooCommerce hardcoded Maxmind code instead of using composer. This would result in an PHP fatal error when there is a lookup.
define('GEOIP_REQUIRED_WOOCOMMERCE_VERSION', '3.9.0');

define('GEOIP_PLUGIN_FILE', __FILE__);
define('GEOIP_PLUGIN_DIR', dirname(GEOIP_PLUGIN_FILE));
define('GEOIP_PLUGIN_BASENAME', plugin_basename(GEOIP_PLUGIN_FILE));
define('GEOIP_DETECT_PLUGIN_URI', plugin_dir_url(GEOIP_PLUGIN_FILE));


// Do PHP & WP Version check
require_once(GEOIP_PLUGIN_DIR . '/check_requirements.php');
if (!geoip_detect_version_check()) {
	require_once(GEOIP_PLUGIN_DIR . '/api-stubs.php');
	return; // Do nothing except emitting the admin notice
}
add_action('plugins_loaded', function() {
	if (!geoip_detect_version_check_after_plugins_loaded()) {
		define('GEOIP_DETECT_LOOKUP_DISABLED', true);
	}
});

require_once(GEOIP_PLUGIN_DIR . '/vendor/autoload.php');
require_once(GEOIP_PLUGIN_DIR . '/init.php');

require_once(GEOIP_PLUGIN_DIR . '/check_compatibility.php');

require_once(GEOIP_PLUGIN_DIR . '/geoip-detect-lib.php');
require_once(GEOIP_PLUGIN_DIR . '/lib/geonames/geonames-country-info.php');
require_once(GEOIP_PLUGIN_DIR . '/lib/get-client-ip.php');
require_once(GEOIP_PLUGIN_DIR . '/lib/dynamic-reverse-proxies/abstract.php');
require_once(GEOIP_PLUGIN_DIR . '/lib/logger.php');
require_once(GEOIP_PLUGIN_DIR . '/lib/ccpa.php');

require_once(GEOIP_PLUGIN_DIR . '/upgrade-plugin.php');
require_once(GEOIP_PLUGIN_DIR . '/api.php');
require_once(GEOIP_PLUGIN_DIR . '/legacy-api.php');
require_once(GEOIP_PLUGIN_DIR . '/deprecated.php');
require_once(GEOIP_PLUGIN_DIR . '/filter.php');
require_once(GEOIP_PLUGIN_DIR . '/api_shortcodes.php');
require_once(GEOIP_PLUGIN_DIR . '/ajax.php');

require_once(GEOIP_PLUGIN_DIR . '/data-sources/registry.php');
require_once(GEOIP_PLUGIN_DIR . '/data-sources/abstract.php');

// These data-source files are optional
include_once(GEOIP_PLUGIN_DIR . '/data-sources/hostinfo.php');
include_once(GEOIP_PLUGIN_DIR . '/data-sources/manual.php');
include_once(GEOIP_PLUGIN_DIR . '/data-sources/auto.php');
include_once(GEOIP_PLUGIN_DIR . '/data-sources/precision.php');
include_once(GEOIP_PLUGIN_DIR . '/data-sources/header.php');
include_once(GEOIP_PLUGIN_DIR . '/data-sources/ipstack.php');
include_once(GEOIP_PLUGIN_DIR . '/data-sources/fastah.php');

// You can define these constants in your theme/plugin if you like.

/**
 * Set to TRUE if the plugin should never auto-update the Maxmind City Lite database.
 */
//define('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED', true);

/**
 * How long the external IP of the server is cached.
 * This is probably only used in dev cases, so per default relatively low.
 */
//define('GEOIP_DETECT_IP_CACHE_TIME', 2 * HOUR_IN_SECONDS);

/**
 * How long the external IP of the server is cached, if no IP was found.
 * Default: the same as GEOIP_DETECT_IP_CACHE_TIME
 */
//define('GEOIP_DETECT_IP_EMPTY_CACHE_TIME', 2 * HOUR_IN_SECONDS);

/**
 * How long the data of the IP is cached. This applies to the Web-APIs (Maxmind Precision, HostIP.info, ipstack, ...)
 * Only successful lookups will be cached.
 */
//define('GEOIP_DETECT_READER_CACHE_TIME', 7 * DAY_IN_SECONDS);

/**
 * AJAX mode: 
 * If you want to reduce the filesize of the used JS file, consider using a JS file variant:
 * 
 * - full: All features (default)
 * - base: Only get_info, no shortcodes, no body_class, no overrides ...
 * - minimal: base, but without the Record class (get_info is returning raw json data instead)
 * 
 */
//define('GEOIP_DETECT_JS_VARIANT', 'full');

require_once(GEOIP_PLUGIN_DIR . '/admin-ui.php');

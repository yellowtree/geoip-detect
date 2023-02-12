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
 * This function is executed each time a new version is installed.
 * Note that downgrading versions is not supported. (Shouldn't lead to problems, though, normally.)
 * 
 * These updates are executed ONCE if the old version is smaller than ...
 * 
 * @param string $old_version
 */
function geoip_detect_do_upgrade($old_version) {
	
	// v2.3.0 Always set default source to hostinfo
	if (version_compare('2.3.0', $old_version, '>')) {
		if (!get_option('geoip-detect-source'))
			update_option('geoip-detect-source', 'hostinfo');
	}
	
	// v2.5.0 Set "DONOTCACHEPAGE"
	if (version_compare('2.5.0', $old_version, '>')) {
		if (get_option('geoip-detect-disable_pagecache') === false) {
			if (get_option('geoip-detect-set_css_country'))
				update_option('geoip-detect-disable_pagecache', '0');
			else 
				update_option('geoip-detect-disable_pagecache', '1');
		}
	}
	
	// v.2.5.1 - Upgrade to 2.5.0 contained a bug. Make sure info is consistent again.
	if (version_compare('2.5.0', $old_version, '=')) {
		if (get_option('geoip-detect-source') === '1') {
			update_option('geoip-detect-source', 'hostinfo');
		}
	}

	// v 2.8.2 Fix auto update hook (re-schedule if necessary)
	if (version_compare('2.8.2', $old_version, '>')) {
		if (wp_next_scheduled( 'geoipdetectupdate' ) === false) {
			$source = new \YellowTree\GeoipDetect\DataSources\Auto\AutoDataSource;
			$source->deactivate();
			if (get_option('geoip-detect-source') == 'auto') {
				$source->activate();
			}
		}
	}

	// v2.11.1 Create beta option in database
	if (version_compare('2.11.1', $old_version, '>')) {
		delete_option('geoip-detect-ajax_beta');
	}

	// v3.3.0 AJAX css body classes new option - set default value to avoid bc break
	if (version_compare('3.3.0', $old_version, '>')) {
		$value = get_option('geoip-detect-set_css_country') && get_option('geoip-detect-ajax_enabled');
		if ($value) {
			update_option('geoip-detect-ajax_set_css_country', '1');
			update_option('geoip-detect-ajax_enqueue_js', '1');
			update_option('geoip-detect-set_css_country', '0');
		}
	}

	// v 4.0.0 Fix CCPA auto update hook (re-schedule if necessary)
	if (version_compare('4.0.0', $old_version, '>') && class_exists('\\YellowTree\\GeoipDetect\\Lib\\CcpaBlacklistCron')) {
		if (wp_next_scheduled('geoipdetectccpaupdate') === false) {
			$ccpaCronScheduler = new \YellowTree\GeoipDetect\Lib\CcpaBlacklistCron;
			$ccpaCronScheduler->schedule();
		}
	}

}

/**
 * Register a routine to be called when a plugin has been updated
 *
 * It works by comparing the current version with the version previously stored in the database.
 *
 * @since 2.3.0
 *
 * @param string $file A reference to the main plugin file
 * @param callback $callback The function to run when the hook is called.
 * @param string $version The version to which the plugin is updating.
 */
function geoip_detect_maybe_upgrade_version( ) {
	if (!is_admin())
		return;

	$version = GEOIP_DETECT_VERSION;
	$current_vers = get_option( 'geoip-detect-plugin_version' );
	
	if ( version_compare( $version, $current_vers, '>' ) ) {
		
		geoip_detect_do_upgrade($current_vers);
		
		$current_vers = $version;
	}

	update_option('geoip-detect-plugin_version', $current_vers );
}
add_action('plugins_loaded', 'geoip_detect_maybe_upgrade_version');

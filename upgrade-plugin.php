<?php
/**
 * This function is executed each time a new version is installed.
 * Note that downgrading versions is not supported. (Shouldn't lead to problems, though, normally.)
 * 
 * @param string $old_version
 */
function geoip_detect_do_upgrade($old_version) {
	
	// v2.3.0 Set default source to hostinfo
	if (version_compare('2.3.0', $old_version, '>')) {
		if (!get_option('geoip-detect-source'))
			update_option('geoip-detect-source', 'hostinfo');
	}
	
	// v2.5.0 Set "DONOTCACHEPAGE"
	if (version_compare('2.5.0', $old_version, '>')) {
		if (!get_option('geoip-detect-disable_pagecache')) {
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
	if ( !is_admin() || !is_user_logged_in())
		return;

	$version = GEOIP_DETECT_VERSION;
	$current_vers = get_option( 'geoip-detect-plugin_version' );
	
	if ( version_compare( $version, $current_vers, '>' ) ) {
		
		geoip_detect_do_upgrade($current_vers);
		
		$current_vers = $version;
	}

	update_option( 'geoip-detect-plugin_version', $current_vers );
}
add_action('plugins_loaded', 'geoip_detect_maybe_upgrade_version');

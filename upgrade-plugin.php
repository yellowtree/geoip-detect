<?php
/**
 * This function is executed each time a new version is installed.
 * Note that downgrading versions is not supported.
 * 
 * @param string $old_version
 */
function geoip_detect_do_upgrade($old_version) {
	
	// v2.3.0 Set default source to hostinfo
	if (version_compare('2.3.0', $old_version, '>')) {
		if (!get_option('geoip-detect-source'))
			update_option('geoip-detect-source', 'hostinfo');
	}
}

/**
 * Register a routine to be called when a plugin has been updated
 *
 * It works by comparing the current version with the version previously stored in the database.
 *
 * @since 3.1
 *
 * @param string $file A reference to the main plugin file
 * @param callback $callback The function to run when the hook is called.
 * @param string $version The version to which the plugin is updating.
 */
function geoip_detect_maybe_upgrade_version( ) {
	if ( !is_admin() || !is_logged_in())
		return;

	$version = GEOIP_DETECT_VERSION;
	$plugin = GEOIP_PLUGIN_FILE;
	
	if ( is_plugin_active_for_network( $plugin ) ) {
		$current_vers = get_site_option( 'geoip_detect_plugin_version' );
		$network = true;
	} elseif ( is_plugin_active( $plugin ) ) {
		$current_vers = get_option( 'geoip_detect_plugin_version' );
		$network = false;
	} else {
		return false;
	}

	if ( version_compare( $version, $current_vers, '>' ) ) {
		
		geoip_detect_do_upgrade($current_vers);
		
		$current_vers = $version;
	}

	if ( $network ) {
		update_site_option( 'geoip_detect_plugin_version', $current_vers );
	} else {
		update_option( 'geoip_detect_plugin_version', $current_vers );
	}
}
add_action('plugins_loaded', 'geoip_detect_maybe_upgrade_version');
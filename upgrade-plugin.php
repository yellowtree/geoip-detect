<?php

// TODO: use update_option() to detect if plugin was updated.

$upgrade = array(
	array('version' => '2.0.1', 'hook' => 'geoip_detect_on_upgrade_to_2_0_1'),
);

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
function register_update_hook( $file, $callback, $version ) {
	if ( !is_admin() )
		return;

	$plugin = plugin_basename( $file );

	if ( is_plugin_active_for_network( $plugin ) ) {
		$current_vers = get_site_option( 'active_plugin_versions', array() );
		$network = true;
	} elseif ( is_plugin_active( $plugin ) ) {
		$current_vers = get_option( 'active_plugin_versions', array() );
		$network = false;
	} else {
		return false;
	}

	if ( version_compare( $version, $current_vers[ $plugin ], '>' ) ) {
		call_user_func( $callback, $current_vers[ $plugin ], $network );

		$current_vers[ $plugin ] = $version;
	}

	if ( $network ) {
		update_site_option( 'active_plugin_versions', $current_vers );
	} else {
		update_option( 'active_plugin_versions', $current_vers );
	}
}
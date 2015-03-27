<?php

define('GEOIP_DETECT_UPDATER_INCLUDED', true);

/**
 * @deprecated
 */
function geoip_detect_update() {
	// TODO fallback?
}

// ------------------ CRON Hooks --------------------------

function geoip_detect_update_cron($immediately_after_activation = false) {
	/**
	 * Filter:
	 * Cron has fired.
	 * Find out if file should be updated now.
	 * 
	 * @param $do_it False if deactivated by define
	 * @param $immediately_after_activation True if this is fired because the plugin was recently activated
	 */
	$do_it = apply_filters('geoip_detect_cron_do_update', !GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED, $immediately_after_activation);	
	
	// Needed for WP File functions. Cron doesn't work without it.
	require_once(ABSPATH.'/wp-admin/includes/file.php');

	if ($do_it)
		geoip_detect_update();
		
	geoip_detect_schedule_next_cron_run();
}
add_action('geoipdetectupdate', 'geoip_detect_update_cron', 10, 1);

function geoip_detect_set_cron_schedule($now = false)
{
	$next = wp_next_scheduled( 'geoipdetectupdate' );
	if ( !$next ) {
		geoip_detect_schedule_next_cron_run();
	}

	if ($now)
		wp_schedule_single_event(time(), 'geoipdetectupdate', array(true));
}

function geoip_detect_schedule_next_cron_run() {
	// The Lite databases are updated on the first tuesday of each month. Maybe not at midnight, so we schedule it for the night afterwards.
	$next = strtotime('first tuesday of next month + 1 day');
	wp_schedule_single_event($next, 'geoipdetectupdate');
}


function geoip_detect_deactivate()
{
	wp_clear_scheduled_hook('geoipdetectupdate');
	wp_clear_scheduled_hook('geoipdetectupdate', array(true));
}
register_deactivation_hook(GEOIP_PLUGIN_FILE, 'geoip_detect_deactivate');

<?php
// You can use this in your theme/plugin to deactivate the auto-update
//define('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED', true);

define('GEOIP_DETECT_UPDATER_INCLUDED', true);

// Needed for WP File functions. Cron doesn't work without it.
require_once(ABSPATH.'/wp-admin/includes/file.php');

function geoip_detect_update()
{
	// TODO: Currently cron is scheduled but not executed. Remove scheduling.
	if (get_option('geoip-detect-source') != 'auto')
		return;

	$download_url = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';
	$download_url = apply_filters('geoip_detect2_download_url', $download_url);

	$outFile = geoip_detect_get_database_upload_filename();

	// Download
	$tmpFile = download_url($download_url);
	if (is_wp_error($tmpFile))
		return $tmpFile->get_error_message();

	// Ungzip File
	$zh = gzopen($tmpFile, 'r');
	$h = fopen($outFile, 'w');

	if (!$zh)
		return __('Downloaded file could not be opened for reading.', 'geoip-detect');
	if (!$h)
		return sprintf(__('Database could not be written (%s).', 'geoip-detect'), $outFile);

	while ( ($string = gzread($zh, 4096)) != false )
		fwrite($h, $string, strlen($string));

	gzclose($zh);
	fclose($h);

	unlink($tmpFile);

	return true;
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

<?php
// You can use this in your theme/plugin to deactivate the auto-update
//define('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED', true);



// Needed for WP File functions. Cron doesn't work without it.
require_once(ABSPATH.'/wp-admin/includes/file.php');

function geoip_detect_get_database_upload_filename()
{
	$upload_dir = wp_upload_dir();
	$dir = $upload_dir['basedir'];

	$filename = $dir . '/' . GEOIP_DETECT_DATA_FILENAME;
	return $filename;
}

function geoip_detect_get_database_upload_filename_filter($filename_before)
{
	$filename = geoip_detect_get_database_upload_filename();
	if (file_exists($filename))
		return $filename;
	
	return $filename_before;
}

add_filter('geoip_detect_get_abs_db_filename', 'geoip_detect_get_database_upload_filename_filter');

function geoip_detect_update()
{
	$download_url = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';

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

	//unlink($tmpFile);

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
}

add_action('geoipdetectupdate', 'geoip_detect_update_cron', 10, 1);


add_filter( 'cron_schedules', 'geoip_detect_cron_add_weekly' );
function geoip_detect_cron_add_weekly( $schedules ) {
	// Adds once weekly to the existing schedules.
	if (!isset($schedules['weekly']))
	{
		$schedules['weekly'] = array(
				'interval' => 604800,
				'display' => __( 'Once Weekly' )
		);
	}
	return $schedules;
}

function geoip_detect_set_cron_schedule($now = false)
{
	// TODO GeoLite2 databases are updated on the first Tuesday of each month.
	if ( !wp_next_scheduled( 'geoipdetectupdate' ) ) {
		wp_schedule_event(time() + WEEK_IN_SECONDS, 'weekly', 'geoipdetectupdate');
	}

	if ($now)
		wp_schedule_single_event(time(), 'geoipdetectupdate', array(true));
}

function geoip_detect_activate()
{
	geoip_detect_set_cron_schedule(true);
}
register_activation_hook(GEOIP_PLUGIN_FILE, 'geoip_detect_activate');


function geoip_detect_deactivate()
{
	wp_clear_scheduled_hook('geoipdetectupdate');
	wp_clear_scheduled_hook('geoipdetectupdate', array(true));
}
register_deactivation_hook(GEOIP_PLUGIN_FILE, 'geoip_detect_deactivate');

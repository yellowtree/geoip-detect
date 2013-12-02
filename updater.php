<?php

//define('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED', true);

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
	$download_url = 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz';

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

if (!defined('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED'))
	add_action('geoipdetectupdate', 'geoip_detect_update');


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

function geoip_detect_activate()
{
	if ( !wp_next_scheduled( 'geoipdetectupdate' ) )
		wp_schedule_event(time() + 7*24*60*60, 'weekly', 'geoipdetectupdate');
}
register_activation_hook(__FILE__, 'geoip_detect_activate');


function geoip_detect_deactivate()
{
	wp_clear_scheduled_hook('geoipdetectupdate');
}
register_deactivation_hook(__FILE__, 'geoip_detect_deactivate');
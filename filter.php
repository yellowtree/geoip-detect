<?php

/*
 * Filter: geoip_detect_record_information
 * After loading the information from the GeoIP-Database, you can add or remove information from it.
 * @param geoiprecord $record Information found.
 * @return geoiprecord 
 * 
 * The filters below are optional. You can remove them by using a remove_filter call somewhere, e.g.
 * 
 * remove_filter('geoip_detect_record_information', 'geoip_detect_fix_corrupt_info', 101);
 */

function geoip_detect_add_verbose_information_to_record($record)
{
	require_once(dirname(__FILE__) . '/vendor/geoip/geoip/geoipregionvars.php');

	if ($record)
	{
		global $GEOIP_REGION_NAME;
		$record->region_name = $GEOIP_REGION_NAME[$record->country_code][$record->region];
	}

	return $record;
}
add_filter('geoip_detect_record_information', 'geoip_detect_add_verbose_information_to_record');

function geoip_detect_add_timezone_information_to_record($record)
{
	require_once(dirname(__FILE__) . '/vendor/geoip/geoip/timezone/timezone.php');

	if ($record)
	{
		global $GEOIP_REGION_NAME;
		$record->timezone =  get_time_zone($record->country_code, $record->region);
	}

	return $record;
}
add_filter('geoip_detect_record_information', 'geoip_detect_add_timezone_information_to_record');

function geoip_detect_fix_corrupt_info($record)
{
	if ($record && $record->latitude < -90 || $record && $record->longitude < -90)
	{
		// File corrupted? Use empty defaults
		$record->latitude = 0;
		$record->longitude = 0;
		$record->city = 'Unknown';
	}
}
add_filter('geoip_detect_record_information', 'geoip_detect_fix_corrupt_info', 101);
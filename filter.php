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
	static $GEOIP_REGION_NAME_COPY;
	if (is_null($GEOIP_REGION_NAME_COPY))
	{
		require(dirname(__FILE__) . '/vendor/geoip/geoip/geoipregionvars.php');
		$GEOIP_REGION_NAME_COPY = $GEOIP_REGION_NAME;
	}
	if ($record)
	{
		$record->region_name = $GEOIP_REGION_NAME_COPY[$record->country_code][$record->region];
	}

	return $record;
}
add_filter('geoip_detect_record_information', 'geoip_detect_add_verbose_information_to_record');

require_once(dirname(__FILE__) . '/vendor/geoip/geoip/timezone/timezone.php');
function geoip_detect_add_timezone_information_to_record($record)
{
	if ($record)
	{
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
	return $record;
}
add_filter('geoip_detect_record_information', 'geoip_detect_fix_corrupt_info', 101);

/**
 * If no information was found, use the external IP of the server and try again.
 * This is necessary to allow local development servers to return sensical data.
 * 
 * @param geoiprecord $record Information found.
 * @return geoiprecord	Hopefolly more accurate information. 
 */ 
function geoip_detect_add_external_ip($record)
{
	static $avoid_recursion = false; // Flag in order to retry only once
	if ($avoid_recursion)
		return $record; // This is the retry with the external adress, so don't do anything
	
	if (!is_object($record))
	{
		$external_ip = geoip_detect_get_external_ip_adress();
		
		$avoid_recursion = true;
		$record = geoip_detect_get_info_from_ip($external_ip);
		$avoid_recursion = false;
	}

	return $record;
}
add_filter('geoip_detect_record_information', 'geoip_detect_add_external_ip');

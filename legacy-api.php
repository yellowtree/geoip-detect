<?php

if (!class_exists('geoiprecord')) {
	class geoiprecord
	{
		public $country_code;
		public $country_code3;
		public $country_name;
		public $region;
		public $city;
		public $postal_code;
		public $latitude;
		public $longitude;
		public $area_code;
		public $dma_code; # metro and dma code are the same. use metro_code
		public $metro_code;
		public $continent_code;
	}
}

/**
 * Get Geo-Information for a specific IP
 * @param string 		$ip IP-Adress (currently only IPv4)
 * @return geoiprecord	GeoInformation. (0 or NULL: no infos found.)
 * @deprecated since v2.0
 */
function geoip_detect_get_info_from_ip($ip)
{
	$ret = geoip_detect2_get_info_from_ip($ip);
	
	$record = new geoiprecord();
	// TODO: What happens if any value is not defined? Muss ich hier mit isset arbeiten?
	$record->country_code = 	$ret->country->isoCode;
	$record->country_code3 = 	$ret->country->isoCode; /* TODO: Mapping table? */
	$record->country_name = 	$ret->country->name;
	$record->region = 			$ret->mostSpecificSubdivision->isoCode;
	$record->region_name = 		$ret->mostSpecificSubdivision->name;
	$record->city = 			$ret->city->name;
	$record->postal_code = 		$ret->postal->code;
	$record->latitude = 		$ret->location->latitude;
	$record->longitude = 		$ret->location->longitude;
	$record->continent_code = 	$ret->continent->code;
	$record->metro_code = 		$ret->location->metroCode;
	$record->timezone = 		$ret->location->timeZone;
	
	
	/**
	 * Filter: geoip_detect_record_information
	 * After loading the information from the GeoIP-Database, you can add or remove information from it.
	 * @param geoiprecord 	$record	Information found.
	 * @param string		$ip		IP that was looked up
	 * @return geoiprecord
	 * @deprecated since v2.0
	 */
	$record = apply_filters('geoip_detect_record_information', $record, $ip);
}

/**
 * Get Geo-Information for the current IP
 * @param string 		$ip (IPv4)
 * @return geoiprecord	GeoInformation. (0 / NULL: no infos found.)
 * @deprecated since v2.0
 */
function geoip_detect_get_info_from_current_ip()
{
	return geoip_detect_get_info_from_ip('me');
}

/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 * 
 * @return string The detected IP Adress. If none is found, '0.0.0.0' is returned instead.
 */
function geoip_detect_get_external_ip_adress()
{
	return geoip_detect2_get_external_ip_adress();
}
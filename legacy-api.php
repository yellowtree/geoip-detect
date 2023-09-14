<?php
/*
Copyright 2013-2023 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (wp-geoip-detect| |posteo.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

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
		public $dma_code;
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
	if ($ret == null || !$ret->country->name) // Better way to detect "empty?"
		$ret = geoip_detect2_get_info_from_ip('me');

	$record = null;
	
	if (is_object($ret)) {	
		$record = new geoiprecord();

		$record->country_code = 	$ret->country->isoCode;
		$record->country_code3 = 	$ret->extra->countryIsoCode3;
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
	}
	
	/**
	 * Filter: geoip_detect_record_information
	 * After loading the information from the Geolocation database, you can add or remove information from it.
	 * @param geoiprecord 	$record	Information found.
	 * @param string		$ip		IP that was looked up
	 * @return geoiprecord
	 * @deprecated since v2.0
	 */
	$record = apply_filters('geoip_detect_record_information', $record, $ip);
	
	return $record;
}

/**
 * Get Geo-Information for the current IP
 * @param string 		$ip (IPv4)
 * @return geoiprecord	GeoInformation. (0 / NULL: no infos found.)
 * @deprecated since v2.0
 */
function geoip_detect_get_info_from_current_ip()
{
	return geoip_detect_get_info_from_ip(geoip_detect2_get_client_ip());
}

/**
* Get client IP (even if it is behind a reverse proxy)
* @deprecated since v2.0
*/
function geoip_detect_get_client_ip() {
	return geoip_detect2_get_client_ip();
}

/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 * 
 * @deprecated since v2.0
 * @return string The detected IP Adress. If none is found, '0.0.0.0' is returned instead.
 */
function geoip_detect_get_external_ip_adress()
{
	return geoip_detect2_get_external_ip_adress();
}
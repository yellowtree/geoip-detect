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
		$mapping = _geoip_detect_get_country_code_mapping();
	
		$record = new geoiprecord();

		$record->country_code = 	$ret->country->isoCode;
		$record->country_code3 = 	$mapping[$record->country_code];
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

function _geoip_detect_get_country_code_mapping() {
	return array (
	  '' => '',
	  'A1' => 'A1',
	  'A2' => 'A2',
	  'AD' => 'AND',
	  'AE' => 'ARE',
	  'AF' => 'AFG',
	  'AG' => 'ATG',
	  'AI' => 'AIA',
	  'AL' => 'ALB',
	  'AM' => 'ARM',
	  'AO' => 'AGO',
	  'AP' => 'AP',
	  'AQ' => 'ATA',
	  'AR' => 'ARG',
	  'AS' => 'ASM',
	  'AT' => 'AUT',
	  'AU' => 'AUS',
	  'AW' => 'ABW',
	  'AX' => 'ALA',
	  'AZ' => 'AZE',
	  'BA' => 'BIH',
	  'BB' => 'BRB',
	  'BD' => 'BGD',
	  'BE' => 'BEL',
	  'BF' => 'BFA',
	  'BG' => 'BGR',
	  'BH' => 'BHR',
	  'BI' => 'BDI',
	  'BJ' => 'BEN',
	  'BL' => 'BLM',
	  'BM' => 'BMU',
	  'BN' => 'BRN',
	  'BO' => 'BOL',
	  'BQ' => 'BES',
	  'BR' => 'BRA',
	  'BS' => 'BHS',
	  'BT' => 'BTN',
	  'BV' => 'BVT',
	  'BW' => 'BWA',
	  'BY' => 'BLR',
	  'BZ' => 'BLZ',
	  'CA' => 'CAN',
	  'CC' => 'CCK',
	  'CD' => 'COD',
	  'CF' => 'CAF',
	  'CG' => 'COG',
	  'CH' => 'CHE',
	  'CI' => 'CIV',
	  'CK' => 'COK',
	  'CL' => 'CHL',
	  'CM' => 'CMR',
	  'CN' => 'CHN',
	  'CO' => 'COL',
	  'CR' => 'CRI',
	  'CU' => 'CUB',
	  'CV' => 'CPV',
	  'CW' => 'CUW',
	  'CX' => 'CXR',
	  'CY' => 'CYP',
	  'CZ' => 'CZE',
	  'DE' => 'DEU',
	  'DJ' => 'DJI',
	  'DK' => 'DNK',
	  'DM' => 'DMA',
	  'DO' => 'DOM',
	  'DZ' => 'DZA',
	  'EC' => 'ECU',
	  'EE' => 'EST',
	  'EG' => 'EGY',
	  'EH' => 'ESH',
	  'ER' => 'ERI',
	  'ES' => 'ESP',
	  'ET' => 'ETH',
	  'EU' => 'EU',
	  'FI' => 'FIN',
	  'FJ' => 'FJI',
	  'FK' => 'FLK',
	  'FM' => 'FSM',
	  'FO' => 'FRO',
	  'FR' => 'FRA',
	  'GA' => 'GAB',
	  'GB' => 'GBR',
	  'GD' => 'GRD',
	  'GE' => 'GEO',
	  'GF' => 'GUF',
	  'GG' => 'GGY',
	  'GH' => 'GHA',
	  'GI' => 'GIB',
	  'GL' => 'GRL',
	  'GM' => 'GMB',
	  'GN' => 'GIN',
	  'GP' => 'GLP',
	  'GQ' => 'GNQ',
	  'GR' => 'GRC',
	  'GS' => 'SGS',
	  'GT' => 'GTM',
	  'GU' => 'GUM',
	  'GW' => 'GNB',
	  'GY' => 'GUY',
	  'HK' => 'HKG',
	  'HM' => 'HMD',
	  'HN' => 'HND',
	  'HR' => 'HRV',
	  'HT' => 'HTI',
	  'HU' => 'HUN',
	  'ID' => 'IDN',
	  'IE' => 'IRL',
	  'IL' => 'ISR',
	  'IM' => 'IMN',
	  'IN' => 'IND',
	  'IO' => 'IOT',
	  'IQ' => 'IRQ',
	  'IR' => 'IRN',
	  'IS' => 'ISL',
	  'IT' => 'ITA',
	  'JE' => 'JEY',
	  'JM' => 'JAM',
	  'JO' => 'JOR',
	  'JP' => 'JPN',
	  'KE' => 'KEN',
	  'KG' => 'KGZ',
	  'KH' => 'KHM',
	  'KI' => 'KIR',
	  'KM' => 'COM',
	  'KN' => 'KNA',
	  'KP' => 'PRK',
	  'KR' => 'KOR',
	  'KW' => 'KWT',
	  'KY' => 'CYM',
	  'KZ' => 'KAZ',
	  'LA' => 'LAO',
	  'LB' => 'LBN',
	  'LC' => 'LCA',
	  'LI' => 'LIE',
	  'LK' => 'LKA',
	  'LR' => 'LBR',
	  'LS' => 'LSO',
	  'LT' => 'LTU',
	  'LU' => 'LUX',
	  'LV' => 'LVA',
	  'LY' => 'LBY',
	  'MA' => 'MAR',
	  'MC' => 'MCO',
	  'MD' => 'MDA',
	  'ME' => 'MNE',
	  'MF' => 'MAF',
	  'MG' => 'MDG',
	  'MH' => 'MHL',
	  'MK' => 'MKD',
	  'ML' => 'MLI',
	  'MM' => 'MMR',
	  'MN' => 'MNG',
	  'MO' => 'MAC',
	  'MP' => 'MNP',
	  'MQ' => 'MTQ',
	  'MR' => 'MRT',
	  'MS' => 'MSR',
	  'MT' => 'MLT',
	  'MU' => 'MUS',
	  'MV' => 'MDV',
	  'MW' => 'MWI',
	  'MX' => 'MEX',
	  'MY' => 'MYS',
	  'MZ' => 'MOZ',
	  'NA' => 'NAM',
	  'NC' => 'NCL',
	  'NE' => 'NER',
	  'NF' => 'NFK',
	  'NG' => 'NGA',
	  'NI' => 'NIC',
	  'NL' => 'NLD',
	  'NO' => 'NOR',
	  'NP' => 'NPL',
	  'NR' => 'NRU',
	  'NU' => 'NIU',
	  'NZ' => 'NZL',
	  'O1' => 'O1',
	  'OM' => 'OMN',
	  'PA' => 'PAN',
	  'PE' => 'PER',
	  'PF' => 'PYF',
	  'PG' => 'PNG',
	  'PH' => 'PHL',
	  'PK' => 'PAK',
	  'PL' => 'POL',
	  'RO' => 'ROU',
	  'RS' => 'SRB',
	  'RU' => 'RUS',
	  'RW' => 'RWA',
	  'SA' => 'SAU',
	  'SB' => 'SLB',
	  'SC' => 'SYC',
	  'SD' => 'SDN',
	  'SE' => 'SWE',
	  'SG' => 'SGP',
	  'SH' => 'SHN',
	  'SI' => 'SVN',
	  'SJ' => 'SJM',
	  'SK' => 'SVK',
	  'SL' => 'SLE',
	  'SM' => 'SMR',
	  'SN' => 'SEN',
	  'SO' => 'SOM',
	  'SR' => 'SUR',
	  'SS' => 'SSD',
	  'ST' => 'STP',
	  'SV' => 'SLV',
	  'SX' => 'SXM',
	  'SY' => 'SYR',
	  'SZ' => 'SWZ',
	  'TC' => 'TCA',
	  'TD' => 'TCD',
	  'TF' => 'ATF',
	  'TG' => 'TGO',
	  'TH' => 'THA',
	  'TJ' => 'TJK',
	  'TK' => 'TKL',
	  'TL' => 'TLS',
	  'TM' => 'TKM',
	  'TN' => 'TUN',
	  'TO' => 'TON',
	  'TR' => 'TUR',
	  'TT' => 'TTO',
	  'TV' => 'TUV',
	  'TW' => 'TWN',
	  'TZ' => 'TZA',
	  'UA' => 'UKR',
	  'UG' => 'UGA',
	  'UM' => 'UMI',
	  'US' => 'USA',
	  'UY' => 'URY',
	  'UZ' => 'UZB',
	  'VA' => 'VAT',
	  'VC' => 'VCT',
	  'VE' => 'VEN',
	  'VG' => 'VGB',
	  'VI' => 'VIR',
	  'VN' => 'VNM',
	  'VU' => 'VUT',
	  'WF' => 'WLF',
	  'WS' => 'WSM',
	  'YE' => 'YEM',
	  'YT' => 'MYT',
	  'ZA' => 'ZAF',
	  'ZM' => 'ZMB',
	  'ZW' => 'ZWE',
	);
}

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

// This file contains function that are necessary for the plugin, but not deemed as API.
// Their name / parameter may change without warning.

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Take the parameter options and add the default values.
 * @param array $options
 * return $options
 */

function _geoip_detect2_process_options($options) {

	// For backwards compat 2.4.0-2.5.0
	if (is_bool($options)) {
		_doing_it_wrong('Geolocation IP Detection Plugin: geoip_detect2_get_info_from_ip()', '$skipCache has been renamed to $options. Instead of TRUE, now use "[\'skipCache\' => TRUE]".', '2.5.0');
		$value = $options;
		$options = [];
		$options['skipCache'] = $value;
	}

	// Check if source exists
	if (isset($options['source'])) {
		$registry = DataSourceRegistry::getInstance();
		if (!$registry->sourceExists($options['source']))
			unset($options['source']);
	}

	/**
	 * Filter: geoip_detect2_options
	 * You can programmatically change the defaults etc.
	 *
	 * @param array $options The options array
	 */
	$options = apply_filters('geoip_detect2_options', $options);


	$defaultOptions = array(
		'skipCache' => false,
		'source' => get_option('geoip-detect-source', DataSourceRegistry::DEFAULT_SOURCE),
	);
	$options = $options + $defaultOptions;

	return $options;
}

/*
 * Get the Maxmind Reader
 * (Use this if you want to use other methods than "city" or otherwise customize behavior.)
 *
 * @param array(string)				List of locale codes to use in name property
 * from most preferred to least preferred. (Default: Site language, en)
 * @param boolean					If locale filter should be skipped (default: No)
 * @return GeoIp2\Database\Reader 	The reader, ready to do its work. Don't forget to `close()` it afterwards. NULL if file not found (or other problems).
 * NULL if initialization went wrong (e.g., File not found.)
 */
function _geoip_detect2_get_reader($locales = null, $skipLocaleFilter = false, &$sourceId = '', $options = []) {
	if (! $skipLocaleFilter) {
		/**
		 * Filter: geoip_detect2_locales
		 *
		 * @param array(string) $locales
		 *        	Current locales.
		 */
		$locales = apply_filters ( 'geoip_detect2_locales', $locales );
	}

	$reader = null;
	$source = DataSourceRegistry::getInstance()->getSource($options['source']);
	if ($source) {
		$reader = $source->getReader($locales, $options);
		$sourceId = $source->getId();
	}

	/**
	 * Filter: geoip_detect2_reader
	 * You can customize your reader here.
	 * This filter will be called for every IP request.
	 *
	 * @param
	 *        	GeoIp2\Database\ProviderInterface Reader (by default: GeoLite City)
	 * @param
	 *        	array(string)							Locale precedence
	 */
	$reader = apply_filters('geoip_detect2_reader', $reader, $locales );

	return $reader;
}

function _ip_to_s($ip) : string {
	$binary = '';
	try {
		$binary = @inet_pton($ip);
	} catch (\Throwable $e) { }
	if (empty($binary))
		return '';
	return base64_encode($binary);
}

function _geoip_detect2_get_data_from_cache($ip, $source) {
	if (!DataSourceRegistry::getInstance()->isSourceCachable($source)) {
		return null;
	}

	$ip_s = _ip_to_s($ip);
	if (!$ip_s) {
		return null;
	}

	$data = get_transient('geoip_detect_c_' . $source . '_' . $ip_s);

	return $data;
}

function _geoip_detect2_add_data_to_cache($data, $ip) {
	$source = $data['extra']['source'];
	if (!DataSourceRegistry::getInstance()->isSourceCachable($source)) {
		return null;
	}
	if (GEOIP_DETECT_READER_CACHE_TIME === 0) {
		// Caching is disabled
		return null;
	}

	$data['extra']['cached'] = time();
	unset($data['maxmind']['queries_remaining']);

	$ip_s = _ip_to_s($ip);
	// Do not cache invalid IPs
	if (!$ip_s) {
		return;
	}

	// Do not cache error lookups (they might be temporary)
	if (!empty($data['extra']['error'])) {
		return;
	}

	set_transient('geoip_detect_c_' . $source . '_' . $ip_s, $data, GEOIP_DETECT_READER_CACHE_TIME);
}

function _geoip_detect2_empty_cache() {
	// This does not work for memcache. But it doesn't hurt either
	global $wpdb;

	$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_geoip_detect_c_%')" );
	return true;
}

/**
 * @return \GeoIp2\Model\Country
 */
function _geoip_detect2_get_record_from_reader($reader, $ip, &$error) {
	$record = null;

	$ip = trim($ip);

	if ($reader) {
		// When plugin installed on development boxes:
		// If the client IP is not a public IP, use the public IP of the server instead.
		// Of course this only works if the internet can be accessed.
		if ($ip == 'me' || geoip_detect_is_internal_ip($ip)) {
			$ip = geoip_detect2_get_external_ip_adress();
		}

		try {
			try {
				$record = $reader->city($ip);
			} catch (\BadMethodCallException $e) {
				$record = $reader->country($ip);
			}
		} catch(\Exception $e) {
			$error = 'Lookup Error: ' . $e->getMessage();
		}

		$reader->close();
	} else {
		$error = 'No reader was found. Check if the configuration is complete and correct.';
	}

	if (is_null($record)) {
		return _geoip_detect2_get_new_empty_record();
	}

	return $record;
}

function _geoip_detect2_get_new_empty_record($ip = '', $error = '') {
	$data = [ 'traits' => [ 'ip_address' => $ip ], 'is_empty' => true ];
	if ($error) {
		$data['extra']['error'] = $error;
	}

	return new  \YellowTree\GeoipDetect\DataSources\City($data, []);
}

function _geoip_detect2_record_enrich_data($record, $ip, $sourceId, $error) : array {
	if (is_object($record) && method_exists($record, 'jsonSerialize')) {
		$data = $record->jsonSerialize();
	} else {
		$data = [ 'traits' => [ 'ip_address' => $ip ], 'is_empty' => true ];
	}

	if (!isset($data['is_empty'])) {
		$data['is_empty'] = false;
	}
	
	if (empty($data['traits']['ip_address'])) {
		$data['traits']['ip_address'] = $ip;
	}
	
	$data['extra']['source'] = $sourceId;
	$data['extra']['cached'] = 0;
	
	if ($error || !isset($data['extra']['error'])) {
		$data['extra']['error'] = $error;
	}

	/**
	 * Filter: geoip_detect2_record_data
	 * After loading the information from the Geolocation database, you can add information to it.
	 *
	 * @param array $data 	Information found.
	 * @param string	 $orig_ip	IP that originally passed to the function.
	 * @return array
	 */
	$data = apply_filters('geoip_detect2_record_data', $data, $ip);

	return $data;
}

/**
 * GeoIPv2 doesn't always include a timezone when v1 did.
 * Region ids have changed, so countries with several time zones are out of luck.
 *
 * @param array $record
 */
function _geoip_detect2_try_to_fix_timezone($data) {
	if (!empty($data['location']['time_zone']))
		return $data;

	if (!function_exists('_geoip_detect_get_time_zone')) {
		require_once(GEOIP_PLUGIN_DIR . '/lib/timezone.php');
	}

	if (!empty($data['country']['iso_code'])) {
		$data['location']['time_zone'] = _geoip_detect_get_time_zone($data['country']['iso_code'], isset($data['subdivisions'][0]['iso_code']) ? $data['subdivisions'][0]['iso_code'] : null);
	} else {
		unset($data['location']['time_zone']);
	}

	return $data;
}
add_filter('geoip_detect2_record_data', '_geoip_detect2_try_to_fix_timezone');

/**
 * Add country name, if not known yet
 */
function _geoip_detect2_add_geonames_data($data) {
	static $countryInfo = null;
	if (is_null($countryInfo))
		$countryInfo = new \YellowTree\GeoipDetect\Geonames\CountryInformation;

	if (!empty($data['country']['iso_code'])) {
		$geonamesData = $countryInfo->getInformationAboutCountry($data['country']['iso_code']);
		$data = array_replace_recursive($geonamesData, $data);

		if (!empty($geonamesData['country']['iso_code3'])) {
			$data['extra']['country_iso_code3'] = $geonamesData['country']['iso_code3'];
		}

		$emoji = $countryInfo->getFlagEmoji($data['country']['iso_code']);
		if ($emoji && empty($data['extra']['flag'])) {
			$data['extra']['flag'] = $emoji;
		}
		$tel = $countryInfo->getTelephonePrefix($data['country']['iso_code']);
		if ($tel && empty($data['extra']['tel'])) {
			$data['extra']['tel'] = $tel;
		}
	}

	return $data;
}
add_filter('geoip_detect2_record_data', '_geoip_detect2_add_geonames_data');


/**
 * IPv6-Adresses can be written in different formats. Make sure they are standardized.
 * For IPv4-Adresses, spaces are removed.
 */
function geoip_detect_normalize_ip(string $ip) : string {
	$ip = trim($ip);
	$binary = '';
	try {
		$binary = @inet_pton($ip);
	} catch (\Throwable $e) { }
	if (empty($binary))
		return $ip; // Probably an IPv6 adress & IPv6 is not supported. Or not a valid IP.

	$ip = inet_ntop($binary);
	return $ip;
}

function geoip_detect_sanitize_ip_cidr(string $ip): string {
	$ip = trim($ip);
	$parts = explode('/', $ip, 2);
	if (isset($parts[1])) {
		if (!is_numeric($parts[1])) {
			unset($parts[1]);
		}
	}
	if (!geoip_detect_is_ip($parts[0])) {
		return '';
	}

	$ip = $parts[0] . (isset($parts[1]) ? ('/' . (int) $parts[1]) : '');
	return $ip;
}

function geoip_detect_sanitize_ip_list(string $ip_list) : string {
	$list = explode(',', $ip_list);
	$ret = [];
	foreach ($list as $ip) {
		$ip = geoip_detect_sanitize_ip_cidr($ip);
		if ($ip) {
			$ret[] = $ip;
		}
	}
	return implode(', ', $ret);
}

/**
 * Remove port from IP string
 * @param string
 * @return string
 */
function geoip_detect_ip_remove_port(string $ip) : string {
	$ip = trim($ip);
	
	if (str_contains($ip, '.')) {  // IPv4 
		// 1.1.1.1:80
		$end = mb_stripos($ip, ':');
		if ($end) {
			$ip = mb_substr($ip, 0, $end);
		}
	} else {
		// [::1]:8080
		$end = mb_stripos($ip, ']:');
		if ($ip[0] === '[' && $end) {
			$ip = mb_substr($ip, 1, $end - 1);
		}
	}

	return $ip;
}

/**
 * Check if the expected IP left matches the actual IP
 * @param string $actual IP
 * @param string|array $expected IP (can include subnet)
 * @param boolean $stripPort Remove ports if it is given (Limitation: not from $expected if array)
 * @return boolean
 */
function geoip_detect_is_ip_equal(string $actual, $expected, bool $stripPort = false ) : bool {
	if ($stripPort) {
		$actual = geoip_detect_ip_remove_port($actual);
		if (is_string($expected)) {
			$expected = geoip_detect_ip_remove_port($expected);
		}
	}
	try {
		return IpUtils::checkIp($actual, $expected);
	} catch(\Exception $e) {
		// IPv6 not supported by PHP
		// Do string comparison instead (very rough: no subnet, no IP normalization)
		if (is_array($expected)) {
			return in_array($actual, $expected, true);
		} else {
			return $actual === $expected;
		}
	}
}

function geoip_detect_is_ip(string $ip, bool $noIpv6 = false) : bool {
	$flags = FILTER_FLAG_IPV4;

	if (GEOIP_DETECT_IPV6_SUPPORTED && !$noIpv6)
		$flags = $flags | FILTER_FLAG_IPV6;

	return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
}

function geoip_detect_is_ip_in_range(string $ip, string $range_start, string $range_end) : bool {
	$long_ip = ip2long($ip);
	if ($long_ip === false) // Not IPv4
		return false;
	if($long_ip >= ip2long($range_start) && $long_ip <= ip2long($range_end))
		return true;
	return false;
}

/**
 * Check if IP is not in RFC private IP range
 * (for local development)
 * @param string $ip	IP (IPv4 or IPv6)
 * @return boolean TRUE if private
 */
function geoip_detect_is_public_ip(string $ip) : bool {
	// filver_var only detects 127.0.0.1 as local ...
	if (geoip_detect_is_ip_equal($ip, '127.0.0.0/8'))
		return false;
	if (trim($ip) === '0.0.0.0')
		return false;

	$flags = FILTER_FLAG_IPV4  // IP can be v4 or v6
		| FILTER_FLAG_NO_PRIV_RANGE // It may not be in the RFC private range
		|  FILTER_FLAG_NO_RES_RANGE; // It may not be in the RFC reserved range

	if (GEOIP_DETECT_IPV6_SUPPORTED)
		$flags = $flags | FILTER_FLAG_IPV6;

	$is_public = filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;

	return $is_public;
}

function geoip_detect_is_internal_ip(string $ip) : bool {
	return geoip_detect_is_ip($ip) && !geoip_detect_is_public_ip($ip);
}

function _geoip_detect2_get_external_ip_services(int $nb = 3, bool $needsCORS = false) : array {
	$ipservicesThatAllowCORS = array(
			'http://ipv4.icanhazip.com',
			'http://v4.ident.me',
			'http://ipinfo.io/ip',
	);
	$ipservicesWithoutCORS = array(
		'http://ipecho.net/plain',
		'https://api.ipify.org',
	);
	// Also possible with parsing: http://checkip.dyndns.org

	$ipservices = $ipservicesThatAllowCORS;
	if (!$needsCORS) {
		$ipservices = array_merge($ipservices, $ipservicesWithoutCORS);
	}

	// Randomizing to avoid querying the same service each time
	shuffle($ipservices);
	$ipservices = apply_filters('geiop_detect_ipservices', $ipservices);
	$ipservices = array_slice($ipservices, 0, $nb);
	return $ipservices;
}

/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 * (This function is not cached. Some providers may throttle our requests, that's why caching is enabled by default.)
 *
 * @return string The detected IPv4 Adress. If none is found, '0.0.0.0' is returned instead.
 */
function _geoip_detect_get_external_ip_adress_without_cache() : string 
{
	$ipservices = _geoip_detect2_get_external_ip_services();

	foreach ($ipservices as $url)
	{
		$ret = wp_remote_get($url, array('timeout' => defined('WP_TESTS_TITLE') ? 3 : 1.5));

		if (is_wp_error($ret)) {
			if (GEOIP_DETECT_DEBUG || defined('WP_TESTS_TITLE')) {
				trigger_error('_geoip_detect_get_external_ip_adress_without_cache(): Curl error (' . $url . '): ' . $ret->get_error_message(), E_USER_NOTICE);
			}
		} else if (isset($ret['response']['code']) && $ret['response']['code'] != 200) {
			if (GEOIP_DETECT_DEBUG || defined('WP_TESTS_TITLE')) {
				trigger_error('_geoip_detect_get_external_ip_adress_without_cache(): HTTP error (' . $url . '): Returned code ' . $ret['response']['code'], E_USER_NOTICE);
			}
		} else {
			if (isset($ret['body'])) {
				$ip = trim($ret['body']);
				if (geoip_detect_is_ip($ip))
					return $ip;
			}
			if (GEOIP_DETECT_DEBUG || defined('WP_TESTS_TITLE')) {
				trigger_error('_geoip_detect_get_external_ip_adress_without_cache(): HTTP error (' . $url . '): Did not return an IP: ' . $ret['body'], E_USER_NOTICE);
			}
		}
	}
	return '0.0.0.0';
}

// @see https://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php
function geoip_detect_get_relative_path($from, $to)
{
	// some compatibility fixes for Windows paths
	$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
	$to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
	$from = str_replace('\\', '/', $from);
	$to   = str_replace('\\', '/', $to);

	$from     = explode('/', $from);
	$to       = explode('/', $to);
	$relPath  = $to;

	foreach($from as $depth => $dir) {
		// find first non-matching dir
		if($dir === $to[$depth]) {
			// ignore this directory
			array_shift($relPath);
		} else {
			// get number of remaining dirs to $from
			$remaining = count($from) - $depth;
			if($remaining > 1) {
				// add traversals up to first matching dir
				$padLength = (count($relPath) + $remaining - 1) * -1;
				$relPath = array_pad($relPath, $padLength, '..');
				break;
			} else {
				$relPath[0] = $relPath[0];
			}
		}
	}
	return implode('/', $relPath);
}

function _geoip_maybe_disable_pagecache() : bool {
	if (!get_option('geoip-detect-disable_pagecache'))
		return false;

	_geoip_detect_disable_pagecache();
	return true;
}

function _geoip_detect_disable_pagecache() {
	// WP Super Cache, W3 Total Cache
	if (!defined('DONOTCACHEPAGE'))
		define('DONOTCACHEPAGE', true);

	if (!defined('DONOTCACHEOBJECT'))
		define('DONOTCACHEOBJECT', true);

	if (!defined('DONOTCACHEDB'))
		define('DONOTCACHEDB', true);

	if (!headers_sent()) {
		header('Cache-Control: private, proxy-revalidate, s-maxage=0');
		header('cf-edge-cache: no-cache' ); // Disable Cloudflare APO
	}
}

function _geoip_dashes_to_camel_case(string $string, bool $capitalizeFirstCharacter = false) : string {
    $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

    if (!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }

    return $str;
}

function geoip_detect_format_localtime($timestamp = -1) : string {
	if ($timestamp === -1) {
		$timestamp = time();
	}
	if ($timestamp == 0) {
		return __('Never', 'geoip-detect');
	}
	
	$format = get_option('date_format') . ' '. get_option('time_format');

	return get_date_from_gmt ( date( 'Y-m-d H:i:s', $timestamp ),  $format);
}
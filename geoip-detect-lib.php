<?php
// This file contains function that are necessary for the plugin, but not deemed as API.
// Their name / parameter may change without warning.

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

/**
 * Take the parameter options and add the default values.
 * @param array $options
 * return $options
 */

function _geoip_detect2_process_options($options) {
	
	// For backwards compat 2.4.0-2.5.0
	if (is_bool($options)) {
		_doing_it_wrong('GeoIP Detection Plugin: geoip_detect2_get_info_from_ip()', '$skipCache has been renamed to $options. Instead of TRUE, now use "array(\'skipCache\' => TRUE)".', '2.5.0');
		$value = $options;
		$options = array();
		$options['skipCache'] = $value;
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
function _geoip_detect2_get_reader($locales = null, $skipLocaleFilter = false, &$sourceId = '', $options = array()) {
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
	$source = DataSourceRegistry::getInstance()->getCurrentSource();
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

function _ip_to_s($ip) {
	$binary = @inet_pton($ip);
	if (empty($binary))
		return '';
	return base64_encode($binary);
}

function _geoip_detect2_get_data_from_cache($ip) {
	// Don't cache for file access based sources (not worth the effort/time)
	$sources_not_cachable = apply_filters('geoip2_detect_sources_not_cachable', array('auto', 'manual'));	
	if (in_array(get_option('geoip-detect-source'), $sources_not_cachable))
		return null;

	$ip_s = _ip_to_s($ip);
	if (!$ip_s)
		return null;
		
	$data = get_transient('geoip_detect_c_' . $ip_s);
	if (is_array($data) && $data['extra']['source'] != get_option('geoip-detect-source'))
		return null;
	
	return $data;
}

function _geoip_detect2_add_data_to_cache($data, $ip) {
	// Don't cache for file access based sources (not worth the effort/time)
	$sources_not_cachable = apply_filters('geoip2_detect_sources_not_cachable', array('auto', 'manual'));	
	if (in_array($data['extra']['source'], $sources_not_cachable))
		return;
	
	$data['extra']['cached'] = time();
	unset($data['maxmind']['queries_remaining']);
	
	$ip_s = _ip_to_s($ip);
	// Do not cache invalid IPs
	if (!$ip_s)
		return;
	
	// Do not cache error lookups (they might be temporary)
	if (!empty($data['extra']['error']))
		return;

	set_transient('geoip_detect_c_' . $ip_s, $data, GEOIP_DETECT_READER_CACHE_TIME);
}

function _geoip_detect2_get_record_from_reader($reader, $ip, &$error) {
	$record = null;
	
	$ip = trim($ip);
	
	if ($reader) {
		// When plugin installed on development boxes:
		// If the client IP is not a public IP, use the public IP of the server instead.
		// Of course this only works if the internet can be accessed.
		if ($ip == 'me' || (geoip_detect_is_ip($ip) && !geoip_detect_is_public_ip($ip))) {
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
	
	return $record;
}

function _geoip_detect2_record_enrich_data($record, $ip, $sourceId, $error) {
	$data = array('traits' => array('ip_address' => $ip), 'is_empty' => true);
	if (is_object($record) && method_exists($record, 'jsonSerialize')) {
		$data = $record->jsonSerialize();
		$data['is_empty'] = false;
	}
	$data['extra']['source'] = $sourceId;
	$data['extra']['cached'] = 0;
	$data['extra']['error'] = $error;

	/**
	 * Filter: geoip_detect2_record_data
	 * After loading the information from the GeoIP-Database, you can add information to it.
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
	if (!empty($data['location']['timezone']))
		return $data;

	if (!function_exists('_geoip_detect_get_time_zone')) {
		require_once(__DIR__ . '/vendor/timezone.php');
	}

	if (!empty($data['country']['iso_code'])) {
		$data['location']['time_zone'] = _geoip_detect_get_time_zone($data['country']['iso_code'], null);
	} else {
		$data['location']['time_zone'] = null;
	}

	return $data;
}
add_filter('geoip_detect2_record_data', '_geoip_detect2_try_to_fix_timezone');

/**
 * IPv6-Adresses can be written in different formats. Make sure they are standardized.
 * For IPv4-Adresses, spaces are removed.
 */
function geoip_detect_normalize_ip($ip) {
	$ip = trim($ip);
	$binary = @inet_pton($ip);
	if (empty($binary))
		return $ip; // Probably an IPv6 adress & IPv6 is not supported. Or not a valid IP.
	
	$ip = inet_ntop($binary);
	return $ip;
}

function geoip_detect_is_ip_equal($ip1, $ip2) {
	$one = @inet_pton($ip1);
	$two = @inet_pton($ip2);
	
	return !empty($one) && $one == $two;
}

function geoip_detect_is_ip($ip, $noIpv6 = false) {
	$flags = FILTER_FLAG_IPV4;
	
	if (GEOIP_DETECT_IPV6_SUPPORTED && !$noIpv6)
		$flags = $flags | FILTER_FLAG_IPV6;
	
	return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
}

function geoip_detect_is_ip_in_range($ip, $range_start, $range_end) {
	$long_ip = ip2long($ip);
	if ($long_ip === false) // Not IPv4
		return false;
	if($long_ip >= ip2long($range_start) && $long_ip <= ip2long($range_end))
		return true;
	return false;
}

/**
 * Check if IP is in RFC private IP range
 * (for local development)
 * @param string $ip	IP (IPv4 or IPv6)
 * @return boolean TRUE if private
 */
function geoip_detect_is_public_ip($ip) {
	// filver_var only detects 127.0.0.1 as local ...
	if (geoip_detect_is_ip_in_range($ip, '127.0.0.0', '127.255.255.255'))
		return false;

	$flags = FILTER_FLAG_IPV4  // IP can be v4 or v6
		| FILTER_FLAG_NO_PRIV_RANGE // It may not be in the RFC private range
		|  FILTER_FLAG_NO_RES_RANGE; // It may not be in the RFC reserved range
	
	if (GEOIP_DETECT_IPV6_SUPPORTED)
		$flags = $flags | FILTER_FLAG_IPV6;
	
	$is_public = filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
	
	return $is_public;
}

/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 * (This function is not cached. Some providers may throttle our requests, that's why caching is enabled by default.)
 * 
 * @return string The detected IPv4 Adress. If none is found, '0.0.0.0' is returned instead.
 */
function _geoip_detect_get_external_ip_adress_without_cache()
{
	$ipservices = array(
		'http://ipv4.icanhazip.com',
//		'http://ifconfig.me/ip', // seems to be slow
		'http://ipecho.net/plain',
		'http://v4.ident.me',
		'http://bot.whatismyipaddress.com',
		'http://ip.appspot.com',
	);
	
	// Randomizing to avoid querying the same service each time
	shuffle($ipservices);
	$ipservices = apply_filters('geiop_detect_ipservices', $ipservices);
	$ipservices = array_slice($ipservices, 0, 3);
	
	foreach ($ipservices as $url)
	{
		$ret = wp_remote_get($url, array('timeout' => defined('WP_TESTS_TITLE') ? 3 : 1));

		if (is_wp_error($ret)) {
			if (WP_DEBUG || defined('WP_TESTS_TITLE')) {
				trigger_error('_geoip_detect_get_external_ip_adress_without_cache(): Curl error (' . $url . '): ' . $ret->get_error_message(), E_USER_NOTICE);
			}
		} else if (isset($ret['response']['code']) && $ret['response']['code'] != 200) {
			if (WP_DEBUG || defined('WP_TESTS_TITLE')) {
				trigger_error('_geoip_detect_get_external_ip_adress_without_cache(): HTTP error (' . $url . '): Returned code ' . $ret['response']['code'], E_USER_NOTICE);
			}			
		} else if (isset($ret['body'])) {
			$ip = trim($ret['body']);
			if (geoip_detect_is_ip($ip))
				return $ip;
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

function _geoip_maybe_disable_pagecache() {
	if (!get_option('geoip-detect-disable_pagecache'))
		return false;
	
	// WP Super Cache, W3 Total Cache
	if (!defined('DONOTCACHEPAGE'))
		define('DONOTCACHEPAGE', true);
	
	if (!defined('DONOTCACHEOBJECT'))
		define('DONOTCACHEOBJECT', true);
	
	if (!defined('DONOTCACHEDB'))
		define('DONOTCACHEDB', true);
	
	if (!headers_sent() && !is_user_logged_in()) {
		header('Cache-Control: private, proxy-revalidate, s-maxage=0');
	}
	
	return true;
}
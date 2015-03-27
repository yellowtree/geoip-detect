<?php
// This file contains function that are necessary for the plugin, but not deemed as API.
// Their name / parameter may change without warning.

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

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
function _geoip_detect2_get_reader($locales = null, $skipLocaleFilter = false, &$sourceId = '') {
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
		$reader = $source->getReader($locales);
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
	$reader = apply_filters ( 'geoip_detect2_reader', $reader, $locales );
	
	return $reader;
}

function _geoip_detect2_get_record_from_reader($reader, $ip) {
	$record = null;
	
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
		} catch(GeoIp2\Exception\GeoIp2Exception $e) {
			if (WP_DEBUG)
				echo 'Error while looking up "' . $ip . '": ' . $e->getMessage();
		} catch(Exception $e) {
			if (WP_DEBUG)
				echo 'Error while looking up "' . $ip . '": ' . $e->getMessage();
		}
	
		$reader->close();
	}
	
	return $record;
}

function _geoip_detect2_record_enrich_data($record, $ip, $sourceId) {
	$data = array('traits' => array('ip_address' => $ip), 'is_empty' => true);
	if (is_object($record) && method_exists($record, 'jsonSerialize')) {
		$data = $record->jsonSerialize();
		$data['is_empty'] = false;
	}
	$data['extra']['source'] = $sourceId;
	$data['extra']['cached'] = 0;

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
 * @deprecated since 2.4.0
 * @return string
 */
function geoip_detect_get_abs_db_filename()
{
	_doing_it_wrong('GeoIP Detection: geoip_detect_get_abs_db_filename', 'geoip_detect_get_abs_db_filename should not be called directly', '2.3.1');
	
	$source = DataSourceRegistry::getInstance()->getCurrentSource();
	if (method_exists($source, 'maxmindGetFilename'))
		return $source->maxmindGetFilename();
	return '';
}



/**
 * IPv6-Adresses can be written in different formats. Make sure they are standardized.
 * For IPv4-Adresses, spaces are removed.
 */
function geoip_detect_normalize_ip($ip) {
	$ip = trim($ip);
	$ip = inet_ntop(inet_pton($ip));
	return $ip;
}

function geoip_detect_is_ip($ip) {
	return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false;
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

	$flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6  // IP can be v4 or v6
		| FILTER_FLAG_NO_PRIV_RANGE // It may not be in the RFC private range
		|  FILTER_FLAG_NO_RES_RANGE; // It may not be in the RFC reserved range
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

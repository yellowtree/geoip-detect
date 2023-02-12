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

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
use YellowTree\GeoipDetect\Lib\GetClientIp;

/**
 * Get Geo-Information for a specific IP
 * @param string 			$ip 		IP-Adress (IPv4 or IPv6). 'me' is the current IP of the server.
 * @param array(string)		$locales 	List of locale codes to use in name property
 * 										from most preferred to least preferred. (Default: Site language, en)
 * @param array				$options 	Property names with options.
 * 		@param boolean 		$skipCache		TRUE: Do not use cache for this request. (Default: FALSE)
 * 		@param string       $source         Change the source for this request only. (Valid values: 'auto', 'manual', 'precision', 'header', 'hostinfo')
 * 		@param float 		$timeout		Total transaction timeout in seconds (Precision+HostIP.info API only)
 * 		@param int			$connectTimeout Initial connection timeout in seconds (Precision API only)
 * @return YellowTree\GeoipDetect\DataSources\City	GeoInformation. (Actually, this is a subclass of \GeoIp2\Model\City)
 *
 * @see https://github.com/maxmind/GeoIP2-php				API Usage
 * @see http://dev.maxmind.com/geoip/geoip2/web-services/	API Documentation
 *
 * @since 2.0.0
 * @since 2.4.0 New parameter $skipCache
 * @since 2.5.0 Parameter $skipCache has been renamed to $options with 'skipCache' property
 * @since 2.7.0 Parameter $options['source'] has been introduced
 */
function geoip_detect2_get_info_from_ip(string $ip, $locales = null, $options = []) : \YellowTree\GeoipDetect\DataSources\City {
	if(defined('GEOIP_DETECT_LOOKUP_DISABLED') && GEOIP_DETECT_LOOKUP_DISABLED) {
		trigger_error('Geolocation IP Detection: The lookup is currently disabled (Error: could not initialize the plugin).');
		return _geoip_detect2_get_new_empty_record('', 'The lookup is currently disabled (Error: could not initialize the plugin).');
	}

	_geoip_maybe_disable_pagecache();
	// 1) Processing the parameters.
	$options = _geoip_detect2_process_options($options);

	/**
	 * Filter: geoip_detect2_locales
	 *
	 * @param array $locales The locales that were passed to the function
	 */
	$locales = apply_filters('geoip_detect2_locales', $locales);


	// 2) Doing the Lookup
	$data = [];
	/**
	 * Filter: geoip_detect2_record_data_override_lookup
	 * Before doing the lookup, changing the data (similar to a cache but also when skipCache is on).
	 * 
	 * @param array $data Empty array
	 * @param array $ip   Ip to lookup information from
	 */
	$data = apply_filters('geoip_detect2_record_data_override_lookup', $data, $ip, $options);

	// Have a look at the cache
	if (!$data && !$options['skipCache']) {
		$data = _geoip_detect2_get_data_from_cache($ip, $options['source']);
	}

	if (!$data) {

		$reader = _geoip_detect2_get_reader(array('en') /* will be replaced anyway */, true, $outSourceId, $options);

		$lookupError = '';
		$record = _geoip_detect2_get_record_from_reader($reader, $ip, $lookupError);

		$data   = _geoip_detect2_record_enrich_data($record, $ip, $outSourceId, $lookupError);

		if (GEOIP_DETECT_DEBUG && !defined('GEOIP_DETECT_DOING_UNIT_TESTS') && $lookupError) {
			trigger_error($lookupError, E_USER_NOTICE);
		}

		// Save result to cache, but no "IP not found in database" or similar errors
		if (!$lookupError) {
			_geoip_detect2_add_data_to_cache($data, $ip);
		}
	}

	/**
	 * Filter: geoip_detect2_record_data_after_cache
	 * After loading the information from the Geolocation-Database AND after the cache, you can add information to it.
	 *
	 * @param array $data 	Information found.
	 * @param string $orig_ip	IP that originally passed to the function.
	 * @return array
	 */
	$data = apply_filters('geoip_detect2_record_data_after_cache', $data, $ip);

	// 3) Returning the data

	// Always return a city record for API compatability. City attributes etc. return empty values.
	$original_record = new \YellowTree\GeoipDetect\DataSources\City($data, $locales);

	/**
	 * Filter: geoip_detect2_record_information
	 * Use geoip_detect2_record_data_after_cache instead if you want to modify the data.
	 *
	 * @return \YellowTree\GeoipDetect\DataSources\City
	 */
	$record = apply_filters('geoip_detect2_record_information', $original_record, $ip, $locales);
	if (! ($record instanceof \YellowTree\GeoipDetect\DataSources\City) ) {
		if (method_exists($record, 'jsonSerialize')) {
			$data = $record->jsonSerialize();
			return new \YellowTree\GeoipDetect\DataSources\City($data, $locales);
		}
		return $original_record;
	}

	return $record;
}

/**
 * Get Geo-Information for the current IP
 *
 * @param array(string)		$locales	List of locale codes to use in name property
 * 										from most preferred to least preferred. (Default: Site language, en)
 * @param array				Property names with options.
 * 		@param boolean 		$skipCache		TRUE: Do not use persistent cache for this request. (Default: FALSE)
 * 		@param boolean		$skipLocalCache	TRUE: Do not use caching in memory (Default: FALSE)
 * 		@param string       $source         Change the source for this request only. (Valid values: 'auto', 'manual', 'precision', 'header', 'hostinfo')
 * 		@param float 		$timeout		Total transaction timeout in seconds (Precision+HostIP.info API only)
 * 		@param int			$connectTimeout Initial connection timeout in seconds (Precision API only)
 * @return \YellowTree\GeoipDetect\DataSources\City	GeoInformation.
 *
 * @since 2.0.0
 * @since 2.4.0 New parameter $skipCache
 * @since 2.5.0 Parameter $skipCache has been renamed to $options with 'skipCache' property
 * @since 2.7.0 Parameter $options['source'] has been introduced
 * @since 5.0.0 The result of this function is cached for the duration of the PHP execution (except if you use skipLocalCache)
 */
function geoip_detect2_get_info_from_current_ip($locales = null, $options = []) {
	/** @var \YellowTree\GeoipDetect\DataSources\City  */
	static $cache = null;

	if (empty($options['skipLocalCache'])) {
		if (!is_null($cache)) {
			$locales = apply_filters('geoip_detect2_locales', $locales);
			$data = $cache->jsonSerialize();
			$data = apply_filters('geoip_detect2_record_data_override_lookup', $data, $cache->traits->ipAddress, $options);
			$data = apply_filters('geoip_detect2_record_data_after_cache', $data, $cache->traits->ipAddress);
			$record = new \YellowTree\GeoipDetect\DataSources\City($data, $locales);
			return $record;
		}
	}

	$ret = geoip_detect2_get_info_from_ip(geoip_detect2_get_client_ip(), $locales, $options);
	if (empty($options['skipLocalCache'])) {
		$cache = $ret;
	}

	return $ret;
}


/**
 * Get the Reader class of the currently chosen source.
 * (Use this if you want to use other methods than "city" or otherwise customize behavior.)
 *
 * @param array(string)		$locales		List of locale codes to use in name property
 * 											from most preferred to least preferred. (Default: Site language, en)
 * @param array				$options		Property names with options.
 * 		@param string       $source         Change the source for this request only. (Valid values: 'auto', 'manual', 'precision', 'header', 'hostinfo')
 * 		@param float 		$timeout		Total transaction timeout in seconds (Precision+HostIP.info API only)
 * 		@param int			$connectTimeout Initial connection timeout in seconds (Precision API only)
 *
 * @since 2.0.0
 * @since 2.5.0 new parameter $options
 * @since 2.7.0 Parameter $options['source'] has been introduced
 */
function geoip_detect2_get_reader($locales = null, $options = []) {
	_geoip_maybe_disable_pagecache();
	$options = _geoip_detect2_process_options($options);

	return _geoip_detect2_get_reader($locales, false, $sourceIdOut, $options);
}

/**
 * Return a human-readable label of the currently chosen source.
 * @param string|\YellowTree\GeoipDetect\DataSources\City $source Id of the source or the returned record
 * @return string The label.
 *
 * @since 2.3.1
 * @since 2.4.0 new parameter $source
 */
function geoip_detect2_get_current_source_description($source = null) {
	if (is_object($source) && $source instanceof \YellowTree\GeoipDetect\DataSources\City) {
		$source = $source->extra->source;
	}
	$registry = DataSourceRegistry::getInstance();
	if (is_null($source)) {
		$source = $registry->getCurrentSource();
	} else {
		$source = $registry->getSource($source);
	}

	if ($source) {
		return $source->getShortLabel();
	}
	return 'Unknown';
}

/**
 * Get client IP (even if it is behind a reverse proxy)
 * For security reasons, the reverse proxy usage has to be enabled on the settings page.
 *
 * @return string Client Ip (IPv4 or IPv6)
 *
 * @since 2.0.0
 */
function geoip_detect2_get_client_ip() : string {
	_geoip_maybe_disable_pagecache();

	static $helper = null;
	if (is_null($helper) || defined('GEOIP_DETECT_DOING_UNIT_TESTS')) {
		$helper = new GetClientIp();

		$trusted_proxies = explode(',', (string) get_option('geoip-detect-trusted_proxy_ips', ''));
		$helper->addProxiesToWhitelist($trusted_proxies);
	}
	$useReverseProxy = get_option('geoip-detect-has_reverse_proxy', 0);
	return $helper->getIp( $useReverseProxy );
}

/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 *
 * @param boolean $unfiltered If true, do not check the options for an external adress. (Default: false)
 * @return string The detected IPv4 Adress. If none is found, '0.0.0.0' is returned instead.
 *
 * @since 2.0.0
 * @since 2.4.3 Reading option 'external_ip' first.
 * @since 2.5.2 New param $unfiltered that can bypass the option.
 */
function geoip_detect2_get_external_ip_adress(bool $unfiltered = false) : string {
	$ip_cache = '';

	if (!$unfiltered)
		$ip_cache = get_option('geoip-detect-external_ip');

	if (!$ip_cache)
		$ip_cache = get_transient('geoip_detect_external_ip');

	if (!$ip_cache) {
		$ip_cache = _geoip_detect_get_external_ip_adress_without_cache();

		$expiryTime = GEOIP_DETECT_IP_CACHE_TIME;
		if (empty($ip_cache) || $ip_cache === '0.0.0.0')
			$expiryTime = GEOIP_DETECT_IP_EMPTY_CACHE_TIME;

		set_transient('geoip_detect_external_ip', $ip_cache, $expiryTime);
	}

	$ip_cache = apply_filters('geoip_detect_get_external_ip_adress', $ip_cache);

	return $ip_cache;
}

/**
 * Call this function if you want to register the JS script for AJAX mode only for specific pages.
 * Can be called via the shortcode `[geoip_detect2_enqueue_javascript]`
 * @see https://github.com/yellowtree/geoip-detect/wiki/API-Usage-Examples#ajax-enqueue-the-js-file-manually
 * @return bool was enqueued
 */
function geoip_detect2_enqueue_javascript(string $reason = 'user') : bool {
	$do_it = apply_filters('geoip_detect_enqueue_javascript', true, $reason);
	$do_it = apply_filters('geoip_detect_enqueue_javascript_' . $reason, $do_it);

	if ($do_it) {
		_geoip_detect2_enqueue_javascript();
	}
	return $do_it;
}
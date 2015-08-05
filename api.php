<?php

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
/**
 * Get Geo-Information for a specific IP
 * @param string 			$ip 		IP-Adress (IPv4 or IPv6). 'me' is the current IP of the server.
 * @param array(string)		$locales 	List of locale codes to use in name property
 * 										from most preferred to least preferred. (Default: Site language, en)
 * @param array				Property names with options.
 * 		@param boolean 		$skipCache		TRUE: Do not use cache for this request. (Default: FALSE)
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
 */
function geoip_detect2_get_info_from_ip($ip, $locales = null, $options = array())
{
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
	$data = array();
	
	// Have a look at the cache first
	if (!$options['skipCache']) {
		$data = _geoip_detect2_get_data_from_cache($ip);
	}
	
	if (!$data) {

		$reader = _geoip_detect2_get_reader(array('en') /* will be replaced anyway */, true, $outSourceId, $options);
		
		$lookupError = '';
		$record = _geoip_detect2_get_record_from_reader($reader, $ip, $lookupError);
		
		$data   = _geoip_detect2_record_enrich_data($record, $ip, $outSourceId, $lookupError);
		
		if (WP_DEBUG && !GEOIP_DETECT_DOING_UNIT_TESTS && $lookupError) {
			trigger_error($lookupError, E_USER_NOTICE);
		}

		// Save result to cache, but no "IP not found in database" or similar errors
		if (!$lookupError)
			_geoip_detect2_add_data_to_cache($data, $ip);
	}
	
	
	// 3) Returning the data
	
	// Always return a city record for API compatability. City attributes etc. return empty values.
	$record = new \YellowTree\GeoipDetect\DataSources\City($data, $locales);
	
	/**
	 * Filter: geoip_detect2_record_information
	 * Use geoip_detect2_record_data instead if you want to modify the data.
	 * 
	 * @return \YellowTree\GeoipDetect\DataSources\City
	 */
	$record = apply_filters('geoip_detect2_record_information', $record, $ip, $locales);

	return $record;
}

/**
 * Get Geo-Information for the current IP
 *
 * @param array(string)		$locales	List of locale codes to use in name property
 * 										from most preferred to least preferred. (Default: Site language, en)
 * @param array				Property names with options.
 * 		@param boolean $skipCache	TRUE: Do not use cache for this request. (Default: FALSE) 
 * @return YellowTree\GeoipDetect\DataSources\City	GeoInformation.
 *
 * @since 2.0.0
 * @since 2.4.0 New parameter $skipCache
 * @since 2.5.0 Parameter $skipCache has been renamed to $options with 'skipCache' property
 */
function geoip_detect2_get_info_from_current_ip($locales = null, $options = array())
{
	return geoip_detect2_get_info_from_ip(geoip_detect2_get_client_ip(), $locales, $options);
}


/**
 * Get the Reader class of the currently chosen source.
 * (Use this if you want to use other methods than "city" or otherwise customize behavior.)
 * 
 * @param array(string)				List of locale codes to use in name property
 * 									from most preferred to least preferred. (Default: Site language, en)
 * @param array				Property names with options.
 * 		@param float 		$timeout		Total transaction timeout in seconds (Precision+HostIP.info API only) 
 * 		@param float		$connectTimeout Initial connection timeout in seconds (Precision API only)
 * @return \YellowTree\GeoipDetect\DataSources\ReaderInterface 	The reader, ready to do its work. Don't forget to `close()` it afterwards. NULL if file not found (or other problems).
 * 
 * @since 2.0.0
 * @since 2.5.0 new parameter $options
 */
function geoip_detect2_get_reader($locales = null, $options = array()) {
	_geoip_maybe_disable_pagecache();
	return _geoip_detect2_get_reader($locales, false, $sourceIdOut, $options);
}

/**
 * Return a human-readable label of the currently chosen source.
 * @param string|\YellowTree\GeoipDetect\DataSources\City Id of the source or the returned record
 * @return string The label.
 * 
 * @since 2.3.1
 * @since 2.4.0 new parameter $source
 */
function geoip_detect2_get_current_source_description($source = null) {
	if (is_object($source) && $source instanceof \YellowTree\GeoipDetect\DataSources\City) {
		$source = $source->extra->source;
	}
	$source = DataSourceRegistry::getInstance()->getSource($source);
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
function geoip_detect2_get_client_ip() {
	_geoip_maybe_disable_pagecache();
	
	$ip = '::1';
	
	if (isset($_SERVER['REMOTE_ADDR']))
		$ip = $_SERVER['REMOTE_ADDR'];
	
	if (get_option('geoip-detect-has_reverse_proxy', 0) && isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		$ip_list = explode(',', @$_SERVER["HTTP_X_FORWARDED_FOR"]);
		$ip_list = array_map('geoip_detect_normalize_ip', $ip_list);
		
		$trusted_proxies = get_option('geoip-detect-trusted_proxy_ips');
		if ($trusted_proxies) {
			// TODO: Expose option to UI. comma-seperated list of IPv4 and v6 adresses.			
			$trusted_proxies = explode(',', $trusted_proxies);
			
			// Always trust localhost
			$trusted_proxies[] = '::1';
			$trusted_proxies[] = '127.0.0.1';
			
			$trusted_proxies = array_map('geoip_detect_normalize_ip', $trusted_proxies);
			$ip_list[] = $ip;
				
			$ip_list = array_diff($ip_list, $trusted_proxies);

		} 
		
		// Each Proxy server append their information at the end, so the last IP is most trustworthy.
		$ip = end($ip_list);
	}
	
	if (!$ip)
		$ip = '::1'; // By default, use localhost
	
	$ip = apply_filters('geoip2_detect2_client_ip', $ip);
	
	return $ip;
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
function geoip_detect2_get_external_ip_adress($unfiltered = false)
{
	$ip_cache = '';
	
	if (!$unfiltered)
		$ip_cache = get_option('geoip-detect-external_ip');
	
	if (!$ip_cache)
		$ip_cache = get_transient('geoip_detect_external_ip');

	if (!$ip_cache) {
		$ip_cache = _geoip_detect_get_external_ip_adress_without_cache();
		set_transient('geoip_detect_external_ip', $ip_cache, GEOIP_DETECT_IP_CACHE_TIME);
	}
	
	$ip_cache = apply_filters('geoip_detect_get_external_ip_adress', $ip_cache);

	return $ip_cache;
}

<?php

/**
 * Get Geo-Information for a specific IP
 * @param string 				$ip IP-Adress (IPv4 or IPv6)
 * @return GeoIp2\Model\City	GeoInformation. (0 or NULL: no infos found.)
 * 
 * @see https://github.com/maxmind/GeoIP2-php				API Usage
 * @see http://dev.maxmind.com/geoip/geoip2/web-services/	API Documentation
 */
function geoip_detect2_get_info_from_ip($ip)
{
	static $reader = null;
	if (is_null($reader)) {
		$data_file = geoip_detect_get_abs_db_filename();
		if (!$data_file)
			return 0;
		
		$reader = new GeoIp2\Database\Reader($data_file);
		$reader = apply_filter('geoip_detect_reader', $reader);
	}

	$record = $reader->city($ip);
	// Handle not found: http://dev.maxmind.com/geoip/geoip2/web-services/#Errors
	
	/**
	 * Filter: geoip_detect_record_information
	 * After loading the information from the GeoIP-Database, you can add or remove information from it.
	 * @param GeoIp2\Model\City $record Information found.
	 * @param string			 $ip	 IP that was looked up
	 * @return GeoIp2\Model\City
	 */
	$record = apply_filters('geoip_detect2_record_information', $record, $ip);

	return $record;
}

/**
 * Get Geo-Information for the current IP
 * @return geoiprecord	GeoInformation. (0 / NULL: no infos found.)
 */
function geoip_detect2_get_info_from_current_ip()
{
	// Does this work with local file? geoip_detect2_get_info_from_ip('me');
	// TODO: Use Proxy IP if available
	return geoip_detect2_get_info_from_ip(@$_SERVER['REMOTE_ADDR']);
}

/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 * 
 * @return string The detected IP Adress. If none is found, '0.0.0.0' is returned instead.
 */
function geoip_detect2_get_external_ip_adress()
{
	$ip_cache = get_transient('geoip_detect_external_ip');

	if ($ip_cache)
		return apply_filters('geoip_detect_get_external_ip_adress', $ip_cache);
	
	$ip_cache = _geoip_detect_get_external_ip_adress_without_cache();
	set_transient('geoip_detect_external_ip', $ip_cache, GEOIP_DETECT_IP_CACHE_TIME);
	
	$ip_cache = apply_filters('geoip_detect_get_external_ip_adress', $ip_cache);
	return $ip_cache;
}

function _geoip_detect_get_external_ip_adress_without_cache()
{
	$ipservices = array(
		'http://ipv4.icanhazip.com',
		'http://ifconfig.me/ip',
		'http://ipecho.net/plain',
		'http://v4.ident.me',
		'http://bot.whatismyipaddress.com',
		'http://ipv4.ipogre.com',
		'http://ip.appspot.com',
	);
	
	// Randomizing to avoid querying the same service each time
	shuffle($ipservices);
	$ipservices = array_slice($ipservices, 0, 3);
	
	foreach ($ipservices as $url)
	{
		$ret = wp_remote_get($url, array('timeout' => 1));

		if (is_wp_error($ret)) {
			if (WP_DEBUG)
				echo 'Curl error: ' . $ret->get_error_message();
		} else if (isset($ret['body'])) {
			return trim($ret['body']);
		}
	}
	return '0.0.0.0';
}

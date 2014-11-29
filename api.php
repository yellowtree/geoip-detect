<?php

/**
 * Get Geo-Information for a specific IP
 * @param string 		$ip IP-Adress (currently only IPv4)
 * @return geoiprecord	GeoInformation. (0 or NULL: no infos found.)
 */
function geoip_detect_get_info_from_ip($ip)
{
	$data_file = geoip_detect_get_abs_db_filename();
	if (!$data_file)
		return 0;

	$gi = geoip_open($data_file, GEOIP_STANDARD);
	$record = geoip_record_by_addr($gi, $ip);
	geoip_close($gi);

	$record = apply_filters('geoip_detect_record_information', $record, $ip);

	return $record;
}

/**
 * Get Geo-Information for the current IP
 * @param string 		$ip (IPv4)
 * @return geoiprecord	GeoInformation. (0 / NULL: no infos found.)
 */
function geoip_detect_get_info_from_current_ip()
{
	$ip = geoip_detect_get_client_ip();
	
	// TODO: Use Proxy IP if available
	return geoip_detect_get_info_from_ip($ip);
}

/**
 * Get client IP (even if it is behind a reverse proxy)
 */
function geoip_detect_get_client_ip() {
	if (get_option('geoip-detect-has_reverse_proxy', 0) && isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		$ip = @$_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$ip = @$_SERVER['REMOTE_ADDR'];
	}
	
	return $ip;
}

/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 * 
 * @return string The detected IP Adress. If none is found, '0.0.0.0' is returned instead.
 */
function geoip_detect_get_external_ip_adress()
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

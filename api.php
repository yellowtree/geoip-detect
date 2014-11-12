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
	// TODO: Use Proxy IP if available
	return geoip_detect_get_info_from_ip(@$_SERVER['REMOTE_ADDR']);
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
	set_transient('geoip_detect_external_ip', $ip_cache, 15 * MINUTE_IN_SECONDS);
	
	$ip_cache = apply_filters('geoip_detect_get_external_ip_adress', $ip_cache);
	return $ip_cache;
}

function _geoip_detect_get_external_ip_adress_without_cache()
{
	$ipservices = array(
			'http://ipv4.icanhazip.com',
			'http://ifconfig.me/ip',
	);
	
	foreach ($ipservices as $url)
	{
		$ret = wp_remote_get($url, array('timeout' => 1));
		if (is_wp_error($ret)) {
			if (WP_DEBUG)
				echo 'Curl error: ' . $ret;
		} else if (isset($ret['body'])) {
			return trim($ret['body']);
		}
	}
	return '0.0.0.0';
}
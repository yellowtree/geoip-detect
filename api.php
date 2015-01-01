<?php

/**
 * Get Geo-Information for a specific IP
 * @param string 				$ip IP-Adress (IPv4 or IPv6). 'me' is the current IP of the server.
 * @param array(string)			List of locale codes to use in name property
 * 								from most preferred to least preferred. (Default: Site language, en)
 * @return GeoIp2\Model\City	GeoInformation.
 * 
 * @see https://github.com/maxmind/GeoIP2-php				API Usage
 * @see http://dev.maxmind.com/geoip/geoip2/web-services/	API Documentation
 */
function geoip_detect2_get_info_from_ip($ip, $locales = null)
{
	$orig_ip = $ip;
	
	$reader = geoip_detect2_get_reader($locales);
	if (!$reader)
		return null;

	$record = null;

	// For development usage: if the client IP is not a public IP, use the public IP of the server instead.
	if ($ip == 'me' || (geoip_detect_is_ip($ip) && !geoip_detect_is_public_ip($ip))) {
		$ip = geoip_detect2_get_external_ip_adress();
	}
	
	
	try {
		$record = $reader->city($ip);
	} catch(GeoIp2\Exception\GeoIp2Exception $e) {
		if (WP_DEBUG)
			echo 'Error while looking up "' . $ip . '": ' . $e->getMessage();
	} catch(Exception $e) {
		if (WP_DEBUG)
			echo 'Error while looking up "' . $ip . '": ' . $e->getMessage();		
	}

	$reader->close();
	
	if ($record === null) {
		$data = array('traits' => array('is_empty' => true, 'ip_address' => $ip));
		$record = new \GeoIp2\Model\City($data, array('en'));
	}
	
	/**
	 * Filter: geoip_detect2_record_information
	 * After loading the information from the GeoIP-Database, you can add or remove information from it.
	 * @param GeoIp2\Model\City $record 	Information found. The 
	 * @param string			 $orig_ip	IP that originally passed to the function.
	 * @return GeoIp2\Model\City
	 */
	$record = apply_filters('geoip_detect2_record_information', $record, $orig_ip);

	return $record;
}

/**
 * Get the Maxmind Reader
 * (Use this if you want to use other methods than "city" or otherwise customize behavior.)
 * 
 * @param array(string)			List of locale codes to use in name property
 * 								from most preferred to least preferred. (Default: Site language, en)
 * @return GeoIp2\Database\Reader The reader, ready to do its work. Don't forget to `close()` it afterwards. NULL if file not found (or other problems).
 */
function geoip_detect2_get_reader($locales = null) {	
	/**
	 * Filter: geoip_detect2_locales
	 * @param array(string) $locales Current locales.
	 */
	$locales = apply_filters('geoip_detect2_locales', $locales);
	
	$reader = null;	
	$data_file = geoip_detect_get_abs_db_filename();
	if ($data_file)
		$reader = new GeoIp2\Database\Reader($data_file, $locales);
	
	/**
	 * Filter: geoip_detect2_reader
	 * You can customize your reader here.
	 * This filter will be called for every IP request.
	 * 
	 * @param GeoIp2\Database\ProviderInterface  Reader (by default: GeoLite City)
	 * @param array(string)							Locale precedence
	 */
	$reader = apply_filters('geoip_detect2_reader', $reader, $locales);
	
	return $reader;
}

/**
 * Get Geo-Information for the current IP
 * @param array(string)			List of locale codes to use in name property
 * 								from most preferred to least preferred. (Default: Site language, en)
 * @return GeoIp2\Model\City	GeoInformation.
 */
function geoip_detect2_get_info_from_current_ip($locales = null)
{
	return geoip_detect2_get_info_from_ip(geoip_detect_get_client_ip(), $locales);
}

	/**
	 * Get client IP (even if it is behind a reverse proxy)
	 * For security reasons, the reverse proxy usage has to be enabled on the settings page.
	 * 
	 * @return string Client Ip (IPv4 or IPv6)
	 */
	function geoip_detect_get_client_ip() {
	if (get_option('geoip-detect-has_reverse_proxy', 0) && isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		$ip = @$_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$ip = @$_SERVER['REMOTE_ADDR'];
	}
	if (!$ip)
		$ip = '::1'; // By default, use localhost
	
	return $ip;
}

/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 * 
 * @return string The detected IPv4 Adress. If none is found, '0.0.0.0' is returned instead.
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



<?php

/**
 * Get Geo-Information for a specific IP
 * @param string 				$ip IP-Adress (IPv4 or IPv6)
 * @param array(string)			List of locale codes to use in name property
 * 								from most preferred to least preferred. (Default: Site language, en)
 * @return GeoIp2\Model\City	GeoInformation. (NULL: no infos found.)
 * 
 * @see https://github.com/maxmind/GeoIP2-php				API Usage
 * @see http://dev.maxmind.com/geoip/geoip2/web-services/	API Documentation
 */
function geoip_detect2_get_info_from_ip($ip, $locales = null)
{
	$reader = geoip_detect2_get_reader($locales);
	if (!$reader)
		return null;

	$record = null;

	if ($ip == 'me' || geoip_detect_is_private_ip($ip)) {
		$ip = geoip_detect2_get_external_ip_adress();
	}
	
	
	try {
		$record = $reader->city($ip);
	} catch(GeoIp2\Exception\GeoIp2Exception $e) {
		if (WP_DEBUG)
				echo $e->getMessage();
	} catch(Exception $e) {
		if (WP_DEBUG)
				echo $e->getMessage();		
	}

	$reader->close();
	
	/**
	 * Filter: geoip_detect_record_information
	 * After loading the information from the GeoIP-Database, you can add or remove information from it.
	 * @param GeoIp2\Model\City $record Information found (or NULL if not found)
	 * @param string			 $ip	 IP that was looked up. If original IP did not retrieve anything (probably intranet) then this is the internet IP of the server.
	 * @return GeoIp2\Model\City
	 */
	$record = apply_filters('geoip_detect2_record_information', $record, $ip);

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
 * 								from most preferred to least preferred.
 * @return GeoIp2\Model\City	GeoInformation. (0 / NULL: no infos found.)
 */
function geoip_detect2_get_info_from_current_ip($locales = null)
{
	return geoip_detect2_get_info_from_ip('me', $locales);
}

/**
 * Get client IP (even if it is behind a reverse proxy)
 * @return string Client Ip
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
			if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip))
				return $ip;
		}
	}
	return '0.0.0.0';
}

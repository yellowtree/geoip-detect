<?php
use GeoIp2\Database\Reader;

/**
 * Get Geo-Information for a specific IP
 * @param string 		$ip IP-Adress (currently only IPv4)
 * @return geoiprecord	GeoInformation. (0 or NULL: no infos found.)
 */
function geoip_detect_get_info_from_ip($ip)
{
	static $reader = null;
	if (is_null($reader)) {
		$data_file = geoip_detect_get_abs_db_filename();
		if (!$data_file)
			return 0;
		
		$reader = new GeoIp2\Database\Reader($data_file);
	}

	$record = $reader->city($ip);
	var_dump(array_keys((array) $record));
	
	// Funktioniert nicht. Proxy object?
	//var_dump((string) $record->city);
	
/*
object(GeoIp2\Model\City)#276 (12) {
  ["city":protected]=>
  object(GeoIp2\Record\City)#269 (3) {
    ["validAttributes":protected]=>
    array(3) {
      [0]=>
      string(10) "confidence"
      [1]=>
      string(9) "geonameId"
      [2]=>
      string(5) "names"
    }
    ["locales":"GeoIp2\Record\AbstractPlaceRecord":private]=>
    array(1) {
      [0]=>
      string(2) "en"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(0) {
    }
  }
  ["location":protected]=>
  object(GeoIp2\Record\Location)#268 (2) {
    ["validAttributes":protected]=>
    array(7) {
      [0]=>
      string(14) "accuracyRadius"
      [1]=>
      string(8) "latitude"
      [2]=>
      string(9) "longitude"
      [3]=>
      string(9) "metroCode"
      [4]=>
      string(10) "postalCode"
      [5]=>
      string(16) "postalConfidence"
      [6]=>
      string(8) "timeZone"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(2) {
      ["latitude"]=>
      float(51)
      ["longitude"]=>
      float(9)
    }
  }
  ["postal":protected]=>
  object(GeoIp2\Record\Postal)#267 (2) {
    ["validAttributes":protected]=>
    array(2) {
      [0]=>
      string(4) "code"
      [1]=>
      string(10) "confidence"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(0) {
    }
  }
  ["subdivisions":protected]=>
  array(0) {
  }
  ["continent":protected]=>
  object(GeoIp2\Record\Continent)#275 (3) {
    ["validAttributes":protected]=>
    array(3) {
      [0]=>
      string(4) "code"
      [1]=>
      string(9) "geonameId"
      [2]=>
      string(5) "names"
    }
    ["locales":"GeoIp2\Record\AbstractPlaceRecord":private]=>
    array(1) {
      [0]=>
      string(2) "en"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(3) {
      ["code"]=>
      string(2) "EU"
      ["geoname_id"]=>
      int(6255148)
      ["names"]=>
      array(8) {
        ["de"]=>
        string(6) "Europa"
        ["en"]=>
        string(6) "Europe"
        ["es"]=>
        string(6) "Europa"
        ["fr"]=>
        string(6) "Europe"
        ["ja"]=>
        string(15) "ヨーロッパ"
        ["pt-BR"]=>
        string(6) "Europa"
        ["ru"]=>
        string(12) "Европа"
        ["zh-CN"]=>
        string(6) "欧洲"
      }
    }
  }
  ["country":protected]=>
  object(GeoIp2\Record\Country)#274 (3) {
    ["validAttributes":protected]=>
    array(4) {
      [0]=>
      string(10) "confidence"
      [1]=>
      string(9) "geonameId"
      [2]=>
      string(7) "isoCode"
      [3]=>
      string(5) "names"
    }
    ["locales":"GeoIp2\Record\AbstractPlaceRecord":private]=>
    array(1) {
      [0]=>
      string(2) "en"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(3) {
      ["geoname_id"]=>
      int(2921044)
      ["iso_code"]=>
      string(2) "DE"
      ["names"]=>
      array(8) {
        ["de"]=>
        string(11) "Deutschland"
        ["en"]=>
        string(7) "Germany"
        ["es"]=>
        string(8) "Alemania"
        ["fr"]=>
        string(9) "Allemagne"
        ["ja"]=>
        string(24) "ドイツ連邦共和国"
        ["pt-BR"]=>
        string(8) "Alemanha"
        ["ru"]=>
        string(16) "Германия"
        ["zh-CN"]=>
        string(6) "德国"
      }
    }
  }
  ["locales":protected]=>
  array(1) {
    [0]=>
    string(2) "en"
  }
  ["maxmind":protected]=>
  object(GeoIp2\Record\MaxMind)#273 (2) {
    ["validAttributes":protected]=>
    array(1) {
      [0]=>
      string(16) "queriesRemaining"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(0) {
    }
  }
  ["registeredCountry":protected]=>
  object(GeoIp2\Record\Country)#272 (3) {
    ["validAttributes":protected]=>
    array(4) {
      [0]=>
      string(10) "confidence"
      [1]=>
      string(9) "geonameId"
      [2]=>
      string(7) "isoCode"
      [3]=>
      string(5) "names"
    }
    ["locales":"GeoIp2\Record\AbstractPlaceRecord":private]=>
    array(1) {
      [0]=>
      string(2) "en"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(3) {
      ["geoname_id"]=>
      int(6252001)
      ["iso_code"]=>
      string(2) "US"
      ["names"]=>
      array(8) {
        ["de"]=>
        string(3) "USA"
        ["en"]=>
        string(13) "United States"
        ["es"]=>
        string(14) "Estados Unidos"
        ["fr"]=>
        string(11) "États-Unis"
        ["ja"]=>
        string(21) "アメリカ合衆国"
        ["pt-BR"]=>
        string(14) "Estados Unidos"
        ["ru"]=>
        string(6) "Сша"
        ["zh-CN"]=>
        string(6) "美国"
      }
    }
  }
  ["representedCountry":protected]=>
  object(GeoIp2\Record\RepresentedCountry)#271 (3) {
    ["validAttributes":protected]=>
    array(5) {
      [0]=>
      string(10) "confidence"
      [1]=>
      string(9) "geonameId"
      [2]=>
      string(7) "isoCode"
      [3]=>
      string(5) "names"
      [4]=>
      string(4) "type"
    }
    ["locales":"GeoIp2\Record\AbstractPlaceRecord":private]=>
    array(1) {
      [0]=>
      string(2) "en"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(0) {
    }
  }
  ["traits":protected]=>
  object(GeoIp2\Record\Traits)#270 (2) {
    ["validAttributes":protected]=>
    array(9) {
      [0]=>
      string(22) "autonomousSystemNumber"
      [1]=>
      string(28) "autonomousSystemOrganization"
      [2]=>
      string(6) "domain"
      [3]=>
      string(16) "isAnonymousProxy"
      [4]=>
      string(19) "isSatelliteProvider"
      [5]=>
      string(3) "isp"
      [6]=>
      string(9) "ipAddress"
      [7]=>
      string(12) "organization"
      [8]=>
      string(8) "userType"
    }
    ["record":"GeoIp2\Record\AbstractRecord":private]=>
    array(1) {
      ["ip_address"]=>
      string(12) "47.64.121.17"
    }
  }
  ["raw":protected]=>
  array(5) {
    ["continent"]=>	
    array(3) {
      ["code"]=>
      string(2) "EU"
      ["geoname_id"]=>
      int(6255148)
      ["names"]=>
      array(8) {
        ["de"]=>
        string(6) "Europa"
        ["en"]=>
        string(6) "Europe"
        ["es"]=>
        string(6) "Europa"
        ["fr"]=>
        string(6) "Europe"
        ["ja"]=>
        string(15) "ヨーロッパ"
        ["pt-BR"]=>
        string(6) "Europa"
        ["ru"]=>
        string(12) "Европа"
        ["zh-CN"]=>
        string(6) "欧洲"
      }
    }
    ["country"]=>
    array(3) {
      ["geoname_id"]=>
      int(2921044)
      ["iso_code"]=>
      string(2) "DE"
      ["names"]=>
      array(8) {
        ["de"]=>
        string(11) "Deutschland"
        ["en"]=>
        string(7) "Germany"
        ["es"]=>
        string(8) "Alemania"
        ["fr"]=>
        string(9) "Allemagne"
        ["ja"]=>
        string(24) "ドイツ連邦共和国"
        ["pt-BR"]=>
        string(8) "Alemanha"
        ["ru"]=>
        string(16) "Германия"
        ["zh-CN"]=>
        string(6) "德国"
      }
    }
    ["location"]=>
    array(2) {
      ["latitude"]=>
      float(51)
      ["longitude"]=>
      float(9)
    }
    ["registered_country"]=>
    array(3) {
      ["geoname_id"]=>
      int(6252001)
      ["iso_code"]=>
      string(2) "US"
      ["names"]=>
      array(8) {
        ["de"]=>
        string(3) "USA"
        ["en"]=>
        string(13) "United States"
        ["es"]=>
        string(14) "Estados Unidos"
        ["fr"]=>
        string(11) "États-Unis"
        ["ja"]=>
        string(21) "アメリカ合衆国"
        ["pt-BR"]=>
        string(14) "Estados Unidos"
        ["ru"]=>
        string(6) "Сша"
        ["zh-CN"]=>
        string(6) "美国"
      }
    }
    ["traits"]=>
    array(1) {
      ["ip_address"]=>
      string(12) "47.64.121.17"
    }
  }
}

 */

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
	);
	
	// Randomizing to avoid querying the same service each time
	shuffle($ipservices);
	
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
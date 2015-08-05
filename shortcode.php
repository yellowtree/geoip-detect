<?php

/**
 * @deprecated 
 */
function geoip_detect_shortcode($attr)
{
	$userInfo = geoip_detect_get_info_from_current_ip();

	$defaultValue = isset($attr['default']) ? $attr['default'] : ''; 
	
	if (!is_object($userInfo))
		return $defaultValue . '<!-- GeoIP Detect: No info found for this IP. -->';

	$propertyName = $attr['property'];
	
	
	if (property_exists($userInfo, $propertyName)) {
		if ($userInfo->$propertyName)
			return $userInfo->$propertyName;
		else
			return $defaultValue;
	}
	
	return $defaultValue . '<!-- GeoIP Detect: Invalid property name. -->';
}
add_shortcode('geoip_detect', 'geoip_detect_shortcode');

/**
 * Short Code
 * 
 * Examples:
 * `[geoip_detect2 property="country"]` -> Germany
 * `[geoip_detect2 property="country.isoCode"]` -> de
 * 
 * `[geoip_detect2 property="country" lang="de"]` -> Deutschland
 * `[geoip_detect2 property="country" lang="fr,de"]` -> Allemagne
 * `[geoip_detect2 property="country.confidence" default="default value"]` -> default value
 * 
 * @param string $property		Property to read. Instead of '->', use '.'
 * @param string $lang			Language(s) (optional. If not set, current site language is used.)
 * @param string $default 		Default Value that will be shown if value not set (optional)
 * @param string $skipCache		if 'true': Do not cache value
 */
function geoip_detect2_shortcode($attr)
{
	$skipCache = isset($attr['skip_cache']) && (strtolower($attr['skip_cache']) == 'true' || $attr['skip_cache'] == '1');
	
	$locales = isset($attr['lang']) ? $attr['lang'] . ',en' : 'en';
	$locales = apply_filters('geoip_detect2_locales', $locales);

	$defaultValue = isset($attr['default']) ? $attr['default'] : ''; 
	
	$properties = explode('.', $attr['property']);
	
	$options = array('skipCache' => $skipCache);
	
	$userInfo = geoip_detect2_get_info_from_current_ip($locales, $options);

	if ($userInfo->isEmpty)
		return $defaultValue . '<!-- GeoIP Detect: No information found for this IP (' . geoip_detect2_get_client_ip() . ') -->';	
	
	$return = '';
	try {
		if (count($properties) == 1) {
			$return = $userInfo->{$properties[0]};
		} else if (count($properties) == 2) {
			$return = $userInfo->{$properties[0]};
			if (!is_object($return))
				throw new \RuntimeException('Invalid property name.');
			$return = $return->{$properties[1]};
		} else {
			throw new \RuntimeException('Only 1 dot supported. Please send a bug report to show me the shortcode you used if you need it ...');
		}
	} catch (\RuntimeException $e) {
		return $defaultValue . '<!-- GeoIP Detect: Invalid property name. -->';
	}

	if (is_object($return) && $return instanceof \GeoIp2\Record\AbstractPlaceRecord)
		$return = $return->name;
	
	if (is_object($return) || is_array($return)) {
		return $defaultValue . '<!-- GeoIP Detect: Invalid property name (sub-property missing). -->';
	}
	
	if ($return)
		return (string) $return;
	else
		return $defaultValue;
	
}
add_shortcode('geoip_detect2', 'geoip_detect2_shortcode');

function geoip_detect2_shortcode_client_ip($attr) {
	$client_ip = geoip_detect2_get_client_ip();
	geoip_detect_normalize_ip($client_ip);
	
	return $client_ip;
}
add_shortcode('geoip_detect2_get_client_ip', 'geoip_detect2_shortcode_client_ip');

function geoip_detect2_shortcode_get_external_ip_adress($attr) {
	$external_ip = geoip_detect2_get_external_ip_adress();
	
	return $external_ip;
}
add_shortcode('geoip_detect2_get_external_ip_adress', 'geoip_detect2_shortcode_get_external_ip_adress');

function geoip_detect2_shortcode_get_current_source_description($attr) {
	$external_ip = geoip_detect2_get_current_source_description();

	return $external_ip;
}
add_shortcode('geoip_detect2_get_current_source_description', 'geoip_detect2_shortcode_get_current_source_description');

<?php

use Guzzle\Common\Exception\RuntimeException;
add_shortcode('geoip_detect', 'geoip_detect_shortcode');
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

add_shortcode('geoip_detect2', 'geoip_detect2_shortcode');
/**
 * Short Code
 * 
 * Examples:
 * [geoip_detect2 property="country"] -> Germany
 * [geoip_detect2 property="country.isoCode"] -> de
 * 
 * [geoip_detect2 property="country" lang="de"] -> Deutschland
 * [geoip_detect2 property="country.confidence" default="default value"] -> default value
 * 
 * @param string $property		Property to read. Instead of '->', use '.'
 * @param string $lang			Language (optional. If not set, current site language is used.)
 * @param string $default 		Default Value that will be shown if value not set (optional)
 * 
 *
 */
function geoip_detect2_shortcode($attr)
{
	$locales = isset($attr['lang']) ? array_unique(array($attr['lang'], 'en')) : null;
	$locales = apply_filters('geoip_detect2_locales', $locales);
		
	$userInfo = geoip_detect2_get_info_from_current_ip($locales);

	$defaultValue = isset($attr['default']) ? $attr['default'] : ''; 
	
	$properties = explode('.', $attr['property']);
	
	
	$return = '';
	try {
		if (count($properties) == 1) {
			$return = $userInfo->{$properties[0]};
		} else if (count($properties) == 2) {
			$return = $userInfo->{$properties[0]}->{$properties[1]};
		} else if (count($properties) == 3) {
			$return = $userInfo->{$properties[0]}->{$properties[1]}->{$properties[2]};
		} else {
			throw new \RuntimeException('Only 2 dots supported. Please send a bug report to show me the shorcode you used if you need it ...');
		}
	} catch (\RuntimeException $e) {
		return $defaultValue . '<!-- GeoIP Detect: Invalid property name. -->';
	}

	if (is_object($return) && isset($return->name))
		$return = $return->name;
	
	if (is_object($return) || is_array($return))
		return $defaultValue . '<!-- GeoIP Detect: Invalid property name (sub-property missing). -->';
	
	if ($return)
		return (string) $return;
	else
		return $defaultValue;
	
}
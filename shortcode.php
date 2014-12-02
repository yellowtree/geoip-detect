<?php

add_shortcode('geoip_detect', 'geoip_detect_shortcode');
function geoip_detect_shortcode($attr)
{
	$lang = isset($attr['lang']) ? $attr['lang'] : '';
	$locales = array_unique(array($lang, 'en')); 
	
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
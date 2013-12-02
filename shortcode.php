<?php

add_shortcode('geoip_detect', 'geoip_detect_shortcode');
function geoip_detect_shortcode($attr)
{
	$userInfo = geoip_detect_get_info_from_current_ip();

	if (!is_object($userInfo))
		return '<!-- GeoIP Detect: No info found for this IP. -->';

	$propertyName = $attr['property'];
	
	if (property_exists($userInfo, $propertyName))
		return $userInfo->$propertyName;
	
	return '<!-- GeoIP Detect: Invalid property name. -->';
}
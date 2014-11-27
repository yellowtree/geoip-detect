<?php

/**
 * If no information was found, use the external IP of the server and try again.
 * This is necessary to allow local development servers to return sensical data.
 * 
 * @param geoiprecord $record Information found.
 * @return geoiprecord	Hopefully more accurate information. 
 */ 
function geoip_detect2_add_external_ip($record)
{
	static $avoid_recursion = false; // Flag in order to retry only once
	if ($avoid_recursion)
		return $record; // This is the retry with the external adress, so don't do anything

	if (!is_object($record))
	{
		$external_ip = geoip_detect2_get_external_ip_adress();

		$avoid_recursion = true;
		$record = geoip_detect2_get_info_from_ip($external_ip);
		$avoid_recursion = false;
	}
	return $record;
}
add_filter('geoip_detect2_record_information', 'geoip_detect2_add_external_ip');


add_filter('body_class', 'geoip_add_body_classes');
function geoip_add_body_classes($classes) {
	if (!get_option('geoip-detect-set_css_country'))
		return $classes;
	
	$info = geoip_detect_get_info_from_current_ip();
	if (!$info)
		return $classes;
	
	$classes[] = 'geoip-country-' . $info->country_code;
	$classes[] = 'geoip-continent-' . $info->continent_code;

	return $classes;
}

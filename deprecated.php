<?php
/**
 * @deprecated If you really need to do that manually, use the AutoDataSource-Class instead.
 */
function geoip_detect_update() {
	_doing_it_wrong('Geolocation IP Detection: geoip_detect_update', ' If you really need to do that manually, use the AutoDataSource-Class instead.', '2.4.0');
	$s = new \YellowTree\GeoipDetect\DataSources\Auto\AutoDataSource();
	return $s->maxmindUpdate();
}

/**
 * @deprecated since 2.4.0
 * @return string
 */
function geoip_detect_get_abs_db_filename()
{
	_doing_it_wrong('Geolocation IP Detection: geoip_detect_get_abs_db_filename', 'geoip_detect_get_abs_db_filename should not be called directly', '2.4.0');

	$source = \YellowTree\GeoipDetect\DataSources\DataSourceRegistry::getInstance()->getCurrentSource();
	if (is_object($reader) && method_exists($source, 'maxmindGetFilename'))
		return $source->maxmindGetFilename();
	return '';
}



/**
 * @deprecated shortcode
 */
function geoip_detect_shortcode($attr)
{
	$userInfo = geoip_detect_get_info_from_current_ip();

	$defaultValue = isset($attr['default']) ? $attr['default'] : '';

	if (!is_object($userInfo))
		return $defaultValue . '<!-- Geolocation IP Detection: No info found for this IP. -->';

	$propertyName = $attr['property'];


	if (property_exists($userInfo, $propertyName)) {
		if ($userInfo->$propertyName)
			return $userInfo->$propertyName;
		else
			return $defaultValue;
	}

	return $defaultValue . '<!-- Geolocation IP Detection: Invalid property name. -->';
}
add_shortcode('geoip_detect', 'geoip_detect_shortcode');

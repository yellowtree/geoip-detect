<?php
/**
 * @deprecated If you really need to do that manually, use the AutoDataSource-Class instead.
 */
function geoip_detect_update() {
	_doing_it_wrong('GeoIP Detection: geoip_detect_update', ' If you really need to do that manually, use the AutoDataSource-Class instead.', '2.4.0');
	$s = new \YellowTree\GeoipDetect\DataSources\Auto\AutoDataSource();
	return $s->maxmindUpdate();
}

/**
 * @deprecated since 2.4.0
 * @return string
 */
function geoip_detect_get_abs_db_filename()
{
	_doing_it_wrong('GeoIP Detection: geoip_detect_get_abs_db_filename', 'geoip_detect_get_abs_db_filename should not be called directly', '2.4.0');

	$source = \YellowTree\GeoipDetect\DataSources\DataSourceRegistry::getInstance()->getCurrentSource();
	if (method_exists($source, 'maxmindGetFilename'))
		return $source->maxmindGetFilename();
	return '';
}
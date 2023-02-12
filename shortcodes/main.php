<?php
/*
Copyright 2013-2023 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (wp-geoip-detect| |posteo.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Short Code
 *
 * Examples:
 * `[geoip_detect2 property="country"]` -> Germany
 * `[geoip_detect2 property="country.isoCode"]` -> DE
 * `[geoip_detect2 property="country.isoCode" ip="8.8.8.8"]` -> US
 *
 * `[geoip_detect2 property="country" lang="de"]` -> Deutschland
 * `[geoip_detect2 property="country" lang="fr,de"]` -> Allemagne
 * `[geoip_detect2 property="country.confidence" skip_cache="true" default="default value"]` -> default value
 *
 * @param string $property		Property to read. For a list of all possible property names, see https://github.com/yellowtree/geoip-detect/wiki/Record-Properties#list-of-all-property-names
 * @param string $lang			Language(s) (optional. If not set, current site language is used.)
 * @param string $default 		Default Value that will be shown if value not set (optional)
 * @param bool   $skip_cache	If 'true': Do not cache value (This parameter is ignored in AJAX mode)
 * @param string $ip			Lookup the data of a specific IP instead of the current client IP (this parameter does not work in AJAX mode)
 * @param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
 *
 * @since 2.5.7 New attribute `ip`
 */
function geoip_detect2_shortcode($orig_attr, $content = '', $shortcodeName = 'geoip_detect2')
{
	$attr = shortcode_atts(array(
		'skip_cache' => 'false',
		'lang' => null,
		'default' => '',
		'property' => '',
		'ip' => null,
		'add_error' => true,
	), $orig_attr, $shortcodeName);

	$shortcode_options = _geoip_detect2_shortcode_options($attr);

	
	if (geoip_detect2_shortcode_is_ajax_mode($orig_attr) && !$attr['ip']) {
		geoip_detect2_enqueue_javascript('shortcode');
		return _geoip_detect2_create_placeholder('span', [ 
			'class' => 'js-geoip-detect-shortcode' 
		], $shortcode_options);
	}
	
	$options = [ 'skipCache' => $shortcode_options['skip_cache'] ];
	
	$ip = $attr['ip'] ?: geoip_detect2_get_client_ip();
	
	$userInfo = geoip_detect2_get_info_from_ip($ip, $shortcode_options['lang'], $options);
	
	$defaultValue = esc_html($attr['default']);

	if ($userInfo->isEmpty)
		return $defaultValue . ($attr['add_error'] ? '<!-- Geolocation IP Detection: No information found for this IP (' . geoip_detect2_get_client_ip() . ') -->' : '');

	try {
		$return = geoip_detect2_shortcode_get_property($userInfo, $attr['property']);
	} catch (\RuntimeException $e) {
		return $defaultValue . ($attr['add_error'] ? '<!-- Geolocation IP Detection: Invalid property name. -->' : '');
	}

	if (is_object($return) && $return instanceof \GeoIp2\Record\AbstractPlaceRecord) {
		$return = $return->name;
	}

	if (is_object($return) || is_array($return)) {
		return $defaultValue . ($attr['add_error'] ? '<!-- Geolocation IP Detection: Invalid property name (sub-property missing). -->' : '');
	}

	if ($return)
		return (string) $return;
	else
		return $defaultValue;

}
add_shortcode('geoip_detect2', 'geoip_detect2_shortcode');

/**
 * High-level API to simplify access to property
 * @param  YellowTree\GeoipDetect\DataSources\City $userInfo     GeoIP information object
 * @param  string $propertyName property name, e.g. "city.isoCode"
 * @return string 
 */
function geoip_detect2_shortcode_get_property_simplified($userInfo, $propertyName, $defaultValue = '') {
	try {
		$return = geoip_detect2_shortcode_get_property($userInfo, $propertyName);
	} catch (\RuntimeException $e) {
		if (GEOIP_DETECT_DEBUG) {
			trigger_error('Undefined property `' . $propertyName . '`');
		}
		$return = $defaultValue;
	}

	if (is_object($return) && $return instanceof \GeoIp2\Record\AbstractPlaceRecord) {
		$return = $return->name;
	}

	if ($return) {
		return (string) $return;
	} else {
		return $defaultValue;
	}
}

/**
 * Get property from object by string
 * @param  YellowTree\GeoipDetect\DataSources\City $userInfo     GeoIP information object
 * @param  string $propertyName property name, e.g. "city.isoCode"
 * @return string|\GeoIp2\Record\AbstractRecord             Property Value
 * @throws \RuntimeException (if Property name invalid)
 */
function geoip_detect2_shortcode_get_property($userInfo, $propertyName) {

	$propertyAccessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessorBuilder()
    	->enableExceptionOnInvalidIndex()
    	->getPropertyAccessor();

	if (str_starts_with($propertyName, 'extra.original.')) {
		$properties = explode('.', $propertyName);
		$properties = array_slice($properties, 2);
		$propertyName = 'extra.original[' . implode('][', $properties) . ']';
	}

	// subdivisions.0.isoCode -> subdivisions[0].isoCode
	$propertyName = preg_replace('/\.([0-9])/', '[$1]', $propertyName);

	try {
		return $propertyAccessor->getValue($userInfo, $propertyName);
	} catch(\Exception $e) {
		throw new \RuntimeException('Invalid property name.');
	}
}

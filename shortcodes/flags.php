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

// This needs the Plugin "SVG Flags" to work!

/**
 * Simple use:
 * 
 * [geoip_detect2_current_flag]
 * 
 * All possible parameters:
 * 
 * [geoip_detect2_current_flag height="10% !important", width="30" class="extra-flag-class" squared="0" default="it" ajax="0"]
 * 
 * @param int|string width   CSS Width of the flag `<span>`-Element (in Pixels or CSS including unit)
 * @param int|string height  CSS Height of the flag `<span>`-Element (in Pixels or CSS including unit)
 * @param int squared	     Instead of being 4:3, the flag should be 1:1 in ratio
 * @param string $class 	 Extra CSS Class of element. All flags will have the class `flag-icon` anyway.
 * @param string $default 	 Default Country in case the visitor's country cannot be determined
 */
function geoip_detect2_shortcode_current_flag($orig_attr, $content = '', $shortcodeName = 'geoip_detect2_current_flag') {
	if (!shortcode_exists('svg-flag') && !defined('GEOIP_DETECT_DOING_UNIT_TESTS')) {
		return '<!-- There should be a flag here. However, the Plugin "SVG Flags" is missing.';
	}

	$attr = shortcode_atts(array(
		'width' => '',
		'height' => '',
		'squared' => '',
		'square' => '',
		'class' => '',
		'default' => '',
		'skip_cache' => false,
		'ajax' => false,
	), $orig_attr, $shortcodeName);

	$skipCache = filter_var($attr['skip_cache'], FILTER_VALIDATE_BOOLEAN );
	$options = [ 'skipCache' => $skipCache ];

	$style = '';
	$processCssProperty = function($name, $value) {
		$value = strtr($value, [' ' => '', ':' => '', ';' => '']);
		if (!$value) {
			return '';
		}
		if (is_numeric($value)) {
			$value .= 'px';
		}
		return $name . ':' . $value . ';';
	};
	$style .= $processCssProperty('height', $attr['height']);
	$style .= $processCssProperty('width', $attr['width']);

	if ($attr['squared'] || $attr['square']) {
		$attr['class'] .= ' flag-icon-squared';
	}

	$attr['class'] .= ' flag-icon';

	$options = [];
	if (geoip_detect2_shortcode_is_ajax_mode($orig_attr)) {
		geoip_detect2_enqueue_javascript('shortcode');
		$attr['class'] .= ' js-geoip-detect-flag';
		$options['default'] = $attr['default'];
	} else {
		$record = geoip_detect2_get_info_from_current_ip(null, $options);
		$country = $attr['default'];
		if ($record->country->isoCode) {
			$country = $record->country->isoCode;
		}
		if (!$country) {
			return '<!-- There should be a flag here, but no country could be detected and the parameter "default" was not set. -->';
		}
		$country = mb_substr($country, 0, 2);
		$country = mb_strtolower($country);

		$attr['class'] .= ' flag-icon-' . $country;
	}

	return _geoip_detect2_create_placeholder('span', [
		'style' => $style,
		'class' => $attr['class'],
	], $options);
}
add_shortcode('geoip_detect2_current_flag', 'geoip_detect2_shortcode_current_flag');
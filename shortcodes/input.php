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
 * Generating an <input />-field that has a geoip value as default
 * 
 * Property can be: continent, country, city, postal.code or any other property understood by `geoip_detect2_get_info_from_ip`
 * 
 * Examples:
 *
 * `[geoip_detect2_text_input name="city" property="city" lang="fr" id="id" class="class"]`
 * A text input that has the detetected city as default (with CSS id "#id" and class ".class")
 *
 * `[geoip_detect2_text_input name="city" property="city" lang="fr" id="id" class="class" default="Paris"]`
 * As above, but in case the city is unknown, use "Paris"
 * 
 * `[geoip_detect2_text_input name="postal" property="postal.code" type="hidden"]`
 * An invisible text input containing the postal code. 
 *
 * $attr is an array that can have these properties:
 * @param string $property Maxmind property string (e.g. "city" or "postal.code")
 * @param string $name Name of the form element
 * @param bool   $required If the field is required or not
 * @param string $id CSS Id of element
 * @param string $class CSS Class of element
 * @param string $type HTML input type of element ("text" by default) (@since 3.1.2)
 * @param string $lang Language(s) (optional. If not set, current site language is used.)
 * @param string $default 		Default Value that will be used if country cannot be detected (optional)
 * @param bool 	 $skip_cache    If 'true': Do not cache value (This parameter is ignored in AJAX mode)
 * @param string $ip            Lookup the data of a specific IP instead of the current client IP (this parameter does not work in AJAX mode)
 * @param string $placeholder	HZML attribute "plaecholer"
 * @param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
 * @param bool   $autosave      1: In Ajax mode, when the user changes the country, save his choice in his browser. (optional, Ajax mode only)
 *
 * @return string The generated HTML
 */
function geoip_detect2_shortcode_text_input($attr) {
	$type = !empty($attr['type']) ? sanitize_key($attr['type']) : '';

	$html_attrs = array(
		'name' => !empty($attr['name']) ? $attr['name'] : 'geoip-text-input',
		'id' => !empty($attr['id']) ? $attr['id'] : '',
		'class' => !empty($attr['class']) ? $attr['class'] : 'geoip-text-input',
		'type' => $type ? $type : 'text',
		'aria-required' => !empty($attr['required']) ? 'required' : '',
		'aria-invalid' => !empty($attr['invalid']) ? $attr['invalid'] : '',
		'placeholder' => !empty($attr['placeholder']) ? $attr['placeholder'] : '',
	);

	if (geoip_detect2_shortcode_is_ajax_mode($attr)) {
		geoip_detect2_enqueue_javascript('shortcode');
		$html_attrs['class'] .= ' js-geoip-text-input';
		if (!empty($attr['autosave'])) {
			$html_attrs['class'] .= ' js-geoip-detect-input-autosave';
		}
		$html_attrs['data-options'] = wp_json_encode(_geoip_detect2_shortcode_options($attr));
	} else {
		$html_attrs['value'] = geoip_detect2_shortcode($attr + [ 'add_error' => false ]);
	}

	$html = '<input ' . _geoip_detect_flatten_html_attr($html_attrs) . '/>';
	return $html;
}
add_shortcode('geoip_detect2_text_input', 'geoip_detect2_shortcode_text_input');
add_shortcode('geoip_detect2_input', 'geoip_detect2_shortcode_text_input');

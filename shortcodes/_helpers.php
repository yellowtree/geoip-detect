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
 * Prepare the options
 */
function _geoip_detect2_shortcode_options($attr) {
	$locales = isset($attr['lang']) ? $attr['lang'] . ',en' : null;
	$locales = apply_filters('geoip_detect2_locales', $locales);

	$opt = [
			'skip_cache' => isset($attr['skip_cache']) ? filter_var($attr['skip_cache'], FILTER_VALIDATE_BOOLEAN ) : false,
			'lang' => $locales,
			'default' =>  isset($attr['default']) ? $attr['default'] : '',
	];
	if (isset($attr['property'])) {
		$opt['property'] = $attr['property'];
	}

	return $opt;
}


function _geoip_detect_flatten_html_attr($attr) {
	$html = '';
	foreach ($attr as $key => $value) {
		if ($value)
			$html .= $key . '="' . esc_attr($value) . '" ';
	}
	return $html;
}


// ----------------------- AJAX support --------------------------------

/**
 * Shortcodes can be executed on the server or via AJAX. Which mode should be used?
 * 
 * If the shortcode has a property called "ajax", then use that.
 * Otherwise check if AJAX is enabled globally, and the use of shortcodes as well.
 */
function geoip_detect2_shortcode_is_ajax_mode($attr) {
	if (isset($attr['ajax'])) {
		$value = filter_var($attr['ajax'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
		if (is_bool($value)) {
			return $value;
		}
	}

	if (get_option('geoip-detect-ajax_enabled') && get_option('geoip-detect-ajax_shortcodes')) {
		return true;
	}
	return false;
}

function _geoip_detect2_html_contains_block_elements($html) {
	if (!$html) {
		return false;
	}
	$html = mb_strtolower($html);

	// There are more. But these are most common
	$blocklevelElements = [
		'div',
		'p',
		'blockquote',
		'figure',
		'form',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'ul',
		'ol',
		'pre',
		'table',
	];
	foreach ($blocklevelElements as $element) {
		if (str_contains($html, '<' . $element)) {
			if (preg_match('#<' . $element . '[\s/>]#', $html)) {
				return true;
			}
		}
	}
	return false;
}

function _geoip_detect2_create_placeholder($tag = "span", $attr = [], $data = null, $innerHTML = '') {
	if ($tag === 'span' && _geoip_detect2_html_contains_block_elements($innerHTML)) {
		$tag = 'div';
	}

	$tag = sanitize_key($tag);
	$html = "<$tag";

	if ($data) {
		$attr['data-options'] = wp_json_encode($data);
	}
	if ($attr) {
		$html .= ' ' . _geoip_detect_flatten_html_attr($attr);
	}
	$html .= ">$innerHTML</$tag>";

	return $html;
}
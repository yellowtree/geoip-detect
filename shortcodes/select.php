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
 * Create a <select>-Input element with all countries.
 *
 * Examples:
 * `[geoip_detect2_countries_select name="mycountry" lang="fr"]`
 * A list of all country names in French, the visitor's country is preselected.
 *
 * `[geoip_detect2_countries_select id="id" class="class" name="mycountry" lang="fr"]`
 * As above, with CSS id "#id" and class ".class"
 *
 * `[geoip_detect2_countries_select name="mycountry" include_blank="true"]`
 * Country names are in the current site language. User can also choose '---' for no country at all.
 *
 * `[geoip_detect2_countries_select name="mycountry" selected="US"]`
 * "United States" is preselected, there is no visitor IP detection going on here
 *
 * `[geoip_detect2_countries_select name="mycountry" default="US"]`
 * Visitor's country is preselected, but in case the country is unknown, use "United States"
 *
 * $attr is an array that can have these properties:
 * @param string $name Name of the form element
 * @param string $id CSS Id of element
 * @param bool   $required If the field is required or not
 * @param string $class CSS Class of element
 * @param string $lang Language(s) (optional. If not set, current site language is used.)
 * @param string $selected      Which country to select by default (2-letter ISO code.) (optional. If not set, the country will be detected by client ip.) (This parameter does not work with AJAX mode.)
 * @param string $default 		Default Value that will be used if country cannot be detected (optional)
 * @param string $include_blank If this value contains 'true', a empty value will be prepended ('---', i.e. no country) (optional)
 * @param bool   $flag          If a flag should be added before the country name (In Windows, there are no flags, ISO-Country codes instead. This is a design choice by Windows.)
 * @param bool   $tel           If the international code should be added after the country name
 * @param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
 * @param bool   $autosave      1: In Ajax mode, when the user changes the country, save his choice in his browser. (optional, Ajax mode only)
 * @param string $list			If used, only these countries will be shown. E.g. "it,de,uk" (optional)
 *
 * @return string The generated HTML
 */
function geoip_detect2_shortcode_country_select($attr) {
	$attr['property'] = 'country.name';
	$shortcode_options = _geoip_detect2_shortcode_options($attr);

	$select_attrs = [
		'name' =>  			!empty($attr['name'])  		? $attr['name'] 	: 'geoip-countries',
		'id' =>    			!empty($attr['id'])    		? $attr['id'] 		: '',
		'class' => 			!empty($attr['class']) 		? $attr['class'] 	: 'geoip_detect2_countries',
		'aria-required' => 	!empty($attr['required']) 	? 'required' 		: '',
		'aria-invalid' => 	!empty($attr['invalid']) 	? $attr['invalid'] 	: '',
		'autocomplete' => 'off',
	];

	$selected = '';
	if (geoip_detect2_shortcode_is_ajax_mode($attr) && !isset($attr['selected']) ) {
		geoip_detect2_enqueue_javascript('shortcode');
		$select_attrs['class'] .= ' js-geoip-detect-country-select';
		if (!empty($attr['autosave'])) {
			$select_attrs['class'] .= ' js-geoip-detect-input-autosave';
		}
		$select_attrs['data-options'] = wp_json_encode($shortcode_options);
	} else {
		if (!empty($attr['selected'])) {
			$selected = $attr['selected'];
		} else {
			$record = geoip_detect2_get_info_from_current_ip();
			$selected = $record->country->isoCode;
		}
		if (empty($selected)) {
			if (isset($attr['default']))
				$selected = $attr['default'];
		}
	}


	
	$countryInfo = new YellowTree\GeoipDetect\Geonames\CountryInformation();
	$countries = $countryInfo->getAllCountries($shortcode_options['lang']);

	if (!empty($attr['list'])) {
		$list = wp_parse_list($attr['list']);

		$allCountries = $countries;

		$countries = [];
		foreach($list as $key) {
			if (str_starts_with($key,'blank_')) {
				$countries[$key] = str_replace('_', ' ', mb_substr($key, 6));
			} else {
				$key = mb_strtoupper($key);
				if (isset($allCountries[$key])) {
					$countries[$key] = $allCountries[$key];
				}
			}
		}

		if ($selected && !isset($countries[$selected])) {
			if (isset($attr['default'])) {
				$selected = '';
			}
		}
	}
	
	if (!empty($attr['flag'])) {
		array_walk($countries, function(&$value, $key) use($countryInfo) {
			$flag = $countryInfo->getFlagEmoji($key);
			$value = $flag . ' ' . $value;
		});
	}
	
	if (!empty($attr['tel'])) {
		array_walk($countries, function(&$value, $key) use($countryInfo) {
			$tel = $countryInfo->getTelephonePrefix($key);
			if ($tel) {
				$value = $value . ' (' . $tel . ')';
			}
		});
	}
	
	/**
	 * Filter: geoip_detect2_shortcode_country_select_countries
	 * Change the list of countries that should show up in the select box.
	 * You can add, remove, reorder countries at will.
	 * If you want to add a blank value (for seperators or so), use a key name that starts with 'blank_'
	 * and then something at will in case you need several of them.
	 *
	 * @param array $countries	List of localized country names
	 * @param array $attr		Parameters that were passed to the shortcode
	 * @return array
	 */
	$countries = apply_filters('geoip_detect2_shortcode_country_select_countries', $countries, $attr);

	$html = '<select ' . _geoip_detect_flatten_html_attr($select_attrs) . '>';
	if (!empty($attr['include_blank']) && $attr['include_blank'] !== 'false') {
		$html .= '<option value="">---</option>';
	}
	foreach ($countries as $code => $label) {
		$code = mb_strtolower($code);
		if (str_starts_with($code,'blank_'))
		{
			$html .= '<option data-c="" value="">' . esc_html($label) . '</option>';
		}
		else
		{
			$html .= '<option data-c="' . esc_attr($code).  '"' . ($code == mb_strtolower($selected) ? ' selected="selected"' : '') . '>' . esc_html($label) . '</option>';
		}
	}
	$html .= '</select>';

	return $html;
}
add_shortcode('geoip_detect2_countries_select', 'geoip_detect2_shortcode_country_select');
add_shortcode('geoip_detect2_countries', 'geoip_detect2_shortcode_country_select');

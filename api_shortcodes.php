<?php
/*
Copyright 2013-2021 Yellow Tree, Siegen, Germany
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
		return _geoip_detect2_create_placeholder('span', [ 'class' => 'js-geoip-detect-shortcode' ], $shortcode_options);
	}
	
	$options = array('skipCache' => $shortcode_options['skip_cache']);
	
	$ip = $attr['ip'] ?: geoip_detect2_get_client_ip();
	
	$userInfo = geoip_detect2_get_info_from_ip($ip, $shortcode_options['lang'], $options);
	
	$defaultValue = $attr['default'];

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
 * Get property from object by string
 * @param  YellowTree\GeoipDetect\DataSources\City $userInfo     GeoIP information object
 * @param  string $propertyName property name, e.g. "city.isoCode"
 * @return string|\GeoIp2\Record\AbstractRecord             Property Value
 * @throws \RuntimeException (if Property name invalid)
 */
function geoip_detect2_shortcode_get_property($userInfo, $propertyName) {
/*
	if (version_compare ( PHP_VERSION, '7.2.5', '<' )) {
		return _deprecated_geoip_detect2_shortcode_get_property($userInfo, $propertyName);	
	}
*/
	$propertyAccessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessorBuilder()
    	->enableExceptionOnInvalidIndex()
    	->getPropertyAccessor();

	if (_geoip_str_begins_with($propertyName, 'extra.original.')) {
		$properties = explode('.', $propertyName);
		$properties = array_slice($properties, 2);
		$propertyName = 'extra.original[' . implode($properties, '][') . ']';
	}

	// subdivisions.0.isoCode -> subdivisions[0].isoCode
	$propertyName = preg_replace('/\.([0-9])/', '[$1]', $propertyName);

	try {
		return $propertyAccessor->getValue($userInfo, $propertyName);
	} catch(\Exception $e) {
		throw new \RuntimeException('Invalid property name.');
	}
}

/**
 * Get property from object by string (old version, deprecated)
 * Remove when PHP <= 7.1 not supported anymore (EOL has passed end 2019, but Maxmind lib is still compatible 5.6)
 * 
 * @param  YellowTree\GeoipDetect\DataSources\City $userInfo     GeoIP information object
 * @param  string $propertyName property name, e.g. "city.isoCode"
 * @return string|\GeoIp2\Record\AbstractRecord             Property Value
 * @throws \RuntimeException (if Property name invalid)
 *
function _deprecated_geoip_detect2_shortcode_get_property($userInfo, $propertyName) {
	$return = '';
	$properties = explode('.', $propertyName);
	if (count($properties) == 1) {
		$return = $userInfo->{$properties[0]};
	} else if ($properties[0] == 'subdivisions' && (count($properties) == 2 || count($properties) == 3)) {
		$return = $userInfo->{$properties[0]};
		if (!is_array($return))
			throw new \RuntimeException('Invalid property name.');
		if (!is_numeric($properties[1]))
			throw new \RuntimeException('Invalid property name (must be numeric, e.g. "subdivisions.0").');
		$return = $return[(int) $properties[1]];

		if (isset($properties[2])) {
			if (!is_object($return))
				throw new \RuntimeException('Invalid property name.');
			$return = $return->{$properties[2]};
		}
	} else if (count($properties) == 2) {
		$return = $userInfo->{$properties[0]};
		if (!is_object($return))
		throw new \RuntimeException('Invalid property name.');
		$return = $return->{$properties[1]};
	} else {
		throw new \RuntimeException('Only 1 dot supported. Please send a bug report to show me the shortcode you used if you need it ...');
	}
	return $return;
}
*/

/**
 * Get the client ip
 * IPv4 or IPv6-Adress of the client. This takes reverse proxies into account, if they are configured on the options page.
 * 
 * [geoip_detect2_get_client_ip]
 * 
 * @param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
 * 
 * @since 2.5.2 
 */
function geoip_detect2_shortcode_client_ip($attr) {
	if (geoip_detect2_shortcode_is_ajax_mode($attr)) {
		return geoip_detect2_shortcode([
			'property' => 'traits.ipAddress',
			'ajax' => isset($attr['ajax']) ? $attr['ajax'] : null,
		]);
	} else {
		$client_ip = geoip_detect2_get_client_ip();
		$client_ip = geoip_detect_normalize_ip($client_ip);
	
		return $client_ip;
	}
}
add_shortcode('geoip_detect2_get_client_ip', 'geoip_detect2_shortcode_client_ip');

function geoip_detect2_shortcode_get_external_ip_adress($attr) {
	$external_ip = geoip_detect2_get_external_ip_adress();
	$external_ip = geoip_detect_normalize_ip($external_ip);

	return $external_ip;
}
add_shortcode('geoip_detect2_get_external_ip_adress', 'geoip_detect2_shortcode_get_external_ip_adress');

function geoip_detect2_shortcode_get_current_source_description() {
	$return = geoip_detect2_get_current_source_description();

	return $return;
}
add_shortcode('geoip_detect2_get_current_source_description', 'geoip_detect2_shortcode_get_current_source_description');

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
 * @param string $selected Which country to select by default (2-letter ISO code.) (optional. If not set, the country will be detected by client ip.) (This parameter does not work with AJAX mode.)
 * @param string $default 		Default Value that will be used if country cannot be detected (optional)
 * @param string $include_blank If this value contains 'true', a empty value will be prepended ('---', i.e. no country) (optional)
 * @param bool   $flag          If a flag should be added before the country name (In Windows, there are no flags, ISO-Country codes instead. This is a design choice by Windows.)
 * @param bool   $tel           If the international code should be added after the country name
 * @param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
 *
 * @return string The generated HTML
 */
function geoip_detect2_shortcode_country_select($attr) {
	$shortcode_options = _geoip_detect2_shortcode_options($attr);

	$select_attrs = array(
		'name' =>  !empty($attr['name']) ? $attr['name'] : 'geoip-countries',
		'id' =>    !empty($attr['id']) ? $attr['id'] : '',
		'class' => !empty($attr['class']) ? $attr['class'] : 'geoip_detect2_countries',
		'aria-required' => !empty($attr['required']) ? 'required' : '',
		'aria-invalid' => !empty($attr['invalid']) ? $attr['invalid'] : '',
		'autocomplete' => 'off',
	);

	$selected = '';
	if (geoip_detect2_shortcode_is_ajax_mode($attr) && !isset($attr['selected']) ) {
		geoip_detect2_enqueue_javascript('shortcode');
		$select_attrs['class'] .= ' js-geoip-detect-country-select';
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
	if (!empty($attr['include_blank']) && $attr['include_blank'] !== 'false')
		$html .= '<option value="">---</option>';
	foreach ($countries as $code => $label) {
		if (substr($code, 0, 6) == 'blank_')
		{
			$html .= '<option data-c="" value="">' . esc_html($label) . '</option>';
		}
		else
		{
			$html .= '<option data-c="' . esc_attr(mb_strtolower($code)).  '"' . ($code == $selected ? ' selected="selected"' : '') . '>' . esc_html($label) . '</option>';
		}
	}
	$html .= '</select>';

	return $html;
}
add_shortcode('geoip_detect2_countries_select', 'geoip_detect2_shortcode_country_select');
add_shortcode('geoip_detect2_countries', 'geoip_detect2_shortcode_country_select');

function _geoip_detect_flatten_html_attr($attr) {
	$html = '';
	foreach ($attr as $key => $value) {
		if ($value)
			$html .= $key . '="' . esc_attr($value) . '" ';
	}
	return $html;
}

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
		$html_attrs['data-options'] = wp_json_encode(_geoip_detect2_shortcode_options($attr));
	} else {
		$html_attrs['value'] = geoip_detect2_shortcode($attr + array('add_error' => false));
	}

	$html = '<input ' . _geoip_detect_flatten_html_attr($html_attrs) . '/>';
	return $html;
}
add_shortcode('geoip_detect2_text_input', 'geoip_detect2_shortcode_text_input');
add_shortcode('geoip_detect2_input', 'geoip_detect2_shortcode_text_input');

/**
 * Just in case somebody really wants to use this shortcode outside of cf7
 */
function geoip_detect_shortcode_user_info() {
    return geoip_detect2_shortcode_user_info_wpcf7('', 'geoip_detect2_user_info', true);
}
add_shortcode('geoip_detect2_user_info', 'geoip_detect_shortcode_user_info');

/**
 *
 * Geo-Dependent Content Hiding
 *
 * Uses an enclosing shortcode to selectively show or hide content. Use either
 * [geoip_detect2_show_if][/geoip_detect2_show_if] or [geoip_detect2_hide_if][/geoip_detect2_hide_if] at your
 * discretion, as they can both be used to accomplish the same thing.
 *
 * Shortcode attributes can be as follows:
 *
 * Inclusive Attributes (note that `hide_if` makes them exclusive):
 *      "continent", "country", "most_specific_subdivision"/"region"/"state"*, "city"
 *
 * * most_specific_subdivision, region, and state are aliases (use the one that makes the most sense to you)
 *
 * Exclusive Attributes (note that `hide_if` makes them inclusive):
 *      "not_country", "not_most_specific_subdivision"/"not_region"/"not_state"*, "not_city"
 *
 * * most_specific_subdivision, region, and state are aliases (use the one that makes the most sense to you)
 *
 * Each attribute may only appear once in a shortcode!
 * The location attributes can take each take full names, ISO abbreviations (e.g., US), or the GeonamesId.
 * All attributes may take multiple values seperated by comma (,).
 *
 * You can use custom property names with the attribute "property" and "property_value" / "not_property_value".
 *
 * Examples:
 *
 * Display TEXT if the visitor is in the US and in Texas.
 *      `[geoip_detect2_show_if country="US" state="TX"]TEXT[/geoip_detect2_show_if]`
 * 	        - OR -
 *      `[geoip_detect2_show_if country="US" region="TX"]TEXT[/geoip_detect2_show_if]`
 * 	        - OR -
 *      `[geoip_detect2_show_if country="US" region="Texas"]TEXT[/geoip_detect2_show_if]`
 *          - OR -
 *      `[geoip_detect2_show_if country="US" most_specific_subdivision="TX"]TEXT[/geoip_detect2_show_if]`
 *
 * Display TEXT if the visitor is in the US, and in either Texas or Louisiana, but hide this content
 * from visitors with IP addresses from cities named Houston.
 *      `[geoip_detect2_show_if country="US" state="TX, LA" not_city="Houston"]TEXT[/geoip_detect2_show_if]`
 *
 * Display TEXT if the visitor is from North America.
 *      `[geoip_detect2_show_if continent="North America"]TEXT[/geoip_detect2_show_if]`
 *          - OR -
 *      `[geoip_detect2_hide_if not_continent="North America"]TEXT[/geoip_detect2_hide_if]`
 *
 * Hide TEXT if the visitor is from the US.
 *      `[geoip_detect2_hide_if country="US"]TEXT[/geoip_detect2_hide_if]`
 *          - OR -
 *      `[geoip_detect2_show_if not_country="US"]TEXT[/geoip_detect2_show_if]`
 *
 * Show TEXT if the visitor is within the timezone Europe/Berlin
 *      `[geoip_detect2_show_if property="location.timeZone" property_value="Europe/Berlin"]TEXT[/geoip_detect2_show_if]`
 * 
 * Show TEXT if the visitor is in the european union
 * 		`[geoip_detect2_show_if property="country.isInEuropeanUnion" property_value="true"]TEXT[/geoip_detect2_show_if]`
 *
 * LIMITATIONS:
 * - You cannot nest several of these shortcodes within one another. Instead, seperate them into several blocks of shortcodes.
 * - City names can be ambigous. For example, [geoip_detect2_show_if country="US,FR" not_city="Paris"] will exclude both Paris in France and Paris in Texas, US. Instead, you can find out the geoname_id or seperate the shortcode to make it more specific.
 *
 */
function geoip_detect2_shortcode_show_if($attr, $content = null, $shortcodeName = '') {
    $showContentIfMatch = ($shortcodeName == 'geoip_detect2_show_if') ? true : false;

	/* Attribute Conditions. Order is not important, as they are combined with a transitive AND condition */
	$attributeNames = array(
        'continent' => 'continent',
		'not_continent' => 'continent',
		
        'country' => 'country',
		'not_country' => 'country',
		
		'most_specific_subdivision' => 'mostSpecificSubdivision',
        'region' => 'mostSpecificSubdivision',
        'state' => 'mostSpecificSubdivision',
        'not_most_specific_subdivision' => 'mostSpecificSubdivision',
        'not_region' => 'mostSpecificSubdivision',
        'not_state' => 'mostSpecificSubdivision',
		
		'city' => 'city',
        'not_city' => 'city',
	);

	$attrDefaults = array(
		'lang' => null,
		'skip_cache' => 'false',
        'property' => null,
        'property_value' => null,
        'not_property_value' => null,
	);
	$attrDefaults = array_merge($attrDefaults,  array_fill_keys(array_keys($attributeNames), null));

    $attr = shortcode_atts($attrDefaults, $attr, $shortcodeName);

	$shortcode_options = _geoip_detect2_shortcode_options($attr);

	$options = array('skipCache' => $shortcode_options['skip_cache']);

	$info = geoip_detect2_get_info_from_current_ip($shortcode_options['lang'], $options);
	
	/**
	 * You can override the detected location information here.
	 * E.g. "Show if in Paris, but if the user has given an adress in his profile, use that city instead"
	 * (Does not work in AJAX mode)
	 * 
	 * @param YellowTree\GeoipDetect\DataSources\City $info
	 * @param array $attr Shortcode attributes given to the function.
	 * @param bool $showContentIfMatch Should the content be shown (TRUE) or hidden (FALSE) if the conditions are true?
	 */
	$info = apply_filters('geoip_detect2_shortcode_show_if_ip_info_override', $info, $attr, $showContentIfMatch);

	$isConditionMatching = true;
	
	/** ToDo Compiling the conditions to an array first:
	 * 
	 * [
	 * 	 "expected": true, // false if hide_if 
	 *   "operator": "AND" // OR not supported yet
	 *   "conditions": [
	 *      [ 
	 * 			"property": "city", 
	 * 			"values": "Paris,Berlin", 
	 * 			"expected": true ] // false if not operator
	 *   ]
	 * ]
	 */

	foreach ($attributeNames as $shortcodeParamName => $maxmindName) {
		if (!empty($attr[$shortcodeParamName])) {
            // Determine actual MaxMind Value(s) for Attribute
			$actualValues = array();
			$alternativePropertyNames = array(
					'name',
					'isoCode',
					'code',
					'geonameId',
			);
			foreach ($alternativePropertyNames as $p) {
				if (isset($info->{$maxmindName}->{$p})) {
					$actualValues[] = $info->{$maxmindName}->{$p};
				}
			}

			$subConditionMatching = geoip_detect2_shortcode_check_subcondition($attr[$shortcodeParamName], $actualValues);

			if (substr($shortcodeParamName, 0, 4) == 'not_') {
				$subConditionMatching = !$subConditionMatching;
			}
			$isConditionMatching = $isConditionMatching && $subConditionMatching;
		}
	}

	// Custom property
	if (!empty($attr['property']) && (!empty($attr['property_value']) || !empty($attr['not_property_value'])) ) {
		$subConditionMatching = false;
		try {
			$actualValue = geoip_detect2_shortcode_get_property($info, $attr['property']);

			if (!empty($attr['property_value'])) {
				$subConditionMatching = geoip_detect2_shortcode_check_subcondition($attr['property_value'], $actualValue);
			}
			if (!empty($attr['not_property_value'])) {
				$subConditionMatching = ! geoip_detect2_shortcode_check_subcondition($attr['not_property_value'], $actualValue);
			}
		} catch (\Exception $e) {
			// Invalid Property or so... ignore.
		}

		$isConditionMatching = $isConditionMatching && $subConditionMatching;
	}

    // All Criteria Passed?
    if ($isConditionMatching === $showContentIfMatch) {
        return do_shortcode($content);
    }
	return '';
}
add_shortcode('geoip_detect2_show_if', 'geoip_detect2_shortcode_show_if');
add_shortcode('geoip_detect2_hide_if', 'geoip_detect2_shortcode_show_if');

function geoip_detect2_shortcode_check_subcondition($expectedValuesRaw, $actualValues) {
	// Parse User Input Values of Attribute
	$attributeValuesArray = explode(',', $expectedValuesRaw);
	$attributeValuesArray = array_map('trim', $attributeValuesArray);

	if ($actualValues === true) {
		$actualValues = array("true", "yes", "y", "1");
	}
	if ($actualValues === false) {
		$actualValues = array("false", "no", "n", "0", "");
	}

	if (!is_array($actualValues)) {
		$actualValues = array($actualValues);
	}

	// Compare case-insensitively
	$attributeValuesArray = array_map('mb_strtolower', $attributeValuesArray);
	$actualValues = array_map('mb_strtolower', $actualValues);

	$intersection = array_intersect($actualValues, $attributeValuesArray);

	return count($intersection) > 0;
}


// ----------------------------------- Flags - This needs the Plugin "SVG Flags" to work ---------------------

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
	$options = array('skipCache' => $skipCache);

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

// ----------------------- AJAX support --------------------------------

/**
 * Shortcodes can be executed on the server or via AJAX. Which mode should be used?
 * 
 * If the shortcode has a property called "ajax", then use that.
 * Otherwise check if AJAX is enabled globally, and the use of shortcodes as well.
 */
function geoip_detect2_shortcode_is_ajax_mode($attr) {
	if (isset($attr['ajax'])) {
		$value = filter_var($attr['ajax'], FILTER_VALIDATE_BOOLEAN );
		return $value;
	}

	if (get_option('geoip-detect-ajax_enabled') && get_option('geoip-detect-ajax_shortcodes')) {
		return true;
	}
	return false;
}

function geoip_detect2_shortcode_enqueue_javascript() {
	geoip_detect2_enqueue_javascript('user_shortcode');
	return '';
}
add_shortcode('geoip_detect2_enqueue_javascript', 'geoip_detect2_shortcode_enqueue_javascript');

function _geoip_detect2_create_placeholder($tag = "span", $attr = [], $data = null) {
	$tag = sanitize_key($tag);
	$html = "<$tag";

	if ($data) {
		$attr['data-options'] = wp_json_encode($data);
	}
	if ($attr) {
		$html .= ' ' . _geoip_detect_flatten_html_attr($attr);
	}
	$html .= "></$tag>";

	return $html;
}
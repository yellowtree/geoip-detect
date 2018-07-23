<?php
/*
Copyright 2013-2018 Yellow Tree, Siegen, Germany
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
 * @deprecated
 */
function geoip_detect_shortcode($attr)
{
	$userInfo = geoip_detect_get_info_from_current_ip();

	$defaultValue = isset($attr['default']) ? $attr['default'] : '';

	if (!is_object($userInfo))
		return $defaultValue . '<!-- GeoIP Detect: No info found for this IP. -->';

	$propertyName = $attr['property'];


	if (property_exists($userInfo, $propertyName)) {
		if ($userInfo->$propertyName)
			return $userInfo->$propertyName;
		else
			return $defaultValue;
	}

	return $defaultValue . '<!-- GeoIP Detect: Invalid property name. -->';
}
add_shortcode('geoip_detect', 'geoip_detect_shortcode');

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
 * @param string $property		Property to read. Instead of '->', use '.'
 * @param string $lang			Language(s) (optional. If not set, current site language is used.)
 * @param string $default 		Default Value that will be shown if value not set (optional)
 * @param string $skip_cache		if 'true': Do not cache value
 *
 * @since 2.5.7 New attribute `ip`
 */
function geoip_detect2_shortcode($attr, $content, $shortcodeName)
{
	$attr = shortcode_atts(array(
		'skip_cache' => 'false',
		'lang' => null,
		'default' => '',
		'property' => '',
		'ip' => null,
	), $attr, $shortcodeName);

	$skipCache = filter_var($attr['skip_cache'], FILTER_VALIDATE_BOOLEAN );

	$locales = isset($attr['lang']) ? $attr['lang'] . ',en' : null;
	$locales = apply_filters('geoip_detect2_locales', $locales);

	$defaultValue = $attr['default'];

	$options = array('skipCache' => $skipCache);

	$ip = $attr['ip'] ?: geoip_detect2_get_client_ip();

	$userInfo = geoip_detect2_get_info_from_ip($ip, $locales, $options);

	if ($userInfo->isEmpty)
		return $defaultValue . '<!-- GeoIP Detect: No information found for this IP (' . geoip_detect2_get_client_ip() . ') -->';

	try {
		$return = geoip_detect2_shortcode_get_property($userInfo, $attr['property']);
	} catch (\RuntimeException $e) {
		return $defaultValue . '<!-- GeoIP Detect: Invalid property name. -->';
	}

	if (is_object($return) && $return instanceof \GeoIp2\Record\AbstractPlaceRecord) {
		$return = $return->name;
	}

	if (is_object($return) || is_array($return)) {
		return $defaultValue . '<!-- GeoIP Detect: Invalid property name (sub-property missing). -->';
	}

	if ($return)
		return (string) $return;
	else
		return $defaultValue;

}
add_shortcode('geoip_detect2', 'geoip_detect2_shortcode');

/**
 * [geoip_detect2_shortcode_get_property description]
 * @param  [type] $userInfo     [description]
 * @param  [type] $propertyName [description]
 * @return [type]               [description]
 * @throws \RuntimeException (if Property name invalid)
 */
function geoip_detect2_shortcode_get_property($userInfo, $propertyName) {
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

function geoip_detect2_shortcode_client_ip($attr) {
	$client_ip = geoip_detect2_get_client_ip();
	geoip_detect_normalize_ip($client_ip);

	return $client_ip;
}
add_shortcode('geoip_detect2_get_client_ip', 'geoip_detect2_shortcode_client_ip');

function geoip_detect2_shortcode_get_external_ip_adress($attr) {
	$external_ip = geoip_detect2_get_external_ip_adress();

	return $external_ip;
}
add_shortcode('geoip_detect2_get_external_ip_adress', 'geoip_detect2_shortcode_get_external_ip_adress');

function geoip_detect2_shortcode_get_current_source_description($attr) {
	$external_ip = geoip_detect2_get_current_source_description();

	return $external_ip;
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
 * @param string $name Name of the form element
 * @param string $id CSS Id of element
 * @param string $class CSS Class of element
 * @param string $lang Language(s) (optional. If not set, current site language is used.)
 * @param string $selected Which country to select by default (2-letter ISO code.) (optional. If not set, the country will be detected by client ip.)
 * @param string $default 		Default Value that will be used if country cannot be detected (optional)
 * @param string $include_blank If this value contains 'true', a empty value will be prepended ('---', i.e. no country) (optional)
 *
 * @return string The generated HTML
 */
function geoip_detect2_shortcode_country_select($attr) {
	$selected = '';
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

	$locales = !empty($attr['lang']) ? $attr['lang'] : null;
	$locales = apply_filters('geoip_detect2_locales', $locales);

	$select_attrs = array(
		'name' => !empty($attr['name']) ? $attr['name'] : 'geoip-countries',
		'id' => @$attr['id'],
		'class' => @$attr['class'],
		'aria-required' => !empty($attr['required']) ? 'required' : '',
		'aria-invalid' => !empty($attr['invalid']) ? $attr['invalid'] : '',
	);
	$select_attrs_html = '';
	foreach ($select_attrs as $key => $value) {
		if ($value)
			$select_attrs_html .= $key . '="' . esc_attr($value) . '" ';
	}

	$countryInfo = new YellowTree\GeoipDetect\Geonames\CountryInformation();
	$countries = $countryInfo->getAllCountries($locales);

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

	$html = '<select ' . $select_attrs_html . '>';
	if (!empty($attr['include_blank']) && $attr['include_blank'] !== 'false')
		$html .= '<option value="">---</option>';
	foreach ($countries as $code => $label) {
		if (substr($code, 0, 6) == 'blank_')
		{
			$html .= '<option value="">' . esc_html($label) . '</option>';
		}
		else
		{
			$html .= '<option' . ($code == $selected ? ' selected="selected"' : '') . '>' . esc_html($label) . '</option>';
		}
	}
	$html .= '</select>';

	return $html;
}
add_shortcode('geoip_detect2_countries_select', 'geoip_detect2_shortcode_country_select');
add_shortcode('geoip_detect2_countries', 'geoip_detect2_shortcode_country_select');

/**
 *
 * Examples:
 *
 * `[geoip_detect2_countries mycountry id:id class:class lang:fr]`
 * A list of all country names in French (with CSS id "#id" and class ".class"), the visitor's country is preselected.
 *
 * `[geoip_detect2_countries mycountry include_blank]`
 * Country names are in the current site language. User can also choose '---' for no country at all.
 *
 * `[geoip_detect2_countries mycountry "US"]`
 * "United States" is preselected, there is no visitor IP detection going on here
 *
 * `[geoip_detect2_countries mycountry default:US]`
 * Visitor's country is preselected, but in case the country is unknown, use "United States"
 *
 */
function geoip_detect2_shortcode_country_select_wpcf7($tag) {
	$tag = new WPCF7_FormTag( $tag );

	$default = (string) reset( $tag->values );
	$default = $tag->get_default_option($default, array('multiple' => false));
	$default = wpcf7_get_hangover( $tag->name, $default ); // Get from $_POST if available

	$class = wpcf7_form_controls_class( $tag->type );
	$validation_error = wpcf7_get_validation_error( $tag->name );
	if ($validation_error)
		$class .= ' wpcf7-not-valid';

	$attr = array(
		'name' => $tag->name,
		'include_blank' => $tag->has_option( 'include_blank' ),
		'required' => substr($tag->type, -1) == '*',
		'invalid' => $validation_error ? 'true' : 'false',
		'id' => $tag->get_id_option(),
		'class' => $tag->get_class_option( $class ),
		'lang' => $tag->get_option('lang', '', true),
		'selected' => $default,
		'default' => $tag->get_option('default', '', true),
	);
	$html = geoip_detect2_shortcode_country_select($attr);

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s">%2$s %3$s</span>',
		sanitize_html_class( $tag->name ), $html, $validation_error );

	return $html;
}

add_action( 'wpcf7_init', 'geoip_detect2_add_shortcodes' );
function geoip_detect2_add_shortcodes() {
	if (function_exists('wpcf7_add_form_tag')) {
		// >=CF 4.6
		wpcf7_add_form_tag(array('geoip_detect2_countries', 'geoip_detect2_countries*'), 'geoip_detect2_shortcode_country_select_wpcf7', true);
	} else if (function_exists('wpcf7_add_shortcode')) {
		// < CF 4.6
		wpcf7_add_shortcode(array('geoip_detect2_countries', 'geoip_detect2_countries*'), 'geoip_detect2_shortcode_country_select_wpcf7', true);
	}
}


function geoip_detect2_shortcode_user_info_wpcf7($output, $name, $isHtml) {
    if ($name != 'geoip_detect2_user_info')
        return $output;

    $lines = array();

    $lines[] = sprintf(__('IP of the user: %s', 'geoip-detect'), geoip_detect2_get_client_ip());
    $info = geoip_detect2_get_info_from_current_ip();
    if ($info->country->name)
        $lines[] = sprintf(__('Country: %s', 'geoip-detect'), $info->country->name);
    if ($info->mostSpecificSubdivision->name)
        $lines[] = sprintf(__('State or region: %s', 'geoip-detect'), $info->mostSpecificSubdivision->name);
    if ($info->city->name)
        $lines[] = sprintf(__('City: %s', 'geoip-detect'), $info->city->name);
	$lines[] = '';
	$lines[] = sprintf(__('Data from: %s', 'geoip-detect'), geoip_detect2_get_current_source_description());

    $lineBreak = $isHtml ? "<br>" : "\n";
    return implode($lineBreak, $lines);
}
add_filter( 'wpcf7_special_mail_tags', 'geoip_detect2_shortcode_user_info_wpcf7', 15, 3 );

function geoip_detect_shortcode_user_info($attr) {
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
 * LIMITATIONS:
 * - You cannot nest several of these shortcodes within one another. Instead, seperate them into several blocks of shortcodes.
 * - City names can be ambigous. For example, [geoip_detect2_show_if country="US,FR" not_city="Paris"] will exclude both Paris in France and Paris in Texas, US. Instead, you can find out the geoname_id or seperate the shortcode to make it more specific.
 *
 */
function geoip_detect2_shortcode_show_if($attr, $content = null, $shortcodeName = '') {
    $showContentIfMatch = ($shortcodeName == 'geoip_detect2_show_if') ? true : false;

    $attr = shortcode_atts(array(
		'lang' => null,
		'skip_cache' => 'false',
        'property' => null,
        'property_value' => null,
        'not_property_value' => null,
        'continent' => null,
        'country' => null,
        'most_specific_subdivision' => null,
        'region' => null,
        'state' => null,
        'city' => null,
        'not_country' => null,
        'not_most_specific_subdivision' => null,
        'not_region' => null,
        'not_state' => null,
        'not_city' => null,),
        $attr, $shortcodeName);

	$skipCache = filter_var($attr['skip_cache'], FILTER_VALIDATE_BOOLEAN );

	$locales = isset($attr['lang']) ? $attr['lang'] . ',en' : null;
	$locales = apply_filters('geoip_detect2_locales', $locales);

	$options = array('skipCache' => $skipCache);

    $info = geoip_detect2_get_info_from_current_ip($locales, $options);
    $isConditionMatching = true;

    /* Attribute Conditions. Order is not important, as they are combined with an transitive AND condition */
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

	foreach ($attributeNames as $shortcodeParamName => $maxmindName) {
		if (!empty($attr[$shortcodeParamName])) {
            // Determine Actual MaxMind Value(s) for Attribute
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

	if (!empty($attr['property']) && (!empty($attr['property_value']) || !empty($attr['not_property_value'])) ) {
		try {
			$actualValue = geoip_detect2_shortcode_get_property($info, $attr['property']);

			if (!empty($attr['property_value'])) {
				$subConditionMatching = geoip_detect2_shortcode_check_subcondition($attr['property_value'], $actualValue);
			}
			if (!empty($attr['not_property_value'])) {
				$subConditionMatching = ! geoip_detect2_shortcode_check_subcondition($attr['not_property_value'], $actualValue);
			}
		} catch (\Exception $e) {
			$subConditionMatching = false;
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

	$actualValues = (array) $actualValues;

	// Compare case-insensitively
	$attributeValuesArray = array_map('mb_strtolower', $attributeValuesArray);
	$actualValues = array_map('mb_strtolower', $actualValues);

	return count(array_intersect($actualValues, $attributeValuesArray)) > 0;
}

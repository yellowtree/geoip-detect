<?php
/*
Copyright 2013-2016 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (info@yellowtree.de)

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
 * `[geoip_detect2 property="country.confidence" default="default value"]` -> default value
 * 
 * @param string $property		Property to read. Instead of '->', use '.'
 * @param string $lang			Language(s) (optional. If not set, current site language is used.)
 * @param string $default 		Default Value that will be shown if value not set (optional)
 * @param string $skipCache		if 'true': Do not cache value
 *
 * @since 2.5.7 New attribute `ip`
 */
function geoip_detect2_shortcode($attr)
{
	$skipCache = isset($attr['skip_cache']) && (strtolower($attr['skip_cache']) == 'true' || $attr['skip_cache'] == '1');
	
	$locales = isset($attr['lang']) ? $attr['lang'] . ',en' : null;
	$locales = apply_filters('geoip_detect2_locales', $locales);

	$defaultValue = isset($attr['default']) ? $attr['default'] : ''; 
	
	$properties = explode('.', $attr['property']);
	
	$options = array('skipCache' => $skipCache);
	
	$ip = isset($attr['ip']) ? $attr['ip'] : geoip_detect2_get_client_ip();
	
	$userInfo = geoip_detect2_get_info_from_ip($ip, $locales, $options);

	if ($userInfo->isEmpty)
		return $defaultValue . '<!-- GeoIP Detect: No information found for this IP (' . geoip_detect2_get_client_ip() . ') -->';	
	
	$return = '';
	try {
		if (count($properties) == 1) {
			$return = $userInfo->{$properties[0]};
		} else if (count($properties) == 2) {
			$return = $userInfo->{$properties[0]};
			if (!is_object($return))
				throw new \RuntimeException('Invalid property name.');
			$return = $return->{$properties[1]};
		} else {
			throw new \RuntimeException('Only 1 dot supported. Please send a bug report to show me the shortcode you used if you need it ...');
		}
	} catch (\RuntimeException $e) {
		return $defaultValue . '<!-- GeoIP Detect: Invalid property name. -->';
	}

	if (is_object($return) && $return instanceof \GeoIp2\Record\AbstractPlaceRecord)
		$return = $return->name;
	
	if (is_object($return) || is_array($return)) {
		return $defaultValue . '<!-- GeoIP Detect: Invalid property name (sub-property missing). -->';
	}
	
	if ($return)
		return (string) $return;
	else
		return $defaultValue;
	
}
add_shortcode('geoip_detect2', 'geoip_detect2_shortcode');

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
	
	$html = '<select ' . $select_attrs_html . '>';
	if (!empty($attr['include_blank']) && $attr['include_blank'] !== 'false') 
		$html .= '<option value="">---</option>';
	foreach ($countries as $code => $label) {
		$html .= '<option' . ($code == $selected ? ' selected="selected"' : '') . '>' . esc_html($label) . '</option>';	
	}
	$html .= '</select>';
	
	return $html;
}
add_shortcode('geoip_detect2_countries_select', 'geoip_detect2_shortcode_country_select');

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
	$tag = new WPCF7_Shortcode( $tag );
	
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
	wpcf7_add_shortcode(array('geoip_detect2_countries', 'geoip_detect2_countries*'), 'geoip_detect2_shortcode_country_select_wpcf7', true);
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

    $lineBreak = $isHtml ? "<br>" : "\n";
    return implode($lineBreak, $lines);
}
add_filter( 'wpcf7_special_mail_tags', 'geoip_detect2_shortcode_user_info_wpcf7', 15, 3 );

function geoip_detect_shortcode_user_info($attr) {
    return geoip_detect2_shortcode_user_info_wpcf7('', 'geoip_detect2_user_info', true);
}
add_shortcode('geoip_detect2_user_info', 'geoip_detect_shortcode_user_info');
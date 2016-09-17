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

function geoip_detect2_add_body_classes($classes) {
	if (!get_option('geoip-detect-set_css_country'))
		return $classes;
	
	$info = geoip_detect2_get_info_from_current_ip();

	if ($info->country->isoCode)
		$classes[] = 'geoip-country-' . $info->country->isoCode;
	
	if ($info->continent->code)
		$classes[] = 'geoip-continent-' . $info->continent->code;

	return $classes;
}
add_filter('body_class', 'geoip_detect2_add_body_classes');


function geoip_detect2_convert_locale_format($locales) {
	if (is_string($locales)) {
		$locales = explode(',', $locales);
		$locales = array_map('trim', $locales);
		
		$locales = array_unique($locales);
	}
	return $locales;
}
add_filter('geoip_detect2_locales', 'geoip_detect2_convert_locale_format', 7);

function geoip_detect2_add_default_locales($locales) {
	if (is_null($locales) || $locales === false) {
		$locales = array();
		
		/* Needed? should be in get_locale()
		if (defined('ICL_LANGUAGE_CODE'))
			$locales[] = ICL_LANGUAGE_CODE;
		*/
		
		$site_locale = get_locale();
		if ($site_locale) {
			$translate = array(
				'pt_BR' => 'pt-BR',
				'zh_CN' => 'zh-CN',
			);
			if (isset($translate[$site_locale]))
				$site_locale = $translate[$site_locale];
			else
				$site_locale = substr($site_locale, 0, 2);
			
			$locales[] = $site_locale;
		}
		$locales[] = 'en';
	}
	return $locales;
}
add_filter('geoip_detect2_locales', 'geoip_detect2_add_default_locales');

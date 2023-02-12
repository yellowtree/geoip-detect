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

namespace YellowTree\GeoipDetect\Geonames;
define ('GEOIP_DETECT_GEONAMES_COUNTRY_INFO', GEOIP_PLUGIN_DIR . '/lib/geonames/data/country-info.php');
define ('GEOIP_DETECT_GEONAMES_COUNTRY_NAMES', GEOIP_PLUGIN_DIR . '/lib/geonames/data/country-names.php');
define ('GEOIP_DETECT_GEONAMES_COUNTRY_FLAGS', GEOIP_PLUGIN_DIR . '/lib/geonames/data/country-flags.php');

class CountryInformation {
	
	protected $data = [];
	
	protected function lazyLoadInformation($filename) {
		if (!isset($this->data[$filename])) {
			$this->data[$filename] = require($filename);
		}
		return $this->data[$filename];
	}
	/**
	 * Get all geonames Information that is known about the country with this ISO code.
	 * 
	 * @param string $iso 2-letter ISO-Code of the country (e.g. 'DE')
	 * @return array Information in record format
	 */
	public function getInformationAboutCountry($iso) {
		$data = $this->lazyLoadInformation(GEOIP_DETECT_GEONAMES_COUNTRY_INFO);
		
		if ($iso == 'keys')
			return array_keys($data['countries']);
		
		if (!isset($data['countries'][$iso]))
			return [];
		
		$country = $data['countries'][$iso];
		if (isset($country['continent']) && is_string($country['continent']))
			$country['continent'] = $data['continents'][$country['continent']];
		return $country;
	}

	/**
	 * The Emoji representing the flag
	 * 
	 * @param string $iso 2-letter ISO-Code of the country(e.g. 'DE')
	 * @return string the emoji char
	 */
	public function getFlagEmoji($iso) {
		$data = $this->lazyLoadInformation(GEOIP_DETECT_GEONAMES_COUNTRY_FLAGS);

		if ($iso == 'keys')
			return array_keys($data);

		if (!isset($data[$iso]['emoji'])) return '';

		return $data[$iso]['emoji'];
	}

	/**
	 * The tel code of a country
	 * 
	 * @param string $iso 2-letter ISO-Code of the country(e.g. 'DE')
	 * @return string the emoji char
	 */
	public function getTelephonePrefix($iso) {
		$data = $this->lazyLoadInformation(GEOIP_DETECT_GEONAMES_COUNTRY_FLAGS);

		if ($iso == 'keys')
			return array_keys($data);

		if (!isset($data[$iso]['tel'])) return '';

		return $data[$iso]['tel'];
	}


	
	/**
	 * Get all country names
	 * @param  string/array $locales Locale of the label (if array: use the first locale available)
	 * @return array ISO Codes => Label Pairs
	 */
	public function getAllCountries($locales = 'en') {
		$data = $this->lazyLoadInformation(GEOIP_DETECT_GEONAMES_COUNTRY_NAMES);

		if ($locales === 'keys')
			return array_keys($data);
		
		foreach ((array) $locales as $locale) {
			if (isset($data[$locale]))
				return $data[$locale];
		}
		return $data['en'];
	}
}

// List of country names, iso, continent id.
// List of continents

// List of timezones

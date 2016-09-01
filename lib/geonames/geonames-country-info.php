<?php

namespace YellowTree\GeoipDetect\Geonames;
define ('GEOIP_DETECT_GEONAMES_COUNTRY_INFO', GEOIP_PLUGIN_DIR . '/lib/geonames/data/country-info.php');

class CountryInformation {
	
	protected $data;
	
	protected function lazyLoadInformation() {
		if (is_null($this->data)) {
			$this->data = require(GEOIP_DETECT_GEONAMES_COUNTRY_INFO);
		}
	}
	
	public function getInformationAbout($iso) {
		$this->lazyLoadInformation();
		// Return in record format
	}
	
	public function getAllCountries($locale) {
		$this->lazyLoadInformation();
		// return array iso => label 
	}
}

// List of country names, iso, continent id.
// List of continents

// List of timezones
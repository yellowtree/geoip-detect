
<?php


class GeonamesTest extends WP_UnitTestCase_GeoIP_Detect {
	
	protected $countryInformation;
	
	public function setUp() {	
		parent::setUp();
		$this->countryInformation = new \YellowTree\GeoipDetect\Geonames\CountryInformation;
	}
	
	public function testCountryInfoMemoryUsage() {
		// return;
		
		$this->assertFileExists(GEOIP_DETECT_GEONAMES_COUNTRY_INFO);
		$mem_before = memory_get_usage();
		$data = require(GEOIP_DETECT_GEONAMES_COUNTRY_INFO);
		$mem_after = memory_get_usage();
		$mem_diff = floor(($mem_after - $mem_before) / 1024) + 1;
		echo " (Geonames CountryInfo takes up ~$mem_diff kB in Memory.) ";
		
		//$this->assertSmaller(1024, $mem_diff);
	}
	
	public function testCountryInfoEveryAttributeIsNotEmpty() {
		$data = $this->countryInformation->getInformationAboutCountry('all');
		$this->assertInternalType('array', $data);
		foreach ($data as $id => $country) {
			if (strlen($id) > 2)
				continue;
			
			$record = new \YellowTree\GeoipDetect\DataSources\City($country, ['en']);
			$this->assertValidGeoIP2Record($record, 'Geonames Country Info of ' . $id, false /* Check continent: YES */, true /* Check Extra Info: NO */);
		}
	}
		
	public function testCountryNamesMemoryUsage() {
		// return;
		
		$this->assertFileExists(GEOIP_DETECT_GEONAMES_COUNTRY_NAMES);
		$mem_before = memory_get_usage();
		$data = require(GEOIP_DETECT_GEONAMES_COUNTRY_NAMES);
		$mem_after = memory_get_usage();
		$mem_diff = floor(($mem_after - $mem_before) / 1024) + 1;
		echo " (Geonames CountryNames takes up ~$mem_diff kB in Memory.) ";
		
		//$this->assertSmallerThan(512, $mem_diff);
	}
	
	public function testGetAllCountries() {
		foreach (['en', 'de', 'it', 'es', 'fr', 'ja', 'pt-BR', 'ru', 'zh-CN'] as $lang_id) {
			$lang = $this->countryInformation->getAllCountries($lang_id);
			
			foreach ($lang as $c_id => $country) {
				$this->assertSame(2, strlen($c_id), 'Country Code "' . $c_id . '" must be 2-chars');
				$this->assertNotEmpty($country, 'Country Label must not be empty');
			}
		}
		
		// Fallback order
		$lang = $this->countryInformation->getAllCountries(['zz', 'qq', 'de']);
		$this->assertSame($lang['AE'], 'Vereinigte Arabische Emirate');
		
		// Use 'en' as fallback
		$lang = $this->countryInformation->getAllCountries(['zz']);
		$this->assertSame($lang['AE'], 'United Arab Emirates');
	}
	
	
}
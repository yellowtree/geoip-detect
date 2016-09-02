
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
		$keys = $this->countryInformation->getInformationAboutCountry('keys');

		foreach ($keys as $id) {
			$country = $this->countryInformation->getInformationAboutCountry($id);

			$record = new \YellowTree\GeoipDetect\DataSources\City($country, ['en']);
			$this->assertValidGeoIP2Record($record, 'Geonames Country Info of ' . $id, false /* Check continent: YES */, true /* Check Extra Info: NO */);
		}
		
		// Some asserts
		
		$info = $this->countryInformation->getInformationAboutCountry('AE');
		$this->assertSame(290557, $info['country']['geoname_id']);
		$this->assertSame('Asien', $info['continent']['names']['de']);
		$this->assertSame('AS', $info['continent']['code']);
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
		$keys = $this->countryInformation->getAllCountries('keys');
		
		foreach ($keys as $lang_id) {
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
	
	public function testEnrichtData() {
		$data = [];
		$data['country']['iso_code'] = 'AE';
		$data['continent']['code'] = 'ZZ'; // This is wrong, of course. Existing data should not be overwritten.
		
		$data = apply_filters('geoip_detect2_record_data', $data);
		
		$this->assertSame('Vereinigte Arabische Emirate', $data['country']['names']['de']);
		$this->assertSame('ZZ', $data['continent']['code']);
		$this->assertSame('Asien', $data['continent']['names']['de']);
		$this->assertNotEmpty($data['location']['latitude']);
	}
	
}
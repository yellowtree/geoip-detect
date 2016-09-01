
<?php


class GeonamesTest extends WP_UnitTestCase_GeoIP_Detect {
	
	public function testAllAttributesContainSomething() {
		// return;
		$this->assertFileExists(GEOIP_DETECT_GEONAMES_COUNTRY_INFO);
		$mem_before = memory_get_usage();
		$data = require(GEOIP_DETECT_GEONAMES_COUNTRY_INFO);
		$mem_after = memory_get_usage();
		$mem_diff = floor(($mem_after - $mem_before) / 1024) + 1;
		echo " (Geonames CountryInfo takes up ~$mem_diff kB in Memory.) ";
		
		$this->assertInternalType('array', $data);
		foreach ($data as $id => $country) {
			if (strlen($id) > 2)
				continue;
			
			$record = new \YellowTree\GeoipDetect\DataSources\City($country, ['en']);
			$this->assertValidGeoIP2Record($record, 'Geonames Country Info of ' . $id, false /* Check continent: YES */, true /* Check Extra Info: NO */);
		}
	}
			
	
	
}
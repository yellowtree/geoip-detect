<?php

class LegacyApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function set_up()
	{
		parent::set_up();
	}
	
	function tear_down()
	{
		remove_filter('geoip_detect_get_external_ip_adress', [ $this, 'filter_set_test_ip' ], 101);
		parent::tear_down();
	}
	
	function testLookup() {
		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIPRecord($record, GEOIP_DETECT_TEST_IP);
		
		// When internal IP adress, then return the content of the external IP of the server
		$record = geoip_detect_get_info_from_ip('192.168.1.1');
		$this->assertValidGeoIPRecord($record, '192.168.1.1');
	}
	
	function testLegacyApi() {
		$actualRecord = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIPRecord($actualRecord, GEOIP_DETECT_TEST_IP);

		$record = new stdClass();
		$record->country_code 	= 'DE';
		$record->country_code3 	= 'DEU';
		$record->country_name 	= 'Germany';
		$record->continent_code = 'EU';
		$record->timezone 		= 'Europe/Berlin';
		
		$this->assertAtLeastTheseProperties($record, $actualRecord);
	}
	
	function testTimezone()
	{
		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
				
		$this->assertStringContainsString('/', $record->timezone, 'Timezone: ' . $record->timezone);
		try {
			new DateTimeZone($record->timezone);
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}
	
	function testRegionName() {
		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertGreaterThan(1, strlen($record->region_name), 'Region Name: "' . $record->region_name);
	}	
}


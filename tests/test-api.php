<?php

function geoip_detetect_test_set_test_database()
{
	return GEOIP_DETECT_TEST_DB_FILENAME;
}

class ApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function setUp()
	{
		add_filter('geoip_detect_get_abs_db_filename', 'geoip_detetect_test_set_test_database', 101);
	}
	
	function tearDown()
	{
		remove_filter('geoip_detect_get_abs_db_filename', 'geoip_detetect_test_set_test_database', 101);
	}
	
	function testLookup() {
		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIPRecord($record, GEOIP_DETECT_TEST_IP);
		
		$record = geoip_detect_get_info_from_ip('192.168.1.1');
		$this->assertValidGeoIPRecord($record, '192.168.1.1');
	}
	
	function testTimezone()
	{
		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
				
		$this->assertContains('/', $record->timezone, 'Timezone: ' . $record->timezone);
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
	
	function testExternalIp() {
		$ip = geoip_detect_get_external_ip_adress();
		$this->assertNotEquals('0.0.0.0', $ip);
	}
}


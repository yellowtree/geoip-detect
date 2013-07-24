<?php

function geoip_detetect_test_set_test_database()
{
	return dirname(__FILE__) . '/GeoLiteCity.dat';
}
add_filter('geoip_detect_get_abs_db_filename', 'geoip_detetect_test_set_test_database', 101);

class ApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function testLookup() {
		$record = geoip_detect_get_info_from_ip('47.64.121.17');
		$this->assertValidGeoIPRecord($record, '47.64.121.17');
		
		$record = geoip_detect_get_info_from_ip('192.168.1.1');
		$this->assertValidGeoIPRecord($record, '192.168.1.1');
	}
	
	function testTimezone()
	{
		$record = geoip_detect_get_info_from_ip('47.64.121.17');
				
		$this->assertContains('/', $record->timezone, 'Timezone: ' . $record->timezone);
		try {
			new DateTimeZone($record->timezone);
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}
	
	function testRegionName() {
		$record = geoip_detect_get_info_from_ip('47.64.121.17');
		
		$this->assertGreaterThan(1, strlen($record->region_name), 'Region Name: "' . $record->region_name);
	}
	
	function testExternalIp() {
		$ip = geoip_detect_get_external_ip_adress();
		$this->assertNotEquals('0.0.0.0', $ip);
	}
}


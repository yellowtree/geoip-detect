<?php

function geoip_detetect_test_set_test_database()
{
	return GEOIP_DETECT_TEST_DB_FILENAME;
}

function geoip_detect_get_external_ip_adress_test_set_test_ip()
{
	return GEOIP_DETECT_TEST_IP;
}

class ApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function setUp()
	{
		// Use Test File
		add_filter('geoip_detect_get_abs_db_filename', 'geoip_detetect_test_set_test_database', 101);
	}
	
	function tearDown()
	{
		remove_filter('geoip_detect_get_abs_db_filename', 'geoip_detetect_test_set_test_database', 101);
		remove_filter('geoip_detect_get_external_ip_adress', 'geoip_detect_get_external_ip_adress_test_set_test_ip', 101);
	}
	
	function testLookup() {
		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIPRecord($record, GEOIP_DETECT_TEST_IP);
		
		// When internal IP adress, then return the content of the external IP of the server
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
		$ip = _geoip_detect_get_external_ip_adress_without_cache();
		$this->assertNotEquals('0.0.0.0', $ip);
	}
	
	function testShortcode() {
		add_filter('geoip_detect_get_external_ip_adress', 'geoip_detect_get_external_ip_adress_test_set_test_ip', 101);
		$this->assertEquals(GEOIP_DETECT_TEST_IP, geoip_detect_get_external_ip_adress());
		
		$string = do_shortcode('[geoip_detect property="country_name"]');
		$this->assertNotEmpty($string, '[geoip_detect property="country_name"]', "The Geoip Detect shortcode did not generate any output");
		$this->assertNotEquals($string, '[geoip_detect property="country_name"]', "The Geoip Detect shortcode does not seem to be called");
		$this->assertNotContains('<!--', $string, "Geoip Detect shortcode threw an error: " . $string);
		
		$string = do_shortcode('[geoip_detect property="INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode threw no error in spite of invalid property name: " . $string);
	}
}


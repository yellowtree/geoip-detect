<?php



function geoip_detect_get_external_ip_adress_test_set_test_ip()
{
	return GEOIP_DETECT_TEST_IP;
}

class LegacyApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function setUp()
	{
		parent::setUp();
	}
	
	function tearDown()
	{
		parent::tearDown();
		remove_filter('geoip_detect_get_external_ip_adress', 'geoip_detect_get_external_ip_adress_test_set_test_ip', 101);
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

		$this->assertEquals(51, $actualRecord->latitude, 'Record is not in Germany', 5);
		$this->assertEquals(10, $actualRecord->longitude, 'Record is not in Germany', 7);
		
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
	
	function testShortcode() {
		add_filter('geoip_detect_get_external_ip_adress', 'geoip_detect_get_external_ip_adress_test_set_test_ip', 101);
		$this->assertEquals(GEOIP_DETECT_TEST_IP, geoip_detect_get_external_ip_adress());
		
		$string = do_shortcode('[geoip_detect property="country_name"]');
		$this->assertNotEmpty($string, '[geoip_detect property="country_name"]', "The Geoip Detect shortcode did not generate any output");
		$this->assertNotEquals($string, '[geoip_detect property="country_name"]', "The Geoip Detect shortcode does not seem to be called");
		$this->assertNotContains('<!--', $string, "Geoip Detect shortcode threw an error: " . $string);
		
		$string = do_shortcode('[geoip_detect property="INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect property="INVALID" default="here"]');
		$this->assertContains('here', $string, "Geoip Detect Shortcode does not contain default value: " . $string);
	}
}


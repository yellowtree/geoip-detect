<?php

function geoip_detect2_test_set_test_database()
{
	return GEOIP_DETECT_TEST_DB_FILENAME;
}

class ApiTest extends WP_UnitTestCase_GeoIP_Detect {
	function setUp()
	{
		// Use Test File
		add_filter('geoip_detect_get_abs_db_filename', 'geoip_detect2_test_set_test_database', 101);
	}
	
	function tearDown()
	{
		remove_filter('geoip_detect_get_abs_db_filename', 'geoip_detect2_test_set_test_database', 101);
	}
	
	function testLookup() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
	}
	
	function testLocale() {
		$record = new GeoIp2\Model\City(array());
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('en'));
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertEquals('Germany', $record->country->name);
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('de'));
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertEquals('Deutschland', $record->country->name);

		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('nn', 'mm', 'de'));
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertEquals('Deutschland', $record->country->name);
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('nn', 'mm'));
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertSame(null, $record->country->name);	
	}
}
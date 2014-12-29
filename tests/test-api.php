<?php

function geoip_detect2_test_set_test_database()
{
	return GEOIP_DETECT_TEST_DB_FILENAME;
}

class ApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function testLookup() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		
		$record = geoip_detect2_get_info_from_current_ip();
		$this->assertValidGeoIP2Record($record, 'current_ip');
		
		$record = geoip_detect2_get_info_from_ip('garbage');
		$this->assertInstanceOf('GeoIp2\Model\City', $record, 'Garbage IP did not return a record object');
		$this->assertSame(true, $record->traits->isEmpty);
		$this->assertEquals(false, $record->country->name);
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

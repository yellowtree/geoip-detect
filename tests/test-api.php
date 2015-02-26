<?php

class ApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function testCurrentIp() {			
		$record = geoip_detect2_get_info_from_current_ip();
		$this->assertValidGeoIP2Record($record, 'current_ip');
	}
	
	function testLookup() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertSame(null, $record->city->confidence);
		
		$record = geoip_detect2_get_info_from_ip('garbage');
		$this->assertInstanceOf('GeoIp2\Model\City', $record, 'Garbage IP did not return a record object');
		$this->assertSame(true, $record->isEmpty);
		$this->assertSame(null, $record->country->name);
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

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
		$this->assertInstanceOf('YellowTree\GeoipDetect\DataSources\City', $record, 'Garbage IP did not return a wordpress record object');
		$this->assertNotEmpty($record->extra->error);
		$this->assertContains('is not a valid IP', $record->extra->error);
		
		$this->assertSame(true, $record->isEmpty);
		$this->assertSame(null, $record->country->name);
	}
	
	function testIPv6() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP_V_6);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertSame('IE', $record->country->isoCode);
	}
	
	function testWhitespace() {
		$record = geoip_detect2_get_info_from_ip('  ' . GEOIP_DETECT_TEST_IP . '   ');
		$this->assertValidGeoIP2Record($record, '  ' . GEOIP_DETECT_TEST_IP . '   ');
		$record = geoip_detect2_get_info_from_ip('  ' . GEOIP_DETECT_TEST_IP_V_6 . '   ');
		$this->assertValidGeoIP2Record($record, '  ' . GEOIP_DETECT_TEST_IP_V_6 . '   ');	
	}
	
	function testLocale() {
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
	
	function testDescription() {
		$this->assertNotEmpty(geoip_detect2_get_current_source_description());
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertNotEmpty(geoip_detect2_get_current_source_description($record));
		
		$desc = do_shortcode('[geoip_detect2_get_current_source_description]');
		$this->assertNotSame('[geoip_detect2_get_current_source_description]', $desc, 'Shortcode was not executed.');
		$this->assertNotEmpty($desc, 'Shortcode returned empty string');
	}

}

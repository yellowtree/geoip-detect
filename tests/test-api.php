<?php

class ApiTest extends WP_UnitTestCase {

	function testUpdate() {
		
		$this->assertTrue( geoip_detect_update() );
		
		$record = geoip_detect_get_info_from_ip('47.64.121.17');
		$this->assertValidGeoIPRecord($record);
		
		$record = geoip_detect_get_info_from_ip('192.168.1.1');
		$this->assertValidGeoIPRecord($record);
	}
	
	function testExternalIp() {
		$ip = geoip_detect_get_external_ip_adress();
		$this->assertNotEquals('0.0.0.0', $ip);
	}
	
	protected function assertValidGeoIPRecord($record)
	{
		$this->assertInstanceOf('geoiprecord', $record);
		$this->assertInternalType('string', $record->country_code);
		$this->assertNotSame('', $record->country_code);
	}
}


<?php

class ApiTest extends WP_UnitTestCase {

	function testUpdate() {
		
		$this->assertTrue( geoip_detect_update() );
		
		$record = geoip_detect_get_info_from_ip('47.64.121.17');
		$this->assertInstanceOf('geoiprecord', $record);
		$this->assertInternalType('string', $record->country_code);
		$this->assertNotSame('', $record->country_code);
	}
	
	function testExternalIp() {
		$ip = geoip_detect_get_external_ip_adress();
		$this->assertEquals('0.0.0.0', $ip);
	}
}


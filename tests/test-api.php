<?php

function geoip_detetect_test_set_test_database()
{
	return dirname(__FILE__) . '/GeoLiteCity.dat';
}
//add_filter('geoip_detect_get_abs_db_filename', 'geoip_detetect_test_set_test_database', 101);

class ApiTest extends WP_UnitTestCase {

	function testUpdate() {
		$this->assertTrue( geoip_detect_update() );
		
		$record = geoip_detect_get_info_from_ip('47.64.121.17');
		$this->assertValidGeoIPRecord($record, '47.64.121.17');
		
		$record = geoip_detect_get_info_from_ip('192.168.1.1');
		$this->assertValidGeoIPRecord($record, '192.168.1.1');
	}
	
	function testExternalIp() {
		$ip = geoip_detect_get_external_ip_adress();
		$this->assertNotEquals('0.0.0.0', $ip);
	}
	
	function testUpdaterFileFilter() {
		//$this->assertEquals('', geoip_detect_get_database_upload_filename_filter());
		$this->assertContains('/upload', geoip_detect_get_database_upload_filename());
		
	}
	
	protected function assertValidGeoIPRecord($record, $ip)
	{
		$this->assertInstanceOf('geoiprecord', $record);
		$this->assertInternalType('string', $record->country_code);
		$this->assertNotSame('', $record->country_code);
	}
}


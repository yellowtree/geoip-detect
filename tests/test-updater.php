<?php

class UpdateTest extends WP_UnitTestCase_GeoIP_Detect {

	function testUpdate() {
		$this->assertTrue( geoip_detect_update() );

		$record = geoip_detect_get_info_from_ip('47.64.121.17');
		$this->assertValidGeoIPRecord($record, '47.64.121.17');

		$record = geoip_detect_get_info_from_ip('192.168.1.1');
		$this->assertValidGeoIPRecord($record, '192.168.1.1');
	}

	function testUpdaterFileFilter() {
		//$this->assertEquals('', geoip_detect_get_database_upload_filename_filter());
		$this->assertContains('/upload', geoip_detect_get_database_upload_filename());

	}
}
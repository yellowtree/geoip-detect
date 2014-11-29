<?php

class UpdateTest extends WP_UnitTestCase_GeoIP_Detect {

	function setUp() {
		// unlink uploads file if exists
		$filename = geoip_detect_get_database_upload_filename();
		if (file_exists($filename))
			@unlink($filename);
	}
	
	/**
	 * @group external-http
	 */
	function testUpdate() {
		$this->markTestSkipped('This test should not be executed by Travis.');
		
		$this->assertTrue( geoip_detect_update() );

		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIPRecord($record, GEOIP_DETECT_TEST_IP);
	}

	function testUpdaterFileFilter() {
		$this->assertEquals('', geoip_detect_get_database_upload_filename_filter(''));
		$this->assertContains('/upload', geoip_detect_get_database_upload_filename());
	}
}
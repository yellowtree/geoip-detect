<?php

class UpdateTest extends WP_UnitTestCase_GeoIP_Detect {

	function set_up() {
		parent::set_up();
		
		// unlink uploads file if exists
		if (function_exists('geoip_detect_get_database_upload_filename')) {
			$filename = geoip_detect_get_database_upload_filename();
			if (file_exists($filename))
				@unlink($filename);
		}
	}
	
	/**
	 * @group external-http
	 */
	function testUpdate() {
		$s = new \YellowTree\GeoipDetect\DataSources\Auto\AutoDataSource();
		
		$this->assertTrue( $s->maxmindUpdate() );

		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
	}
}
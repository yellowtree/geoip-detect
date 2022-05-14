<?php

class UpdateTest extends WP_UnitTestCase_GeoIP_Detect {

	function set_up() {
		parent::set_up();
		
		add_filter('pre_option_geoip-detect-auto_license_key', [ $this, 'filter_set_user_secret' ], 101);

		// unlink uploads file if exists
		if (function_exists('geoip_detect_get_database_upload_filename')) {
			$filename = geoip_detect_get_database_upload_filename();
			if (file_exists($filename))
				@unlink($filename);
		}
	}

	function tear_down() {
		remove_filter('pre_option_geoip-detect-auto_license_key', [ $this, 'filter_set_user_secret' ], 101);

		parent::tear_down();
	}

	function filter_set_user_secret() {
		$id = getenv('WP_MAXMIND_USER_SECRET');
		if ($id)
			return $id;
		else {
			$this->markTestSkipped('No maxmind update credentials found.');
			return 'asdfsadf';
		}
	}
	
	/**
	 * @group external-http
	 */
	function testUpdate() {
		$s = new \YellowTree\GeoipDetect\DataSources\Auto\AutoDataSource();

		$this->assertTrue( $s->maxmindUpdate(true) );


		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
	}
}
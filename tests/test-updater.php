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

	function tear_down() {
		remove_filter('pre_option_geoip-detect-auto_license_key', [ $this, 'filter_set_user_secret' ], 101);
		remove_filter('pre_option_geoip-detect-auto_license_key', [ $this, 'filter_set_invalid_user_secret' ], 100);
		remove_filter('geoip_detect2_download_url', [ $this, 'filter_set_test_file' ], 101);

		parent::tear_down();
	}

	function filter_set_invalid_user_secret() { 
		return 'bla';
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

	function filter_set_test_file() {
		// Yes, use http instead of https to test redirect
		return 'http://github.com/yellowtree/geoip-detect/raw/develop/tests/GeoLite2-Country.mmdb.tar.gz';
	}
	
	/**
	 * @group external-http
	 */
	function testUpdate() {
		add_filter('pre_option_geoip-detect-auto_license_key', [ $this, 'filter_set_user_secret' ], 101);
		$this->assertUpdateWorks();
	}

	function assertUpdateWorks() {
		$s = new \YellowTree\GeoipDetect\DataSources\Auto\AutoDataSource();

		$this->assertTrue( $s->maxmindUpdate(true) );


		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
	}

	/**
	 * @group external-http
	 */
	function testUpdateWithTestFile() {
		add_filter('pre_option_geoip-detect-auto_license_key', [ $this, 'filter_set_invalid_user_secret' ], 100);
		add_filter('geoip_detect2_download_url', [ $this, 'filter_set_test_file' ], 101);
		$this->assertUpdateWorks();
	}

	function testUrlConsideredSafe() {
		$url = 'https://mm-prod-geoip-databases.a2649acb697e2c09b632799562c076f2.r2.cloudflarestorage.com/bla.tar.gz';
		$this->assertSame(wp_http_validate_url($url), $url, 'URL ' . $url . ' is not considered safe by wordpress, update of Maxmind data will fail');
	}
}
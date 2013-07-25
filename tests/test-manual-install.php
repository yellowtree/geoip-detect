<?php

define('TEST_GEOIP_PLUGIN_DATA_FILENAME', dirname(__FILE__) . '/../' . GEOIP_DETECT_DATA_FILENAME);

class ManualInstallTest extends WP_UnitTestCase_GeoIP_Detect {

	function setUp() {
		if (file_exists(TEST_GEOIP_PLUGIN_DATA_FILENAME))
			unlink(TEST_GEOIP_PLUGIN_DATA_FILENAME);
	}
	
	function testNoDatabaseFound() {
		$this->assertSame('', geoip_detect_get_abs_db_filename() );

		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertSame(0, $record);
	}

	function testManualInstall() {
		
		$ret = @copy(GEOIP_DETECT_TEST_DB_FILENAME, TEST_GEOIP_PLUGIN_DATA_FILENAME);
		if (!$ret)
			$this->skip('Test could not be executed: Copy failed');
		
		$this->assertNotSame('', geoip_detect_get_abs_db_filename(), 'Did not detect manual database' );
		
		$record = geoip_detect_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIPRecord($record, GEOIP_DETECT_TEST_IP);
	}
}
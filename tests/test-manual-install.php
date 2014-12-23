<?php

define('TEST_GEOIP_PLUGIN_DATA_FILENAME', dirname(__FILE__) . '/../' . GEOIP_DETECT_DATA_FILENAME);

class ManualInstallTest extends WP_UnitTestCase_GeoIP_Detect {

	function setUp() {
		if (file_exists(TEST_GEOIP_PLUGIN_DATA_FILENAME))
			unlink(TEST_GEOIP_PLUGIN_DATA_FILENAME);
	}
	
	function testNoDatabaseFound() {
		if (file_exists(TEST_GEOIP_PLUGIN_DATA_FILENAME))
			$this->skip('Test could not be executed: ' . TEST_GEOIP_PLUGIN_DATA_FILENAME . ' could not be deleted.');
		
		$thrown = false;
		try {
			$this->assertSame('', geoip_detect_get_abs_db_filename() );
		} catch(Exception $e) {
			$thrown = true;
		}
		if (!$thrown)
			$this->fail('geoip_detect_get_abs_db_filename(): No database missing exception was thrown.');

		$thrown = false;
		try {
			$reader = geoip_detect2_get_reader();
			$this->assertSame(null, $reader, 'geoip_detect2_get_info_from_ip() : should have returned NULL because there is no database'); 
		} catch(Exception $e) {
			$thrown = true;
		}
		if (!$thrown)
			$this->fail('geoip_detect2_get_reader(): No database missing exception was thrown.');
		
		$thrown = false;
		try {
			$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, 'geoip_detect2_get_info_from_ip() : should have returned NULL because there is no database');
			$this->assertSame(null, $ret);
		} catch(Exception $e) {
			$thrown = true;
		}
		if (!$thrown)
			$this->fail('geoip_detect2_get_info_from_ip(): No database missing exception was thrown.');
			
	}

	function testManualInstall() {
		
		$ret = @copy(GEOIP_DETECT_TEST_DB_FILENAME, TEST_GEOIP_PLUGIN_DATA_FILENAME);
		if (!$ret)
			$this->skip('Test could not be executed: Copy failed');
		
		$this->assertNotSame('', geoip_detect_get_abs_db_filename(), 'Did not detect manual database' );
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
	}
}
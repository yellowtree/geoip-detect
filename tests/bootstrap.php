<?php

require_once getenv( 'WP_TESTS_DIR' ) . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../geoip-detect.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';

define('GEOIP_DETECT_TEST_DB_FILENAME', dirname(__FILE__) . '/' . GEOIP_DETECT_DATA_FILENAME);
define('GEOIP_DETECT_TEST_IP', '47.64.121.17');

class WP_UnitTestCase_GeoIP_Detect extends WP_UnitTestCase
{
	protected function assertValidGeoIPRecord($record, $ip)
	{
		$assert_text = 'When looking up info for IP ' . $ip . ':';
		$this->assertInstanceOf('geoiprecord', $record, $assert_text);
		$this->assertInternalType('string', $record->country_code, $assert_text);
		$this->assertEquals(2, strlen($record->country_code), $assert_text);
		
		$properties = array('country_code', 'country_code3', 'country_name', 'latitude', 'longitude', 'continent_code');

		foreach ($properties as $name) {
			$this->assertObjectHasAttribute($name, $record);
		}
	}
	
	protected function assertAtLeastTheseProperties($expected, $actual) {
		$checkObject = new stdClass;
		foreach ($expected as $name => $value) {
			$this->assertObjectHasAttribute($name, $actual);
			
			$checkObject->$name = $actual->$name;
		}
		
		$this->assertEquals($expected, $checkObject);
	}
} 
<?php

require_once getenv( 'WP_TESTS_DIR' ) . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../geoip-detect.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';

class WP_UnitTestCase_GeoIP_Detect extends WP_UnitTestCase
{
	protected function assertValidGeoIPRecord($record, $ip)
	{
		$assert_text = 'When looking up info for IP ' . $ip . ':';
		$this->assertInstanceOf('geoiprecord', $record, $assert_text);
		$this->assertInternalType('string', $record->country_code, $assert_text);
		$this->assertEquals(2, strlen($record->country_code), $assert_text);
	}
} 
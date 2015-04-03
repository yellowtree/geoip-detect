<?php

//define('WP_DEBUG', true);
require_once 'vendor/autoload.php';

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../geoip-detect.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

ini_set('error_reporting', ini_get('error_reporting') | E_USER_NOTICE);


define('GEOIP_DETECT_TEST_DB_FILENAME', __DIR__ . '/' . GEOIP_DETECT_DATA_FILENAME);
define('GEOIP_DETECT_TEST_COUNTRY_DB_FILENAME', __DIR__ . '/GeoLite2-Country.mmdb');
define('GEOIP_DETECT_TEST_IP', '88.64.140.3');
define('GEOIP_DETECT_TEST_IP_V_6', '2001:4860:4801:5::91');
define('GEOIP_DETECT_EXTERNAL_IP', '88.64.140.3');
define('GEOIP_DETECT_TEST_IP_SERIVCE_PROVIDER', 'https://raw.githubusercontent.com/yellowtree/wp-geoip-detect/master/tests/html/ipv4.txt');


class WP_UnitTestCase_GeoIP_Detect extends WP_UnitTestCase
{
	private $setup_was_called = false;
	public function setUp() {
		// Use Test File
		add_filter('geoip_detect_get_abs_db_filename', array($this, 'filter_set_test_database'), 101);
		add_filter('pre_option_geoip-detect-source', array($this, 'filter_set_default_source'), 101);
		add_filter('pre_transient_geoip_detect_external_ip', array($this, 'filter_set_external_ip'), 101);
		
		$this->setup_was_called = true;
	}
	

	function filter_set_external_ip() {
		return GEOIP_DETECT_EXTERNAL_IP;
	}
	
	function filter_set_test_database()
	{
		return GEOIP_DETECT_TEST_DB_FILENAME;
	}
	
	function filter_set_test_ip($ip) {
		return GEOIP_DETECT_TEST_IP;
	}
	
	function filter_set_default_source() {
		return 'manual';
	}
	
	public function tearDown() {
		remove_filter('geoip_detect_get_abs_db_filename', array($this, 'filter_set_test_database'), 101);
		remove_filter('pre_option_geoip-detect-source', array($this, 'filter_set_default_source'), 101);
		remove_filter('pre_transient_geoip_detect_external_ip', array($this, 'filter_set_external_ip'), 101);
		$this->setup_was_called = false;
	}
	
	public function testDatabaseLocation() {
		$this->assertSame(true, $this->setup_was_called, 'parent::setUp() has not been called.');
	}
	
	protected function assertValidGeoIPRecord($record, $ip)
	{
		$assert_text = 'When looking up info for IP ' . $ip . ':';
		$this->assertInstanceOf('geoiprecord', $record, $assert_text);
		$this->assertInternalType('string', $record->country_code, $assert_text);
		$this->assertEquals(2, strlen($record->country_code), $assert_text);
		$this->assertEquals(3, strlen($record->country_code3), $assert_text);
		$this->assertEquals(2, strlen($record->continent_code), $assert_text);
		
		$properties = array('country_code', 'country_code3', 'country_name', 'latitude', 'longitude', 'continent_code');

		foreach ($properties as $name) {
			$this->assertObjectHasAttribute($name, $record);
		}
	}
	
	
/**
 * 
 * Enter description here ...
 * @param GeoIp2\Model\City $record
 * @param int $ip
 */
	protected function assertValidGeoIP2Record($record, $ip)
	{
		$assert_text = 'When looking up info for IP "' . $ip . '":';
		$this->assertInstanceOf('YellowTree\GeoipDetect\DataSources\City', $record, $assert_text);
		$this->assertSame(false, $record->isEmpty);	
		
		$this->assertInternalType('string', $record->country->isoCode, $assert_text);
		$this->assertEquals(2, strlen($record->country->isoCode), $assert_text);
		$this->assertEquals(2, strlen($record->continent->code), $assert_text);
		$this->assertInternalType('array', $record->country->names, $assert_text);
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
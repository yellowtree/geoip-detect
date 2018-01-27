<?php
if (!defined('GEOIP_DETECT_IP_EMPTY_CACHE_TIME'))
	define('GEOIP_DETECT_IP_EMPTY_CACHE_TIME', 1);

define('GEOIP_DETECT_DOING_UNIT_TESTS', true);
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
if (!file_exists(GEOIP_DETECT_TEST_DB_FILENAME))
	die('Error: Maxmind City Test file is missing.');

define('GEOIP_DETECT_TEST_COUNTRY_DB_FILENAME', __DIR__ . '/GeoLite2-Country.mmdb');
if (!file_exists(GEOIP_DETECT_TEST_DB_FILENAME))
	die('Error: Maxmind Country Test file is missing.');

define('GEOIP_DETECT_TEST_IP', '88.64.140.3');
define('GEOIP_DETECT_TEST_IP_V_6', '2a00:1450:4001:801::101f');
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
	
	function filter_set_test_ip() {
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
 * Test if the returned record is valid.
 * 
 * @param YellowTree\GeoipDetect\DataSources\City $record
 * @param string $ip
 */
	protected function assertValidGeoIP2Record($record, $ip, $skipContinentTest = false, $skipExtraTest = false)
	{
		$assert_text = 'When looking up info for IP "' . $ip . '": ';
		$this->assertInstanceOf('YellowTree\GeoipDetect\DataSources\City', $record, $assert_text);
		$assert_text .= '(record: ' . json_encode($record) . ')' . "\n";
		
		if (!$skipExtraTest) {
			$this->assertSame('', $record->extra->error, $assert_text . 'extra->error should be empty');
			$this->assertSame(false, $record->isEmpty, $assert_text . 'isEmpty should not be false');	
			$this->assertNotEmpty($record->extra->source, $assert_text . 'extra->source should not be empty');
			$this->assertNotEmpty($record->traits->ipAddress, $assert_text . 'requested IP should not be empty');
		}
		
		$this->assertInternalType('string', $record->country->isoCode, $assert_text . 'country->isoCode should not be empty');
		$this->assertEquals(2, strlen($record->country->isoCode), $assert_text . 'country->isoCode should be 2 chars long');
		if (!$skipContinentTest)
			$this->assertEquals(2, strlen($record->continent->code), $assert_text  . 'continent->code should be 2 chars long');
		$this->assertInternalType('array', $record->country->names, $assert_text . 'country->names should be an array');
		
		if (geoip_detect_is_ip($ip))
			$this->assertSame(geoip_detect_normalize_ip($ip), geoip_detect_normalize_ip($record->traits->ipAddress), $assert_text);

	}
	
	protected function assertEmptyGeoIP2Record($record, $ip) {
		$assert_text = 'When looking up info for IP "' . $ip . '": ';
		$this->assertInstanceOf('YellowTree\GeoipDetect\DataSources\City', $record, $assert_text);
		$this->assertSame(true, $record->isEmpty, $assert_text . 'isEmpty should be true');
		$this->assertNotEmpty($record->extra->source, $assert_text . 'extra->source should not be empty');
		
		$this->assertEquals('', $record->country->isoCode, $assert_text . 'country->isoCode should be empty');
		$this->assertNotEmpty($record->traits->ipAddress, $assert_text . 'requested IP should not be empty');
	}
	
	protected function assertAtLeastTheseProperties($expected, $actual) {
		$checkObject = new stdClass;
		foreach ($expected as $name => $value) {
			$this->assertObjectHasAttribute($name, $actual);
			
			$checkObject->$name = $actual->$name;
		}
		
		$this->assertEquals($expected, $checkObject);
	}

    /**
     * Empty method to disable integration with WP Core Trac.
     *
     * Overrides the parent's method to disable fetching information
     * from the WordPress Core Trac ticket tracker which causes tests
     * with the `@ticket` annotation in our own test cases to fail CI.
     *
     * @link https://core.trac.wordpress.org/browser/tags/4.4/tests/phpunit/includes/testcase.php#L434
     *
     * @return void
     */
    protected function checkRequirements () {
        // do nothing!
    }
} 

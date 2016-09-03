<?php
use GeoIp2\WebService\Client;
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

class PrecisionSourceTest extends WP_UnitTestCase_GeoIP_Detect {

	public function setUp() {
		add_filter('pre_option_geoip-detect-precision-user_id', array($this, 'filter_set_user_id'), 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_user_secret'), 101);
		
		parent::setUp();
	}
	
	public function tearDown() {
		remove_filter('pre_option_geoip-detect-precision-user_id', array($this, 'filter_set_user_id'), 101);
		remove_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_user_secret'), 101);
		remove_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_wrong_user_secret'), 102);
		remove_filter('pre_option_geoip-detect-precision_api_type', array($this, 'filter_set_precision_method_insights'), 102);
	}
	
	function filter_set_default_source() {
		return 'precision';
	}
	
	function filter_set_user_id() {
		$id = getenv('WP_PRECISION_USER_ID');
		if ($id)
			return $id;
		else
			return 17;
	}
	
	function filter_set_user_secret() {
		$id = getenv('WP_PRECISION_USER_SECRET');
		if ($id)
			return $id;
		else {
			$this->markTestSkipped('No precision credentials found.');
			return 'asdfsadf';
		}
	}
	
	function filter_set_wrong_user_secret() {
		return 'dd';
	}
	
	function filter_set_precision_method_insights() {
		return 'insights';
	}
	
	function testDataSourceExists() {
		remove_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_user_secret'), 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_wrong_user_secret'), 102);
		
		$registry = DataSourceRegistry::getInstance();
		
		$source = $registry->getCurrentSource();
		$this->assertNotNull($source, "Source was null");
		$this->assertSame('precision', $source->getId(), 'Id of current source is incorrect');
		
		$reader = $source->getReader();
		$this->assertNotNull($reader, "Reader was null");
	}
	
	/**
	 * @group external-http
	 * @expectedException \GeoIp2\Exception\AuthenticationException
	 */
	function testMaxmindApiPasswordWrong() {
		$client = new Client('17', 'sadf');		
		$client->city('8.8.8.8');
	}
	
	/**
	 * @group external-http
	 * @expectedException \GeoIp2\Exception\AuthenticationException
	 */
	function testMaxmindApiPasswordWrong2() {
		$client = new Client('', '');
		$client->city('8.8.8.8');
	}
	
	/**
	 * @group external-http
	 * @expectedException \GeoIp2\Exception\AuthenticationException
	 */
	function testNoPasswordManualLookup() {
		remove_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_user_secret'), 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_wrong_user_secret'), 102);
		
		$reader = geoip_detect2_get_reader();
		
		$this->assertNotNull($reader);
		$reader->city(GEOIP_DETECT_TEST_IP);		
	}
	
	/**
	 * @group external-http
	 */
	function testTimeout() {
		remove_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_user_secret'), 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_wrong_user_secret'), 102);
		
		$before = microtime(true);
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('en'), array('timeout' => 0.01, 'skipCache' => true));
		$after = microtime(true);
		
		$this->assertLessThan(0.1, $after - $before);
		$this->assertEmptyGeoIP2Record($ret, 'timed out');
		$this->assertNotEmpty($ret->extra->error);
	}
	
	/**
	 * @group external-http
	 */
	function testNoPassword() {
		remove_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_user_secret'), 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_wrong_user_secret'), 102);
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertTrue($record->isEmpty);
		$this->assertContains('authenticated', $record->extra->error);
	}
	
	/**
	 * @group external-http
	 */
	/*
	function testWorking() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		
		var_dump($record);
		
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertFalse($record->isEmpty);
		$this->assertEmpty($record->extra->error);
	}
	*/
	
	/**
	 * @group external-http
	 */
	/*
	function testInsightsNotWorking() {
		add_filter('pre_option_geoip-detect-precision_api_type', array($this, 'filter_set_precision_method_insights'), 102);
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertTrue($record->isEmpty);
		$this->assertContains('out of queries', $record->extra->error);
	}
	*/

}
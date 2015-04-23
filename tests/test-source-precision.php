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
	}
	
	function filter_set_default_source() {
		return 'precision';
	}
	
	function filter_set_user_id() {
		return 6;
	}
	
	function filter_set_user_secret() {
		return 'dd';
	}
	
	function filter_set_wrong_user_secret() {
		return 'dd';
	}
	
	function testDataSourceExists() {
		$registry = DataSourceRegistry::getInstance();
		
		$source = $registry->getCurrentSource();
		$this->assertNotNull($source, "Source was null");
		$this->assertSame('precision', $source->getId(), 'Id of current source is incorrect');
		
		$reader = $source->getReader();
		$this->assertNotNull($reader, "Reader was null");
	}
	
	/**
	 * @expectedException \GeoIp2\Exception\AuthenticationException
	 */
	function testMaxmindApiPasswordWrong() {
		$client = new Client('17', 'sadf');		
		$client->city('8.8.8.8');
	}
	
	/**
	 * @expectedException \GeoIp2\Exception\AuthenticationException
	 */
	function testMaxmindApiPasswordWrong2() {
		$client = new Client('', '');
		$client->city('8.8.8.8');
	}
	
	/**
	 * @expectedException \GeoIp2\Exception\AuthenticationException
	 */
	function testNoPasswordManualLookup() {
		add_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_wrong_user_secret'), 102);
		
		$reader = geoip_detect2_get_reader();
		
		$this->assertNotNull($reader);
		$reader->city(GEOIP_DETECT_TEST_IP);		
	}
	
	function testNoPassword() {
		add_filter('pre_option_geoip-detect-precision-user_secret', array($this, 'filter_set_wrong_user_secret'), 102);
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertTrue($record->isEmpty);
		$this->assertNotEmpty($record->extra->error);
	}

}
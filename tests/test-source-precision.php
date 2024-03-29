<?php
use GeoIp2\WebService\Client;
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

class PrecisionSourceTest extends WP_UnitTestCase_GeoIP_Detect {

	public function set_up() {
		parent::set_up();

		add_filter('pre_option_geoip-detect-precision-user_id', [ $this, 'filter_set_user_id' ], 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_user_secret' ], 101);
		
	}
	
	public function tear_down() {
		remove_filter('pre_option_geoip-detect-precision-user_id', [ $this, 'filter_set_user_id' ], 101);
		remove_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_user_secret' ], 101);
		remove_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_wrong_user_secret' ], 102);
		remove_filter('pre_option_geoip-detect-precision_api_type', [ $this, 'filter_set_precision_method_insights' ], 102);

		parent::tear_down();
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
		remove_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_user_secret' ], 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_wrong_user_secret' ], 102);
		
		$registry = DataSourceRegistry::getInstance();
		
		$source = $registry->getCurrentSource();
		$this->assertNotNull($source, "Source was null");
		$this->assertSame('precision', $source->getId(), 'Id of current source is incorrect');
		
		$reader = $source->getReader();
		$this->assertNotNull($reader, "Reader was null");
	}
	
	/**
	 * @group external-http
	 */
	function testMaxmindApiPasswordWrong() {
		$this->expectException(\GeoIp2\Exception\AuthenticationException::class);
		$client = new Client('17', 'sadf');		
		$client->city('8.8.8.8');
	}
	
	/**
	 * @group external-http
	 */
	function testMaxmindApiPasswordWrong2() {
		$this->expectException(\GeoIp2\Exception\AuthenticationException::class);
		$client = new Client('', '');
		$client->city('8.8.8.8');
	}
	
	/**
	 * @group external-http
	 */
	function testNoPasswordManualLookup() {
		$this->expectException(\GeoIp2\Exception\AuthenticationException::class);
		remove_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_user_secret' ], 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_wrong_user_secret' ], 102);
		
		$reader = geoip_detect2_get_reader();
		
		$this->assertNotNull($reader);
		$reader->city(GEOIP_DETECT_TEST_IP);		
	}
	
	/**
	 * @group external-http
	 */
	function testTimeout() {
		remove_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_user_secret' ], 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_wrong_user_secret' ], 102);
		
		$before = microtime(true);
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, [ 'en' ], [ 'timeout' => 0.01, 'skipCache' => true ]);
		$after = microtime(true);
		
		$this->assertLessThan(0.1, $after - $before);
		$this->assertEmptyGeoIP2Record($ret, 'timed out');
		$this->assertNotEmpty($ret->extra->error);
	}
	
	/**
	 * @group external-http
	 */
	function testNoPassword() {
		remove_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_user_secret' ], 101);
		add_filter('pre_option_geoip-detect-precision-user_secret', [ $this, 'filter_set_wrong_user_secret' ], 102);
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertTrue($record->isEmpty);
		$this->assertStringContainsString('authenticated', $record->extra->error);
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
		add_filter('pre_option_geoip-detect-precision_api_type', [ $this, 'filter_set_precision_method_insights' ], 102);
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertTrue($record->isEmpty);
		$this->assertStringContainsString('out of queries', $record->extra->error);
	}
	*/

}
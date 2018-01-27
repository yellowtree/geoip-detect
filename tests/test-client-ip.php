<?php

class GetClientIpTest extends WP_UnitTestCase_GeoIP_Detect {
	
	protected $trusted;
	
	function test_set_trusted_proxies() {
		return $this->trusted;
	}
	
	function option_use_proxy() {
		return 1;
	}
	
	
	public function setUp() {
		parent::setUp();
		add_filter('pre_option_geoip-detect-has_reverse_proxy', array($this, 'option_use_proxy'), 101);
		add_filter('pre_option_geoip-detect-trusted_proxy_ips', array($this, 'test_set_trusted_proxies'), 101);
		$this->trusted = [];
	}
	public function tearDown() {
		parent::tearDown();
		remove_filter('pre_option_geoip-detect-has_reverse_proxy', array($this, 'option_use_proxy'), 101);
		remove_filter('pre_option_geoip-detect-trusted_proxy_ips', array($this, 'test_set_trusted_proxies'), 101);
		unset($_SERVER['REMOTE_ADDR']);
		unset($_SERVER['HTTP_X_FORWARDED_FOR']);
	}
	
	/**
	 * @dataProvider dataReverseProxy
	 */
	public function testReverseProxy($expectedIp, $RemoteAddr, $ForwardedFor, $Whitelist) {
		$_SERVER['REMOTE_ADDR'] = $RemoteAddr;
		$_SERVER['HTTP_X_FORWARDED_FOR'] = $ForwardedFor;
		$this->trusted = $Whitelist;
		$this->assertSame($expectedIp, geoip_detect2_get_client_ip());
	}
	
	public function dataReverseProxy() {
		return array(
			array('1.2.3.4', '1.2.3.4', '', ''),
			array('1.2.3.4', '9.9.9.9,1.2.3.4', '', ''), // REMOTE_ADDR with , @see https://wordpress.org/support/topic/php-fatal-error-uncaught-exception-invalidargumentexception/?replies=2#post-8128737
			array('1.2.3.4', '9.9.9.9', '1.2.3.4', ''),
			array('1.2.3.4', '9.9.9.9', '10.10.10.10, 1.2.3.4', ''),
			array('1.2.3.4', '9.9.9.9', '10.10.10.10, 1.2.3.4, 11.11.11.11', '11.11.11.11'),
		);
	}
}
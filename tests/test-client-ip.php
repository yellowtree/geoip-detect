<?php

class GetClientIpTest extends WP_UnitTestCase_GeoIP_Detect {
	
	protected $trusted;
	protected $useProxy = 0;
	
	function set_trusted_proxies() {
		return $this->trusted;
	}
	
	function option_use_proxy() {
		return $this->useProxy;
	}
	
	
	public function setUp() {
		parent::setUp();
		add_filter('pre_option_geoip-detect-has_reverse_proxy', array($this, 'option_use_proxy'), 101);
		add_filter('pre_option_geoip-detect-trusted_proxy_ips', array($this, 'set_trusted_proxies'), 101);
		$this->trusted = '';
	}
	public function tearDown() {
		parent::tearDown();
		remove_filter('pre_option_geoip-detect-has_reverse_proxy', array($this, 'option_use_proxy'), 101);
		remove_filter('pre_option_geoip-detect-trusted_proxy_ips', array($this, 'set_trusted_proxies'), 101);
		unset($_SERVER['REMOTE_ADDR']);
		unset($_SERVER['HTTP_X_FORWARDED_FOR']);
		$this->useProxy = 0;
	}
	
	/**
	 * @dataProvider dataReverseProxy
	 */
	public function testReverseProxy($expectedIp, $RemoteAddr, $ForwardedFor, $Whitelist, $name = '') {
		$_SERVER['REMOTE_ADDR'] = $RemoteAddr;
		$_SERVER['HTTP_X_FORWARDED_FOR'] = $ForwardedFor;
		$this->useProxy = 1;
		$this->trusted = $Whitelist;
		$this->assertSame($expectedIp, geoip_detect2_get_client_ip(), "Failing test $name:");
	}
	
	public function dataReverseProxy() {
		return array(
			array('1.2.3.4', '9.9.9.9', '1.2.3.4', 											'9.9.9.9', '1 proxy'),
			array('1.2.3.4', '9.9.9.9', '1.2.3.4, 14.14.14.14', 							'14.14.14.14, 9.9.9.9', '2 proxies'),
			array('1.2.3.4', '9.9.9.9', '1.2.3.4', 											'', '1 proxy without whitelist (backwards compat)'),
			array('1.2.3.4', '1.2.3.4, 9.9.9.9', '', 										'9.9.9.9', 'REMOTE_ADDR with ,'), // @see https://wordpress.org/support/topic/php-fatal-error-uncaught-exception-invalidargumentexception/?replies=2#post-8128737
			array('1.2.3.4', '9.9.9.9', '10.10.10.10, 1.2.3.4', 							'', 'Missing whitelist'),
			array('1.2.3.4', '9.9.9.9', '10.10.10.10, 1.2.3.4, 11.11.11.11', 				'9.9.9.9, 11.11.11.11', 'Part whitelist'),
			array('1.2.3.4', '9.9.9.9', '10.10.10.10, 1.2.3.4, 11.11.11.11, ::1, 127.0.0.1', '9.9.9.9, 11.11.11.11', 'Localhost'), 
			array('1.2.3.4', '9.9.9.9', '10.10.10.10, 1.2.3.4, 11.11.11.11', 				'9.9.9.17/18, 11.11.11.0/24', 'Whitelist with subnets'),
			array('1.2.3.4', '9.9.9.9', '10.10.10.10, 1.2.3.4, 2001:0db8:a::', 				'9.9.9.17/18, 1.2.3.178/29, 2001:db8::/32', 'Whitelist with subnets'),
			array('1::1', '9.9.9.9', '10.10.10.10, 1::1, 2001:0db8:a::', 					'9.9.9.17/18, 1.2.3.178/29, 2001:db8::/32', 'Whitelist with subnets'),
		);
	}
	
	/**
	 * @dataProvider dataSimpleIp
	 */ 
	public function testSimpleIp($expectedIp, $RemoteAddr, $name) {
		$_SERVER['REMOTE_ADDR'] = $RemoteAddr;
		$this->useProxy = 0;
		$this->assertSame($expectedIp, geoip_detect2_get_client_ip(), "Failing test $name:");
	}
	
	public function dataSimpleIp() {
		return array(
			array('1.2.3.4', '1.2.3.4', 'simple'),
			array('1.2.3.4', '9.9.9.9,1.2.3.4', 'REMOTE_ADDR with ,'), // @see https://wordpress.org/support/topic/php-fatal-error-uncaught-exception-invalidargumentexception/?replies=2#post-8128737
			array('::1', '', 'empty'),
			array('::1', 'asdfasdf', 'wrong'),
		);
	}
}
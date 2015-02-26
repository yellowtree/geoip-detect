<?php

function option_use_proxy() {
	return 1;
}

class ReverseProxyTest extends WP_UnitTestCase_GeoIP_Detect {
	
	
	function setUp() {
		parent::setUp();
		
		$_SERVER['HTTP_X_FORWARDED_FOR'] = GEOIP_DETECT_TEST_IP;
		
		add_filter('pre_option_geoip-detect-has_reverse_proxy', 'option_use_proxy', 101);
	}
	function tearDown() {
		parent::tearDown();
		
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '';
		
		remove_filter('pre_option_geoip-detect-has_reverse_proxy', 'option_use_proxy', 101);
	}
	
	function testGetClientIpWithoutOption() {
		remove_filter('pre_option_geoip-detect-has_reverse_proxy', 'option_use_proxy', 101);
		$this->assertSame('::1', geoip_detect2_get_client_ip());
	}
	
	function testOneProxy() {
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());
	}
	
	function testTwoProxies() {
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.1.1, ' . GEOIP_DETECT_TEST_IP;
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());
		
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.1.1 , ' . GEOIP_DETECT_TEST_IP;
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());

		$_SERVER['HTTP_X_FORWARDED_FOR'] = '1.1.1.1,' . GEOIP_DETECT_TEST_IP;
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());
	}
}
<?php

function option_use_proxy() {
	return 1;
}

function test_set_trusted_proxies() {
	return '1.1.1.1, 2.2.2.2, 3.3.3.3, FE80::0202:B3FF:FE1E:8329';
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
		remove_filter('pre_option_geoip-detect-trusted_proxy_ips', 'test_set_trusted_proxies', 101);
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
	
	function testNormalizeIpv6() {
		$this->assertSame('fe80::202:b3ff:fe1e:8329', geoip_detect_normalize_ip('FE80::0202:B3FF:FE1E:8329'));
		$this->assertSame('fe80::202:b3ff:fe1e:8329', geoip_detect_normalize_ip('fe80:0:0:0:202:b3ff:fe1e:8329'));
		
		
	}
	
	function testNormalizeIpv4() {
		$this->assertSame('1.1.1.1', geoip_detect_normalize_ip(' 1.1.1.1 '));
	}
	
	function testTrustedProxies() {
		add_filter('pre_option_geoip-detect-trusted_proxy_ips', 'test_set_trusted_proxies', 101);
		
		$_SERVER['HTTP_X_FORWARDED_FOR'] = GEOIP_DETECT_TEST_IP.', 1.1.1.1';
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());
		
		$_SERVER['HTTP_X_FORWARDED_FOR'] = GEOIP_DETECT_TEST_IP.', 1.1.1.1, 2.2.2.2';
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());
		
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '2.2.2.3, '. GEOIP_DETECT_TEST_IP.', 1.1.1.1';
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());

		$_SERVER['HTTP_X_FORWARDED_FOR'] = '2.2.2.2, '. GEOIP_DETECT_TEST_IP.', 1.1.1.1';
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());

		$_SERVER['HTTP_X_FORWARDED_FOR'] = '3.4.5.6, '. GEOIP_DETECT_TEST_IP.', 2.2.2.2, fe80:0:0:0:202:b3ff:fe1e:8329';
		$this->assertSame(GEOIP_DETECT_TEST_IP, geoip_detect2_get_client_ip());
	}
}
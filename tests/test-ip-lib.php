<?php

class IpLibTest extends WP_UnitTestCase_GeoIP_Detect {

	function testIpToS() {
		$this->assertNotEmpty(_ip_to_s('127.0.0.1'));
		$this->assertEmpty(_ip_to_s('garbage'));
	}

	function testPublicIpFilter() {
		$this->assertSame(true, geoip_detect_is_public_ip(GEOIP_DETECT_TEST_IP));
		$this->assertSame(false, geoip_detect_is_public_ip('10.0.0.2'));
		$this->assertSame(false, geoip_detect_is_public_ip('169.254.1.1'));
	}

	function testLoopbackFilter() {
		$this->assertSame(false, geoip_detect_is_public_ip('::1'));
		$this->assertSame(false, geoip_detect_is_public_ip('127.0.0.1'));
		$this->assertSame(false, geoip_detect_is_public_ip('127.0.1.1'));
	}

	function testInvalidIpFilter() {
		$this->assertSame(false, geoip_detect_is_public_ip('999.0.0.1'));
		$this->assertSame(false, geoip_detect_is_public_ip('asdfasfasdf'));
		$this->assertSame(false, geoip_detect_is_public_ip(':::'));
		$this->assertSame(false, geoip_detect_is_public_ip(''));
	}
	
	function testEqualIpAdresses() {
		$this->assertTrue(geoip_detect_is_ip_equal('2001:0DB8:0:0:1::1', '2001:0db8:0000:0000:0001:0000:0000:0001'));
		$this->assertTrue(geoip_detect_is_ip_equal('2001:0DB8:0:0:1::1', '2001:0db8:0000:0000:1:0000:0000:0001'));
		$this->assertTrue(geoip_detect_is_ip_equal('2001:0DB8:0:0:1::1', '2001:0db8:0000:0000:1:0000:0000:0001'));
		$this->assertTrue(geoip_detect_is_ip_equal('0:0:0:0:0:0:0:1', '::1'));
		$this->assertTrue(geoip_detect_is_ip_equal('0:0:0:0:0:0:0:0', '::'));
		$this->assertTrue(geoip_detect_is_ip_equal('::ffff:192.0.2.128', '::ffff:c000:0280'));
		$this->assertFalse(geoip_detect_is_ip_equal('2001:0DB8:0:0:2::1', '2001:0db8:0000:0000:1:0000:0000:0001'));

		$this->assertTrue(geoip_detect_is_ip_equal('8.8.8.8', [ '1.1.1.1', '::8', '8.8.8.8' ] ));
		$this->assertFalse(geoip_detect_is_ip_equal('8.8.8.7', [ '1.1.1.1', '::4', '8.8.8.8' ] ));

	}

	function testEqualIpAdressesWithPort() {
		$this->assertTrue(geoip_detect_is_ip_equal('2001:0DB8:0:0:1::1', '2001:0db8:0000:0000:0001:0000:0000:0001', true));
		$this->assertTrue(geoip_detect_is_ip_equal('[2001:0DB8:0:0:1::1]:15', '2001:0db8:0000:0000:0001:0000:0000:0001', true));
		$this->assertTrue(geoip_detect_is_ip_equal('[2001:0DB8:0:0:1::1]:15', '[2001:0db8:0000:0000:0001:0000:0000:0001]:15', true));
		$this->assertFalse(geoip_detect_is_ip_equal('[2001:0DB8:0:0:1::2]:15', '2001:0db8:0000:0000:0001:0000:0000:0001', true));

		$this->assertTrue(geoip_detect_is_ip_equal('8.8.8.8:56', [ '1.1.1.1', '::8', '8.8.8.8' ], true ));
		$this->assertFalse(geoip_detect_is_ip_equal('8.8.8.8:56', [ '1.1.1.1', '::8', '8.8.8.8' ], false ));
// Not supported
//		$this->assertTrue(geoip_detect_is_ip_equal('8.8.8.8', [ '1.1.1.1', '::8', '8.8.8.8:80' ], true ));
//		$this->assertFalse(geoip_detect_is_ip_equal('8.8.8.7', [ '1.1.1.1', '::8', '8.8.8.8:80' ], true ));
	}

	function testStripPort() {
		$this->assertSame('1.2.3.1', geoip_detect_ip_remove_port('1.2.3.1'));
		$this->assertSame('1.2.3.2', geoip_detect_ip_remove_port(' 1.2.3.2  '));
		$this->assertSame('1.2.3.3', geoip_detect_ip_remove_port(' 1.2.3.3:80'));
		$this->assertSame('::1', geoip_detect_ip_remove_port('::1'));
		$this->assertSame('::2', geoip_detect_ip_remove_port('[::2]:8'));
		$this->assertSame('a:b::2', geoip_detect_ip_remove_port('[a:b::2]:8080'));
		$this->assertSame('[a:b::2]8080', geoip_detect_ip_remove_port('[a:b::2]8080'));
		$this->assertSame('a:b::2]:8080', geoip_detect_ip_remove_port('a:b::2]:8080'));
		$this->assertSame('[a:b::2:8080', geoip_detect_ip_remove_port('[a:b::2:8080'));
	}

	function testSanitizeIpList() {
		$this->assertSame('1.2.3.4', geoip_detect_sanitize_ip_list('1.2.3.4'));
		$this->assertSame('::1', geoip_detect_sanitize_ip_list('::1'));
		$this->assertSame('', geoip_detect_sanitize_ip_list('1'));
		$this->assertSame('1.2.3.4, 4.3.2.1', geoip_detect_sanitize_ip_list('1.2.3.4,a,4.3.2.1'));
		$this->assertSame('1.2.3.4, 4.3.2.1', geoip_detect_sanitize_ip_list('1.2.3.4 , a,    4.3.2.1'));
	}

	function testSanitizeIpCidr() {
		$this->assertSame('1.2.3.4', geoip_detect_sanitize_ip_cidr('1.2.3.4'));
		$this->assertSame('::1', geoip_detect_sanitize_ip_cidr('::1'));
		$this->assertSame('', geoip_detect_sanitize_ip_cidr('1'));
		$this->assertSame('', geoip_detect_sanitize_ip_cidr('1.2.3.4,4.3.2.1'));
	}



}

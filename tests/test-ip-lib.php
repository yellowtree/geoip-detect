<?php

class IpLibTest extends WP_UnitTestCase_GeoIP_Detect {

	function setUp()
	{
		parent::setUp();
	}

	function tearDown()
	{
		parent::tearDown();
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
}
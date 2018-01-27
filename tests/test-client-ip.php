<?php

class GetClientIpTest extends WP_UnitTestCase_GeoIP_Detect {
	public function setUp() {
		parent::setUp();
	}
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testSimpleIp() {
		$expectedIp = '1.2.3.4';
		$_SERVER['REMOTE_ADDR'] = '1.2.3.4';
		$this->assertSame($expectedIp, geoip_detect2_get_client_ip());
	}
	
	public function testReverseProxy() {
		
	}
}
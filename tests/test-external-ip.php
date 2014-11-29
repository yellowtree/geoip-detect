<?php

function ipTestServiceProvider() {
	return array(GEOIP_DETECT_TEST_IP_SERIVCE_PROVIDER);	
}

function ipTestServiceInvalidProvider() {
	return array('http://aaa.example.org/test');
}

class ExternalIpTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function setUp()
	{
	}
	
	function tearDown()
	{
		remove_filter('geiop_detect_ipservices','ipTestServiceProvider', 101);
		remove_filter('geiop_detect_ipservices', array($this, 'externalIpProvidersFilter'), 101);
		remove_filter('geiop_detect_ipservices', 'ipTestServiceInvalidProvider', 101);
	}
	
	
	function testExternalIp() {
		add_filter('geiop_detect_ipservices', 'ipTestServiceProvider', 101);
		
		$ip = _geoip_detect_get_external_ip_adress_without_cache();
		$this->assertNotEquals('0.0.0.0', $ip);
	}
	
	function testInvalidIp() {
		add_filter('geiop_detect_ipservices', 'ipTestServiceInvalidProvider', 101);
		
		try {
			_geoip_detect_get_external_ip_adress_without_cache();
		} catch (Exception $e) {
			$this->assertSame('', '');
			return;
		}
		$this->fail('Invalid IP provider did not provoke an error');
	}
	
	/**
	 * @group external-http
	 */
	function testExternalIpProviders() {
		$this->skip('This test should not be executed by Travis.');
		
		add_filter('geiop_detect_ipservices', array($this, 'externalIpProvidersFilter'), 101);
		
		$this->providers = null;
		
		do {
			$ip = _geoip_detect_get_external_ip_adress_without_cache();
			$this->assertNotEquals('0.0.0.0', $ip, 'Provider did not work: ' . $this->currentProvider);	
		} while (count($this->providers));
	}
	
	protected $providers;
	protected $currentProvider;
	
	function externalIpProvidersFilter($providers) {
		if (is_null($this->providers)) {
			$this->providers = $providers; 
		}
		$this->currentProvider = array_pop($this->providers);
		
		return array($this->currentProvider);
	}
}
<?php

function ipTestServiceProvider() {
	return [ GEOIP_DETECT_TEST_IP_SERIVCE_PROVIDER ];	
}

function ipTestServiceInvalidProvider() {
	return [ 'http://aaa.example.org/test' ];
}


class ExternalIpTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function setUp()
	{
		parent::setUp();
	}
	
	function tearDown()
	{
		parent::tearDown();
		remove_filter('geiop_detect_ipservices','ipTestServiceProvider', 101);
		remove_filter('geiop_detect_ipservices', [ $this, 'externalIpProvidersFilter' ], 101);
		remove_filter('geiop_detect_ipservices', 'ipTestServiceInvalidProvider', 101);
		remove_filter('geiop_detect_ipservices', [ $this, 'filterProviderNone' ], 199);
	}
	
	/**
	 * @group external-http
	 */
	function testExternalIp() {
		add_filter('geiop_detect_ipservices', 'ipTestServiceProvider', 101);
		
		try {
			$ip = _geoip_detect_get_external_ip_adress_without_cache();
			$this->assertNotEquals('0.0.0.0', $ip);
		} catch (PHPUnit_Framework_Error_Warning $e) {
			if (strpos($e->getMessage(), 'timed out') !== false) {
				$this->markTestSkipped('External IP Service timed out ...');
			} else {
				throw $e;
			}
		}
	}
	
	/**
	 * @group external-http
	 */
	function testExternalIpShortcode() {
		$ip = do_shortcode('[geoip_detect2_get_external_ip_adress]');
		$this->assertTrue(geoip_detect_is_ip($ip), 'The return of the shortcode was not an ip adress: "' . $ip . '"');
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
	function testCurrentIpCli() {
		$ret = geoip_detect2_get_info_from_current_ip();
		$this->assertValidGeoIP2Record($ret, 'current');
	}
	
	/**
	 * @group external-http
	 */
	function testInternalIp() {
		$ret = geoip_detect2_get_info_from_ip('127.1.1.1');
		$this->assertValidGeoIP2Record($ret, 'current');

		$ret = geoip_detect2_get_info_from_ip('0.0.0.0');
		$this->assertValidGeoIP2Record($ret, 'current');
	}
	

	/**
	 * @group external-http
	 */
	function testExternalIpProviders() {
		remove_filter('pre_transient_geoip_detect_external_ip', [ $this, 'filter_set_external_ip' ], 101);
		add_filter('geiop_detect_ipservices', [ $this, 'externalIpProvidersFilter' ], 101);
		
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
		
		return [ $this->currentProvider ];
	}
	
	function testTransientCaching() {
		remove_filter('pre_transient_geoip_detect_external_ip', [ $this, 'filter_set_external_ip' ], 101);
		if (GEOIP_DETECT_IP_EMPTY_CACHE_TIME > 10)
			$this->markTestSkipped('Constant could not be redefined');
		
		$TRANSIENT_NAME = 'geoip_detect_external_ip';
		delete_transient($TRANSIENT_NAME);
		
		// Check if transient is really away
		$transient = get_transient($TRANSIENT_NAME);
		$this->assertSame(false, $transient, 'BEFORE: Transient has not been deleted - why?');
		
		// Add transient
		add_filter('geiop_detect_ipservices', [ $this, 'filterProviderNone' ], 199);
		$this->assertSame('0.0.0.0', geoip_detect2_get_external_ip_adress(true));
		
		// Transient should be there
		$transient = get_transient($TRANSIENT_NAME);
		$this->assertSame('0.0.0.0', $transient, 'Transient has not been created');
		
		sleep(GEOIP_DETECT_IP_EMPTY_CACHE_TIME + 1);
		
		// The transient should be expired now, so check that it doesn't exist
		$transient = get_transient($TRANSIENT_NAME);
		$this->assertSame(false, $transient, 'AFTER: Transient should have expired by now ...');
	}
	
	function filterProviderNone() {
		return [];	
	}
}

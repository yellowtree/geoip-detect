<?php
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

class HostinfoSourceTest extends WP_UnitTestCase_GeoIP_Detect {

	function filter_set_default_source() {
		return 'hostinfo';
	}

	function testDataSourceExists() {
		$registry = DataSourceRegistry::getInstance();

		$source = $registry->getCurrentSource();
		$this->assertNotNull($source, "Source was null");
		$this->assertSame('hostinfo', $source->getId(), 'Id of current source is incorrect');

		$reader = $source->getReader();
		$this->assertNotNull($reader, "Reader was null");
	}

	/**
	 * @group external-http
	 */
	function testLookup() {
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, [ 'en' ], [ 'skipCache' => true ]);
		
		$this->assertValidGeoIP2Record($ret, GEOIP_DETECT_TEST_IP, true);
		$this->assertSame('hostinfo', $ret->extra->source);
		$this->assertSame('DE', $ret->country->isoCode);
		$this->assertSame(GEOIP_DETECT_TEST_IP, $ret->traits->ipAddress);
	}

	/**
	 * @group external-http
	 */
	function testLookupV6() {
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP_V_6, [ 'en' ], [ 'skipCache' => true ]);
		$this->assertNotEmpty($ret->extra->error); // Ipv6 is not supported
	}
	
	/**
	 * @group external-http
	 */
	function testLookupTimeout() {
		$before = microtime(true);
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, [ 'en' ], [ 'timeout' => 0.01, 'skipCache' => true ]);
		$after = microtime(true);
		$this->assertLessThan(0.2, $after - $before, 'Timeout option was not respected?');
		$this->assertEmptyGeoIP2Record($ret, 'timed out');
	}
	

}
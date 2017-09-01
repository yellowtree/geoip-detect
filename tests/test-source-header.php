<?php
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

class HeaderSourceTest extends WP_UnitTestCase_GeoIP_Detect {

	public function setUp() {
		parent::setUp();
		
		add_filter('pre_option_geoip-detect-header-provider', array($this, 'filter_set_provider'), 101);
	}

	public function tearDown() {
		unset($_SERVER['CloudFront-Viewer-Country']);
		unset($_SERVER["HTTP_CF_IPCOUNTRY"]);
		
		remove_filter('pre_option_geoip-detect-header-provider', array($this, 'filter_set_provider'), 101);
		remove_filter('pre_option_geoip-detect-header-provider', array($this, 'filter_set_provider_cloudflare'), 102);
	}

	function filter_set_default_source() {
		return 'header';
	}
	
	function filter_set_provider() {
		return 'aws';
	}
	
	function filter_set_provider_cloudflare() {
		return 'cloudflare';	
	}

	function testDataSourceExists() {
		$registry = DataSourceRegistry::getInstance();

		$source = $registry->getCurrentSource();
		$this->assertNotNull($source, "Source was null");
		$this->assertSame('header', $source->getId(), 'Id of current source is incorrect');

		$reader = $source->getReader();
		$this->assertNotNull($reader, "Reader was null");
	}

	function testLookupAws() {
		$_SERVER['CloudFront-Viewer-Country'] = 'de';
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		
		$this->assertValidGeoIP2Record($ret, GEOIP_DETECT_TEST_IP, true);
		$this->assertSame('header', $ret->extra->source);
		$this->assertSame(null, $ret->mostSpecificSubdivision->isoCode);
		$this->assertSame('DE', $ret->country->isoCode);
		$this->assertSame('Germany', $ret->country->name);
		$this->assertSame(GEOIP_DETECT_TEST_IP, $ret->traits->ipAddress);
		
		$_SERVER['CloudFront-Viewer-Country'] = '';
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		
		$this->assertEmptyGeoIP2Record($ret, GEOIP_DETECT_TEST_IP);
		$this->assertSame('header', $ret->extra->source);
		$this->assertSame(null, $ret->country->isoCode);
		$this->assertSame(GEOIP_DETECT_TEST_IP, $ret->traits->ipAddress);
	}
	
	function testLookupCloudflare() {
		add_filter('pre_option_geoip-detect-header-provider', array($this, 'filter_set_provider_cloudflare'), 102);
		$_SERVER['HTTP_CF_IPCOUNTRY'] = 'us';
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		
		$this->assertValidGeoIP2Record($ret, GEOIP_DETECT_TEST_IP, true);
		$this->assertSame('header', $ret->extra->source);
		$this->assertSame(null, $ret->mostSpecificSubdivision->isoCode);
		$this->assertSame('US', $ret->country->isoCode);
		$this->assertSame(GEOIP_DETECT_TEST_IP, $ret->traits->ipAddress);
		
		
		$_SERVER['HTTP_CF_IPCOUNTRY'] = 'xx';
		$ret = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		
		$this->assertEmptyGeoIP2Record($ret, GEOIP_DETECT_TEST_IP);
		$this->assertSame('header', $ret->extra->source);
		$this->assertSame(null, $ret->country->isoCode);
		$this->assertSame(GEOIP_DETECT_TEST_IP, $ret->traits->ipAddress);
	}
	

}
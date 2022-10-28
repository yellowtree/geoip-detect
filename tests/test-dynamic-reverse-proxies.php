
<?php
// Temporarily, include it here
require_once(GEOIP_PLUGIN_DIR . '/lib/dynamic-reverse-proxies/abstract.php');

class DynamicReverseProxyTest extends WP_UnitTestCase_GeoIP_Detect {

	function testGetDataProvider() {
		$object = \YellowTree\GeoipDetect\DynamicReverseProxies\DataManager::getDataProvider('aws');
		$object = \YellowTree\GeoipDetect\DynamicReverseProxies\DataManager::getDataProvider('nonesense');
		$this->assertSame(null, $object, 'DataProvider for nonesense was not null');
	}

	/**
	 * @group external-http
	 */
	function testDynamicAws() {
		$object = \YellowTree\GeoipDetect\DynamicReverseProxies\DataManager::getDataProvider('aws');
		$ips = $object->getIps();

		$this->assertGreaterThan(40, count($ips));
		$this->assertContains('120.52.22.96/27', $ips);
	}

    /**
	 * @group external-http
	 */
    function testReload() {
        $manager = new \YellowTree\GeoipDetect\DynamicReverseProxies\DataManager('cloudflare');
        $this->assertTrue($manager->reload(), 'Reload didnt work');
        $ips = $manager->getIpsFromCache();
        $this->assertGreaterThan(15, count($ips));
    }
	
	/**
	 * @group external-http
	 */
	function testDynamicCloudflare() {
		$object = \YellowTree\GeoipDetect\DynamicReverseProxies\DataManager::getDataProvider('cloudflare');
		$ips = $object->getIps();

		$this->assertGreaterThan(15, count($ips));
		$this->assertContains('190.93.240.0/20', $ips);
		$this->assertContains('2405:b500::/32', $ips);
	}
}
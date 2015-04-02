<?php

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

class DataSourcesTest extends WP_UnitTestCase_GeoIP_Detect {
	
	/**
	 * @var DataSourceRegistry
	 */
	private $registry; 

	public function setUp() {
		parent::setUp();
		$this->registry = DataSourceRegistry::getInstance();
	} 

	public function testPresenceOfAllDataSources() {
		$sources = $this->registry->getAllSources();
		$source_ids = array_keys($sources);
		sort($source_ids);
		
		$this->assertSame(array('auto', 'hostinfo', 'manual', 'precision'), $source_ids);
	}
	
	public function testEachSourceForFormalValidity() {
		$sources = $this->registry->getAllSources();
		
		foreach ($sources as $source) {
			$id = $source->getId();
			$this->assertRegExp('/^[-_a-z0-9]+$/i', $id, 'Invalid chars in id name');
			
			$label = $source->getLabel();
			$this->assertNotEmpty($label, 'Label of "' . $id . '" missing.');
			
			$desc = geoip_detect2_get_current_source_description($id);
			$this->assertNotEmpty($desc, 'Description of "' . $id . '" missing.');
		}
	}
}
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
	
	public function testEachSourceForFormalValidity() {
		$sources = $this->registry->getAllSources();
		
		$this->assertSame(3, count($sources), 'Not all sources where found ...');
		foreach ($sources as $source) {
			$id = $source->getId();
			$this->assertRegExp('/^[-_a-z0-9]+$/i', $id);
			
			$label = $source->getLabel();
			$this->assertNotEmpty($label, 'Label of "' . $id . '" missing.');
			
			$desc = geoip_detect2_get_current_source_description($id);
			$this->assertNotEmpty($desc, 'Description of "' . $id . '" missing.');
		}
	}
}
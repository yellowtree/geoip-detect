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
		
		echo count($sources) . ' datasources found.';
		foreach ($sources as $source) {
			if (false) $source = new AbstractDataSource();
			$id = $source->getId();
			$this->assertRegExp('/^[-_a-z0-9]*$/i', $id);
			
			$label = $source->getLabel();
			$this->assertNotEmpty($label);
		}
	}
}
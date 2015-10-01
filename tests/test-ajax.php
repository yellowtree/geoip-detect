<?php

class AjaxTest extends WP_UnitTestCase_GeoIP_Detect {

	function setUp()
	{
		parent::setUp();
	}

	function tearDown()
	{
		parent::tearDown();
	}
	
	function testCountryExists() {
		$data = _geoip_detect_ajax_get_data(array('de', 'en'));
		$this->assertEquals('Deutschland', $data['country']['name']);
		
		$data = _geoip_detect_ajax_get_data(array('nn', 'en'));
		$this->assertEquals('Germany', $data['country']['name']);
		
		$data = _geoip_detect_ajax_get_data(array('nn', 'mm'));
		$this->assertEquals('', $data['country']['name']);
		
		$data = _geoip_detect_ajax_get_data(array('fr'));
		$this->assertEquals('Allemagne', $data['country']['name']);	
	}
	
	function testAllValuesExist() {
		
	}
}

<?php

class ShortcodeTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function setUp() {
		parent::setUp();
		add_filter('geoip_detect_get_external_ip_adress', 'geoip_detect_get_external_ip_adress_test_set_test_ip', 101);
		$this->assertEquals(GEOIP_DETECT_TEST_IP, geoip_detect_get_external_ip_adress());
	}
	
	function tearDown() {
		parent::tearDown();
		remove_filter('geoip_detect_get_external_ip_adress', 'geoip_detect_get_external_ip_adress_test_set_test_ip', 101);
	}
	
	function testShortcodeOneProperty() {
		$string = do_shortcode('[geoip_detect2 property="country"]');
		$this->assertNotEmpty($string, '[geoip_detect2 property="country"]', "The Geoip Detect shortcode did not generate any output");
		$this->assertNotEquals($string, '[geoip_detect2 property="country"]', "The Geoip Detect shortcode does not seem to be called");
		$this->assertNotContains('<!--', $string, "Geoip Detect shortcode threw an error: " . $string);
	}
	
	function testShortcodeOnePropertyOutput() {
		$string = do_shortcode('[geoip_detect2 property="country"]');
		$this->assertEquals('Germany', do_shortcode('[geoip_detect2 property="country"]'));
	}
	
	function testShortcodeTwoPropertiesOutput() {
		$this->assertEquals('Germany', do_shortcode('[geoip_detect2 property="country.name"]'));
		$this->assertEquals('de', do_shortcode('[geoip_detect2 property="country.isoCode"]'));
		$this->assertEquals('de', do_shortcode('[geoip_detect2 property="country.iso_code"]')); // ?
	}
	function testLang() {
		$this->assertEquals('Deutschland', do_shortcode('[geoip_detect2 property="country" lang="de"]'));
	}
	
	function testDefaultValue() {	
		$this->assertEquals('default value', do_shortcode('[geoip_detect2 property="country.confidence" default="default value"]'));
	}
	
	function testInvalidShortcode() {
		$string = do_shortcode('[geoip_detect2 property="INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="city.INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="INVALID" default="here"]');
		$this->assertContains('here', $string, "Geoip Detect Shortcode does not contain default value: " . $string);
	}
}
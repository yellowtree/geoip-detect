<?php

function geoip_detect2_test_set_test_database()
{
	return GEOIP_DETECT_TEST_DB_FILENAME;
}

class ApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function testLookup() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		
		$record = geoip_detect2_get_info_from_current_ip();
		$this->assertValidGeoIP2Record($record, 'current_ip');
		
		$record = geoip_detect2_get_info_from_ip('garbage');
		$this->assertInstanceOf('GeoIp2\Model\City', $record, 'Garbage IP did not return a record object');
		$this->assertSame(true, $record->traits->isEmpty);
		$this->assertEquals(false, $record->country->name);
	}
	
	function testLocale() {
		$record = new GeoIp2\Model\City(array());
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('en'));
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertEquals('Germany', $record->country->name);
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('de'));
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertEquals('Deutschland', $record->country->name);

		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('nn', 'mm', 'de'));
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertEquals('Deutschland', $record->country->name);
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, array('nn', 'mm'));
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertSame(null, $record->country->name);	
	}
	
	function testShortcode() {
		add_filter('geoip_detect_get_external_ip_adress', 'geoip_detect_get_external_ip_adress_test_set_test_ip', 101);
		$this->assertEquals(GEOIP_DETECT_TEST_IP, geoip_detect_get_external_ip_adress());
		
		$string = do_shortcode('[geoip_detect2 property="country"]');
		$this->assertNotEmpty($string, '[geoip_detect2 property="country"]', "The Geoip Detect shortcode did not generate any output");
		$this->assertNotEquals($string, '[geoip_detect2 property="country"]', "The Geoip Detect shortcode does not seem to be called");
		$this->assertNotContains('<!--', $string, "Geoip Detect shortcode threw an error: " . $string);
		
		$string = do_shortcode('[geoip_detect2 property="country"]');
		$this->assertEquals('Germany', do_shortcode('[geoip_detect2 property="country"]'));
		$this->assertEquals('Germany', do_shortcode('[geoip_detect2 property="country.name"]'));
		$this->assertEquals('de', do_shortcode('[geoip_detect2 property="country.isoCode"]'));
		$this->assertEquals('de', do_shortcode('[geoip_detect2 property="country.iso_code"]')); // ?
		
		$this->assertEquals('Deutschland', do_shortcode('[geoip_detect2 property="country" lang="de"]'));
		
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

<?php

class ApiTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function tearDown() {
		parent::tearDown();
		$_SERVER['REMOTE_ADDR'] = '';
	}
	
	function testCurrentIp() {			
		$record = geoip_detect2_get_info_from_current_ip();
		$this->assertValidGeoIP2Record($record, 'current_ip');
	}
	
	function testLookup() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertSame(null, $record->city->confidence);
		
		$record = geoip_detect2_get_info_from_ip('garbage');
		$this->assertInstanceOf('GeoIp2\Model\City', $record, 'Garbage IP did not return a record object');
		$this->assertInstanceOf('YellowTree\GeoipDetect\DataSources\City', $record, 'Garbage IP did not return a wordpress record object');
		$this->assertNotEmpty($record->extra->error);
		$this->assertContains('is not a valid IP', $record->extra->error);
		
		$this->assertSame(true, $record->isEmpty);
		$this->assertSame(null, $record->country->name);
	}
	
	function testErrorLookup() {
		$record = geoip_detect2_get_info_from_ip('1.0.0.146,40.196.197.115');
		$this->assertInstanceOf('GeoIp2\Model\City', $record, 'Garbage IP did not return a record object');
		$this->assertInstanceOf('YellowTree\GeoipDetect\DataSources\City', $record, 'Garbage IP did not return a wordpress record object');
		$this->assertNotEmpty($record->extra->error);
		$this->assertContains('is not a valid IP', $record->extra->error);
		
		$this->assertSame(true, $record->isEmpty);
		$this->assertSame(null, $record->country->name);	
	}
	
	function testEmptyLookup() {
		$this->assertFalse(geoip_detect_is_public_ip('0.0.0.0'), '0.0.0.0 should not be a public IP');
		$this->assertTrue(geoip_detect_is_ip('0.0.0.0'), '0.0.0.0 should be an IP');
		$this->assertTrue(geoip_detect_is_ip_equal('0.0.0.0', '0.0.0.0'), '0.0.0.0 should work with equal');
		
		$record = geoip_detect2_get_info_from_ip('0.0.0.0'); //Fallback to external IP
		$this->assertSame(false, $record->isEmpty);
		$this->assertSame(GEOIP_DETECT_TEST_IP, $record->traits->ipAddress);
		$this->assertSame('Eschborn', $record->city->name);	
		$this->assertSame('🇩🇪', $record->extra->flag);	
		$this->assertSame('+49', $record->extra->tel);	
		$this->assertSame('DEU', $record->extra->countryIsoCode3);	
		$this->assertSame('EUR', $record->extra->currencyCode);	
	}
	
	function testExtendedRemoteAddr() {
		$_SERVER['REMOTE_ADDR'] = '1.1.1.1, ' . GEOIP_DETECT_TEST_IP; 
		$record = geoip_detect2_get_info_from_current_ip();
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
	}
	
		
	function testIPv6() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP_V_6);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP_V_6);
		$this->assertSame('IE', $record->country->isoCode);
	}
	
	function testWhitespace() {
		$record = geoip_detect2_get_info_from_ip('  ' . GEOIP_DETECT_TEST_IP . '   ');
		$this->assertValidGeoIP2Record($record, '  ' . GEOIP_DETECT_TEST_IP . '   ');
		$record = geoip_detect2_get_info_from_ip('  ' . GEOIP_DETECT_TEST_IP_V_6 . '   ');
		$this->assertValidGeoIP2Record($record, '  ' . GEOIP_DETECT_TEST_IP_V_6 . '   ');	
	}
	
	function testLocale() {
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
	
	function testDescription() {
		$this->assertNotEmpty(geoip_detect2_get_current_source_description());
		
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertNotEmpty(geoip_detect2_get_current_source_description($record));
		
		$desc = do_shortcode('[geoip_detect2_get_current_source_description]');
		$this->assertNotSame('[geoip_detect2_get_current_source_description]', $desc, 'Shortcode was not executed.');
		$this->assertNotEmpty($desc, 'Shortcode returned empty string');
	}
	
	function testFillInTimezone() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertSame('Europe/Berlin', $record->location->timeZone, 'Timezone must be dectected via country');
		
		$record = geoip_detect2_get_info_from_ip('8.8.8.8');
		$this->assertValidGeoIP2Record($record, '8.8.8.8');
		$this->assertSame('America/Los_Angeles', $record->location->timeZone, 'Timezone must be dectected via country/state');
	}

	function testBodyClass() {
		$classes = geoip_detect2_get_body_classes();
		$this->assertContains('geoip-continent-EU', $classes, var_export($classes, true));
		$this->assertContains('geoip-country-DE', $classes, var_export($classes, true));
		$this->assertContains('geoip-province-HE', $classes, var_export($classes, true));

		// Deactivate this test for now - the test file seems to be too old
		// $this->assertContains('geoip-country-is-in-european-union', $classes, var_export($classes, true));
	}

	function testCamelcase() {

		$this->assertSame('helloWorld', _geoip_dashes_to_camel_case('hello_world'));
		$this->assertSame('helloWorld.youAreLoved', _geoip_dashes_to_camel_case('hello_world.you_are_loved'));
		$this->assertSame('helloWorld.youAreLoved.really', _geoip_dashes_to_camel_case('hello_world.you_are_loved.really'));
	}

	function testEnqueue() {
		geoip_detect2_enqueue_javascript();
	}
}

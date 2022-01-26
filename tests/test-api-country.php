<?php

class CountryApiTest extends WP_UnitTestCase_GeoIP_Detect {

	function filter_set_test_database() {
		return GEOIP_DETECT_TEST_COUNTRY_DB_FILENAME;
	}
	
	function testCurrentIp() {
		$record = geoip_detect2_get_info_from_current_ip();
		$this->assertValidGeoIP2Record($record, 'current_ip');
	}

	function testLookup() {
		$record = geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, [ 'en' ]);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertSame(null, $record->city->name);
		$this->assertSame('Germany', $record->country->name);
	}
}
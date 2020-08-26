<?php

define('CCPA_TEST_IP', '1.1.1.1');

class CcpaTest extends WP_UnitTestCase_GeoIP_Detect {

    protected $ccpaBlacklistStub = [CCPA_TEST_IP];

    public function setBlacklist() {
        return $this->ccpaBlacklistStub;
    }

    public function testLookup() {
        $record = geoip_detect2_get_info_from_ip(CCPA_TEST_IP);
        $this->assertSame(true, $record->isEmpty, 'The CCPA blacklist didnt work');
        $this->assertNotEmpty($record->extra->error);
    }
}
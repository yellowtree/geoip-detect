<?php

class AjaxTest extends WP_UnitTestCase_GeoIP_Detect {

    function testFunction() {
        $ret = _geoip_detect_ajax_get_data();
        $this->assertValidGeoIP2Record(new \YellowTree\GeoipDetect\DataSources\City($ret, ['en']), GEOIP_DETECT_TEST_IP);
        $this->assertSame(2929134, $ret['city']['geoname_id']);
        $this->assertSame('Eschborn', $ret['city']['names']['de']);
    }
}
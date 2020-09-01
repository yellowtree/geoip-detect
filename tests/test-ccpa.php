<?php

class CcpaTest extends WP_UnitTestCase_GeoIP_Detect {

    protected $ccpaBlacklistStub = [];

    public function createBlacklist() 
    {
        $ccpaBlacklistStub = [];

        $ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => '1.1.1.1'
        ];
        $ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => '2.2.2.2/24'
        ];
        $ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => '2:2:2::2/48' // @see https://www.vultr.com/resources/subnet-calculator-ipv6/?ipv6_address=2%3A2%3A2%3A2%3A%3A2&display=short&prefix_length=48
        ];
        $this->ccpaBlacklistStub = $ccpaBlacklistStub;
    }
    public function setBlacklist($list)
    {
        return $this->ccpaBlacklistStub;
    }

	public function setUp() {
        parent::setUp();
        $this->createBlacklist();
        YellowTree\GeoipDetect\Lib\CcpaBlacklistOnLookup::resetList();
        add_filter   ('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', array($this, 'setBlacklist'), 101);
	}
	public function tearDown() {
        parent::tearDown();
        
        remove_filter('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', array($this, 'setBlacklist'), 101);
	}


    public function testLookup() {
        $record = geoip_detect2_get_info_from_ip('1.1.1.1');
        $this->assertEmptyGeoIP2Record($record, '1.1.1.1');
        $this->assertEmpty($record->country->name, 'The CCPA blacklist didnt work');
        $this->assertNotEmpty($record->extra->error);
        $this->assertContains('mytest', $record->extra->error);
    }

    public function testOtherIps() {
        $record = geoip_detect2_get_info_from_ip('2.2.2.2');
        $this->assertContains('mytest', $record->extra->error);

        $record = geoip_detect2_get_info_from_ip('2.2.2.254');
        $this->assertContains('mytest', $record->extra->error);
        
        $record = geoip_detect2_get_info_from_ip('2.2.3.2');
        $this->assertNotContains('mytest', $record->extra->error);
    }

    public function testOtherIpsV6() {
        $record = geoip_detect2_get_info_from_ip('2:2:2:2::2');
        $this->assertEmptyGeoIP2Record($record, '2:2:2:2::2');
        $this->assertContains('mytest', $record->extra->error);

        $record = geoip_detect2_get_info_from_ip('2:2:2:2::1');
        $this->assertContains('mytest', $record->extra->error);
    }

    public function testIpsThatAreNotBlacklisted() {
        $ipv4 = '8.8.8.8';
        $record = geoip_detect2_get_info_from_ip($ipv4);
        $this->assertValidGeoIP2Record($record, $ipv4);
        $this->assertNotContains('mytest', $record->extra->error);

        $ipv6 = '2:2:3:2::2';
        $record = geoip_detect2_get_info_from_ip($ipv6);
        $this->assertNotContains('mytest', $record->extra->error);
    }
}
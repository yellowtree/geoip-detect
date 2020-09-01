<?php

define('CCPA_TEST_IP', '1.1.1.1');
define('CCPA_TEST_IP_NETWORK', '2.2.2.2/24');
define('CCPA_TEST_IP_NETWORK_IPV6', '2:2:2:2::2/24');

class CcpaTest extends WP_UnitTestCase_GeoIP_Detect {

    protected $ccpaBlacklistStub = [];

    public function createBlacklist() 
    {
        $this->ccpaBlacklistStub = [];

        $this->ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => CCPA_TEST_IP
        ];
        $this->ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => CCPA_TEST_IP_NETWORK
        ];
        $this->ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => CCPA_TEST_IP_NETWORK_IPV6
        ];
    }
    public function setBlacklist($list)
    {var_dump($this->ccpaBlacklistStub);die('dd');
        return $this->ccpaBlacklistStub;
    }

	public function setUp() {
        parent::setUp();
        $this->createBlacklist();
		add_filter   ('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', array($this, 'setBlacklist'));
	}
	public function tearDown() {
		parent::tearDown();
        
        remove_filter('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', array($this, 'setBlacklist'));
	}


    public function testLookup() {
        add_filter   ('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', array($this, 'setBlacklist'));
        $record = geoip_detect2_get_info_from_ip(CCPA_TEST_IP);
        $this->assertEmpty($record->country->name, 'The CCPA blacklist didnt work');
        $this->assertSame(true, $record->isEmpty);
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
        $this->assertContains('mytest', $record->extra->error);

        $record = geoip_detect2_get_info_from_ip('2:2:2:2::1');
        $this->assertContains('mytest', $record->extra->error);
        
        $record = geoip_detect2_get_info_from_ip('2:2:3:2::2');
        $this->assertNotContains('mytest', $record->extra->error);
    }
}
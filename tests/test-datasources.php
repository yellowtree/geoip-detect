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
	
	public function tearDown() {
		parent::tearDown();
		remove_filter('pre_option_geoip-detect-source', [ $this, 'filter_set_invalid_default_source' ], 105);
		remove_filter('pre_option_geoip-detect-source', [ $this, 'filter_set_wrong_default_source' ], 106);
	}
	
	public function filter_set_invalid_default_source() {
		return 'invalid';
	}
	
	public function filter_set_wrong_default_source() {
		return 'header';
	}

	public function testPresenceOfAllDataSources() {
		$sources = $this->registry->getAllSources();
		$source_ids = array_keys($sources);
		sort($source_ids);
		
		$this->assertSame([ 'auto', 'header', 'hostinfo', 'ipstack', 'manual', 'precision' ], $source_ids);
	}
	
	public function testInvalidDatasources() {
		try {
			$ret = $this->registry->getSource('invalid');
		} catch (PHPUnit_Framework_Error_Notice $e) {
			$this->assertSame(false, false);
			$msg = $e->getMessage();
			if (strpos($msg, 'no such source was found') !== false)
				return;
			throw $e;
		}
		$this->fail('No notice thrown in spite of invalid datasource');
	}
	
	public function testInvalidCurrentDatasource() {
		add_filter('pre_option_geoip-detect-source', [ $this, 'filter_set_invalid_default_source' ], 105);
		
		try {
			$source = $this->registry->getCurrentSource();
		} catch (PHPUnit_Framework_Error_Notice $e) {
			$this->assertSame(false, false);
			$msg = $e->getMessage();
			if (strpos($msg, 'no such source was found') !== false)
				return;
			throw $e;
		}
		$this->fail('No notice thrown in spite of invalid current datasource');
	}
	
	public function testManualOverrideDatasource() {
		add_filter('pre_option_geoip-detect-source', [ $this, 'filter_set_wrong_default_source' ], 106);
		$source = $this->registry->getCurrentSource();
		$this->assertEquals('header', $source->getId());
			
		// Test lookup with manual source name
		$record =  geoip_detect2_get_info_from_ip(GEOIP_DETECT_TEST_IP, [ 'en' ], [ 'source' => 'manual' ]);
		$this->assertValidGeoIP2Record($record, GEOIP_DETECT_TEST_IP);
		$this->assertEquals('Germany', $record->country->name);
	}
	
	public function testEachSourceForFormalValidity() {
		$sources = $this->registry->getAllSources();
		
		if (!count($sources)) {
			$this->fail('There should be at least one source available?!');
		}
		foreach ($sources as $source) {
			$id = $source->getId();
			$this->assertRegExp('/^[-_a-z0-9]+$/i', $id, 'Invalid chars in id name');
			
			$label = $source->getLabel();
			$this->assertNotEmpty($label, 'Label of "' . $id . '" missing.');
			
			$desc = geoip_detect2_get_current_source_description($id);
			$this->assertNotEmpty($desc, 'Description of "' . $id . '" missing.');
		}
	}

    /**
     * @group admin_notices
     *
     * @ticket 22
     *
    public function testAdminNoticeDatabaseMissingRequiresManageOptionsCapability () {
        $args = [ 'role' => 'subscriber' ];
        if (!$this->factory) {
            $this->factory = new WP_UnitTest_Factory();
            $args['user_login'] = hash('md5', uniqid() . microtime());
            $args['user_email'] = hash('md5', uniqid() . microtime()) . '@example.invalid';
        }
        $id = $this->factory->user->create($args);
        wp_set_current_user($id);
        $this->expectOutputString('');
        geoip_detect_admin_notice_database_missing();
    }
*/
    /**
     * @group admin_notices
     *
     * @ticket 22
     */
    public function testAdminNoticeDatabaseMissingPrintsOutputWithManageOptionsCapability () {
        $args = [ 'role' => 'administrator' ];
        if (!$this->factory) {
            $this->factory = new WP_UnitTest_Factory();
            $args['user_login'] = hash('md5', uniqid() . microtime());
            $args['user_email'] = hash('md5', uniqid() . microtime()) . '@example.invalid';
        }
        $id = $this->factory->user->create($args);
        wp_set_current_user($id);
        $this->expectOutputRegex('/' . __('Geolocation IP Detection: No database installed', 'geoip-detect') . '/');
        geoip_detect_admin_notice_database_missing();
    }

}

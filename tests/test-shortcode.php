<?php

function shortcode_empty_reader() {
	return null;
}

class ShortcodeTest extends WP_UnitTestCase_GeoIP_Detect {

	function setUp() {
		parent::setUp();
		add_filter('geoip_detect_get_external_ip_adress', array($this, 'filter_set_test_ip'), 101);
		$this->assertEquals(GEOIP_DETECT_TEST_IP, geoip_detect_get_external_ip_adress());
		setlocale(LC_NUMERIC, 'C'); // Set locale to always use . as decimal point
	}

	function tearDown() {
		parent::tearDown();
		remove_filter('geoip_detect_get_external_ip_adress', array($this, 'filter_set_test_ip'), 101);
        remove_filter('geoip2_detect2_client_ip', array($this, 'filter_set_test_ip'), 101);
		remove_filter('geoip_detect2_reader', 'shortcode_empty_reader', 101);
		remove_filter('geoip2_detect_sources_not_cachable', array($this, 'filter_empty_array'), 101);
		remove_filter('geoip_detect2_shortcode_country_select_countries', array($this, 'shortcodeFilter'), 101);
	}

	function testShortcodeOneProperty() {
		$string = do_shortcode('[geoip_detect2 property="country"]');
		$this->assertNotEmpty($string, '[geoip_detect2 property="country"]', "The Geoip Detect shortcode did not generate any output");
		$this->assertNotEquals($string, '[geoip_detect2 property="country"]', "The Geoip Detect shortcode does not seem to be called");
		$this->assertNotContains('<!--', $string, "Geoip Detect shortcode threw an error: " . $string);
	}

	function testShortcodeProperties() {
		$this->assertEquals('Hesse', do_shortcode('[geoip_detect2 property="mostSpecificSubdivision"]'));
		$this->assertEquals('HE', do_shortcode('[geoip_detect2 property="mostSpecificSubdivision.isoCode"]'));
		$this->assertEquals(GEOIP_DETECT_TEST_IP, do_shortcode('[geoip_detect2 property="traits.ipAddress"]'));
		$this->assertEquals('Eschborn', do_shortcode('[geoip_detect2 property="city"]'));
		$this->assertEquals('Europe/Berlin', do_shortcode('[geoip_detect2 property="location.timeZone"]'));
		$this->assertEquals('Europe', do_shortcode('[geoip_detect2 property="continent"]'));
		$this->assertEquals('EU', do_shortcode('[geoip_detect2 property="continent.code"]'));
	}
	function testSubdivisionProperties() {
		$this->assertEquals('HE', do_shortcode('[geoip_detect2 property="subdivisions.0.isoCode"]'));
		$this->assertEquals('Hesse', do_shortcode('[geoip_detect2 property="subdivisions.0"]'));
		$this->assertContains('<!--',do_shortcode('[geoip_detect2 property="subdivisions.1"]'));
		$this->assertContains('<!--',do_shortcode('[geoip_detect2 property="subdivisions.wrong"]'));
	}
	function testNonStringProperties() {
		$this->assertEquals('2929134', do_shortcode('[geoip_detect2 property="city.geonameId"]'));
		$this->assertEquals('50.1333', do_shortcode('[geoip_detect2 property="location.latitude"]'));
	}

	function testShortcodeOnePropertyOutput() {
		$this->assertEquals('Germany', do_shortcode('[geoip_detect2 property="country"]'));
	}

	function testShortcodeTwoPropertiesOutput() {
		$this->assertEquals('Germany', do_shortcode('[geoip_detect2 property="country.name"]'));
		$this->assertEquals('DE', do_shortcode('[geoip_detect2 property="country.isoCode"]'));
	}
	function testLang() {
		$this->assertEquals('Deutschland', do_shortcode('[geoip_detect2 property="country" lang="de"]'));
		$this->assertEquals('Deutschland', do_shortcode('[geoip_detect2 property="country" lang="zz,de"]'));
		$this->assertEquals('Deutschland', do_shortcode('[geoip_detect2 property="country" lang=" zz, de "]'));
	}

	function testManualIp() {
		$this->assertEquals('US', do_shortcode('[geoip_detect2 property="country.isoCode" ip="8.8.8.8"]'));
	}

	function testDefaultValue() {
		$this->assertEquals('default value', do_shortcode('[geoip_detect2 property="country.confidence" default="default value"]'));
	}

	function testInvalidShortcode() {
		$this->assertContains('sub-property missing', do_shortcode('[geoip_detect2 property="location"]'));

		$string = do_shortcode('[geoip_detect2 property="INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"INVALID\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="city.INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"city.INVALID\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="INVALID.city"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"INVALID.city\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="city.names.INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"city.names.INVALID\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="INVALID" default="here"]');
		$this->assertContains('here', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"INVALID\" default=\"here\"]does not contain default value: " . $string);
	}

	function testEmptyData() {
		// Force fallback to empty database. We want to test the case that no information is found.
		// This can be the case, for example when a development machine has no internet access and thus cannot get any external IP.
		add_filter('geoip_detect2_reader', 'shortcode_empty_reader', 101);

		$string = do_shortcode('[geoip_detect2 property="city" default="default"]');
		$this->assertContains('No information found', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"city\"] should inform when no information accessible to this IP: " . $string);
		$this->assertContains('default', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"city\"] should use default: " . $string);
	}

	function filter_empty_array() {
		return array();
	}

	function testSkipCache() {
		// enable caching
		add_filter('geoip2_detect_sources_not_cachable', array($this, 'filter_empty_array'), 101);

		// Make sure this is in the cache
		do_shortcode('[geoip_detect2 property="extra.cached"]');

		$cached_time = do_shortcode('[geoip_detect2 property="extra.cached"]');
		$this->assertNotEmpty($cached_time, 'Cache property cannot be read');
		$this->assertNotEquals('0', $cached_time, 'Normally, this request should be cached.');

		$this->assertEquals('', do_shortcode('[geoip_detect2 property="extra.cached" skip_cache="true"]'), 'skip_cache parameter ignored?');
		$this->assertEquals('', do_shortcode('[geoip_detect2 property="extra.cached" skip_cache="TRUE"]'), 'skip_cache parameter ignored?');
		$this->assertEquals('', do_shortcode('[geoip_detect2 property="extra.cached" skip_cache="1"]'), 'skip_cache parameter ignored?');

		$this->assertEquals('default', do_shortcode('[geoip_detect2 property="extra.cached" skip_cache="true" default="default"]', 'default value does not work together with skip_cache'));
	}

    public function testShortcodeCF7UserInfo() {
        add_filter('geoip_detect2_client_ip', array($this, 'filter_set_test_ip'), 101);

        $this->assertEquals('', geoip_detect2_shortcode_user_info_wpcf7('', 'asdfsadf', false));

        $userInfo = geoip_detect2_shortcode_user_info_wpcf7('', 'geoip_detect2_user_info', false);
        $this->assertNotEmpty($userInfo);
        $this->assertContains(GEOIP_DETECT_TEST_IP, $userInfo);
        $this->assertContains('Country: Germany', $userInfo);
        $this->assertContains('State or region: Hesse' , $userInfo);
        $this->assertContains('City: Eschborn' , $userInfo);
		$this->assertContains('Data from: GeoLite2 City database' , $userInfo);
    }

	public function testShortcodeCountrySelect() {
		$html = do_shortcode('[geoip_detect2_countries include_blank="false"]');
		$this->assertNotContains('---', $html, 'Should not contain blank');
		$this->assertNotContains('[geoip_detect2_countries', $html, 'Shortcode was not found.');
		$this->assertContains('name="geoip-countries"', $html);
		$this->assertContains('Germany', $html);
		$this->assertContains('"selected">Germany', $html);

		$html = geoip_detect2_shortcode_country_select(array('include_blank' => false));
		$this->assertNotContains('---', $html, 'Should not contain blank');

		$html = do_shortcode('[geoip_detect2_countries include_blank="true"]');
		$this->assertContains('---', $html, 'Should contain blank but didn\'t');

		$html = do_shortcode('[geoip_detect2_countries selected="US"]');
		$this->assertContains('"selected">United', $html);
	}

	public function testShortcodeCountryFilter() {
		add_filter('geoip_detect2_shortcode_country_select_countries', array($this, 'shortcodeFilter'), 101, 2);

		$html = do_shortcode('[geoip_detect2_countries selected="aa"]');
		$this->assertNotContains('Germany', $html);
		$this->assertNotContains('<option>----', $html);
		$this->assertContains('value="">----', $html);
		$this->assertContains('value="">*', $html);
		$this->assertContains('A', $html);
		$this->assertContains('"selected">A', $html);
	}

	/**
	 * @dataProvider dataShortcodeShowIf
	 */
	public function testShortcodeShowIf($result, $txt) {
		$return = do_shortcode($txt);
		$this->assertSame($result, $return);
	}

	/**
	 * @dataProvider dataShortcodeShowIf
	 */
	public function testShortcodeHideIf($result, $txt) {
		// Negate the tests
		$txt = str_replace('geoip_detect2_show_if', 'geoip_detect2_hide_if', $txt);
		$result = $result ? '' : 'yes';

		$return = do_shortcode($txt);
		$this->assertSame($result, $return);
	}

	public function dataShortcodeShowIf() {
		return array(
		/* #0 */		array('no condition', '[geoip_detect2_show_if]no condition[/geoip_detect2_show_if]' ),
		/* #1 */		array('yes', '[geoip_detect2_show_if country="DE"]yes[/geoip_detect2_show_if]' ),
		/* #2 */		array('yes', '[geoip_detect2_show_if country="de"]yes[/geoip_detect2_show_if]' ),
		/* #3 */		array('yes', '[geoip_detect2_show_if country="Germany"]yes[/geoip_detect2_show_if]' ),
		/* #4 */		array('yes', '[geoip_detect2_show_if country="germany"]yes[/geoip_detect2_show_if]' ),
		/* #5 */		array('',    '[geoip_detect2_show_if country="US"]yes[/geoip_detect2_show_if]' ),
		/* #6 */		array('',    '[geoip_detect2_show_if country="DE" city="Munic" lang="en"]yes[/geoip_detect2_show_if]' ),
		/* #7 */		array('yes', '[geoip_detect2_show_if country="DE" city="Eschborn"]yes[/geoip_detect2_show_if]' ),
		/* #8 */		array('lang', '[geoip_detect2_show_if lang="es" country="Alemania"]lang[/geoip_detect2_show_if]' ),
		/* #8 */		array('', '[geoip_detect2_show_if lang="en" country="Alemania"]yes[/geoip_detect2_show_if]' ),
		/* #9 */		array('yes', '[geoip_detect2_show_if state="HE"]yes[/geoip_detect2_show_if]' ),
		/* #10 */		array('yes', '[geoip_detect2_show_if region="HE"]yes[/geoip_detect2_show_if]' ),
		/* #11 */		array('yes', '[geoip_detect2_show_if most_specific_subdivision="HE"]yes[/geoip_detect2_show_if]' ),
		/* #12 */		array('',    '[geoip_detect2_show_if state="NN"]yes[/geoip_detect2_show_if]' ),
		/* #13 */		array('yes', '[geoip_detect2_show_if continent="EU"]yes[/geoip_detect2_show_if]' ),
		/* #14 */		array('yes', '[geoip_detect2_show_if timezone="Europe/Berlin"]yes[/geoip_detect2_show_if]' ),
		/* #15 */		array('not_country', '[geoip_detect2_show_if not_country="FR"]not_country[/geoip_detect2_show_if]' ),
		/* #16 */		array('', '[geoip_detect2_show_if not_country="DE"]yes[/geoip_detect2_show_if]' ),
		/* #17 */		array('', '[geoip_detect2_show_if continent="EU" not_country="DE"]yes[/geoip_detect2_show_if]' ),
		/* #18 */		array('yes', '[geoip_detect2_show_if country="US, DE"]yes[/geoip_detect2_show_if]' ),
		/* #19 */		array('yes', '[geoip_detect2_show_if country="US,DE , FR"]yes[/geoip_detect2_show_if]' ),
		/* #20 */		array('', '[geoip_detect2_show_if country="US,FR"]yes[/geoip_detect2_show_if]' ),
		);
	}

	public function shortcodeFilter($countries, $attr) {
		return array(
			'aa' => 'A',
			'blank_asdfsa' => '----',
			'blank_asdf' => '*',
			'b' => 'B'
		);
	}
}

/* Data of Test IP:
array(7) {
  ["city"]=>
  array(2) {
    ["geoname_id"]=>
    int(2929134)
    ["names"]=>
    array(4) {
      ["en"]=>
      string(8) "Eschborn"
      ["ja"]=>
      string(18) "エシュボルン"
      ["ru"]=>
      string(12) "Эшборн"
      ["zh-CN"]=>
      string(15) "埃施博尔恩"
    }
  }
  ["continent"]=>
  array(3) {
    ["code"]=>
    string(2) "EU"
    ["geoname_id"]=>
    int(6255148)
    ["names"]=>
    array(8) {
      ["de"]=>
      string(6) "Europa"
      ["en"]=>
      string(6) "Europe"
      ["es"]=>
      string(6) "Europa"
      ["fr"]=>
      string(6) "Europe"
      ["ja"]=>
      string(15) "ヨーロッパ"
      ["pt-BR"]=>
      string(6) "Europa"
      ["ru"]=>
      string(12) "Европа"
      ["zh-CN"]=>
      string(6) "欧洲"
    }
  }
  ["country"]=>
  array(3) {
    ["geoname_id"]=>
    int(2921044)
    ["iso_code"]=>
    string(2) "DE"
    ["names"]=>
    array(8) {
      ["de"]=>
      string(11) "Deutschland"
      ["en"]=>
      string(7) "Germany"
      ["es"]=>
      string(8) "Alemania"
      ["fr"]=>
      string(9) "Allemagne"
      ["ja"]=>
      string(24) "ドイツ連邦共和国"
      ["pt-BR"]=>
      string(8) "Alemanha"
      ["ru"]=>
      string(16) "Германия"
      ["zh-CN"]=>
      string(6) "德国"
    }
  }
  ["location"]=>
  array(3) {
    ["latitude"]=>
    float(50,1333)
    ["longitude"]=>
    float(8,55)
    ["time_zone"]=>
    string(13) "Europe/Berlin"
  }
  ["registered_country"]=>
  array(3) {
    ["geoname_id"]=>
    int(2921044)
    ["iso_code"]=>
    string(2) "DE"
    ["names"]=>
    array(8) {
      ["de"]=>
      string(11) "Deutschland"
      ["en"]=>
      string(7) "Germany"
      ["es"]=>
      string(8) "Alemania"
      ["fr"]=>
      string(9) "Allemagne"
      ["ja"]=>
      string(24) "ドイツ連邦共和国"
      ["pt-BR"]=>
      string(8) "Alemanha"
      ["ru"]=>
      string(16) "Германия"
      ["zh-CN"]=>
      string(6) "德国"
    }
  }
  ["subdivisions"]=>
  array(1) {
    [0]=>
    array(3) {
      ["geoname_id"]=>
      int(2905330)
      ["iso_code"]=>
      string(2) "HE"
      ["names"]=>
      array(4) {
        ["de"]=>
        string(6) "Hessen"
        ["en"]=>
        string(5) "Hesse"
        ["es"]=>
        string(6) "Hessen"
        ["fr"]=>
        string(5) "Hesse"
      }
    }
  }
  ["traits"]=>
  array(1) {
    ["ip_address"]=>
    string(11) "88.64.140.3"
  }
}
 */

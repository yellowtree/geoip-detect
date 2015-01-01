<?php
/*
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
	
	function testShortcodeProperties() {
		$this->assertEquals('Hesse', do_shortcode('[geoip_detect2 property="mostSpecificSubdivision"]'));
		$this->assertEquals('HE', do_shortcode('[geoip_detect2 property="mostSpecificSubdivision.isoCode"]'));
		$this->assertEquals(GEOIP_DETECT_TEST_IP, do_shortcode('[geoip_detect2 property="traits.ipAddress"]'));
		$this->assertEquals('Eschborn', do_shortcode('[geoip_detect2 property="city"]'));
		$this->assertEquals('2929134', do_shortcode('[geoip_detect2 property="city.geonameId"]'));
		$this->assertContains('sub-property missing', do_shortcode('[geoip_detect2 property="location"]'));
		$this->assertEquals('50,1333', do_shortcode('[geoip_detect2 property="location.latitude"]'));
		$this->assertEquals('Europe/Berlin', do_shortcode('[geoip_detect2 property="location.timeZone"]'));		
		$this->assertEquals('Europe', do_shortcode('[geoip_detect2 property="continent"]'));		
		$this->assertEquals('EU', do_shortcode('[geoip_detect2 property="continent.code"]'));		
		
	}
	
	function testShortcodeOnePropertyOutput() {
		$string = do_shortcode('[geoip_detect2 property="country"]');
		$this->assertEquals('Germany', do_shortcode('[geoip_detect2 property="country"]'));
	}
	
	function testShortcodeTwoPropertiesOutput() {
		$this->assertEquals('Germany', do_shortcode('[geoip_detect2 property="country.name"]'));
		$this->assertEquals('DE', do_shortcode('[geoip_detect2 property="country.isoCode"]'));
	}
	function testLang() {
		$this->assertEquals('Deutschland', do_shortcode('[geoip_detect2 property="country" lang="de"]'));
	}
	
	function testDefaultValue() {	
		$this->assertEquals('default value', do_shortcode('[geoip_detect2 property="country.confidence" default="default value"]'));
	}
	
	function testInvalidShortcode() {
		$string = do_shortcode('[geoip_detect2 property="INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"INVALID\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="city.INVALID"]');
		$this->assertContains('<!--', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"city.INVALID\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="INVALID" default="here"]');
		$this->assertContains('here', $string, "Geoip Detect Shortcode [geoip_detect2 property=\"INVALID\" default=\"here\"]does not contain default value: " . $string);
	}
}
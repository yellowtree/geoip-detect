<?php

function shortcode_empty_reader() {
	return null;
}

class ShortcodeTest extends WP_UnitTestCase_GeoIP_Detect {

	function setUp() {
		parent::setUp();
		add_filter('geoip_detect_get_external_ip_adress', [ $this, 'filter_set_test_ip' ], 101);
		$this->assertEquals(GEOIP_DETECT_TEST_IP, geoip_detect_get_external_ip_adress());
		setlocale(LC_NUMERIC, 'C'); // Set locale to always use . as decimal point
	}

	function tearDown() {
		parent::tearDown();
		remove_filter('geoip_detect_get_external_ip_adress', [ $this, 'filter_set_test_ip' ], 101);
        remove_filter('geoip2_detect2_client_ip', [ $this, 'filter_set_test_ip' ], 101);
		remove_filter('geoip_detect2_reader', 'shortcode_empty_reader', 101);
		remove_filter('geoip2_detect_sources_not_cachable', [ $this, 'filter_empty_array' ], 101);
		remove_filter('geoip_detect2_shortcode_country_select_countries', [ $this, 'shortcodeFilter' ], 101);
		remove_filter('geoip_detect2_record_data_override_lookup', [ $this, 'filterEmptyRecordData' ], 101);
	}

	function filterEmptyRecordData() {
		$record = _geoip_detect2_record_enrich_data([], GEOIP_DETECT_TEST_IP, 'empty', 'error string');
		return $record;
	}

	function testShortcodeOneProperty() {
		$string = do_shortcode('[geoip_detect2 property="country"]');
		$this->assertNotEmpty($string, '[geoip_detect2 property="country"]', "The Geolocation IP Detection shortcode did not generate any output");
		$this->assertNotEquals($string, '[geoip_detect2 property="country"]', "The Geolocation IP Detection shortcode does not seem to be called");
		$this->assertNotContains('<!--', $string, "Geolocation IP Detection shortcode threw an error: " . $string);
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

	/* Does not work.
	function testShortcodePropertiesUnderscorized() {
		$this->assertEquals('Europe/Berlin', do_shortcode('[geoip_detect2 property="location.time_zone"]'));
		$this->assertEquals(GEOIP_DETECT_TEST_IP, do_shortcode('[geoip_detect2 property="traits.ip_address"]'));
		$this->assertEquals('Hesse', do_shortcode('[geoip_detect2 property="most_specific_subdivision"]'));
		$this->assertEquals('HE', do_shortcode('[geoip_detect2 property="most_specific_subdivision.iso_code"]'));
	}
	*/


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
		$this->assertContains('<!--', $string, "Geolocation IP Detection Shortcode [geoip_detect2 property=\"INVALID\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="city.INVALID"]');
		$this->assertContains('<!--', $string, "Geolocation IP Detection Shortcode [geoip_detect2 property=\"city.INVALID\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="INVALID.city"]');
		$this->assertContains('<!--', $string, "Geolocation IP Detection Shortcode [geoip_detect2 property=\"INVALID.city\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="city.names.INVALID"]');
		$this->assertContains('<!--', $string, "Geolocation IP Detection Shortcode [geoip_detect2 property=\"city.names.INVALID\"] threw no error in spite of invalid property name: " . $string);
		$string = do_shortcode('[geoip_detect2 property="INVALID" default="here"]');
		$this->assertContains('here', $string, "Geolocation IP Detection Shortcode [geoip_detect2 property=\"INVALID\" default=\"here\"]does not contain default value: " . $string);
	}

	function testEmptyData() {
		// Force fallback to empty database. We want to test the case that no information is found.
		// This can be the case, for example when a development machine has no internet access and thus cannot get any external IP.
		add_filter('geoip_detect2_reader', 'shortcode_empty_reader', 101);

		$string = do_shortcode('[geoip_detect2 property="city" default="default"]');
		$this->assertContains('No information found', $string, "Geolocation IP Detection Shortcode [geoip_detect2 property=\"city\"] should inform when no information accessible to this IP: " . $string);
		$this->assertContains('default', $string, "Geolocation IP Detection Shortcode [geoip_detect2 property=\"city\"] should use default: " . $string);
	}

	function filter_empty_array() {
		return [];
	}

	function testSkipCache() {
		// enable caching
		add_filter('geoip2_detect_sources_not_cachable', [ $this, 'filter_empty_array' ], 101);

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
        add_filter('geoip_detect2_client_ip', [ $this, 'filter_set_test_ip' ], 101);

        $this->assertEquals('', geoip_detect2_shortcode_user_info_wpcf7('', 'asdfsadf', false));

        $userInfo = geoip_detect2_shortcode_user_info_wpcf7('', 'geoip_detect2_user_info', false);
        $this->assertNotEmpty($userInfo);
        $this->assertContains(GEOIP_DETECT_TEST_IP, $userInfo);
        $this->assertContains('Country: Germany', $userInfo);
        $this->assertContains('State or region: Hesse' , $userInfo);
        $this->assertContains('City: Eschborn' , $userInfo);
		$this->assertContains('Data from: GeoLite2 City database' , $userInfo);
	}

	/**
	 * @dataProvider dataShortcodeCF7UserInfo
	 */
	public function testDataShortcodeCF7UserInfo($expected, $name) {
		$userInfo = geoip_detect2_shortcode_user_info_wpcf7('', $name, false);
		$this->assertSame($expected, $userInfo, 'Wrong output for CF7 special Tag [' . $name .']');
	}

	public function dataShortcodeCF7UserInfo() {
		return [
			[ GEOIP_DETECT_TEST_IP, 'geoip_detect2_get_client_ip' ],
			[ 'GeoLite2 City database', 'geoip_detect2_get_current_source_description' ],
			[ 'Germany', 'geoip_detect2_property_country' ],
			[ 'Hesse', 'geoip_detect2_property_most_specific_subdivision' ],
			[ 'Hesse', 'geoip_detect2_property_region' ],
			[ 'Hesse', 'geoip_detect2_property_state' ],
			[ 'HE', 'geoip_detect2_property_subdivisions__0__iso_code' ],
			[ 'Eschborn', 'geoip_detect2_property_city' ],
			[ 'DE', 'geoip_detect2_property_country__iso_code' ],
			[ 'DE', 'geoip_detect2_property_country__isoCode' ],
			[ '🇩🇪', 'geoip_detect2_property_extra__flag' ],
			[ '+49', 'geoip_detect2_property_extra__tel' ],
			[ 'manual', 'geoip_detect2_property_extra__source' ],
			[ 'DEU', 'geoip_detect2_property_extra__country_iso_code_3' ],
			[ 'DEU', 'geoip_detect2_property_extra__country_iso_code3' ],
			[ 'EUR', 'geoip_detect2_property_extra__currency_code' ],
			[ GEOIP_DETECT_TEST_IP, 'geoip_detect2_property_traits__ip_address' ],
		];
	}
	
	/**
	 * @dataProvider dataShortcodeCF7Filter
	 */
	public function testShortcodeCF7Filter($expectedResult, $tagname) {
		$this->assertContains($expectedResult, apply_filters('wpcf7_special_mail_tags', $tagname, $tagname, false, new \WPCF7_MailTag('', $tagname, '')));
	}

	public function dataShortcodeCF7Filter() {
		return [
			 [ 'unknown', 'unknown' ],
			 [ GEOIP_DETECT_TEST_IP, 'geoip_detect2_get_client_ip' ]
		];
	}

	public function testShortcodeCountrySelect() {
		$html = do_shortcode('[geoip_detect2_countries include_blank="false"]');
		$this->assertNotContains('---', $html, 'Should not contain blank');
		$this->assertNotContains('[geoip_detect2_countries', $html, 'Shortcode was not found.');
		$this->assertContains('name="geoip-countries"', $html);
		$this->assertContains('Germany', $html);
		$this->assertContains('"selected">Germany', $html);

		$html = geoip_detect2_shortcode_country_select([ 'include_blank' => false ]);
		$this->assertNotContains('---', $html, 'Should not contain blank');

		$html = do_shortcode('[geoip_detect2_countries include_blank="true"]');
		$this->assertContains('---', $html, 'Should contain blank but didn\'t');

		$html = do_shortcode('[geoip_detect2_countries selected="US"]');
		$this->assertContains('"selected">United', $html);
	}

	public function testShortcodeCountryFilter() {
		add_filter('geoip_detect2_shortcode_country_select_countries', [ $this, 'shortcodeFilter' ], 101, 2);

		$html = do_shortcode('[geoip_detect2_countries selected="aa"]');
		$this->assertNotContains('Germany', $html);
		$this->assertNotContains('<option>----', $html);
		$this->assertContains('value="">----', $html);
		$this->assertContains('value="">*', $html);
		$this->assertContains('A', $html);
		$this->assertContains('"selected">A', $html);
	}

	public function testShortcodeTextInput() {
		$html = do_shortcode('[geoip_detect2_text_input name="yourcity" property="city" lang="fr" id="thisismyid" class="myclassname" default="Paris" required="true"]');
		$this->assertContains('<input', $html);
		$this->assertContains('value="Eschborn"', $html);
		$this->assertContains('class="myclassname"', $html);
		$this->assertContains('id="thisismyid"', $html);
		$this->assertContains('name="yourcity"', $html);
		$this->assertContains('required', $html);

		$html = do_shortcode('[geoip_detect2_text_input name="yourcity" property="country" lang="fr" id="thisismyid" class="myclassname" default="Paris" required="true"]');
		$this->assertContains('value="Allemagne"', $html);

		$html = do_shortcode('[geoip_detect2_text_input name="postal" property="location.timeZone" type="hidden"]');
		$this->assertContains('name="postal"', $html);
		$this->assertContains('type="hidden"', $html);
		$this->assertContains('value="Europe/Berlin"', $html);
	}

	public function testShortcodeCF7Countries() {
		$countries = [ 'type' => 'geoip_detect2_countries', 'basetype' => 'geoip_detect2_countries', 'raw_name' => 'mycountry', 'name' => 'mycountry', 'options' => [ 
			'include_blank', 'autosave', 'ajax:1', 'flag', 'tel' 
		] ];
		$html = geoip_detect2_shortcode_country_select_wpcf7($countries);
		$this->assertContains('value=""', $html);
		$this->assertContains('+49', $html);
		$this->assertContains('js-geoip-detect-country-select', $html );

		$countries = [ 'type' => 'geoip_detect2_countries', 'basetype' => 'geoip_detect2_countries', 'raw_name' => 'mycountry', 'name' => 'mycountry', 'options' => [ 
			'include_blank', 'autosave', 'ajax:0', 'flag', 'tel' 
		] ];
		$html = geoip_detect2_shortcode_country_select_wpcf7($countries);
		$this->assertNotContains('js-geoip-detect-country-select', $html );
	}
	public function testShortcodeCF7Input() {
		$input = [ 'type' => 'geoip_detect2_text_input', 'basetype' => 'geoip_detect2_text_input', 'raw_name' => 'city', 'name' => 'city', 'options' => [ 
			'property:city', 'lang:fr', 'id:id', 'class:testclass', 'ajax:1', 'autosave', 
		] ];
		$html = geoip_detect2_shortcode_text_input_wpcf7($input);
		$this->assertContains('id="id"', $html);
		$this->assertContains('testclass', $html);
		$this->assertContains('js-geoip-text-input', $html );

		$input = [ 'type' => 'geoip_detect2_text_input', 'basetype' => 'geoip_detect2_text_input', 'raw_name' => 'city', 'name' => 'city', 'options' => [ 
			'property:city', 'lang:fr', 'id:id', 'class:testclass', 'ajax:0', 
		] ];
		$html = geoip_detect2_shortcode_text_input_wpcf7($input);
		$this->assertNotContains('js-geoip-text-input', $html );
	}

	/**
	 * @dataProvider dataShortcodeShowIf
	 */
	public function testShortcodeShowIf($result, $txt) {
		$return = do_shortcode($txt);
		$this->assertSame($result, $return, "Shortcode failed: " . $txt);
	}

	/**
	 * @dataProvider dataShortcodeShowIf
	 */
	public function testShortcodeHideIf($result, $txt) {
		// Negate the tests
		$txt = str_replace('geoip_detect2_show_if', 'geoip_detect2_hide_if', $txt);
		if ($result === 'hu') {
			$result = 'ha';
		} elseif ($result === 'ha') {
			$result = 'hu';
		} else {
			$result = $result ? '' : 'yes';
		}

		$return = do_shortcode($txt);
		$this->assertSame($result, $return, "Shortcode failed: " . $txt);
	}

	public function do_shortcode_geoip_detect2_test_show_if($atts) {
		$this->last_atts = (array) $atts;
	}

	protected $last_atts = false;

	private function getDataSet($data) {
		$data_set = [];
		$i = 0;
		foreach ($data as $row) {
			list($expected, $input) = $row;

			$input = str_replace('geoip_detect2_show_if', 'geoip_detect2_test_show_if', $input);
			$this->last_atts = false;
			do_shortcode($input);
			if ($this->last_atts !== false) {
				$parsed = geoip_detect2_shortcode_parse_conditions_from_attributes($this->last_atts);
				$opt = _geoip_detect2_shortcode_options($this->last_atts);
				
				if ($expected === 'ha') {
					$expected = false;
				} else {
					$expected = !! $expected;
				}
				$data_set[] = [$i, $input, $expected, $parsed, $opt ];

			}
			$i++;
		}
		return $data_set;
	}

	public function testGenerateForJS() {
		add_shortcode('geoip_detect2_test_show_if', [ $this, 'do_shortcode_geoip_detect2_test_show_if' ]);

		$data = $this->dataShortcodeShowIf();
		$data_set = $this->getDataSet($data);
		file_put_contents(__DIR__ . '/fixture_shortcode_show_if.json', json_encode($data_set, JSON_PRETTY_PRINT));

		$data = $this->dataShortcodeShowIfEmpty();
		$data_set = $this->getDataSet($data);
		file_put_contents(__DIR__ . '/fixture_shortcode_show_if_empty.json', json_encode($data_set, JSON_PRETTY_PRINT));

		$this->assertSame(true, true);
	}

	public function dataShortcodeShowIf() {
		return array(
		/* #0 */		[ 'no condition', '[geoip_detect2_show_if]no condition[/geoip_detect2_show_if]'  ],

		/* #1 */		[ 'yes', '[geoip_detect2_show_if country="DE"]yes[/geoip_detect2_show_if]'  ],
		/* #2 */		[ 'yes', '[geoip_detect2_show_if country="de"]yes[/geoip_detect2_show_if]'  ],
		/* #3 */		[ 'yes', '[geoip_detect2_show_if country="Germany"]yes[/geoip_detect2_show_if]'  ],
		/* #4 */		[ 'yes', '[geoip_detect2_show_if country="germany"]yes[/geoip_detect2_show_if]'  ],
		/* #5 */		[ '',    '[geoip_detect2_show_if country="US"]yes[/geoip_detect2_show_if]'  ],

		/* #6 */		[ '',    '[geoip_detect2_show_if country="DE" city="Munic" lang="en"]yes[/geoip_detect2_show_if]'  ],
		/* #7 */		[ 'yes', '[geoip_detect2_show_if country="DE" city="Eschborn"]yes[/geoip_detect2_show_if]'  ],
		/* #8 */		[ 'yes', '[geoip_detect2_show_if country="DE" city="2929134"]yes[/geoip_detect2_show_if]'  ],

		/* #9 */		[ 'yes', '[geoip_detect2_show_if continent="EU" not_country="FR" city="Eschborn"]yes[/geoip_detect2_show_if]'  ],
		
		/* #10 */		[ 'lang', '[geoip_detect2_show_if lang="es" country="Alemania"]lang[/geoip_detect2_show_if]'  ],
		/* #11 */		[ '',     '[geoip_detect2_show_if lang="en" country="Alemania"]yes[/geoip_detect2_show_if]'  ],
		
		/* #12 */		[ 'yes', '[geoip_detect2_show_if state="HE"]yes[/geoip_detect2_show_if]'  ],
		/* #13 */		[ 'yes', '[geoip_detect2_show_if region="HE"]yes[/geoip_detect2_show_if]'  ],
		/* #14 */		[ 'yes', '[geoip_detect2_show_if most_specific_subdivision="HE"]yes[/geoip_detect2_show_if]'  ],
		/* #15 */		[ '',    '[geoip_detect2_show_if state="NN"]yes[/geoip_detect2_show_if]'  ],
		/* #16 */		[ 'yes', '[geoip_detect2_show_if continent="EU"]yes[/geoip_detect2_show_if]'  ],
		
		/* #17 */		[ 'yes', '[geoip_detect2_show_if property="location.timeZone" property_value="Europe/Berlin"]yes[/geoip_detect2_show_if]'  ],
		/* #18 */		[ '',    '[geoip_detect2_show_if property="location.timeZone" not_property_value="Europe/Berlin"]yes[/geoip_detect2_show_if]'  ],
		/* #19 */		[ '',    '[geoip_detect2_show_if property="invalid.property" property_value="Europe/Berlin"]yes[/geoip_detect2_show_if]'  ],
		/* #20 */		[ 'yes', '[geoip_detect2_show_if property="invalid.property" not_property_value="Europe/Berlin"]yes[/geoip_detect2_show_if]'  ],
		
		/* #21 */		[ 'not_country', '[geoip_detect2_show_if not_country="FR"]not_country[/geoip_detect2_show_if]'  ],
		/* #22 */		[ '',    '[geoip_detect2_show_if not_country="DE"]yes[/geoip_detect2_show_if]'  ],
		/* #23 */		[ '',    '[geoip_detect2_show_if continent="EU" not_country="DE"]yes[/geoip_detect2_show_if]'  ],
		/* #24 */		[ 'yes', '[geoip_detect2_show_if country="US, DE"]yes[/geoip_detect2_show_if]'  ],
		/* #25 */		[ 'yes', '[geoip_detect2_show_if country="US,DE , FR"]yes[/geoip_detect2_show_if]'  ],
		/* #26 */		[ '',    '[geoip_detect2_show_if country="US,FR"]yes[/geoip_detect2_show_if]'  ],

		// Boolean values
		/* #27 */		[ '',    '[geoip_detect2_show_if property="isEmpty" property_value="1"]yes[/geoip_detect2_show_if]'  ],
		/* #28 */		[ 'yes', '[geoip_detect2_show_if property="isEmpty" property_value="false"]yes[/geoip_detect2_show_if]'  ],
		/* #29 */		[ 'yes', '[geoip_detect2_show_if property="isEmpty" property_value="no"]yes[/geoip_detect2_show_if]'  ],
		/* #30 */		[ '',    '[geoip_detect2_show_if property="isEmpty" property_value="YES"]yes[/geoip_detect2_show_if]'  ],
		/* #31 */		// [ 'yes', '[geoip_detect2_show_if property="country.isInEuropeanUnion" property_value="true"]yes[/geoip_detect2_show_if]'  ],
		/* #32 */		// [ '', '[geoip_detect2_show_if property="country.isInEuropeanUnion" property_value="0"]yes[/geoip_detect2_show_if]'  ],

		// Operator OR
		/* #33 */		[ '', '[geoip_detect2_show_if operator="or"]yes[/geoip_detect2_show_if]'  ], /* weird input, weird output. But actually consistent. */
		/* #34 */		[ 'yes', '[geoip_detect2_show_if region="HE" operator="or" country="France"]yes[/geoip_detect2_show_if]'  ],
		/* #35 */		[ '',    '[geoip_detect2_show_if region="BY" operator="or" country="France"]yes[/geoip_detect2_show_if]'  ],
		/* #36 */		[ 'yes', '[geoip_detect2_show_if region="BY" operator="or" country="Germany"]yes[/geoip_detect2_show_if]'  ],
		/* #37 */		[ 'yes', '[geoip_detect2_show_if region="BY" operator="or" country="France" property="extra.countryIsoCode3" property_value="DEU"]yes[/geoip_detect2_show_if]'  ],

		// Else
		/* #36 */		[ 'hu', '[geoip_detect2_show_if country="DE"]hu[else]ha[/geoip_detect2_show_if]'  ],
		/* #37 */		[ 'ha',  '[geoip_detect2_show_if country="EN"]hu[else]ha[/geoip_detect2_show_if]'  ],


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

	
	/**
	 * @dataProvider dataShortcodeShowIfEmpty
	 */
	function testShortcodeEmpty($result, $txt) {
		add_filter('geoip_detect2_record_data_override_lookup', [ $this, 'filterEmptyRecordData' ], 101);
		$return = do_shortcode($txt);
		$this->assertSame($result, $return, "Shortcode failed: " . $txt);
	}

	/**
	 * @dataProvider dataShortcodeShowIfEmpty
	 */
	public function testShortcodeHideIfEmpty($result, $txt) {
		add_filter('geoip_detect2_record_data_override_lookup', [ $this, 'filterEmptyRecordData' ], 101);
		// Negate the tests
		$txt = str_replace('geoip_detect2_show_if', 'geoip_detect2_hide_if', $txt);
		if ($result === 'hu') {
			$result = 'ha';
		} elseif ($result === 'ha') {
			$result = 'hu';
		} else {
			$result = $result ? '' : 'yes';
		}

		$return = do_shortcode($txt);
		$this->assertSame($result, $return, "Shortcode failed: " . $txt);
	}

	function dataShortcodeShowIfEmpty() {
		return [
			/* #0 */ ['ha',  '[geoip_detect2_show_if country="DE"]hu[else]ha[/geoip_detect2_show_if]'],
			/* #1 */ ['',    '[geoip_detect2_show_if country="DE"]yes[/geoip_detect2_show_if]'],
			/* #2 */ ['yes', '[geoip_detect2_show_if country=""]yes[/geoip_detect2_show_if]'],
			/* #3 */ ['hu',  '[geoip_detect2_show_if country=""]hu[else]ha[/geoip_detect2_show_if]'],
			/* #4 */ ['ha',  '[geoip_detect2_show_if city="Berlin"]hu[else]ha[/geoip_detect2_show_if]'],
			/* #5 */ ['yes', '[geoip_detect2_show_if city=""]yes[/geoip_detect2_show_if]'],
			/* #6 */ ['yes', '[geoip_detect2_show_if city="" country=""]yes[/geoip_detect2_show_if]'],
			/* #7 */ ['ha',  '[geoip_detect2_show_if city="DE" country=""]hu[else]ha[/geoip_detect2_show_if]'],
			/* #8 */ ['yes', '[geoip_detect2_show_if property="isEmpty" property_value="1"]yes[/geoip_detect2_show_if]'],
			/* #9 */ ['',    '[geoip_detect2_show_if property="isEmpty" property_value="0"]yes[/geoip_detect2_show_if]'],
			/* #10 */['',    '[geoip_detect2_show_if property="isEmpty" property_value=""]yes[/geoip_detect2_show_if]'],
		];
	}


	function testShortcodeAjax() {
		$this->assertContains('traits.ipAddress', do_shortcode('[geoip_detect2 property="traits.ipAddress" ajax="1"]'));
		$this->assertContains('js-geoip-detect-shortcode', do_shortcode('[geoip_detect2 property="traits.ipAddress" ajax="1"]'));
		
		$this->assertContains('data-options', do_shortcode('[geoip_detect2_countries_select name="mycountry" lang="fr" ajax="1"]'));
		$this->assertContains('js-geoip-detect-country-select', do_shortcode('[geoip_detect2_countries_select name="mycountry" lang="fr" ajax="1"]'));
		
		$this->assertContains('postal.code', do_shortcode('[geoip_detect2_text_input name="postal" property="postal.code" type="hidden" ajax="1"]'));
		$this->assertContains('js-geoip-text-input', do_shortcode('[geoip_detect2_text_input name="postal" property="postal.code" type="hidden" ajax="true"]'));
		
		$this->assertContains('it', do_shortcode('[geoip_detect2_current_flag height="10% !important", width="30" class="extra-flag-class" squared="0" default="it" ajax="1"]'));
		$this->assertContains('js-geoip-detect-flag', do_shortcode('[geoip_detect2_current_flag height="10% !important", width="30" class="extra-flag-class" squared="0" default="it" ajax="1"]'));
	}

	/**
	 * @dataProvider dataShortcodeAjaxElse
	 */
	function testShortcodeAjaxElse($expected, $shortcode) {
		$output = do_shortcode($shortcode);

		$this->assertContains('hu', $output);
		$this->assertContains('ha', $output);
		$this->assertSame(2, substr_count($output, '<span'));
		$this->assertContains('&quot;not&quot;:0', $output);
		$this->assertContains('&quot;not&quot;:1', $output);
	}

	function dataShortcodeAjaxElse() {
		return [
			[ 'hu', '[geoip_detect2_show_if country="DE" ajax="1"]hu[else]ha[/geoip_detect2_show_if]' ],
			[ 'hu', '[geoip_detect2_hide_if country="DE" ajax="1"]ha[else]hu[/geoip_detect2_show_if]' ],
		];
	}

	/**
	 * @dataProvider dataBlockElement
	 */
	function testBlockElement($expected, $html) {
		$actual = _geoip_detect2_html_contains_block_elements($html);
		$this->assertSame($expected, $actual, 'Check if HTML block element');
	}

	function dataBlockElement() {
		return [
			[ false, '' ],
			[ false, '<span>bla</span>'],
			[ false, 'asdf<span />bla'],
			[ true,  '<P>Hallo</P>'],
			[ true,  '<p >Hallo</p >'],
			[ true,  '<div class="hu">hu</div>'],
			[ true,  '<ol><li>hu</li></ol>'],
			[ true,  '<div />'],
			[ true,  '<div/>'],
			[ false, '<pp/>'],
			[ false, '<pp />'],
			[ false, '<pp class=""></pp>'],
			[ false, '<pp></pp>'],
		];
	}
}

/* Data of Test IP:
[ 7 ] {
  ["city"]=>
  [ 2 ] {
    ["geoname_id"]=>
    int(2929134)
    ["names"]=>
    [ 4 ] {
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
  [ 3 ] {
    ["code"]=>
    string(2) "EU"
    ["geoname_id"]=>
    int(6255148)
    ["names"]=>
    [ 8 ] {
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
  [ 3 ] {
    ["geoname_id"]=>
    int(2921044)
    ["iso_code"]=>
    string(2) "DE"
    ["names"]=>
    [ 8 ] {
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
  [ 3 ] {
    ["latitude"]=>
    float(50,1333)
    ["longitude"]=>
    float(8,55)
    ["time_zone"]=>
    string(13) "Europe/Berlin"
  }
  ["registered_country"]=>
  [ 3 ] {
    ["geoname_id"]=>
    int(2921044)
    ["iso_code"]=>
    string(2) "DE"
    ["names"]=>
    [ 8 ] {
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
  [ 1 ] {
    [0]=>
    [ 3 ] {
      ["geoname_id"]=>
      int(2905330)
      ["iso_code"]=>
      string(2) "HE"
      ["names"]=>
      [ 4 ] {
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
  [ 1 ] {
    ["ip_address"]=>
    string(11) "88.64.140.3"
  }
}
 */

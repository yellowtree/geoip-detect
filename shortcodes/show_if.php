<?php 

/**
 *
 * Geo-Dependent Content Hiding
 *
 * Uses an enclosing shortcode to selectively show or hide content. Use either
 * [geoip_detect2_show_if][/geoip_detect2_show_if] or [geoip_detect2_hide_if][/geoip_detect2_hide_if] at your
 * discretion, as they can both be used to accomplish the same thing.
 *
 * Shortcode attributes can be as follows:
 *
 * Inclusive Attributes (note that `hide_if` makes them exclusive):
 *      "continent", "country", "most_specific_subdivision"/"region"/"state"*, "city"
 *
 * * most_specific_subdivision, region, and state are aliases (use the one that makes the most sense to you)
 *
 * Exclusive Attributes (note that `hide_if` makes them inclusive):
 *      "not_country", "not_most_specific_subdivision"/"not_region"/"not_state"*, "not_city"
 *
 * * most_specific_subdivision, region, and state are aliases (use the one that makes the most sense to you)
 *
 * Each attribute may only appear once in a shortcode!
 * The location attributes can take each take full names, ISO abbreviations (e.g., US), or the GeonamesId.
 * All attributes may take multiple values seperated by comma (,).
 *
 * You can use custom property names with the attribute "property" and "property_value" / "not_property_value".
 * 
 * Conditions are chained by an AND operator by default, but you can also use operator="or" instead.
 * 
 * Examples:
 *
 * Display TEXT if the visitor is in the US and in Texas.
 *      `[geoip_detect2_show_if country="US" state="TX"]TEXT[/geoip_detect2_show_if]`
 * 	        - OR -
 *      `[geoip_detect2_show_if country="US" region="TX"]TEXT[/geoip_detect2_show_if]`
 * 	        - OR -
 *      `[geoip_detect2_show_if country="US" region="Texas"]TEXT[/geoip_detect2_show_if]`
 *          - OR -
 *      `[geoip_detect2_show_if country="US" most_specific_subdivision="TX"]TEXT[/geoip_detect2_show_if]`
 *
 * Display TEXT if the visitor is in the US, and in either Texas or Louisiana, but hide this content
 * from visitors with IP addresses from cities named Houston.
 *      `[geoip_detect2_show_if country="US" state="TX, LA" not_city="Houston"]TEXT[/geoip_detect2_show_if]`
 *
 * Display TEXT if the visitor is from North America.
 *      `[geoip_detect2_show_if continent="North America"]TEXT[/geoip_detect2_show_if]`
 *          - OR -
 *      `[geoip_detect2_hide_if not_continent="North America"]TEXT[/geoip_detect2_hide_if]`
 *
 * Hide TEXT if the visitor is from the US.
 *      `[geoip_detect2_hide_if country="US"]TEXT[/geoip_detect2_hide_if]`
 *          - OR -
 *      `[geoip_detect2_show_if not_country="US"]TEXT[/geoip_detect2_show_if]`
 *
 * Show TEXT if the visitor is within the timezone Europe/Berlin
 *      `[geoip_detect2_show_if property="location.timeZone" property_value="Europe/Berlin"]TEXT[/geoip_detect2_show_if]`
 * 
 * Show TEXT if the visitor is in the european union
 * 		`[geoip_detect2_show_if property="country.isInEuropeanUnion" property_value="true"]TEXT[/geoip_detect2_show_if]`
 * 
 * Show TEXT if the visitor is from Berlin OR France (since 4.0.0)
 * 		`[geoip_detect2_show_if city="Berlin" operator="or" country="France"]TEXT[/geoip_detect2_show_if]`
 *
 * Show TEXT if the visitor is from Berlin, otherwise show OTHER (since 4.1.0)
 * 		`[geoip_detect2_show_if city="Berlin"]TEXT[else]OTHER[/geoip_detect2_show_if]`
 * 
 * Show NO COUNTRY if no country was detected (since 4.3.0)
 * 		`[geoip_detect2_show_if country=""]NO COUNTRY[/geoip_detect2_show_if]`
 *
 * LIMITATIONS:
 * - You cannot nest several of these shortcodes within one another. Instead, seperate them into several blocks of shortcodes.
 * - City names can be ambigous. For example, [geoip_detect2_show_if country="US,FR" not_city="Paris"] will exclude both Paris in France and Paris in Texas, US. Instead, you can find out the geoname_id or seperate the shortcode to make it more specific.
 * - Conditions can either be combined by AND or OR. It is not possible to write this condition within a shortcode: (city = Berlin AND country = Germany) OR country = France
 */
function geoip_detect2_shortcode_show_if($attr, $content = '', $shortcodeName = '') {
	$shortcode_options = _geoip_detect2_shortcode_options($attr);
	$options = [ 'skipCache' => $shortcode_options['skip_cache'] ];
	
	$showContentIfMatch = ($shortcodeName === 'geoip_detect2_show_if');
	
	$attr = (array) $attr;

	$else_seperator = apply_filters('geoip_detect2_shortcode_show_if_else_seperator', '[else]');
	$parts = explode($else_seperator, $content, 2);
	$content_if = $parts[0];
	$content_else = isset($parts[1]) ? $parts[1] : '';
	
	$parsed = geoip_detect2_shortcode_parse_conditions_from_attributes($attr, !$showContentIfMatch);

	if (geoip_detect2_shortcode_is_ajax_mode($attr)) {
		geoip_detect2_enqueue_javascript('shortcode');
		
		$shortcode_options['parsed'] = $parsed;
		$span_attributes = [ 'class' => 'js-geoip-detect-show-if', 'style' => 'display: none !important' ];
		
		$span_if = _geoip_detect2_create_placeholder('span', $span_attributes, $shortcode_options, do_shortcode($content_if));

		$shortcode_options['parsed']['not'] = ($shortcode_options['parsed']['not'] === 1 ? 0 : 1); // negate
		$span_else = _geoip_detect2_create_placeholder('span', $span_attributes, $shortcode_options, do_shortcode($content_else));
		
		return $span_if . $span_else;
	} else {
		$info = geoip_detect2_get_info_from_current_ip($shortcode_options['lang'], $options);

		/**
		 * You can override the detected location information here.
		 * E.g. "Show if in Paris, but if the user has given an adress in his profile, use that city instead"
		 * (Does not work in AJAX mode)
		 * 
		 * @param YellowTree\GeoipDetect\DataSources\City $info
		 * @param array $attr Shortcode attributes given to the function.
		 * @param bool $showContentIfMatch Should the content be shown (TRUE) or hidden (FALSE) if the conditions are true?
		 */
		$info = apply_filters('geoip_detect2_shortcode_show_if_ip_info_override', $info, $attr, $showContentIfMatch);

		$evaluated = geoip_detect2_shortcode_evaluate_conditions($parsed, $info);

		if ($evaluated) {
			return do_shortcode($content_if);
		} else {
			return do_shortcode($content_else);
		}
	}
}
add_shortcode('geoip_detect2_show_if', 'geoip_detect2_shortcode_show_if');
add_shortcode('geoip_detect2_hide_if', 'geoip_detect2_shortcode_show_if');

function geoip_detect2_shortcode_parse_conditions_from_attributes(array $attr, bool $hide_if = false) {
	/* Attribute Conditions. Order is not important, as they are combined with a transitive AND condition */
	$attributeNames = array(
        'continent' => 'continent',
		'not_continent' => 'continent',
		
        'country' => 'country',
		'not_country' => 'country',
		
		'most_specific_subdivision' => 'mostSpecificSubdivision',
        'region' => 'mostSpecificSubdivision',
        'state' => 'mostSpecificSubdivision',
        'not_most_specific_subdivision' => 'mostSpecificSubdivision',
        'not_region' => 'mostSpecificSubdivision',
        'not_state' => 'mostSpecificSubdivision',
		
		'city' => 'city',
        'not_city' => 'city',
	);

	$parsed = [
		'op' => ( !empty($attr['operator']) && strtolower($attr['operator']) === 'or' ) ? 'or' : 'and',
	];
	$parsed['not'] = $hide_if ? 1 : 0;

	$conditions = [];


	foreach ($attributeNames as $shortcodeParamName => $maxmindName) {
		if (isset($attr[$shortcodeParamName])) {
			$condition = [
				'p' => $maxmindName,
				'v' => geoip_detect2_shortcode_prepare_values($attr[$shortcodeParamName]),
			];
			if (substr($shortcodeParamName, 0, 4) == 'not_') {
				$condition['not'] = 1;
			}
			$conditions[] = $condition;
		}
	}

	// Custom property
	if (isset($attr['property'])) {
		if (isset($attr['property_value'])) {
			$condition = [
				'p' => $attr['property'],
				'v' => geoip_detect2_shortcode_prepare_values($attr['property_value']),
			];
			$conditions[] = $condition;			
		} else if (isset($attr['not_property_value'])) {
			$condition = [
				'p' => $attr['property'],
				'v' => geoip_detect2_shortcode_prepare_values($attr['not_property_value']),
				'not' => 1
			];
			$conditions[] = $condition;
		}
	}

	$parsed['conditions'] = $conditions;

	return apply_filters('geoip_detect2_shortcode_show_if_parsed_result', $parsed, $attr, !$hide_if);
}

function geoip_detect2_shortcode_prepare_values(string $value) : string {
	// Parse User Input Values of Attribute
	$attributeValuesArray = explode(',', $value);
	
	$attributeValuesArray = array_map('trim', $attributeValuesArray);
	$attributeValuesArray = array_map('mb_strtolower', $attributeValuesArray);

	return implode(',', $attributeValuesArray);
}

// --------------------------------------------------------------------------------------------------------
// 

/**
 * This function has its JS equivalent. If the code is changed here, it also needs to be changed in the JS file.
 * 
 * @see ./js/shortcodes.js : function geoip_detect2_shortcode_evaluate_conditions()
 */
function geoip_detect2_shortcode_evaluate_conditions(array $parsed, \GeoIp2\Model\AbstractModel $info) : bool {
	$alternativePropertyNames = [
		'name',
		'isoCode',
		'code',
		'geonameId',
	];

	$isConditionMatching = ($parsed['op'] === 'or') ? false : true;

	foreach ($parsed['conditions'] as $condition) {
		// Actual value(s)
		try {
			$value = geoip_detect2_shortcode_get_property($info, $condition['p']);

			$values = [];
			if (is_object($value)) {
				foreach($alternativePropertyNames as $p) {
					if (isset($value->{$p})) {
						$values[] = $value->{$p};
					}
				}
			} else {
				$values = [ $value ];
			}

			$subConditionMatching = geoip_detect2_shortcode_check_subcondition($condition['v'], $values);
	
		} catch (\Exception $e) {
			// Invalid Property or so... ignore this condition.
			$subConditionMatching = false;
		}

		if (!empty($condition['not'])) {
			$subConditionMatching = ! $subConditionMatching;
		}


		if ($parsed['op'] === 'or') {
			$isConditionMatching = $isConditionMatching || $subConditionMatching;
		} else {
			$isConditionMatching = $isConditionMatching && $subConditionMatching;
		}
	}

	if (!empty($parsed['not'])) {
		$isConditionMatching = ! $isConditionMatching;
	}

	return $isConditionMatching;
}

/**
 * This function has its JS equivalent. If the code is changed here, it also needs to be changed in the JS file.
 * 
 * @see ./js/shortcodes.js : function geoip_detect2_shortcode_check_subcondition()
 */
function geoip_detect2_shortcode_check_subcondition(string $expectedValues, array $actualValues) : bool {
	if (isset($actualValues[0])) {
		if ($actualValues[0] === true) {
			$actualValues = ['true', 'yes', 'y', '1'];
		} else if ($actualValues[0] === false) {
			$actualValues = ['false', 'no', 'n', '0', ''];
		}
	}
	
	$expectedValues = explode(',', $expectedValues);

	if (in_array("", $expectedValues)) {
		// Value is not defined in record?
		if (count($actualValues) === 0) { 
			return true;
		}
	}

	// Compare case-insensitively
	$actualValues = array_map('mb_strtolower', $actualValues);
	
	$intersection = array_intersect($actualValues, $expectedValues);

	return count($intersection) > 0;
}
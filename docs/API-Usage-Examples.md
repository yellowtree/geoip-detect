_(Feel free to add your own examples on this page.)_

## Basic Use

```php
if (function_exists('geoip_detect2_get_info_from_current_ip')) {
	$userInfo = geoip_detect2_get_info_from_current_ip();
	if ($userInfo->country->isoCode == 'DE') {
		echo 'Hallo! Schön dass Sie hier sind!';
	}
} else {
	echo '<!-- Warning: The plugin Geolocation IP Detection is not active. -->';
}
```

## CSS Use

Hide/Show text only if visitor from Germany:

In your CSS file:

```css
.geoip { display: none !important; }
.geoip-country-UK .geoip-show-UK { display: block !important; }
.geoip-country-DE .geoip-show-DE { display: block !important; }

.geoip-hide { display: block !important; }
.geoip-country-UK .geoip-hide-UK { display: none !important; }
.geoip-country-DE .geoip-hide-DE { display: none !important; }
```

In your HTML (e.g. in the post content, when switching the editor to the HTML mode):

```html
<div class="geoip geoip-show-DE">
This text is shown only in Germany
</div>
<div class="geoip-hide geoip-hide-DE">
This text is hidden only in Germany
</div>
```

You need to enable the option `Add a country-specific CSS class to the <body>-Tag` to make this work.

## Shortcode Examples

<pre>
[geoip_detect2 property="country"] -> Germany
[geoip_detect2 property="country" lang="de"] -> Deutschland
[geoip_detect2 property="country.isoCode"] -> de
[geoip_detect2 property="city"] -> Frankfurt/Main
[geoip_detect2 property="mostSpecificSubdivision"] -> Hesse
[geoip_detect2 property="mostSpecificSubdivision.isoCode"] -> HE
[geoip_detect2 property="location.longitude"] -> 9.202
[geoip_detect2 property="location.latitude"] -> 48.9296
[geoip_detect2 property="location.timeZone"] -> Europe/Berlin
[geoip_detect2 property="continent"] -> Europe
[geoip_detect2 property="continent.code"] -> EU
[geoip_detect2 property="invalid_or_empty_property_name" default="default value"] -> default value
</pre>

## Complete Solutions

### Add the city name to the hash of the URL

```php
add_action('wp_head', 'geoip_add_city_to_hash', 5);
function geoip_add_city_to_hash(){
	if (!function_exists('geoip_detect2_get_info_from_current_ip'))
		return;

	$userInfo = geoip_detect2_get_info_from_current_ip();
	$city = $userInfo->city->name;
	if ($city) {
?>
<script>
window.location.hash = <?php echo json_encode('#' . $city) ?>;
</script>
<?php
	}
}
```

### Redirect depending on country

```php
add_action('template_redirect', 'geoip_redirect', 5);
function geoip_redirect(){
	if (is_admin() || !function_exists('geoip_detect2_get_info_from_current_ip'))
		return;

	// This condition prevents a redirect loop:
	// Redirect only if the home page is called. Change this condition to the specific page or URL you need.
	if (!is_home())
		return;

	if (!function_exists('geoip_detect2_get_info_from_current_ip'))
		return;

	$userInfo = geoip_detect2_get_info_from_current_ip();
	$countryCode = $userInfo->country->isoCode;
	switch ($countryCode) {
		case 'DE':
			$url = '/germany';
			break;
		case 'US':
			$url = '/usa';
			break;
		default:
			$url = '';
	}
	if ($url) {
		wp_redirect(get_site_url(null, $url));
		exit;
	}
}
```

If you use a caching plugin, you will probably want to use the [AJAX mode](https://github.com/yellowtree/geoip-detect/wiki/API:-AJAX): (You need to enable AJAX & Frontend JS.)

```js
jQuery(document).ready(function($) {
    // This condition prevents a redirect loop:
    // Redirect only if the home page is called. Change this condition to the specific page or URL you need.
    // You can remove it if the Javascript is not included in the page where the redirect below points to.
    if (window.location.pathname != '/') return;

    geoip_detect.get_info().then(function(record) {
        if (record.error()) {
            console.error('WARNING Geodata Error:' + record.error() );
        }

        var country = record.get('country.iso_code');
        console.log('Country "' + country + '" detected');

        if (country == 'DE') {
            window.location.pathname = '/germany';
        } else if (country == 'US') {
            window.location.pathname = '/usa';
        }
    });

});
```
(Add this to your site's JS file or, if you want to redirect only on a certain page, inside a `<script> ... </script>`)


### Calculate distance from a known location

```php
/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula. 
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [km]
 * @return float Distance between points in [km] (same as earthRadius)
 * @see https://stackoverflow.com/a/10054282
 */
function haversineGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}

// Los Angeles
$location['lat'] = 37.6293;
$location['lon'] = -122.1163;

$myLocation = $location; // Change if default Location should be something else
$record = geoip_detect2_get_info_from_current_ip();
if ($record->location->longitude) {
  $myLocation['lon'] = $record->location->longitude;
  $myLocation['lat'] = $record->location->latitude;  
}

$distance = haversineGreatCircleDistance($location['lat'], $location['lon'], $myLocation['lat'], $myLocation['lon']); // Returns distance in km. If you need a different unit, change the $earthRadius
```

### Use Maxmind Precise only for customers in Germany.
(Since 2.7.0)

```php
if (function_exists('geoip_detect2_get_info_from_current_ip')) {
	$record = geoip_detect2_get_info_from_current_ip();
	if ($record->country->isoCode == 'DE') {
		$record = geoip_detect2_get_info_from_current_ip(null, [ 'source' => 'precise' ]);
	}
} else {
	echo '<!-- Warning: The plugin Geolocation IP Detection is not active. -->';
}
```

For this to work, you need to first activate the precise datasource, enter the credentials. Then activate 'Maxmind automatic update' (or any other source) to use as default source.

### Get Country Information from GeoNames database instead of Maxmind lookup
(Since 2.6.0)

The plugin contains the geonames database that is also included in the Maxmind mmdb-Files.
In this way, you can look up the translated names of a country and its continent by using this function:

```php
/**
 * Get the information from GeoNames Database
 * @param string $iso_code 2-letter ISO code of country
 * @return array Information about country and its continent,
 *               in the same format as the geoIP records but as array instead of object.
 */
function get_info_from_country($iso_code) {
	if (!function_exists('geoip_detect2_get_info_from_current_ip'))
		return [  ];

	$countryInfo = new \YellowTree\GeoipDetect\Geonames\CountryInformation;
	$data = $countryInfo->getInformationAboutCountry($iso_code);
	return $data;
}
```

### Enrich the country select box with select2

[Select2](https://select2.org/) is useful for the country select box as it contains so many entries. Here is what you need to add to your theme's `function.php` (thanks [KoolPal](https://wordpress.org/support/topic/select2-for-geoip_detect2_shortcode_country_select_wpcf7/)):

```php
function geoip_detect_enqueue_select2_jquery() {
    wp_enqueue_style( 'select2css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css' );
    wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', [  'jquery'  ] );
}
add_action( 'wp_enqueue_scripts', 'geoip_detect_enqueue_select2_jquery' );

function geoip_detect_init_select2_jquery() {
?>
<script>
jQuery(document).ready(function($){
    $('.wpcf7-geoip_detect2_countries, .geoip_detect2_countries').select2();
});
</script>
<?php
}
add_action('wp_footer', 'geoip_detect_init_select2_jquery');
```
(Of course you can save the files into your theme folder and change the urls accordingly, and you should update the versions if needed.)

### Change record data (e.g. for testing purposes)

There is a wordpress filter that you can use in order to show the website as if shown from a specific location.
Add the following code to your theme's `functions.php`:

```php
// Plugin Version <= 2.12.0 (but beware, does not really work if caching is enabled!) 
// add_filter('geoip_detect2_record_data', 'change_geoip_data', 12, 2);

// Plugin Version > 2.12.0
add_filter('geoip_detect2_record_data_after_cache', 'change_geoip_data', 12, 2);

function change_geoip_data($data, $ip) {
	/*
	 * Output record data - you can then copy it below into the $json variable
	 * and change the attribute names that you are using in your code :
	 */
	//echo json_encode($data,  JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE); die();

	$json = '{
    "country": {
        "iso_code": "DE",
        "iso_code3": "DEU",
        "geoname_id": 2921044,
        "names": {
            "en": "Germany",
            "de": "Deutschland",
            "it": "Germania",
            "es": "Alemania",
            "fr": "Allemagne",
            "ja": "ドイツ連邦共和国",
            "pt-BR": "Alemanha",
            "ru": "Германия",
            "zh-CN": "德国"
        },
        "is_in_european_union": true
    },
    "continent": {
        "code": "EU",
        "names": {
            "en": "Europe",
            "de": "Europa",
            "it": "Europa",
            "es": "Europa",
            "fr": "Europe",
            "ja": "ヨーロッパ",
            "pt-BR": "Europa",
            "ru": "Европа",
            "zh-CN": "欧洲"
        },
        "geoname_id": 6255148
    },
    "location": {
        "latitude": 48.843,
        "longitude": 9.3626,
        "accuracy_radius": 20,
        "time_zone": "Europe\/Berlin"
    },
    "city": {
        "geoname_id": 2885540,
        "names": {
            "en": "Korb"
        }
    },
    "postal": {
        "code": "71404"
    },
    "registered_country": {
        "geoname_id": 2921044,
        "is_in_european_union": true,
        "iso_code": "DE",
        "names": {
            "de": "Deutschland",
            "en": "Germany",
            "es": "Alemania",
            "fr": "Allemagne",
            "ja": "ドイツ連邦共和国",
            "pt-BR": "Alemanha",
            "ru": "Германия",
            "zh-CN": "德国"
        }
    },
    "subdivisions": [
        {
            "geoname_id": 2953481,
            "iso_code": "BW",
            "names": {
                "de": "Baden-Württemberg",
                "en": "Baden-Württemberg Region",
                "es": "Baden-Württemberg",
                "fr": "Bade-Wurtemberg",
                "ja": "バーデン＝ヴュルテンベルク州",
                "ru": "Баден-Вюртемберг",
                "zh-CN": "巴登-符腾堡"
            }
        }
    ],
    "traits": {
        "ip_address": "88.64.140.9"
    },
    "is_empty": false,
    "extra": {
        "source": "auto",
        "cached": 0,
        "error": ""
    }
}';

	$data = json_decode($json, true);
	return $data;
}
```

### AJAX: Enqueue the JS file manually

Moved to [https://github.com/yellowtree/geoip-detect/wiki/API%3A-AJAX#enqueue-the-js-file-manually]

### Allowing access only from a certain country

Be very careful with this. Even if you only have DE customers, they might be temporarily in another country etc. ... 
And if you are blocking on city/region level, check if the accuracy of the IP database you use is high enough for your purposes.

```php
add_action('plugins_loaded', function() {
    if (!function_exists('geoip_detect2_get_info_from_current_ip')) {
        return;
    }
    if ( is_admin() || is_user_logged_in() ) { // No restrictions for Wordpress admin
        return;
    }

    $userInfo = geoip_detect2_get_info_from_current_ip();
    if ($userInfo->country->isoCode != 'DE') {
        wp_die('This website is available only in Germany. You can contact us at info@example.org');
    }
});
```

### Country selector that can be overridden by the user

Since 5.0.0, when the property `autosave` is enabled in AJAX mode, the user selection of another is also being saved as a cookie. Thus, the plugin will detect a sensible default, but the user can override this selection. 

NOTE: A country selector is not the same as a language selector!

```html
<p>Select your country: [geoip_detect2_countries_select name="mycountry" autosave="1" list="it,cz,blank_Other_countries" default=""]</p>
[geoip_detect2_show_if country="it"] <p>
Content for Italia
</p>[/geoip_detect2_show_if]
[geoip_detect2_show_if country="cz"] <p>
Content for Czech
</p>[/geoip_detect2_show_if]
[geoip_detect2_show_if not_country="cz,it"] <p>
Content for other countries
</p>[/geoip_detect2_show_if]

<p>(Debug: Current saved country is: <b>[geoip_detect2 property="country"]</b> / ISO: <b>[geoip_detect2 property="country.isoCode"] </b>)</p>
<p>Override (from Autosave or manual) active? <b>[geoip_detect2_show_if property="extra.override" property_value="1"]Yes[else]No[/geoip_detect2_show_if]</b></p>
<p><a href="#" onclick="geoip_detect.remove_override(); return false;">Reset override</a></p>
```
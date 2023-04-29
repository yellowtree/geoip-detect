You are using a site cache? This is what AJAX mode is made for. It needs to be enabled in the options.

If the cached HTML is geo-independent, yet it should be customized depending on the geo-data, then now you can use this AJAX call to figure out the user's location. You can then use this information to show/hide content, inject the country name into a field, redirect to a different page, etc.

# JSON Endpoint

GET `https://example.com/wp-admin/admin-ajax.php?action=geoip_detect2_get_info_from_current_ip`

The function `geoip_detect2_get_info_from_current_ip()` can be called via JS and returns the JSON data. This only works if the option "Enable JS API" is checked. 

## HTTP Response Codes

| Code | Meaning                               |
|------|---------------------------------------|
| 200  | OK (but properties might be empty)    |
| 500  | Lookup Error (e.g. file is corrupted) |
| 412  | AJAX Setup Error                      |

## JSON Response object

The result from the datasource. See [[Record Properties]] for all available properties. No property name is guaranteed to exist:

```js
var city = record.city && record.city.names && record.city.names.en;
var ip = record.traits && record.traits.ip_address;
var error = record.extra && record.extra.error;
```

## Caveats:

* It is not possible to prevent securely that no other website is using this JSON Endpoint. That's why it is discouraged to use it for paid APIs like Maxmind Precision.
* In order to improve this situation a little bit, the AJAX call is testing the HTTP referer. If the referer domain is not the same domain as the wordpress `site_url()`, then the AJAX call will be rejected. (However, Referers may be forged easily.) If you are using other domains as source, add them to the wordpress filter `geoip_detect2_ajax_allowed_domains`.
* Also, only `geoip_detect2_get_info_from_current_ip` can be called in this way, you cannot pass an IP as a URL parameter. (But still, client IPs can be forged - it's hard to do but possible.)
* If you need to change the options such as `skipCache`, set them in the wordpress filter `geoip_detect2_ajax_options`.

# Frontend JS (helper functions)

This JS provides an JS API to access the data returned by the JSON Endpoint. Enable the option `Add JS to make the access to the AJAX endpoint easier.` or [enqueue the JS file manually](API-Usage-Examples#ajax-enqueue-the-js-file-manually)

```js
// Example to show JS usage
// This example assumes that jQuery is available on your frontend - jQuery is not required for the JS code of the plugin

jQuery(document).ready(function($) {
  geoip_detect.get_info().then(function(record) {
    if (record.error()) {
      console.error('WARNING Geodata Error:' + record.error() );
      $('.geo-error').text(record.error());
    }

    // Debug: Show raw data of record. (Warning: No property in this object is guaranteed to exist.)
    // console.log('Record', record.data);

    // If no locales are given, use the website language
    $('.geo-continent').text(record.get('continent')); 

    // Second parameter is the default value if the property value is empty or non-existent. For example, the IP might be from a satellite connection.
    $('.geo-continent').text(record.get('continent', 'Weird: no country detected.'));
    
    // Return the German name of the country, if not available, use English
    $('.geo-country').text(record.get_with_locales('country', ['de']));
    
    // Return the German name of the country, if not available, show "default text"
    $('.geo-country-de').text(record.get('country.names.de', 'default text'));
    
    // Try French first, then German, then English. The pseudo-property "name" is also supported ('city' would result in the same return value).
    $('.geo-city').text(record.get_with_locales('city.name', ['fr', 'de', 'en'], 'No city detected.'));
    
    // The same property names can be used as in the shortcode syntax 
    $('.geo-city-id').text(record.get('city.geoname_id'));
    $('.geo-ip').text(record.get('traits.ip_address'));

  });

  // This will return the same JS promise as above, so that this will not result in a second AJAX request.
  geoip_detect.get_info().then(function(record) {
    $('.geo-country-2').text(record.get_with_locales('country', ['en']));
  }); 

});
```

```html
Continent: <span class="geo-continent"></span><br>
Country (de): <span class="geo-country"></span><br>
Country (de): <span class="geo-country-de"></span><br>
City (fr,de,en): <span class="geo-city"></span><br>
City-Id: <span class="geo-city-id"></span><br>
IP: <span class="geo-ip"></span><br>
Error: <span class="geo-error"></span><br>

Country (en): <span class="geo-country-2"></span><br>
```

By default, the result is cached in a localstorage cookie so that subsequent visits by the same visit will not issue a new AJAX request. You can configure this behaviour in your theme's `function.php` like this:

```php
add_filter('geoip_detect2_ajax_localize_script_data', function($data) {
    $data['cookie_name'] = ''; // Disable cookies completely
    // or $data['cookie_duration_in_days'] = 14 // How long the cookie is valid, default is 1 (day)
    return $data;
});
```

### Storing data that overrides detected data
(Since 4.0.0)

It is best practise to give the user the option to override the geo-detected data, for example when showing different currencies in different countries. 
This how you can set a new record manually in JavaScript:

```js
new_record = { country: { iso_code: 'en' }}; // Specify all properties that you will later use in your code, as e.g. the country name is not added automatically.
geoip_detect.set_override(new_record, { duration_in_days: 1 });
// Old Syntax before 5.0.0: geoip_detect.set_override(new_record, 1);

// If you want to override only one property, merging it with the current record (since 5.1.0):
geoip_detect.set_override_with_merge('country.iso_code', 'en', { duration_in_days: 1 });
```

If you want to undo this override, simply call `geoip_detect.remove_override()`. 

(If you want to remove all overrides server-side, the easiest method to do this is renaming the cookie name.)

Since 5.0.0, calling `set_override` or `set_override_with_merge` by default re-evaluates all AJAX shortcodes on that page. This behavior can be turned off:

```js
geoip_detect.set_override(new_record, { duration_in_days: 1, reevaluate: false });
geoip_detect.set_override_with_merge('country.iso_code', 'en', { duration_in_days: 1, reevaluate: false });
```

### Enqueue the JS file manually

If you only need this JS on some sites, enqueue the JS file manually and uncheck the option `Add JS to make the access to the AJAX endpoint easier.`.

You can add the shortcode `[geoip_detect2_enqueue_javascript]` (since 3.3.0) on the pages/posts where you have geo-dependent content. 

Or, if you need a more general solution:

```php
$whitelist = [
	'/' /* home */,
	'/some/query/',
	'/other/page/'
];

$server_request = $_SERVER['REQUEST_URI'];
// Comment this line if you also want to match GET parameters like /path/?page=34
$server_request = parse_url($server_request, PHP_URL_PATH);

if (in_array($server_request, $whitelist)) {
	geoip_detect2_enqueue_javascript();
}
```

# Country-specific CSS class for the <body>-Tag

(Since 3.3.0)

If you have enabled the option `Add a country-specific CSS class to the <body>-Tag.`, enabling AJAX mode and Frontend JS will add these CSS classes via AJAX as well.

# Resolve shortcodes (via AJAX)

(Since 4.0.0)

If this option is enabled, the shortcodes will generate HTML contains placeholder tags (span) that are filled via the helper JS functions of the plugin (instead of generating the output on the server).

Normally, this will apply to all shortcodes, except `[geoip_detect2_get_current_source_description]` and `[geoip_detect2_get_external_ip_adress]` (because AJAX doesn't make sense there)

## Examples:

`[geoip_detect2_countries mycountry default:US]`

The shortcodes don't need to be changed. The Contact Form 7 shortcodes also work with AJAX mode.

`[geoip_detect2 property="country" lang="de" default="No country detected" ajax="1"]`

Execute this shortcode as AJAX even if the option `Resolve shortcodes (via AJAX)` is disabled. The options `Enable AJAX endpoint` still needs to be enabled.

`[geoip_detect2_countries name="mycountry" ajax="0"]`

Generate a countries select with the current country selected in the HTML (no AJAX), even if the option `Resolve shortcodes (via AJAX)` is enabled.

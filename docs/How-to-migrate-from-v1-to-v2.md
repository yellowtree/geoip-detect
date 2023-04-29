<pre>
TL;DR

The Plugin now uses a completely rewritten API from Maxmind.
Most of your legacy code should continue to work, 
if not, read this document to find out why.

Go to Tools > Geolocation IP Detection and click on "Update now",
so that the plugin can load the new database.
</pre>

## What's New

MaxMind released a new version of their library that is accessing the database, as well as a new database file:

[MaxMind What's New](http://dev.maxmind.com/geoip/geoip2/whats-new-in-geoip2/)

This enables language support for city/country/continent names, for instance.

## New Function Names

In order to preserve backwards compatibility, the new API got [new functions](https://github.com/yellowtree/wp-geoip-detect/wiki/API-Documentation):

`function geoip_detect2_get_info_from_ip($ip, $locales = null) { ... }`

`function geoip_detect2_get_info_from_current_ip($locales = null) { ... }`


The Functions return a \GeoIp2\Model\City-Object. The Backend (under Tools) will help you get started. For all properties, refer to the [Maxmind API Documentation](http://dev.maxmind.com/geoip/geoip2/web-services/). If you use a property that is not described there (e.g. because of a typo) the object will throw a RuntimeException. This is by design to make it more discoverable while coding.

You will also notice that you can now indicate a language that you want your geo-labels in. Not all names are translated in all languages, so you can give an fallback order (`array('de', 'en')` means "German if possible, English otherwise". Also see [List of supported locales](http://dev.maxmind.com/geoip/geoip2/web-services/#Languages) ). By default (`NULL`), it uses the current wordpress (backend) language for names or english as fallback.

Note that unlike the MaxMind API, the plugin functions do not throw exceptions (when IP invalid, no data available or other errors) - instead, they return an object that returns `NULL` for every (valid) property name. If you want to see the exceptions to get more details about why a specific request failed, you can get the MaxMind Reader object and proceed as in their [PHP Documentation](https://github.com/maxmind/GeoIP2-php):

`$reader = geoip_detect2_get_reader();`


## New shortcode

The shortcode now supports language selection as well.
See [Documentation](https://github.com/yellowtree/wp-geoip-detect/wiki/API-Documentation).

## API changes

* If the client IP is a public IP, but no information is found in the IP database, the API no longer falls back to the information of the server. Instead, the returned record has empty country/city etc. fields and an additional `$record->isEmpty` which is `true`.
* The API no longer returns `NULL` if there is no information about this IP, but an empty GeoIP object instead.

## Old Function Names / Shortcodes

The old function names and the old shortcode syntax return the same data structure as before, but internally still uses the v2-Database. This means that most of your legacy code will continue to run.

### Old Properties that will not work:
* `dma_code`: This property is not supported by GeoIPv2.
* `area_code`: This property is not supported by GeoIPv2.

### Old Properties that _might_ not work:
* `region` and `region_name`: As the data-basis changed, there seem to be less regional coding. Also, the region-ids have changed: now they are always two-letter-ISO-Codes ("GeoIP Legacy databases included ISO codes for US and Canada and FIPS codes for all other countries.").
* `timezone`: Similarly, there is less data available. If it is not available, the plugin tries to fill it in - but without a region to rely on this only works in so many countries (ie countries with only 1 timezone).
* `country_code3`: This property is not supported by GeoIPv2. However, the plugins tries to map the 2-letter codes to these 3-letter codes.




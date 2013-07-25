=== GeoIP Detection ===
Contributors: benjaminpick
Tags: geoip, ip, locator, latitude, longitude
Requires at least: 3.5
Tested up to: 3.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Retrieving Geo-Information using the Maxmind GeoIP (Lite) Database.

== Description ==

Provides geographic information detected by an IP adress. This can be used in themes or other plugins.

= Features: =

* Provides 3 functions: 
  * `geoip_detect_get_info_from_ip($ip)`: Lookup Geo-Information of the specified IP 
  * `geoip_detect_get_info_from_current_ip()`: Lookup Geo-Information of the current website user
  * `geoip_detect_get_external_ip_adress()`: Fetch the internet adress of the webserver
* Auto-Update the GeoIP database once a week
* See the results of a specific IP in the wordpress backend (under Tools > GeoIP Detection).

= How can I use these functions? =

* You could choose the currency of the store based on the country name
* You could suggest an timezone to use when displaying dates
* You could show the store nearest to your customer
* Etc. ... You tell me! I'm rather curious what you'll do with this plugin!

*This product includes GeoLite data created by MaxMind, available from http://www.maxmind.com.*

== Installation ==

This plugin does not contain the database itself, so it has to be loaded before first use.
2 alternative ways of doing this:

= Automatic Installation =

Go to Tools > GeoIP Detect and click on the button `"Update now"`.
The database is written into the `/uploads`-Folder.

= Manual Installation =

1. Download the database at http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
2. extract it and 
3. put it into the plugin directory.

== Frequently Asked Questions ==

= How exact is this data? =

Think of it as an "educated guess": IP adresses and their allocation change on a frequent basis.
If you need [more exact data](http://www.maxmind.com/en/geolite_city_accuracy "GeoLiteCity Accuracy"), consider purchasing the commercial version of the data.


= Technically speaking, how could I verify if my visitor comes from Germany? =

Put this code somewhere in your template files:

    $userInfo = geoip_detect_get_info_from_current_ip();`
    if ($userInfo && $userInfo->country_code == 'DE')`
        echo 'Hallo! Schön dass Sie hier sind!';
   
To see which property names are supported, refer to the [Plugin Backend](http://wordpress.org/plugins/geoip-detect/screenshots/).

== Screenshots ==

1. Backend page (under Tools > GeoIP Detection)

== Changelog ==

= 1.3 =
* FIX: Manual install works again (was broken since 1.2)

= 1.2 =
* FIX: property region_name is now filled again (was broken since 1.1) 

= 1.1 =
* Add function `geoip_detect_get_external_ip_adress()`: Ask a webservice to tell me the external IP of the webserver.
* New filter: When developing locally, the external IP is used to determine the geographic location.

= 1.0 =

* First working release.

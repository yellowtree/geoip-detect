=== GeoIP Detection ===
Contributors: benjaminpick
Tags: geoip, ip, maxmind, geolocation, locator, latitude, longitude
Requires at least: 3.5
Tested up to: 4.3
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL

Retrieving Geo-Information using one the Maxmind GeoIP2 databases.

== Description ==

Provides geographic information detected by an IP adress. This can be used in themes or other plugins,
as a shortcode, or via CSS body classes. The city & country names are translated in different languages ([supported languages](https://dev.maxmind.com/geoip/geoip2/web-services/#Languages-8)).

= Features: =

* Provides these 5 functions (see [API Documentation](https://github.com/yellowtree/wp-geoip-detect/wiki/API-Documentation)): 
  * `geoip_detect2_get_info_from_ip($ip, $locales = array('en'), $options = array())`: Lookup Geo-Information of the specified IP 
  * `geoip_detect2_get_info_from_current_ip($locales = array('en'), $options = array())`: Lookup Geo-Information of the current website user
  * `geoip_detect2_get_current_source_description(...)`: Return a human-readable label of the currently chosen source.
  * `geoip_detect2_get_external_ip_adress()`: Fetch the internet adress of the webserver
  * `geoip_detect2_get_client_ip()`: Get client IP (even if it is behind a reverse proxy)
* You can use one of these data sources (see [comparison](https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ#which-data-source-should-i-choose)):
  * Free: [Maxmind GeoIP2 Lite City](http://dev.maxmind.com/geoip/geoip2/geolite2/), automatically updated every month (licensed CC BY-SA. See [FAQ](https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ).)
  * Commercial: [Maxmind GeoIP2 City](https://www.maxmind.com/en/geoip2-country-database) or [Maxmind GeoIP2 Country](https://www.maxmind.com/en/geoip2-city)
  * Commercial Web-API: [Maxmind GeoIP2 Precision](https://www.maxmind.com/en/geoip2-precision-services) (City, Country or Insights)
  * Free (default source): [HostIP.info](http://www.hostip.info/) (English, IPv4 only)
* For the property names, see the results of a specific IP in the wordpress backend (under *Tools > GeoIP Detection*).
* You can include these properties into your posts and pages by using the shortcode `[geoip_detect2 property="country.name" default="(country could not be detected)" lang="en"]` (where 'country.name' can be one of the other property names as well, and 'default' and 'lang' are optional).
* When enabled on the options page, it adds CSS classes to the body tag such as `geoip-country-DE` and `geoip-continent-EU`.
* When enabled on the options page, the client IP respects a reverse proxy of the server.

See [API Documentation](https://github.com/yellowtree/wp-geoip-detect/wiki/API-Documentation) for more info.

= How can I use these functions? =

* You could choose the currency of the store based on the country name
* You could suggest an timezone to use when displaying dates
* You could show the store nearest to your customer
* You show or hide content specific to a geographic target group
* Etc. ... You tell me! I'm rather curious what you'll do with this plugin!

**System Requirements**: You will need at least PHP 5.3.1.

*This product can provide GeoLite2 data created by MaxMind, available from http://www.maxmind.com.*

== Installation ==

* Install the plugin
* Go to the plugin's option page and choose a data source.
* Test it by clicking on "Lookup" on the lookup page.

== Frequently Asked Questions ==

The FAQ has moved to Github:
https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ

You'll also find API usage examples there:
https://github.com/yellowtree/wp-geoip-detect/wiki/API-Usage-Examples

== Screenshots ==

1. Lookup page (under Tools > GeoIP Lookup)
2. Options page (under Preferences > GeoIP Detection)

== Upgrade Notice == 

= 2.5.1 =

Hotfix: If you upgraded to 2.5.0, please verify that the correct datasource is still chosen. Sorry for any inconvenience caused.

= 2.5.0 =

If you use a caching plugin, you don't need to exempt geo-content pages manually anymore. When the API of this plugin is called, then this plugin signals to the caching plugin that this page should not be cached. You can disable this behavior on the options page.

= 2.4.2 =

You don't need to upgrade to this version, this release is mainly to fix installation behavior.

= 2.4.1 =

Reverting Requirements check to behavior before 2.4.0

= 2.4.0 =

Maxmind Precision API support is here ... Try it out, I would tag it "experimental" at the moment.

= 2.3.1 =

The plugin was down for licensing issues.
All users must now opt in to use the database because it is licensed CC BY-SA.
Otherwise, the Web-API of HostIP.info is used. 

= 2.3.0 =

The plugin was down for licensing issues.
All users must now opt in to use the database because it is licensed CC BY-SA.
Otherwise, the Web-API of HostIP.info is used. 

= 2.1.1 =

Update to v2.x is a major update.
At least PHP 5.3.1 is required now.
See Migration Guide at https://github.com/yellowtree/wp-geoip-detect/wiki/How-to-migrate-from-v1-to-v2

= 2.0.1 =

This major update uses the new Maxmind API (v2). 
At least PHP 5.3.1 is required now.
See Migration Guide at https://github.com/yellowtree/wp-geoip-detect/wiki/How-to-migrate-from-v1-to-v2

= 1.7.1 =

Cron update was broken again ...

= 1.6 =

Automatic weekly update didn't work in all installations.

= 1.5 =

Fixing automatic weekly updates.


== Changelog ==

= 2.5.2 =
* FIX: Also disallow proxy caching via HTTP header, if possible.
* NEW: Shortcodes for the other API functions: `[geoip_detect2_get_current_source_description]`, `[geoip_detect2_get_client_ip]`, and `[geoip_detect2_get_external_ip_adress]`
* FIX: geoip_detect2_get_external_ip_adress() : do not filter if $unfiltered is true.

= 2.5.1 =
* FIX: Upgrade script did change the source.
* FIX: Page caching is only disabled on upgrade when set_css_country is disabled.

= 2.5.0 =
* CHANGE: The parameter $skipCache is now $options['skipCache']. Using $skipCache is deprecated, but still works. 
* NEW: $options['timeout'] for Web-API lookups can now be specified.
* FIX: Hostip.info did not set traits->ipAddress
* FIX: Hostip.info does not include data for IPv6. Add a lookup error message.
* NEW: Disable page caching if geoip-API was called (this is configurable in the options). (Supported plugins: WP Super Cache, W3 Total Cache, ZenCache, and possibly others)
* Maxmind vendor code was updated to the current version.

= 2.4.3 =
* FIX: Options Page: The checkboxes didn't show (even though the option was saved) since 2.4.0
* NEW: A fixed external IP can now be specified on the options page. (Useful in development scenarios without internet, or mixed internet/intranet cases. You can also use this to speed up things on the production server if you know the IP will not change.)
* NEW: Hidden feature/side-effect: Clicking on save in the General Options section also empties the external IP cache. 

= 2.4.2 = 
* FIX: Trim whitespace of IP adress.
* FIX: some PHP notices.
* FIX: The Installation message "No database installed" failed to install the Maxmind database since 2.4.0 
* NEW: Show IPv6-not-supported notice.
* FIX: Add empty fallback functions in case the plugin requirements are not met. (To avoid fatal errors.)
 
= 2.4.1 =
* FIX: Revert IPv6 check. (Sorry for this. I thought PHP compiled without IPv6 would be esoteric.)

= 2.4.0 =
This is a major refactor in order to support multiple sources properly. The Lookup and the Options were seperated into 2 screens (accessible in the menu under `Tools` and `Options`, respectively.)

* NEW: Add a Cache for Web-API-Requests. Consequently, the function geoip_detect2_get_info_from_ip() received a new parameter "$skipCache" to skip this cache if not needed. You can check if the result is coming from the cache by checking $result->extra->cached (it is 0 when not cached, UNIX timestamp of cache event otherwise).
* This also applies to the shortcode API (`[geoip_detect2 property="extra.cached" skip_cache="true"]`)
* NEW: Error messages during lookup are now in `$record->extra->error`.
* NEW: Experimental support for the Maxmind Precision API.
* NEW: Shortcodes now also support fallback languages. (`[geoip_detect2 property="country" lang="fr,de"]`)
* FIX: Check for IPv6 support for PHP.
* FIX: Country data now also get timezones.

= 2.3.1 =
* NEW: API function geoip_detect2_get_current_source_description() (as there are different sources to choose from now)
* FIX: Show error message if PHP < 5.3 (instead of fatal error)

= 2.3.0 =
* NEW: Add HostIP.info-Support

= 2.2.0 =
* FIX: Update Maxmind Reader to 1.0.3 (fixing issues when the PHP extension mbstring was not installed)
* NEW: Commercial databases are now supported. You can specify a file path in the options.
* NEW: A country database (lite or commercial) database now works as well.
* NEW: License is now displayed before install.

= 2.1.2 =
* FIX: Show error message if PHP < 5.3 (instead of fatal error)
* FIX: Support multiple proxies (but currently only one reverse proxy)

= 2.1.1 =
* FIX: Notice "Database missing" should not show during/right after database update.

= 2.1.0 =
* NEW: A nagging admin notice shows up on every wp-admin page when no database is installed (yet).

= 2.0.1 =
* NEW: Using v2 version of the API.
See Migration Guide at [Github](https://github.com/yellowtree/wp-geoip-detect/wiki/How-to-migrate-from-v1-to-v2)

Other changes:

* NEW: The v2-functions now support location names in other locales. By default, they return the current site language if possible.
* NEW: The new shortcode [geoip_detect2 ...] also supports a "lang"-Attribute.
* NEW: IPv6 addresses are now supported as well.
* Legacy function names and shortcode should work in most cases. For details check the guide above.

= 2.0.0 =

(Was not released on wordpress.org to make sure that development releases get this update as well.)

= 1.8 =
* NEW: Support reverse proxies (you have to enable it in the plugin options.)
* NEW: Shortcode now has a default value when no information for this IP found.

= 1.7.1 =
* FIX: Fatal error on cron run

= 1.7 =
* FIX: Schedule Database update to do in background immediately after plugin installation/re-activation.
* FIX: Longitude can be smaller than -90

= 1.6 =
* NEW: Can add a country- and continent-specific class on the body tag. You need to activate this in the options.
* FIX: Automatic weekly update. (Didn't work on all installations).
* FIX: Do not include Maxmind Libraries again if already included by another plugin/theme

= 1.5 =
* FIX: Automatic weekly update. Go to the plugin page (Tools menu) to verify that an update is planned.

= 1.4 =
* Feature: Add shortcode [geoip_detect property="(property name)"] for direct use in posts/pages

= 1.3 =
* FIX: Manual install works again (was broken since 1.2)

= 1.2 =
* FIX: property region_name is now filled again (was broken since 1.1) 

= 1.1 =
* Add function `geoip_detect_get_external_ip_adress()`: Ask a webservice to tell me the external IP of the webserver.
* New filter: When developing locally, the external IP is used to determine the geographic location.

= 1.0 =
* First working release.

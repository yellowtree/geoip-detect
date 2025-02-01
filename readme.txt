=== Geolocation IP Detection ===
Contributors: benjaminpick
Tags: geolocation, locator, geoip, maxmind, ipstack
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.2
Stable tag: 5.5.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL

Provides geographic information detected by an IP adress.

== Description ==

Provides geographic information detected by an IP adress. This can be used in themes or other plugins, as a shortcode, or via CSS body classes. The city & country names are translated in different languages ([supported languages](https://dev.maxmind.com/geoip/geoip2/web-services/#Languages-8)).

= Features: =

* You can use one of these data sources (see [comparison](https://github.com/yellowtree/geoip-detect/wiki/FAQ#which-data-source-should-i-choose)):
  * Free (default source): [HostIP.info](http://www.hostip.info/) (IPv4 only)
  * Free with registration: [Maxmind GeoIP2 Lite City](http://dev.maxmind.com/geoip/geoip2/geolite2/), automatically updated weekly
  * Commercial: [Maxmind GeoIP2 City](https://www.maxmind.com/en/geoip2-country-database) or [Maxmind GeoIP2 Country](https://www.maxmind.com/en/geoip2-city)
  * Commercial Web-API: [Maxmind GeoIP2 Precision](https://www.maxmind.com/en/geoip2-precision-services) (City, Country or Insights)
  * Hosting-Provider dependent: [Cloudflare](https://support.cloudflare.com/hc/en-us/articles/200168236-What-does-CloudFlare-IP-Geolocation-do-) or [Amazon AWS CloudFront](https://aws.amazon.com/blogs/aws/enhanced-cloudfront-customization/) (Country)
  * Free or Commercial Web-API: [Ipstack](https://ipstack.com)
  * Commercial Web-API via AWS Marketplace: [Fastah](https://aws.amazon.com/marketplace/pp/prodview-k5gjowexrefl2)
* Provides these 5 functions (see [API Documentation](https://github.com/yellowtree/geoip-detect/wiki/API:-PHP)):
  * `geoip_detect2_get_info_from_ip($ip, $locales = array('en'), $options = array())`: Lookup Geo-Information of the specified IP
  * `geoip_detect2_get_info_from_current_ip($locales = array('en'), $options = array())`: Lookup Geo-Information of the current website user
  * `geoip_detect2_get_current_source_description(...)`: Return a human-readable label of the currently chosen source.
  * `geoip_detect2_get_external_ip_adress()`: Fetch the internet adress of the webserver
  * `geoip_detect2_get_client_ip()`: Get client IP (even if it is behind a reverse proxy)
* For the property names, see the results of a specific IP in the wordpress backend (under *Tools > Geolocation IP Detection*).
* You can include these properties into your posts and pages by using the shortcode `[geoip_detect2 property="country.name" default="(country could not be detected)" lang="en"]` (where 'country.name' can be one of the other property names as well, and 'default' and 'lang' are optional).
* You can show or hide content by using a shortcode `[geoip_detect2_show_if country="FR, DE" not_city="Berlin"]TEXT[/geoip_detect2_show_if]`. See [Shortcode Documentation](https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes#show-or-hide-content-depending-on-the-location).
* When enabled on the options page, it adds CSS classes to the body tag such as `geoip-province-HE`, `geoip-country-DE` and `geoip-continent-EU`.
* If you are using a page cache, it is recommended to use the AJAX mode (see [AJAX](https://github.com/yellowtree/geoip-detect/wiki/API:-AJAX))
* When enabled on the options page, the client IP respects a reverse proxy of the server.
* If you are using [Contact Form 7](https://wordpress.org/plugins/contact-form-7/), you can use these shortcodes:
  * A select input with all countries, the detected country being selected by default: `[geoip_detect2_countries mycountry]`
  * A text input that is pre-filled with the detected city (or other property): `[geoip_detect2_text_input city property:city lang:fr id:id class:class default:Paris]`
  * Geolocation information for the email text: `[geoip_detect2_user_info]`
* If you are using [WP Forms](https://wordpress.org/plugins/wpforms-lite/), you can use this shortcode:
  * Geolocation information for the email text: `[geoip_detect2_user_info]`
* Together with [SVG Flags](https://wordpress.org/plugins/svg-flags-lite/) you can show the flag of the detected country: `[geoip_detect2_current_flag]` (see [documentation](https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes#add-a-flag-of-the-visitors-country))

See [Documentation](https://github.com/yellowtree/geoip-detect/wiki) for more info.

= How can I use these functions? =

* You could choose the currency of the store based on the country name
* You could pre-fill the shipping country
* You could show the store nearest to your customer
* You show or hide content specific to a geographic target group
* Etc. ... You tell me! I'm rather curious what you'll do with this plugin!
* Be careful to comply to the applicable laws. For example Regulation (EU) 2018/302 ...
* If you need to get the user's timezone, it is more accurate to use JS solutions.

**System Requirements**: You will need at least PHP 7.2.5 . Also, if you use the plugin WooCommerce, you'll need at least WooCommerce 3.9.0 .

*GDPR: See [Is this plugin GDPR-compliant?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#is-this-plugin-gdpr-compliant)*

*This extension is "charity-ware". If you are happy with it, please [leave a tip](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL) for the benefit of [this charity](http://www.jmem-hainichen.de/homepage). (See [FAQ](https://github.com/yellowtree/geoip-detect/wiki/FAQ#what-you-mean-by-this-plugin-is-charity-ware) for more infos.)*

*[Here are other ways to contribute to the development of this plugin.](https://github.com/yellowtree/geoip-detect/blob/master/CONTRIBUTING.md)*

*This product can provide GeoLite2 data created by MaxMind, available from http://www.maxmind.com.*

== Installation ==

* Install the plugin
* Go to the plugin's option page and choose a data source.
* Test it by clicking on "Lookup" on the lookup page.

=== Troubleshooting ===

* Does `geoip_detect2_get_info_from_current_ip()` return the same country, regardless of where you are visiting the site from? 
* Maybe your server has a reverse proxy configured. You can check this: Go to the options page and look for "reverse proxy". Are there 2 IPs listed there? If so, which one corresponds to your [public IP](https://www.whatismyip.com/)?
* Or maybe you are using a site cache plugin. Then enable the option `Disable caching a page that contains a shortcode or API call to geo-dependent functions.`

[More Troubleshooting Hints](https://github.com/yellowtree/geoip-detect/wiki/Troubleshooting)

== Frequently Asked Questions ==

[Technically speaking, how could I verify if my visitor comes from Germany?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#technically-speaking-how-could-i-verify-if-my-visitor-comes-from-germany)

[How can I show text only if the visitor is coming from Germany?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#how-can-i-show-text-only-if-the-visitor-is-coming-from-germany)

[How can I add the current country name as text in my page?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#how-can-i-add-the-current-country-name-as-text-in-my-page)

[Which data source should I choose?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#which-data-source-should-i-choose)

[Can I change the time period how long the data is cached?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#can-i-change-the-time-period-how-long-the-data-is-cached)

[The Maxmind Lite databases are restricted by an EULA. Can I host a form where users can look-up the geographic information of an IP?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#the-maxmind-lite-databases-are-restricted-by-an-eula-can-i-host-a-form-where-users-can-look-up-the-geographic-information-of-an-ip)

[Does this plugin work in a MultiSite-Network environment?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#does-this-plugin-work-in-a-multisite-network-environment)

[Is this plugin GDPR-compliant?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#is-this-plugin-gdpr-compliant)

[What does "Privacy Exclusions" mean?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#what-does-privacy-exclusions-mean)

[What do you mean by "This plugin is charity-ware"?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#what-do-you-mean-by-this-plugin-is-charity-ware)

**Further documentation**

[PHP Functions](https://github.com/yellowtree/geoip-detect/wiki/API:-PHP)

[JS Functions for AJAX mode](https://github.com/yellowtree/geoip-detect/wiki/API%3A-AJAX)

[Shortcodes](https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes)

[Shortcodes for Contact Form 7](https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes-for-Contact-Form-7)

[Record Properties](https://github.com/yellowtree/geoip-detect/wiki/Record-Properties)

[API usage examples](https://github.com/yellowtree/geoip-detect/wiki/API-Usage-Examples)

== Screenshots ==

1. Lookup page (under Tools > Geolocation Lookup)
2. Options page (under Preferences > Geolocation IP Detection)

== Upgrade Notice ==

= 5.5.0 =

When using the default datasource "hostip.info", the region code (i.e. CA) is now correctly moved to the property `mostSpecificSubdivision` (previously, it was part of the property `city`)

= 5.0.0 =

If you are using AJAX mode, please read the changelog.

== Changelog ==

= 5.5.0 =
* FIX [!]: In the datasource "hostip.info", the region code (i.e. CA) is now correctly moved to the property `mostSpecificSubdivision` (previously, it was part of the property `city`)
* Library updates

= 5.4.1 =
* NEW: JS now emit events 'geoip-detect-shortcodes-done' and 'geoip-detect-body-classes-done', see https://github.com/yellowtree/geoip-detect/wiki/API:-AJAX#events
* FIX: Remove Fatal Error in check_requirements ("Undefined constand GEOIP_DETECT_DEBUG")

= 5.4.0 =
* NEW: Infos can be added to a mail sent by WPForms (with Smart Tag `{geoip_detect2_user_info}`)
* FIX: Remove Fatal Error on uninstall ("Undefined constand GEOIP_DETECT_DEBUG")
* Library updates

= 5.3.2 =
* FIX: Some country codes such as "PT" were missing in the (deprecated) legacy API mapping.
* FIX: Maxmind compatibility does not show a notice anymore when the plugin folder is non-standard (e.g. due to symlinks) or the Filename ends with Reader.php
* Library updates

= 5.3.1 =
* FIX: Respect JS file variant
* UI: Show selected file variant in backend options
* Library updates

= 5.3.0 =
* NEW: You can now specify to use a JS file variant if you are only using a subset of the features (see https://github.com/yellowtree/geoip-detect/wiki/API:-AJAX#js-variants) 
* Note[!]: frontend.js has now been renamed to frontend_full.js
* Library updates

= 5.2.2 =
* FIX: The format for new Maxmind licence keys has changed. (Existing licence keys will continue to work.)
(5.2.1 was a broken release)

= 5.2.0 =
* NEW: The list of reverse proxies of AWS CloudFront or CloudFlare can now be added as "known proxy"
* FIX [!]: AWS Cloudfront header name changed to HTTP_CLOUDFRONT_VIEWER_COUNTRY
* Reduced JS size (AJAX mode)
* Library updates

= 5.1.1 =
* NEW: For the reverse proxy configuration, internal adresses (such as 10.0.0.0/8) are now whitelisted by default. You can override this behaviour by using the wordpress filter `geoip_detect2_client_ip_whitelist_internal_ips`.
* NEW: Body classes now include the city name in English (e.g. geoip-city-Munich)
* FIX: Some server configurations showed this warning: Use of undefined constant CURL_HTTP_VERSION_2_0

= 5.1.0 =
New Datasource: Fastah Web API (beta), see https://aws.amazon.com/marketplace/pp/prodview-k5gjowexrefl2

AJAX mode:
* NEW: The JS function `geoip_detect.set_override_with_merge` can modify the override record in one property, merging it with the currently saved property

Other minor changes:
* FIX: In non-AJAX mode, properties such as "extra.original.zip" can be accessed again
* FIX: Automatic download of Maxmind database now also works when the temp folder is group/world writeable (as in AWS configurations)
* If you want to enable more Warnings (e.g. while debugging), you can add `define('GEOIP_DETECT_DEBUG', true)` to your wp-config.php or so.
* Library updates

= 5.0.0 =
In this release, there a small breaking changes marked by [!].

AJAX mode:
* FIX [!]: Empty attribute values such as `[geoip_detect2_show_if country=""]Country was not detected[/geoip_detect2_show_if]` are now working (they were ignored before)
* FIX [!]: Shortcodes that have an invalid value for the property `ajax` (e.g. `[geoip_detect2_text_input ajax="invalid"]`) are now using the AJAX option instead of always disabling AJAX
* FIX: In CF7, the country selector can now be used in AJAX mode
* FIX: In AJAX mode, the shortcode `[geoip_detect2_show_if]` renders as a `<div>` if it detects that the containing content has HTML block level elements
* NEW (Beta): In AJAX mode, the new property `autosave` saves the user input as local override for this browser. `[geoip_detect2_countries mycountry autosave]` and `[geoip_detect2_text_input city property:city autosave]`. (Please give feedback if this works as expected!)
* FIX: In AJAX mode, calling the method `set_override(record, duration_in_days)` now refreshes the AJAX shortcodes and CSS body classes.
-> Thus, it is now possible to quickly implement different content for different countries with an autodetected default country, see https://github.com/yellowtree/geoip-detect/wiki/API-Usage-Examples#country-selector-that-can-be-overridden-by-the-user

Other changes:
* NEW: Drastically improving performance if the lookup is performed for the current IP more than once (e.g. because of shortcodes without AJAX mode)
* UI: Showing the time for the subsequent lookup on the Test Lookup page
* FIX: Maxmind Datasource: Check if the database file is really a file, not a directory
* NEW: Header Datasource: Now a custom HTTP header can be used via the wordpress filter `geoip_detect2_source_header_http_key`

Other minor changes:
* Update the list of available APIs for getting the external IP (as whatismyip went down)
* Minimum Wordpress version is 5.4 now. 
* Update some internal libraries & dev tools
* Checked compatibility with PHP 8.1

The code of the plugin has not changed much, I have named this version 3.0 to indicate the major change on Maxmind's side of things. They explain it in this blog post:
https://blog.maxmind.com/2019/12/18/significant-changes-to-accessing-and-using-geolite2-databases/

[Older changelog](https://github.com/yellowtree/geoip-detect/blob/master/CHANGELOG.md)

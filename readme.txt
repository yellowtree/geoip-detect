=== Geolocation IP Detection ===
Contributors: benjaminpick
Tags: geolocation, locator, geoip, maxmind, ipstack
Requires at least: 4.0
Tested up to: 5.6
Requires PHP: 5.6
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL

== Description ==

Provides geographic information detected by an IP adress. This can be used in themes or other plugins, as a shortcode, or via CSS body classes. The city & country names are translated in different languages ([supported languages](https://dev.maxmind.com/geoip/geoip2/web-services/#Languages-8)).

= Features: =

* Provides these 5 functions (see [API Documentation](https://github.com/yellowtree/geoip-detect/wiki/API:-PHP)):
  * `geoip_detect2_get_info_from_ip($ip, $locales = array('en'), $options = array())`: Lookup Geo-Information of the specified IP
  * `geoip_detect2_get_info_from_current_ip($locales = array('en'), $options = array())`: Lookup Geo-Information of the current website user
  * `geoip_detect2_get_current_source_description(...)`: Return a human-readable label of the currently chosen source.
  * `geoip_detect2_get_external_ip_adress()`: Fetch the internet adress of the webserver
  * `geoip_detect2_get_client_ip()`: Get client IP (even if it is behind a reverse proxy)
* You can use one of these data sources (see [comparison](https://github.com/yellowtree/geoip-detect/wiki/FAQ#which-data-source-should-i-choose)):
  * Free (default source): [HostIP.info](http://www.hostip.info/) (IPv4 only)
  * Free with registration: [Maxmind GeoIP2 Lite City](http://dev.maxmind.com/geoip/geoip2/geolite2/), automatically updated weekly
  * Commercial: [Maxmind GeoIP2 City](https://www.maxmind.com/en/geoip2-country-database) or [Maxmind GeoIP2 Country](https://www.maxmind.com/en/geoip2-city)
  * Commercial Web-API: [Maxmind GeoIP2 Precision](https://www.maxmind.com/en/geoip2-precision-services) (City, Country or Insights)
  * Hosting-Provider dependent: [Cloudflare](https://support.cloudflare.com/hc/en-us/articles/200168236-What-does-CloudFlare-IP-Geolocation-do-) or [Amazon AWS CloudFront](https://aws.amazon.com/blogs/aws/enhanced-cloudfront-customization/) (Country)
  * Free or Commercial Web-API: [Ipstack](https://ipstack.com)
* For the property names, see the results of a specific IP in the wordpress backend (under *Tools > Geolocation IP Detection*).
* You can include these properties into your posts and pages by using the shortcode `[geoip_detect2 property="country.name" default="(country could not be detected)" lang="en"]` (where 'country.name' can be one of the other property names as well, and 'default' and 'lang' are optional).
* You can show or hide content by using a shortcode `[geoip_detect2_show_if country="FR, DE" not_city="Berlin"]TEXT[/geoip_detect2_show_if]`. See [Shortcode Documentation](https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes#show-or-hide-content-depending-on-the-location).
* When enabled on the options page, it adds CSS classes to the body tag such as `geoip-province-HE`, `geoip-country-DE` and `geoip-continent-EU`.
* When enabled on the options page, the client IP respects a reverse proxy of the server.
* If you are using [Contact Form 7](https://wordpress.org/plugins/contact-form-7/), you can use these shortcodes:
  * A select input with all countries, the detected country being selected by default: `[geoip_detect2_countries mycountry]`
  * A text input that is pre-filled with the detected city (or other property): `[geoip_detect2_text_input city property:city lang:fr id:id class:class default:Paris]`
  * Geolocation information for the email text: `[geoip_detect2_user_info]`

See [Documentation](https://github.com/yellowtree/geoip-detect/wiki) for more info.

= How can I use these functions? =

* You could choose the currency of the store based on the country name
* You could pre-fill the shipping country
* You could show the store nearest to your customer
* You show or hide content specific to a geographic target group
* Etc. ... You tell me! I'm rather curious what you'll do with this plugin!
* Be careful to comply to the applicable laws. For example Regulation (EU) 2018/302 (going into effect 03 Dec 2018)...
* If you need to get the user's timezone, it is more accurate to use JS solutions.

**System Requirements**: You will need at least PHP 5.6 (soon: PHP 7.2)

*GDPR: See [Is this plugin GDPR-compliant?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#is-this-plugin-gdpr-compliant)*

*This extension is "charity-ware". If you are happy with it, please [leave a tip](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL) for the benefit of [this charity](http://www.jmem-hainichen.de/homepage). (See [FAQ](https://github.com/yellowtree/geoip-detect/wiki/FAQ#what-you-mean-by-this-plugin-is-charity-ware) for more infos.)*

*This product can provide GeoLite2 data created by MaxMind, available from http://www.maxmind.com.*

== Installation ==

* Install the plugin
* Go to the plugin's option page and choose a data source.
* Test it by clicking on "Lookup" on the lookup page.

=== Troubleshooting ===

Does `geoip_detect2_get_info_from_current_ip()` return the same country, regardless of where you are visiting the site from? 
Maybe your server has a reverse proxy configured. You can check this: Go to the options page and look for "reverse proxy". Are there 2 IPs listed there? If so, which one corresponds to your [public IP](https://www.whatismyip.com/)?
Or maybe you are using a site cache plugin. Then enable the option `Disable caching a page that contains a shortcode or API call to geo-dependent functions.`

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

= 3.2.0 =

This plugin version simplifies complying the the EULA of Maxmind by automatically retrieving and honoring their Privacy Exclusion List. 
You need to enter your Account ID in the options. 
Find more information about the Privacy Exclusion API in the FAQ of the plugin.

= 3.1.0 =
The property access for shortcodes has been rewritten so that property names such as "extra.original.zip" (Datasource: ipstack) are possible now.

= 3.0.3.1 = 
Hotfix for the Manual download Maxmind datasource.
The Plugin was renamed to Geolocation IP Detection in order to prevent trademark issues. 

= 3.0.3 = 
The Plugin was renamed to Geolocation IP Detection in order to prevent trademark issues. 

= 3.0.1 = 

3.0 was not compatible with the WooCommerce plugin.

= 3.0 =

If you use Maxmind "Automatic download" then you need to upgrade to this plugin version in order to continue to receive database update. The Database license changed and you will need to register at their website and agree to the EULA.

= 2.13.0 =

PHP 5.6 is required now. If you are using the AJAX mode, this version will drastically reduce the number of requests as it will store the visitor's geo-information in a cookie.

= 2.12.0 =

New: Ipstack.com can be used as data source

= 2.11.0 =

The Download code of the automatically updated Maxmind file was rewritten for better performance. Also, AJAX support is now in beta (see documentation).

= 2.9.2 =

Hotfix: In 2.9.1, this plugin was incompatible with other Contact Form 7-Special Mailtags (https://contactform7.com/special-mail-tags/).

= 2.9.1 =

Online Shops: Be careful to comply to (EU) 2018/302 (going into effect 03 Dec 2018) in how you use this plugin !

= 2.9.0 =

There have been changes to the reverse proxy logic. If you have enabled a reverse proxy, check if the detected IP is correct.
New: Shortcode for showing/hiding content!

== Changelog ==

= 3.2.0 =
* NEW: The plugin now integrates the Maxmind Privacy Exclusion API. If you are using a Maxmind datasource, the plugin will return an empty result when looking up an IP that is on the privacy blacklist. You need to enter your Account ID for this.
* FIX: If timeZone is unknown, leave empty value instead of NULL
* FIX: Improve compatibility with PHP 8.0
* UI: Improving some strings for clearer documentation
* AJAX mode is now declared stable (no code change)

= 3.1.2 =
* NEW: The shortcode `[geoip_detect2_text_input]` now has a parameter `type` for hidden or other HTML5 input types (see [Postal code example](https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes-for-Contact-Form-7#create-a-text-input-that-is-prefilled-with-a-geodetected-property))
* FIX: The Backend UI "Lookup" does not show an empty timezone anymore if there is no data attached to this IP.
* NEW: In all datasources, the new record property `$record->extra->currencyCode` for the currency code of the detected country has been added
* FIX: Compatibility with PHP 8.0

= 3.1.1 =
* NEW: Add the possibility to access the ISO-3166 alpha3 version of `$record->country`: `$record->extra->countryIsoCode3` or `[geoip_detect2 property="extra.countryIsoCode3"]`
* FIX: The (CF7) shortcode `[geoip_detect2_countries]` now selects the selected country on page reload (the HTML tag autocomplete is set to `off` now)
* FIX: Subnets can now be entered in the preferences of the reverse proxy again (this was a regression of the Admin UI changes in 3.0.3)
* FIX: Do not log "It has not changed since the last update." as a cron-related error that should be shown to the user.

= 3.1.0 =
* FIX: The property access for shortcodes has been rewritten so that property names such as "extra.original.zip" (Datasource: ipstack) are possible now.
* FIX: The lookup page now also shows subdivisions (e.g. for IPs from Uk that have 2 levels of subdivisions)
* NEW: The (CF7) shortcode `[geoip_detect2_countries mycountry include_blank flag tel]` now adds the flag emoji (or ISO code in Windows) and the telephone international code to the country name
* FIX: AJAX mode: Using localStorage instead of Cookies for caching (as we hit the Cookie size limitation sometimes)
* FIX: AJAX mode: Remove jQuery dependency
* FIX: AJAX mode: `geoip_detect2_enqueue_javascript()` can be used now in function.php or templates (see [PHP Example](https://github.com/yellowtree/geoip-detect/wiki/API-Usage-Examples#ajax-enqueue-the-js-file-manually))

= 3.0.4 =
* When an error occurs during the Cron update of the Maxmind database, it is now shown in the backend.
* FIX: All times shown in the Admin backend now use the timezone set by Wordpress
* FIX: In the Admin Options, it was not possible to revert an hardcoded "External IP of this server" back to "automatic detection"
* FIX: `[geoip_detect2_show_if property="country.isInEuropeanUnion" property_value="true"]Products list for EU[/geoip_detect2_show_if]` now works properly (boolean values can be "true"/"yes" or "false"/"no")
* FIX: `[geoip_detect2_current_flag]` now compatible with the [SVG Flags](https://wordpress.org/plugins/svg-flags-lite/) version 0.9.0. See [Documentation](https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes#add-a-flag-of-the-visitors-country) for more infos. 
* Minor admin improvement: If the value "IPs of trusted proxies" is set, but "The server is behind a reverse proxy" is not ticked, issue an warning

= 3.0.3.1 =
* Hotfix: The filename specified in the manual datasource can be changed properly again.

= 3.0.3 =
* The Plugin has been renamed to "Geolocation IP Detection" in order to prevent trademark issues
* FIX: Minor improvements in the backend UI
* FIX: Security hardening against XSS

= 3.0.2 =
(Was not released)

= 3.0.1 =
* FIX: Button "Update now" now works also on the lookup page.
* FIX: Reverted the vendor code to the one used in 2.13 because it broke installations with the WooCommerce-plugin. I will update the vendor code again once we found a long-term solution for this interdepency.

= 3.0 =
* MAJOR CHANGE: Due to legal reasons, Maxmind now requires registration and some use cases are no longer allowed with the free data. If you use the Maxmind data source with automatic update, the update will fail for versions < 3.0 or if you have not entered a license key yet.
* Updated the Maxmind update mechanism
* Updated Maxmind vendor code 

The code of the plugin has not changed much, I have named this version 3.0 to indicate the major change on Maxmind's side of things. They explain it in this blog post:
https://blog.maxmind.com/2019/12/18/significant-changes-to-accessing-and-using-geolite2-databases/

= 2.13 =
* NEW: JS/AJAX mode now caches the response as a cookie so that every user only needs to call the AJAX requests once
* NEW: If you install the plugin [SVG Flags](https://wordpress.org/plugins/svg-flags-lite/), you can use this shortcode to show the flag of the current country: `[geoip_detect2_current_flag]`. See [Documentation](https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes#add-a-flag-of-the-visitors-country) for more infos. 
* FIX: Example PHP code on Lookup page now displays nicer array syntax (and fixing a deprecation warning)
* Updated Maxmind vendor code - PHP 5.6 is required now

= 2.12.1 =
* NEW: With the new Wordpress filter `geoip_detect2_record_data_after_cache` you can change the record data for testing purposes (see https://github.com/yellowtree/geoip-detect/wiki/API-Usage-Examples#change-record-data-eg-for-testing-purposes)
* NEW: All datasources now also have the properties `extra->flag` (containing the flag as Unicode Emoji) and `extra->tel` (containing the country dial code)
* Some cleanup in ipstack & showing all properties in backend.

= 2.12.0 =
* NEW: It is now possible to use ipstack.com as a data source.
* The Backend Lookup UI now can show all properties and you can choose if you want to see the PHP, Shortcode or JS syntax.
* The property "extra->original" now contains the original Web Answer array from the datasources ipstack & hostinfo

= 2.11.2 = 
* The auto-updater of the Maxmind City Lite source now updates more often (every 1-2weeks) in order to get more accurate data.

= 2.11.1 =
* FIX: When activating the plugin on Wordpress MultiSite, an error was thrown before
* NEW: Add body class "geoip-country-is-in-european-union" if the detected country is inside of the European Union
* JS/AJAX support for cached pages (Public BETA now. See https://github.com/yellowtree/geoip-detect/wiki/API%3A-AJAX)
* NEW: If AJAX and body classes are enabled, body classes are added via AJAX.

= 2.11.0 =
* NEW: JS/AJAX support for cached pages (This is in **BETA**. Read https://github.com/yellowtree/geoip-detect/wiki/API%3A-AJAX on how to activate it)
* FIX: Improve performance of unpacking the Maxmind file (Source: Automatic download) - important for hosts with a low max_execution_time
* NEW: On removal (in the Backend), the plugin will delete its options from the database and the downloaded Maxmind file

= 2.10.0 =
* NEW: The whitelisted proxies can now be subnets such as `11.11.11.0/24`
* NEW: Add a ContactForm7-Tag `geoip_detect2_text_input` (see https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes-for-Contact-Form-7#create-a-text-input-that-is-prefilled-with-a-geodetected-property)
* NEW: A new wordpress filter allows overriding of the detected geo-information inside the `geoip_detect2_shortcode_show_if`-Shortcode. Use the already-existing filter `geoip_detect2_record_information` instead if you want to override this information for all shortcodes and API calls.
* Updated Maxmind vendor code
* Increased WP minimum version to 4.0

= 2.9.2 =
* FIX: ContactForm7-Mailtag disabled mailtags from other plugins.

= 2.9.1 =
* NEW: Add ContactForm7-Mailtags so that the user information formatting can be customized: `geoip_detect2_get_client_ip`, `geoip_detect2_get_current_source_description`, `geoip_detect2_property_country`, `geoip_detect2_property_state`, `geoip_detect2_property_city`. Of course you can still use `geoip_detect2_user_info` as shortcode for all these informations.
* FIX: On some server, the plugin had wrongly assumed that PHP was compiled without IPv6-support.

= 2.9.0 =
* Add default Privacy text for GDPR compliance.
* The reverse proxy logic was heavily changed. If you run into configuration errors, try the debug panel (see link after the reverse proxy option).
* NEW: Reverse proxies can now be whitelisted - all non-whitelisted proxies are treated as user IP.
* NEW: Shortcode to show/hide content dynamically. (`[geoip_detect2_show_if country="US" not_state="Texas"]TEXT[/geoip_detect2_show_if]`) (Thanks to @DynAggelos!)
* NEW: All shortcodes now support multiple subdivisions (`[geoip_detect2 property="subdivisions.0.isoCode"]`)
* NEW: The CSS classes that are added to the body-tag (if enabled in the options) now also include the most specific subdivision (`geoip-province-HE`).
* Maxmind vendor code was updated to the current version (2.9.0).

[Older changelog](https://github.com/yellowtree/geoip-detect/blob/master/CHANGELOG.md)

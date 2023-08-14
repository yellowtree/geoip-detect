=== Geolocation IP Detection ===
Contributors: benjaminpick
Tags: geolocation, locator, geoip, maxmind, ipstack
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.2
Stable tag: 5.3.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL

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

= 5.0.0 =

If you are using AJAX mode, please read the changelog.

= 4.0.1 =

Hotfix - avoid fatal erros if another plugin also has the Maxmind library included

= 4.0.0 =

Improving Shortcodes (and Shortcodes for AJAX!)
New Minimum Requirements: PHP 7.2.5, and if you use WooCommerce it needs to be 3.9.0 or later.

= 3.3.0 =

Improving AJAX mode - now you can use it for specific pages.

= 3.2.1 = 

This update fixes an issue of 3.2.0 if your installation has WP_DEBUG enabled.

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

== Changelog ==

= 5.3.0 =
* NEW: You can now specify to use a JS file variant if you are only using a subset of the features (see https://github.com/yellowtree/geoip-detect/wiki/API:-AJAX#JS-Variants) 
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

= 4.2.3 =
* FIX: Further improve the Maxmind admin notice UI
* Update some smaller libraries

= 4.2.2 =
* FIX: Show Maxmind admin notice only on GeoIP pages to make it less intrusive

= 4.2.1 =
* FIX: Do not disable lookup automatically when potentially incompatible Maxmind libraries are found.

= 4.2.0 =
* NEW: Show a warning on the options page when there are incompatibilities with other plugins that also use the Maxmind libraries.
* FIX: Remove an incompatibility of the libraries with Toolset or other Laravel-based plugins
* NEW: In CF7, you can now add any property to the mail body with a special syntax, e.g. `[geoip_detect2_property_country__iso_code]`
* FIX (JS): Replace the internally used library 'lodash' with 'just' to reduce the JS file size
* FIX (JS): Improve error handling in AJAX mode
* FIX: Port numbers in reverse proxies are ignored now (removes incompatibility with Azure reverse proxies)
* FIX: Prevent Cloudflare APO from caching when using AJAX mode or page caching is disabled in the plugin options

= 4.1.0 =
* NEW: An `else` shortcode for `geoip_detect2_show_if` and `geoip_detect2_hide_if`: `[geoip_detect2_show_if city="Berlin"]You are in Berlin[else]You are not in Berlin[/geoip_detect2_show_if]`
* FIX: The JS for AJAX wasn't working for Safari browsers
* FIX: Improving some edge cases of `Record.get_with_locales()` and other methods of `Record` to be consistent with non-AJAX mode
* FIX: Revert more Maxmind libraries to fix incompatibility with WooCommerce

= 4.0.1 =
* FIX: Revert Maxmind library to 2.10.0 for now as the most current version seems to be incompatible with Wordfence and other plugins using the older version of the Maxmind library

= 4.0.0 =
This version has many changes regarding the Shortcodes API. It is a major version because it increases some system requirements (see below).

* NEW: Shortcodes can now also be resolved in AJAX mode (without coding JS). 
If you are using a page cache, AJAX mode is the best solution for you. And thanks to shortcodes, this doesn't need custom coding anymore. 
You can keep using the same shortcodes as before - just tick the options "Enable AJAX endpoint" and "Resolve shortcodes (via AJAX)". 
Instead of doing the geo-lookup while generating the HTML, it will generate boilerplate HTML (for the cache) that will be filled by the plugin's JS automatically (in the client's browser).
* NEW: JS has a new function called `set_override(record, duration_in_days)` (see [AJAX documentation](https://github.com/yellowtree/geoip-detect/wiki/API:-AJAX)) so that the record information can be overridden manually (e.g. when the user selects a certain country manually). A PHP equivalent will follow.
* NEW: The JS syntax of the shortcodes now supports both underscore_case and camelCase (e.g. both country.is_in_european_union and country.isInEuropeanUnion are valid)
* NEW: [geoip_detect2_show_if] and [geoip_detect2_hide_if] now have a new attribute `operator="OR"` - this can be used to create conditions such as "continent = EU OR country = US"

Other Improvements:
* NEW (UI): Add a "Empty Cache"-Button on the Lookup page if the data source is caching the results
* FIX: In some cases, the Privacy Exclusions Update wasn't rescheduled properly before
* FIX: Ipstack: The property country.isInEuropeanUnion is now filled properly.
* Updated vendor code

Also note:
* The minimum PHP version is now 7.2.5
* Minimum Wordpress version is now 5.0
* The plugin is now using PHP Type-Hinting for API functions - if you used the PHP API, please check if you see PHP errors
* If you are using WooCommerce, you need at least version 3.9.0 (released Jan 2020) - otherwise this plugin lookup disables itself

As always, if you are happy about the plugin, please consider [donating](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL), [becoming a Beta-Tester](https://github.com/yellowtree/geoip-detect/wiki/Beta_Testing) or otherwise [contributing](https://github.com/yellowtree/geoip-detect/blob/master/CONTRIBUTING.md) to it.


= 3.3.0 =
* NEW shortcode `[geoip_detect2_enqueue_javascript]` if you are using AJAX mode, but only on certain wordpress pages.
* NEW option "Add a country-specific CSS class to the <body>-Tag (via AJAX)." It is enabled automatically when upgrading the plugin, if the options "AJAX" and the "body tag" was enabled before.
* Some UI fixes

= 3.2.1 =
* FIX: Fix a fatal error that can occur in 3.2.0 if WP_DEBUG is enabled on your installation.

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

[Older changelog](https://github.com/yellowtree/geoip-detect/blob/master/CHANGELOG.md)

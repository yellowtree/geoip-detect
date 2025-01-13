== Old Changelog ==

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

= 2.8.2 =
* Maxmind vendor code was updated to the current version (2.7.0).
* FIX: There was a fatal error when using PHP 7.2 (thx jj-dev)
* FIX: The automatic update of Maxmind Geoip2 Lite City database was fixed.
* FIX: Always try to fill in more information into the country information from the GeoNames-DB.

= 2.8.1 =

(Was not released on wordpress.org)

= 2.8.0 =

* FIX: Localhost now always is a trusted proxy (for standard reverse proxy configurations, however the checkbox "uses a reverse proxy" still needs to be activated.)
* FIX: Timezone was overwritten by country data even though the maxmind data had already detected a timezone.
* ADD: If the manual datasource is used, the file will continue to be found if the site is moved to another host
* Maxmind vendor code was updated to the current version (2.6.0).
* PHP 5.4 is now required (due to the maxmind library).

= 2.7.0 =

* ADD: The options array of `geoip_detect2_get_info_from_ip` now has a new parameter for overriding the current source for a single lookup. See [API usage examples](https://github.com/yellowtree/wp-geoip-detect/wiki/API-Usage-Examples)
* ADD: New filter `geoip_detect2_shortcode_country_select_countries` for the country list of `[geoip_detect2_countries]`
* ADD: New constant `GEOIP_DETECT_IP_EMPTY_CACHE_TIME` that can be used to specify a shorter cache time in case temporarily no external IP was found.
* FIX: Compatibility with CF 4.6 (remove deprecated function call)
* Maxmind vendor code was updated to the current version (2.4.5).

= 2.6.0 =

* ADD: New datasources for Cloudflare & Amazon AWS CloudFront (countries for current IP only).
* ADD: Country information (names, lat/lon, continent, localized in the different languages) are now filled in for sources that only detect the country code (Cloudflare, Amazon, hostip.info)
* ADD: 2 shortcodes for [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) (a select with all countries `[geoip_detect2_countries mycountry]`, and tracking information for the email text `[geoip_detect2_user_info]`) - see [Documentation](https://github.com/yellowtree/wp-geoip-detect/wiki/API-Documentation#wp-contactform7-shortcodes)
* FIX: Cron scheduling is checked every time you visit the plugin page.
* FIX: Timezones of US & Canada are now detected more often (if country+state is known)
* FIX: Shortcode didn't use current sitelang as default, but always english
* Maxmind vendor code was updated to the current version (2.4.2).

= 2.5.7 =
* ADD: Shortcodes can now optionally specifiy the IP: `[geoip_detect2 property="country.isoCode" ip="(ipv4 or ipv6)"]`
* ADD: Plugin is now translated into German.
* FIX: `geoip_detect2_get_info_from_current_ip()` now also handles the case when REMOTE_ADDR contains multiple IP adresses

= 2.5.6 =
* FIX: Removed noticed concerning open_basedir.

= 2.5.5 =

* Clean-up changes to prepare plugin translation.
* FIX: Only show the "no database installed" admin notice to admins (props @meitar)

= 2.5.4 =

* FIX: Manual datasource filepath handling corrected.
* FIX: Potential incompability with BuddyPress removed.

= 2.5.3 =

* FIX: (Security) Add nonces to backend to avoid CSRF (thanks to Gerard Arall).
* FIX: Do not use PHP shortcode tags (<?=) as some servers do not support it with PHP 5.3
* Maxmind vendor code was updated to the current version (2.3.3).

= 2.5.2 =
* FIX: Also disallow proxy caching via HTTP header, if possible.
* NEW: Shortcodes for the other API functions: `[geoip_detect2_get_current_source_description]`, `[geoip_detect2_get_client_ip]`, and `[geoip_detect2_get_external_ip_adress]`
* FIX: geoip_detect2_get_external_ip_adress() : do not filter if $unfiltered is true.

= 2.5.1 =
* FIX: Upgrade script did change the source.
* FIX: Page caching is only disabled on upgrade when `set_css_country` is disabled.

= 2.5.0 =
* CHANGE: The parameter $skipCache is now $options['skipCache']. Using $skipCache is deprecated, but still works.
* NEW: $options['timeout'] for Web-API lookups can now be specified.
* FIX: Hostip.info did not set traits->ipAddress
* FIX: Hostip.info does not include data for IPv6. Add a lookup error message.
* NEW: Disable page caching if geoip-API was called (this is configurable in the options). (Supported plugins: WP Super Cache, W3 Total Cache, ZenCache, and possibly others)
* Maxmind vendor code was updated to the current version (2.3.1).

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
* NEW: 
License is now displayed before install.

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

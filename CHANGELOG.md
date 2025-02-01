== Old Changelog ==

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

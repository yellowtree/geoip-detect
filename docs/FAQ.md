## Something doesn't work. What can I do?

See [[Troubleshooting]].

## Technically speaking, how could I verify if my visitor comes from Germany?

Put this code somewhere in your template files:

```php
$userInfo = geoip_detect2_get_info_from_current_ip();
if ($userInfo->country->isoCode == 'de')
    echo 'Hallo! Schön dass Sie hier sind!';
```

To see which property names are supported, refer to the list of [[Record Properties]] or to the plugin's Lookup page.

## How can I show text only if the visitor is coming from Germany?

There are no shortcodes for this. Instead, you should use CSS:

```css
.geoip { display: none !important; }
.geoip-country-DE .geoip-show-DE { display: block !important; }
```

```html
<div class="geoip geoip-show-DE">
Shipping to Germany is especially cheap!
</div>
```

You need to enable the option `Add a country-specific CSS class to the <body>-Tag` to make this work.
See [[API Usage Examples#css-use]] for a more elaborate example.

## How can I add the current country name as text in my page?

Add this plugin shortcode somewhere in the page or post content:

    Wie ist das Wetter in [geoip_detect2 property="country.name" lang="de" default="ihrem Land"] ?

For more information, check the [[API: Shortcodes]] documentation and [[API Usage Examples]].  

## Which data source should I choose?

Each source has its advantages and disadvantages:

|                                           |    Cost   |  Precision | Performance | Registration |
| ----------------------------------------- | :-------: | :--------: | :---------: | :----------: |
| [HostIP.info](http://hostip.info/)        |   Free    |  Low       | Low<br>(Web-API) | No         |
| [DB-IP IP to City Lite](https://db-ip.com/db/download/ip-to-city-lite)                 |   Free (attribution requrired)    |  Medium    | High<br>(File) | No         |
| [Maxmind GeoIP2 Lite City/Country](https://www.maxmind.com/en/geolite2/signup) |   Free    |  Medium    | High<br>(File) |  [Yes](https://www.maxmind.com/en/geolite2/signup) |
| [Maxmind GeoIP2 City/Country](https://www.maxmind.com/en/geoip2-country-database)      |   Paid monthly    | High       | High<br>(File) | [Yes](https://www.maxmind.com/en/geoip2-country-database) |
| [Maxmind GeoIP2 Precision](https://www.maxmind.com/en/request-service-trial?service_geoip=1)                  |  Paid per lookup query   | High       | Low<br>(Web-API) | [Yes](https://www.maxmind.com/en/request-service-trial?service_geoip=1) |
| [ipstack](https://ipstack.com/)                                   |  Free/Paid monthly   | High       | Low<br>(Web-API) | [Yes](https://ipstack.com/product) |
| [Fastah](https://aws.amazon.com/marketplace/pp/prodview-k5gjowexrefl2) | Paid monthly | High, esp. mobile | Low<br>(Web-API) | [Yes](https://aws.amazon.com/marketplace/pp/prodview-k5gjowexrefl2)

Legend:
* Column _Cost_: The price can depend on several factors: if you need city or country data, if pay per month, per year, or per query, etc. A price per query, of course, is helpful if you use a low amount of queries per month.
* Column _Precision_: To compare the commercial and the free data of Maxmind, see [accuracy stats per country](https://www.maxmind.com/en/geoip2-city-database-accuracy). Accuracy between data sources may differ according to your main target country.
* Column _Performance_: There are great differences how long a lookup can take. The Web-APIs take 0.5-1 second per Lookup (that's why they are cached for new each IP you request data from), the File-based APIs only about 0.01 second. 
* Column _Registration_: Some services require you to sign up at their website before you can use this datasource.

Additional Notes for certain sources:
* If you choose "DB-IP", you must include a link back to DB-IP.com on pages that display or use results from the database. 
* If you choose "automatic" and enter the account's license key, this plugin will install "Maxmind GeoIP2 Lite City" and update it weekly. This is the easiest option.
* If you choose "manual", you can use any of the file-based Maxmind-Databases. It's recommended that you update the database regularly, for example by using the [shell script provided by Maxmind](http://dev.maxmind.com/geoip/geoipupdate/).
* If you choose "ipstack", note that the free plan does not allow HTTPS-Encryption of their lookup – which is bad if you have to comply to GDPR.

## Can I change the time period how long the data is cached?

By default, lookups via Web-API are cached for 7 days. If you want to define a different time span, add this code somewhere in your theme or plugin:

```php
if (!defined('GEOIP_DETECT_READER_CACHE_TIME'))
	define('GEOIP_DETECT_READER_CACHE_TIME', 14 * DAY_IN_SECONDS);
```

## The Maxmind Lite databases are restricted by an EULA. Can I host a form where users can look-up the geographic information of an IP?

In general, no. Read the information by Maxmind:

* [Site Licence Overview](https://www.maxmind.com/en/site-license-overview) 
* Details: [End User Licence Agreement](https://www.maxmind.com/en/end-user-license-agreement#internal-restricted-business-purposes)

(This changed on Dec 30, 2019. Before, the databases were licensed "Creative Commons ShareAlike-Attribution" - this is not the case anymore.)

## Does this plugin work in a WordPress MultiSite-Network environment?

There seem to be no issues with WordPress multisite, but it is not officially supported.

## Is this plugin GDPR-compliant?

If your website is available in EU-countries, it must comply to GDPR-Regulations for privacy. Using this plugin is not GDPR-compliant per se, because it all depends on your use case and whether you explain it in your privacy policy.

1. If you use a web-based source (hostip.info, Maxmind Precision, ipstack), the plugin stores all IPs that visited the site in a cache (by default for 7 days) for performance reasons. If you want to disable this behavior, add `define('GEOIP_DETECT_READER_CACHE_TIME', 0);` in your theme's `function.php`. 

2. If you use the source Ipstack.com, you must use encryption (thus you need to pay to ipstack.com because their free plans currently do not allow encryption).

3. If you are using AJAX/JS mode, then the plugin is storing the geo-information of the user as a cookie. This should be mentioned in the privacy policy. You can disable the setting of the cookie by adding the following code in your theme's `function.php` - however, this will probably increase the server load (depending on your implementation).

```php
add_filter('geoip_detect2_ajax_localize_script_data', function($options) {
    $options['cookie_name'] = '';
    return $options;
});
```

4. Be especially careful when using geographic information in order to change the pricing amount or other selling options, as this might not be legal. Also, if you store the geographic information server-side, this should probably be opt-in and the GDPR Privacy Policy should mention what exactly is stored, if you connect it to other data (profiling), how long you store it, and so on.

5. If you want to use this plugin on a opt-in basis ("explicit consent"), the easiest way is to use AJAX mode and then tell the consent manager to block the JS file pattern `plugins/geoip-detect/js/dist/frontend` (how this works will depend on which cookie banner tool you are using).

## What does "Privacy Exclusions" mean?

Due to the California Consumer Privacy Act (CCPA), Maxmind has created an EULA that makes it mandatory for those who use their data to check if the IPs they are looking up are on a certain blacklist. This is done automatically by the plugin:

1. Once a day, the plugin gets a list of blacklisted IPs from the servers of Maxmind. Your Accound Id and Licence Key from Maxmind is needed for this.
2. On every lookup, the plugin checks locally if the IP is on that blacklist - and if so, it does not return any data from the database. `$record->extra->error` then has the message: "This IP has no informations attached by request of the IP owner (Reason: ccpa_do_not_sell)."
3. Some time later, at the next update of the Maxmind database, Maxmind will have removed the information attached to the IP from the database.

You can choose to disable that behavior.

```php 
// Make sure you are compliant to the EULA in a different way!
add_filter('geoip_detect2_maxmind_ccpa_do_update', '__return_false');
add_filter('geoip_detect2_maxmind_ccpa_enabled',   '__return_false');
```

## What do you mean by "This plugin is charity-ware"?

I have decided against trying to make this plugin profitable. Also, the work project I have been developing this for is long finished. If you want to encourage me to keep on maintaining this, and say "thank you for your work", please consider donating for this charity:

[Youth With A Mission, Hainichen, Germany](http://www.jmem-hainichen.de/homepage) is a Christian non-profit organisation whose main aim is to strengthen the value of family in society. (Disclaimer: I am employed by that charity now.)

[Paypal Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL)

## How can I test that my geoip-implementation works?
You can use a VPN provider to get an IP in your desired country and test using that output. 
Alternatively, you can use tools dedicated for Geo-IP testing such as [GeoScreenshot](https://www.geoscreenshot.com/)

## How can I install a beta version of this plugin?

See [[Beta Testing]].

*Here is a list of common problems and how to debug them.*

## General

Add the following line to your `/wp-config.php`:

```php
define('GEOIP_DETECT_DEBUG', true);
```

This will enable more warnings that might be useful for debugging purposes.

## Q0: Country X is detected for my IP, but I know country Y is correct?

This can be because of one of these 3 common problems:
- Is the detected IP of the client the real client IP? (see Q1 below)
- Do you use a page caching plugin? (see Q2 below)
- Is the data of the database provider wrong? (see Q3 below)

## Q1: He says my IP is X, but I know it is Y?

#### How to diagnose:
- Go to the backend to `Tools > Geolocation Lookup`. The IP shown at `Your current IP:` is the detected IP. Click on `(Not correct?)`  should be the same as the one listed at `Real client IP`
- If these IPs match, continue at Q2 (site caching). If they do not match:

#### Possible solutions
- Probably there are reverse proxies in between the website and the client, either as a software running on your server (such as nginx) or other servers as part of your hosting setup (load balancing, CDN, or similar). This needs to be configured:
- Go to  `Tools > Geolocation Lookup`, click on `(Not correct?)`. (You arrive at the `Client IP Debug Panel`)
  - Is the real client IP listed in `HTTP_X_FORWARDED_FOR` ? Then you need to configure your reverse proxy settings: 
    1. Copy the IP from `REMOTE_ADDR`
    2. Go to `Settings > Geolocation IP Detection`
    3. Enable `The server is behind a reverse proxy`
    4. Add the IP from `REMOTE_ADDR` to `IPs of trusted proxies:` (ideally you make sure it's either internal IPs or IPs that belong to the IP space of your hosting provider)
    5. Add the other IPs from `HTTP_X_FORWARDED_FOR` that are not your real client IP
    6. If your server supports IPv6, make sure you include both the IPv4 and IPv6 adresses of these proxies. You can try it by accessing your website via IPv4 / IPv6.
  - If that IP is nowhere else on that page, then you found a special reverse proxy that either uses a different HTTP header for the real client IP or it's not configured to pass on the client IP. Talk with your administrator about it.

## Q2: Sometimes the IP detection shows the correct results, but sometimes not?

#### How to diagnose:

* Do you use a site cache, or a HTML optimizing plugin? (Does it work when you are logged in in the Wordpress backend, and only sometimes/not at all when you are logged out. Then most certainly it is a caching mechanism.)
* Is there a CDN/reverse proxy in front of your server? The plugin tries to disable the site caching mechanism of such proxies, but it's up to them if they honor these requests or not. 
* If you can, disable the caching globally to check if that's the problem.

#### Possible solutions:

* Use the [AJAX mode](https://github.com/yellowtree/geoip-detect/wiki/API:-AJAX) instead. It is made for the use of site cache plugins.
* Enable the option `Disable caching a page that contains a shortcode or API call to geo-dependent functions.`. This option should be enabled if you use any kind of site cache ... However, some site cache plugins are ignoring this option (known not to work: WPRocket, Siteground Caching - let me know if you find more of them) - in this case, either use AJAX mode or exclude these pages manually from the site cache ("whitelisting").


## Q3: How can I check if the plugin gets it wrong, or the database provider has the correct information?

While some are more precise than others (see [Which data source should I choose?](https://github.com/yellowtree/geoip-detect/wiki/FAQ#which-data-source-should-i-choose)), no data source has 100% accuracy. There are IPs with no data at all, IPs with only country, and IPs with the wrong city (even the wrong country sometimes). 

The plugin takes the data of the data sources and tries to add data (such as the timezone, flag, etc.). So normally, it does not remove data (except if I do a bad mistake), and it certainly doesn't change data to a different country/city (except if you fiddle around with the Wordpress filters of the plugin).

#### How to diagnose: 
- Get the raw result of what the Plugin returns:
  - Check the IP under `Tools > Geolocation Lookup`. It will show all properties that the plugin provides. If you use a shortcode or the AJAX mode, still this function is what will be returned.
  - Do it again by ticking `Skip Cache`. If the result is different now, it had a wrong lookup result cached before. (This cache is only used for web-lookup sources such as Maxmind Precision, ipstack, etc.)
  - If the result is still different to what you expected, then maybe you are using a site cache (see Q2).

- Try looking up the IP directly at the vendor:
    - Hostinfo: https://hostip.info/
    - Maxmind: https://www.maxmind.com/en/geoip-demo
    * This is the data available in Maxmind Precision, a paid plan of Maxmind. The Lite database is less accurate.
    - Ipstack: https://ipstack.com/

If they have the wrong information, contact them about it, they might correct the information in their database. 

If it is correct at their site, but not correct/missing in the plugin, then write me a bug report (see Q9).

## Q9: Which informations do you need for a bug report?

Most important:
- Which feature of the plugin is not working / should be improved?
- If you see PHP Errors/Notices, please copy them

Important context information:
- Which data source are you using?
- Which site cache plugin are you using (if any)?
- Did you try the suggested solutions above?
- Plugin Version, PHP Version?
- Any special server setup (Cloud? Docker? etc.)
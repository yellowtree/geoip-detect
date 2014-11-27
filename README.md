Tests: [![Build Status](https://travis-ci.org/yellowtree/wp-geoip-detect.png?branch=master)](https://travis-ci.org/yellowtree/wp-geoip-detect)

# GeoIP Detection #

* **Contributors:** [benjaminpick] (http://profiles.wordpress.org/benjaminpick)

* **License:** [GPL v2 or later] ( http://www.gnu.org/licenses/gpl-2.0.html)

Retrieving Geo-Information using the Maxmind GeoIP (Lite) Database.

## Description ##

Provides geographic information detected by an IP adress. This can be used in themes or other plugins, or via CSS body classes.

#### Features: ####

* Provides 3 functions: 
  * `geoip_detect_get_info_from_ip($ip)`: Lookup Geo-Information of the specified IP 
  * `geoip_detect_get_info_from_current_ip()`: Lookup Geo-Information of the current website user
  * `geoip_detect_get_external_ip_adress()`: Fetch the internet adress of the webserver
* Auto-Update the GeoIP database once a week
* For the property names, see the results of a specific IP in the wordpress backend (under Tools > GeoIP Detection).
* You can include these properties into your posts and pages by using the shortcode `[geoip_detect property####"country_name"]` (where 'country_name' can be one of the other property names as well).
* When enabled on the plugin page, it adds CSS classes to the body tag such as `geoip-country-DE` and `geoip-continent-EU`.

#### How can I use these functions? ####

* You could choose the currency of the store based on the country name
* You could suggest an timezone to use when displaying dates
* You could show the store nearest to your customer
* Etc. ... You tell me! I'm rather curious what you'll do with this plugin!

*This product includes GeoLite data created by MaxMind, available from http://www.maxmind.com.*

## Installation ##

This plugin does not contain the database itself, so it has to be loaded before first use.
2 alternative ways of doing this:

#### Automatic Installation ####

Go to Tools > GeoIP Detect and click on the button `"Update now"`.
The database is written into the `/uploads`-Folder.

#### Manual Installation ####

1. Download the database at http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
2. extract it and 
3. put it into the plugin directory.

## Frequently Asked Questions ##

#### How exact is this data? ####

Think of it as an "educated guess": IP adresses and their allocation change on a frequent basis.
If you need [more exact data](http://www.maxmind.com/en/geolite_city_accuracy "GeoLiteCity Accuracy"), consider purchasing the commercial version of the data.

#### Technically speaking, how could I verify if my visitor comes from Germany? ####

Put this code somewhere in your template files:

    $userInfo #### geoip_detect_get_info_from_current_ip();
    if ($userInfo && $userInfo->country_code ## 'DE')
        echo 'Hallo! Schön dass Sie hier sind!';

Or, add the plugin shortcode somewhere in the page or post content:

    Heyo, over there in [geoip_detect property####"country_name"] !
   
To see which property names are supported, refer to the [Plugin Backend](http://wordpress.org/plugins/geoip-detect/screenshots/).

#### What is planned to be implemented? ####

Maxmind released a new API version (v2) with localized country names and a accuracy percentage. Work in Progress.

## Screenshots ##

1. Backend page (under Tools > GeoIP Detection)

## Upgrade Notice ##

#### 1.7.1 ####

Cron update was broken again ...

#### 1.6 ####

Automatic weekly update didn't work in all installations.

#### 1.5 ####

Fixing automatic weekly updates.


## Changelog ##

#### 1.7.1 ####
* FIX: Fatal error on cron run

#### 1.7 ####
* FIX: Schedule Database update to do in background immediately after plugin installation/re-activation.
* FIX: Longitude can be smaller than -90

#### 1.6 ####
* NEW: Can add a country- and continent-specific class on the body tag. You need to activate this in the options.
* FIX: Automatic weekly update. (Didn't work on all installations).
* FIX: Do not include Maxmind Libraries again if already included by another plugin/theme

#### 1.5 ####
* FIX: Automatic weekly update. Go to the plugin page (Tools menu) to verify that an update is planned.

#### 1.4 ####
* Feature: Add shortcode [geoip_detect property####"(property name)"] for direct use in posts/pages

#### 1.3 ####
* FIX: Manual install works again (was broken since 1.2)

#### 1.2 ####
* FIX: property region_name is now filled again (was broken since 1.1) 

#### 1.1 ####
* Add function `geoip_detect_get_external_ip_adress()`: Ask a webservice to tell me the external IP of the webserver.
* New filter: When developing locally, the external IP is used to determine the geographic location.

#### 1.0 ####

* First working release.

---
title: "API: Shortcodes for Contact Form 7"
---
These shortcodes you can use in forms created with [WP ContactForm7](http://wordpress.org/plugins/contact-form-7/):

### Create a select input with all countries
(since 2.6.0)

Examples:

`[geoip_detect2_countries mycountry id:id class:class lang:fr]`<br>
A list of all country names in French (with CSS id "#id" and class ".class"), the visitor's country is preselected.

`[geoip_detect2_countries mycountry include_blank]`<br>
Country names are in the current site language. User can also choose '---' for no country at all.

`[geoip_detect2_countries mycountry flag tel]`<br>
(since 3.0.5)<br>
Country names have a UTF-8 flag (or ISO-Code) in front of the country name, and the (+1) internation phone code after it

`[geoip_detect2_countries mycountry "US"]`<br>
"United States" is preselected, there is no visitor IP detection going on here

`[geoip_detect2_countries mycountry default:US]`<br>
Visitor's country is preselected, but in case the country is unknown, use "United States"

`[geoip_detect2_countries mycountry autosave]`
Visitor's country is preselected, but when the visitor changes the country, his choice is saved in his browser (in AJAX-mode only) 

```
@param string $id CSS Id of element
@param string $class CSS Class of element
@param string $lang Language(s) (optional. If not set, current site language is used.)
@param string $default 		Default Value that will be used if country cannot be detected (optional)
@param string $include_blank If this value exists, a empty value will be prepended ('---', i.e. no country) (optional)
@param bool   $flag          If a flag should be added before the country name (In Windows, there are no flags, ISO-Country codes instead. This is a design choice by Windows.)
@param bool   $tel           If the international code should be added after the country name
@param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
@param bool   $autosave      1: In Ajax mode, when the user changes the country, save his choice in his browser. (optional, Ajax mode only)

```

### Create a text input that is prefilled with a geodetected property
(since 2.10.0)

Property can be: continent, country, city, postal.code, location.timeZone or any other property understood by the shortcode `geoip_detect2`

Examples:

`[geoip_detect2_text_input city property:city lang:fr id:id class:class]`<br>
A text input that has the detetected city as default (with CSS id "#id" and class ".class")

`[geoip_detect2_text_input city property:city lang:fr id:id class:class default:Paris]`<br>
As above, but in case the city is unknown, use "Paris"

`[geoip_detect2_text_input postal property:postal.code class:hidden type:hidden]`<br>
An invisible text input containing the postal code. (The `type` parameter is available since 3.1.2)



### Insert the geoinfos of the user into the email text
(since 2.6.0)

Just use `[geoip_detect2_user_info]` in the email body. The result will me like this:

```
IP of the user: 88.64.140.3
Country: Germany
State or region: Hesse
City: Eschborn

Data from: GeoLite2 City database
```

If you want to customize the labels or formatting ... use this text in the email body as starting point (since 2.9.1):

```
IP of the user: [geoip_detect2_get_client_ip]
Country: [geoip_detect2_property_country]
State or region: [geoip_detect2_property_region]
City: [geoip_detect2_property_city]

Data from: [geoip_detect2_get_current_source_description]
```

Result: same as above.

You can print any property name that your data source allows (since 4.2.0), e.g.
```
Country Flag: [geoip_detect2_property_extra__flag]
Country Iso Code: [geoip_detect2_property_country__iso_code]
Country Iso 3 Code: [geoip_detect2_property_extra__country_iso_code_3]
3-letter currency code: [geoip_detect2_property_extra__currency_code]
```

As you might have guessed, you can use the [property names](https://github.com/yellowtree/geoip-detect/wiki/Record-Properties) by:

* Replacing `.` by 2 underscores
* Reformatting `geonameId` (pascalCase) to `geoname_id` (underscore_case)
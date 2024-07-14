---
title: "API: Shortcodes"
---
### Geolocation Information

Examples:

`[geoip_detect2 property="country"]` -> Germany<br />
`[geoip_detect2 property="country.isoCode"]` -> de

`[geoip_detect2 property="country" lang="de"]` -> Deutschland<br />
`[geoip_detect2 property="country" lang="fr,de"]` -> Allemagne<br />
`[geoip_detect2 property="country.confidence" default="default value"]` -> default value

`[geoip_detect2 property="country.isoCode" ip="8.8.8.8"]` -> US

```
@param string $property		Property to read. For a list of all possible property names, see https://github.com/yellowtree/geoip-detect/wiki/Record-Properties#list-of-all-property-names
@param string $lang			Language(s) (optional. If not set, current site language is used.)
@param string $default 		Default Value that will be shown if value not set (optional)
@param string $skipCache	if 'true': Do not cache value (optional)
@param string $ip			IPv4 or v6 to look up. (optional. If not set, the current client IP is used.)

@since 2.5.7 New attribute `ip`
```

### Get a human-readable source description
(since 2.5.2)

`[geoip_detect2_get_current_source_description]` -> 

Print a human-readable label of the currently chosen source.
(This shortcode will always be executed on the server, no AJAX mode available.)

### Get detected client ip
(since 2.5.2)

`[geoip_detect2_get_client_ip]` -> 173.18.0.20

IPv4 or IPv6-Adress of the client. This takes reverse proxies into account, if they are configured on the options page.

### Get the external IP of the server
(since 2.5.2)

`[geoip_detect2_get_external_ip_adress]` -> 173.194.116.196

This will be the IPv4 address of the server.
(This shortcode will always be executed on the server, no AJAX mode available.)

### Create a select input with all countries
(since 2.6.0)

Examples:

`[geoip_detect2_countries_select name="mycountry" lang="fr"]`<br>
A list of all country names in French, the visitor's country is preselected.

`[geoip_detect2_countries_select id="id" class="class" name="mycountry" lang="fr"]`<br>
As above, with CSS id "#id" and class ".class"

`[geoip_detect2_countries_select name="mycountry" include_blank="true"]`<br>
Country names are in the current site language. User can also choose '---' for no country at all.

`[geoip_detect2_countries name="mycountry" flag="true" tel="true"]`
(since 3.0.5)<br>
Country names have a UTF-8 flag in front of the country name, and the (+1) internation phone code after it <br>
(In Windows, there are no flags, ISO-Country codes instead. This is a design choice by Windows.)

`[geoip_detect2_countries_select name="mycountry" selected="US"]`<br>
"United States" is preselected, there is no visitor IP detection going on here

`[geoip_detect2_countries_select name="mycountry" default="US"]`<br>
Visitor's country is preselected, but in case the country is unknown, use "United States"

`[geoip_detect2_countries_select name="mycountry" autosave="1"]`<br>
Visitor's country is preselected, and when the visitor changes the selected value, the selected value is stored in the browser for future requests. All AJAX shortcodes on the same page and the body CSS classes will update accordingly without a full HTTP site reload. (Since 5.0.0)

```
@param string $name Name of the form element
@param string $id CSS Id of element
@param string $class CSS Class of element
@param string $lang Language(s) (optional. If not set, current site language is used.)
@param string $selected Which country to select by default (2-letter ISO-Code.) (optional. If not set, the country will be detected by client ip.)
@param string $default 		Default Value that will be used if country cannot be detected (optional)
@param string $include_blank If this value contains 'true', a empty value will be prepended ('---', i.e. no country) (optional)
@param bool   $flag          If a flag should be added before the country name
@param bool   $tel           If the international code should be added after the country name
@param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
@param bool   $autosave      1: In Ajax mode, when the user changes the country, save his choice in his browser. (optional, Ajax mode only)
```


### Create a text input with a geoip value prefilled
(since 2.10.0)

Generating a <input />-field that has a geoip value as default

Property can be: continent, country, city, postal.code or any other property understood by `geoip_detect2_get_info_from_ip`

Examples:

`[geoip_detect2_text_input name="city" property="city" lang="fr" id="id" class="class"]`<br>
A text input that has the detetected city as default (with CSS id "#id" and class ".class")

`[geoip_detect2_text_input name="city" property="city" lang="fr" id="id" class="class" default="Paris"]`<br>
As above, but in case the city is unknown, use "Paris"

`[geoip_detect2_text_input name="postal" property="postal.code" type="hidden"]`<br>
An invisible text input containing the postal code. 

```
@param string $property Maxmind property string (e.g. "city" or "postal.code")
@param string $name Name of the form element
@param bool   $required If the field is required or not
@param string $id CSS Id of element
@param string $class CSS Class of element
@param string $type HTML input type of element ("text" by default) (@since 3.1.2)
@param string $lang Language(s) (optional. If not set, current site language is used.)
@param string $default 		Default Value that will be used if country cannot be detected (optional)
@param bool 	 $skip_cache
@param string $ip
@param string $placeholder
@param bool   $ajax          1: Execute this shortcode as AJAX | 0: Execute this shortcode on the server | Unset: Use the global settings (execute as AJAX if both 'AJAX' and 'Resolve shortcodes (via Ajax)' are enabled)
@param bool   $autosave      1: In Ajax mode, when the user changes the country, save his choice in his browser. (optional, Ajax mode only)
```

### Show or hide content depending on the location
(since 2.9.0)

`geoip_detect2_show_if` does exactly the contrary of `geoip_detect2_hide_if` - so use the name that feels more natural to your use case.

- All conditions are combined with AND (by default)
- All conditions can take various possible values (OR) seperated by comma (,)
- All conditions can be given as [ISO-Code](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements), full name or GeonamesId
- WARNING: These shortcodes cannot be nested. (You cannot nest shortcodes of the same name in Wordpress.) Instead, split it into several shortcode blocks or combine the conditions in one shortcode.
- WARNING: City names can be ambigous. For example: In the US, there are many cities called "Paris". Instead, you can use the [geonameId](https://www.ip2location.com/free/geoname-id) of the city.
- Conditions can either be combined by AND or OR. It is not possible to write this condition within a shortcode: (city = Berlin AND country = Germany) OR country = France

Examples:

```
[geoip_detect2_show_if country="US"] TEXT [/geoip_detect2_show_if]
```
TEXT will be shown if the user is in the US.

```
[geoip_detect2_show_if country="US" state="Texas"] TEXT [/geoip_detect2_show_if]
```
TEXT will be shown if the user is in the state of Texas, US. 

```
[geoip_detect2_hide_if lang="en" country="France" not_city="Paris, Lyon"] TEXT [/geoip_detect2_hide_if]
```
TEXT will be hidden if the user is in France, except if he lives in Paris or Lyon. Use the english country/city name for the comparison.

```
[geoip_detect2_show_if property="location.timeZone" property_value="Europe/Berlin"] TEXT [/geoip_detect2_show_if]
```
TEXT will be shown if the user is living in the Timezone Europe/Berlin. `property` can take any property name.

```
[geoip_detect2_show_if property="country.isInEuropeanUnion" property_value="true"] TEXT [/geoip_detect2_show_if]
```
Show TEXT if the visitor is in the european union

```
[geoip_detect2_show_if city="Berlin" operator="or" country="France"] TEXT [/geoip_detect2_show_if]
```
Show TEXT if the visitor is from Berlin OR France (since 4.0.0)

```
[geoip_detect2_show_if city="Berlin"]TEXT[else]OTHER[/geoip_detect2_show_if]
```
Show TEXT if the visitor is from Berlin, otherwise show OTHER (since 4.1.0)

```
[geoip_detect2_show_if country=""]NO COUNTRY[/geoip_detect2_show_if]
```
Show NO COUNTRY if no country was detected (since 5.0.0)


Possible attribute names:  `continent`, `country`, `most_specific_subdivision`/`region`/`state` (those are aliased, choose any), `city`

Exclusive attributes: `not_continent`, `not_country`, `not_most_specific_subdivision`/`not_region`/`not_state` (those are aliased, choose any), `not_city`

Other attribute names: `property` and `property_value` (or exclusive: `not_property_value`)

The attributes `lang` and `skip_cache` work as in `[geoip_detect2 ...]`

The attribute `operator` can be "AND" or "OR" (since 4.0.0)

### Add a flag of the visitor's country
(since 2.13.0)

**Important:** This shortcode requires plugin [SVG Flags](https://wordpress.org/plugins/svg-flags-lite/) to be installed (and activated).

Simple use:
```
[geoip_detect2_current_flag]
```

All possible parameters:
```
[geoip_detect2_current_flag height="10% !important", width="30" class="extra-flag-class" squared="0" default="it"]
```

```
@param int|string width   CSS Width of the flag `<span>`-Element (in Pixels or CSS including unit)
@param int|string height  CSS Height of the flag `<span>`-Element (in Pixels or CSS including unit)
@param string skipCache	if 'true': Do not cache value (optional)
@param int squared	     Instead of being 4:3, the flag should be 1:1 in ratio
@param string class 	 Extra CSS Class of element. All flags will have the class `flag-icon` anyway.
@param string default 	 Default Country in case the visitor's country cannot be determined
```

If you want to style all flags:

```css
.flag-icon { margin-top: 4px !important; }
```

### AJAX Mode:
(since 3.3.0)

```
[geoip_detect2_enqueue_javascript]
```

Enqueue the helper JS for this page only. Useful if only that page(s) depend use the geo-API.

### Where can I use these shortcodes?

It works in a post or page content, but not in a title. If you use it in your theme, use [do_shortcode()](https://codex.wordpress.org/Function_Reference/do_shortcode).

### It doesn't work. Why?

Check the HTML to see error messages that are commented out (`<!-- ... -->`).

## WP ContactForm7 Shortcodes

See [CF7 Shortcodes API Documentation](./API:-Shortcodes-for-Contact-Form-7)

---
title: "API: Shortcodes for WP Forms"
---
These shortcodes you can use in forms created with [WP Forms Lite / WP Forms Pro](https://wordpress.org/plugins/wpforms-lite/):


### Insert the geoinfos of the user into the email text
(since 5.4.0)

Just use `{geoip_detect2_user_info}` in the email body. The result will me like this:

```
IP of the user: 88.64.140.3
Country: Germany
State or region: Hesse
City: Eschborn

Data from: GeoLite2 City database
```

If you want to customize the labels or formatting ... use this text in the email body as starting point (since 2.9.1):

```
IP of the user: {geoip_detect2_get_client_ip}
Country: {geoip_detect2_property_country}
State or region: {geoip_detect2_property_region}
City: {geoip_detect2_property_city}

Data from: {geoip_detect2_get_current_source_description}
```

Result: same as above.

Actually, you can print any property name that your data source allows, e.g.
```
Country Flag: {geoip_detect2_property_extra__flag}
Country Iso Code: {geoip_detect2_property_country__iso_code}
Country Iso 3 Code: {geoip_detect2_property_extra__country_iso_code_3}
3-letter currency code: {geoip_detect2_property_extra__currency_code}
```

As you might have guessed, you can use the [property names](https://github.com/yellowtree/geoip-detect/wiki/Record-Properties) by:

* Replacing `.` by 2 underscores
* Reformatting `geonameId` (pascalCase) to `geoname_id` (underscore_case)
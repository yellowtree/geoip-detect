# Properties added by the plugin

Additional to the [Maxmind properties](http://dev.maxmind.com/geoip/geoip2/web-services/), this wordpress plugin adds some properties:


```php
$record->isEmpty
```

(since 2.0.0)
TRUE if the record does not contain any data. This can happen because not every IP has related information.

<br>

```php
$record->extra->cached
```
(since 2.4.0)
0 if not cached.
A unix timestamp if cached (contains the time when it was cached)

<br>

```php
$record->extra->source
```
(since 2.4.0)
Source that this record is coming from.

<br>

```php
$record->extra->error
```
(since 2.4.0)
Error message of this lookup, if any. In error case $record->isEmpty is TRUE.

<br>

```php
$record->extra->flag
```
(since 2.12.1)
Unicode flag of the country of the visitor


<br>

```php
$record->extra->tel
```
(since 2.12.1)
International telefone code (such as +1) of the visitor


<br>

```php
$record->extra->countryIsoCode3
```
(since 3.1.1)
ISO-3166 alpha3 version of `$record->country` 

<br>

```php
$record->extra->currencyCode
```
(since 3.1.2)
ISO 4217 currency code of the country


<br>

```php
$record->extra->original
```
(since 2.12.0)

Original data array that the Web-API (such as hostinfo or ipstack) had returned.



# List of all property names

The record can be accessed in PHP, in the Wordpress shortcodes and in AJAX. 

**Note:** Not all properties might be filled (depending on the data source you use and the information available for the requested IP). You can use the backend lookup function to see a more realistic set of properties, and their PHP/Shortcode/JS syntax.

(Before 4.0.0, JS syntax was `country.is_in_european_union` instead of `country.isInEuropeanUnion`. Now, both are valid.)

```
continent
continent.code
continent.name
continent.names.{lang, e.g. en}
continent.geonameId

country
country.confidence
country.isInEuropeanUnion
country.isoCode
country.name
country.names.{lang, e.g. en}
country.geonameId

registeredCountry.geonameId
registeredCountry.isInEuropeanUnion
registeredCountry.isoCode
registeredCountry.name
registeredCountry.names.{lang, e.g. en}

representedCountry.geonameId
representedCountry.isInEuropeanUnion
representedCountry.isoCode
representedCountry.name
representedCountry.names.{lang, e.g. en}
representedCountry.type

subdivisions.0.confidence
subdivisions.0.geonameId
subdivisions.0.isoCode
subdivisions.0.name
subdivisions.0.names.{lang, e.g. en}

subdivisions.1.confidence
subdivisions.1.geonameId
subdivisions.1.isoCode
subdivisions.1.name
subdivisions.1.names.{lang, e.g. en}

mostSpecificSubdivision.confidence
mostSpecificSubdivision.geonameId
mostSpecificSubdivision.isoCode
mostSpecificSubdivision.name
mostSpecificSubdivision.names.{lang, e.g. en}

city
city.name
city.names.{lang, e.g. en}
city.geonameId

postal.code
postal.confidence

location.accuracyRadius
location.averageIncome
location.latitude
location.longitude
location.metroCode
location.populationDensity
location.timeZone


traits.autonomousSystemNumber
traits.autonomousSystemOrganization
traits.domain
traits.isAnonymous
traits.isAnonymousProxy
traits.isAnonymousVpn
traits.isHostingProvider
traits.isPublicProxy
traits.isSatelliteProvider
traits.isTorExitNode
traits.isp
traits.ipAddress
traits.organization
traits.userType

maxmind.queriesRemaining

## The properties below are not from Maxmind, but from the plugin itself

isEmpty

extra.flag
extra.tel
extra.cached
extra.source
extra.error
extra.countryIsoCode3
extra.currencyCode

# This is available for web-api datasources such as ipstack/hostinfo
extra.original
```

See [Maxmind properties](http://dev.maxmind.com/geoip/geoip2/web-services/) to access the documentation what their values mean.

Iso country Codes are [2-letter ISO 3166-1 codes](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements).
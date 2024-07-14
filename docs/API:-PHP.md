These are the PHP functions that you can use in your theme or plugin:

```php
/**
 * Get Geo-Information for a specific IP
 * @param string 			$ip 		IP-Adress (IPv4 or IPv6). 'me' is the current IP of the server.
 * @param array(string)		$locales 	List of locale codes to use in name property
 * 										from most preferred to least preferred. (Default: Site language, en)
 * @param array				Property names with options.
 * 		@param boolean 		$skipCache		TRUE: Do not use cache for this request. (Default: FALSE)
 * 		@param string       $source         Change the source for this request only. (Valid values: 'auto', 'manual', 'precision', 'header', 'hostinfo')
 * 		@param float 		$timeout		Total transaction timeout in seconds (Precision+HostIP.info API only) 
 * 		@param int			$connectTimeout Initial connection timeout in seconds (Precision API only)
 * @return YellowTree\GeoipDetect\DataSources\City	GeoInformation. (Actually, this is a subclass of \GeoIp2\Model\City)
 * 
 * @see https://github.com/maxmind/GeoIP2-php				API Usage
 * @see http://dev.maxmind.com/geoip/geoip2/web-services/	API Documentation
 *
 * @since 2.0.0
 * @since 2.4.0 New parameter $skipCache
 * @since 2.5.0 Parameter $skipCache has been renamed to $options with 'skipCache' property
 * @since 2.7.0 Parameter $options['source'] has been introduced
 */
function geoip_detect2_get_info_from_ip($ip, $locales = null, $options = array()) { ... }
```

```YellowTree\GeoipDetect\DataSources\City``` is a subclass of ```GeoIp2\Model\City``` and yes, for simplicity this object type is always used even if one of the country data sources is used. See [Record Properties](./Record Properties.md) for all possible property names (all from Maxmind plus a few from this plugin).

```php
/**
 * Get Geo-Information for the current IP
 *
 * @param array(string)		$locales	List of locale codes to use in name property
 * 										from most preferred to least preferred. (Default: Site language, en)
 * @param array				Property names with options.
 * 		@param boolean 		$skipCache		TRUE: Do not use cache for this request. (Default: FALSE)
 * 		@param string       $source         Change the source for this request only. (Valid values: 'auto', 'manual', 'precision', 'header', 'hostinfo')
 * 		@param float 		$timeout		Total transaction timeout in seconds (Precision+HostIP.info API only) 
 * 		@param int			$connectTimeout Initial connection timeout in seconds (Precision API only)
 * @return YellowTree\GeoipDetect\DataSources\City	GeoInformation.
 *
 * @since 2.0.0
 * @since 2.4.0 New parameter $skipCache
 * @since 2.5.0 Parameter $skipCache has been renamed to $options with 'skipCache' property
 * @since 2.7.0 Parameter $options['source'] has been introduced
 */
function geoip_detect2_get_info_from_current_ip($locales = null, $options = array()) { ... }
```

```php
/**
 * Get the Reader class of the currently chosen source.
 * (Use this if you want to use other methods than "city" or otherwise customize behavior.)
 * 
 * @param array(string)				List of locale codes to use in name property
 * 									from most preferred to least preferred. (Default: Site language, en)
 * @param array				Property names with options.
 * 		@param string       $source         Change the source for this request only. (Valid values: 'auto', 'manual', 'precision', 'header', 'hostinfo')
 * 		@param float 		$timeout		Total transaction timeout in seconds (Precision+HostIP.info API only) 
 * 		@param int			$connectTimeout Initial connection timeout in seconds (Precision API only)
 * 
 * @since 2.0.0
 * @since 2.5.0 new parameter $options
 * @since 2.7.0 Parameter $options['source'] has been introduced
 */
function geoip_detect2_get_reader($locales = null, $options = array()) { ... }
```

```php
/**
 * Return a human-readable label of the currently chosen source.
 * @param string|object Id of the source or the returned record
 * @return string The label.
 * 
 * @since 2.3.1
 * @since 2.4.0 new parameter $source
 */
function geoip_detect2_get_current_source_description($source = null) { ... }
```


```php
/**
 * Sometimes we can only see an local IP adress (local development environment.)
 * In this case we need to ask an internet server which IP adress our internet connection has.
 * 
 * @param boolean $unfiltered If true, do not check the options for an external adress. (Default: false)
 * @return string The detected IPv4 Adress. If none is found, '0.0.0.0' is returned instead.
 * 
 * @since 2.0.0
 * @since 2.4.3 Reading option 'external_ip' first.
 * @since 2.5.2 New param $unfiltered that can bypass the option.
 */
function geoip_detect2_get_external_ip_adress($unfiltered = false) { ... }
```

This function is used automatically if the API sees an local IP adress (IPv4 or IPv6).

```php
/**
 * Get client IP (even if it is behind a reverse proxy)
 * For security reasons, the reverse proxy usage has to be enabled on the settings page.
 * 
 * @return string Client Ip (IPv4 or IPv6)
 * 
 * @since 2.0.0
 */
function geoip_detect2_get_client_ip() { ... }
```

## Shortcodes
(See [API: Shortcodes](./API: Shortcodes.md))

## JS API
(See [API: AJAX](./API: AJAX.md))
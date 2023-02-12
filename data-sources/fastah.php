<?php
/*
Copyright 2013-2023 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (wp-geoip-detect| |posteo.de)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace YellowTree\GeoipDetect\DataSources\Fastah;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

class Reader implements \YellowTree\GeoipDetect\DataSources\ReaderInterface {

	const URL = 'https://ep.api.getfastah.com/whereis/v1/json/';
    protected $options = [];
    protected $params = [];

	function __construct($params, $locales, $options) {
        $this->params= $params;
        $this->params['language'] = reset($locales);
        
        // TODO: Fastah API responses only support 'en' at this time - this parameter is currently not accepted by the REST API
        //if (empty($this->params['language'])) {
            $this->params['language'] = 'en';
        //}

		$default_options = [
            'timeout' => 2,
        ];
		$this->options = $options + $default_options;
	}

    protected function locales($locale, $value) {
        $locales = ['en' => $value];
        if ($locale !== 'en') {
            $locales[$locale] = $value;
        }
        return $locales;
    }
/*
    protected function api_call($ip) {
        $requestArgs = array(
            'method' => 'GET',
            'protocol_version' => ($this->params['http2'] === 1) ? 2.0 : 1.1,
            'timeout' => $this->options['timeout'],
            'header' => array(
                'Fastah-Key' => $this->params['key']
            ),
        );
        $context = stream_context_create(array('http' => $requestArgs));
        $body = @file_get_contents($this->build_url($ip, ['language' => 'en']), false, $context);
        $data = json_decode( $body, true );
        return $data;
    }
*/

    protected function api_call($ip) {
        $requestArgs = [
            'method' => 'GET',
            'httpversion' => ($this->params['http2'] === 1) ? 2.0 : 1.1,
            'timeout' => $this->options['timeout'],
            'headers' => [
                'Fastah-Key' => $this->params['key'],
		        'User-Agent' => GEOIP_DETECT_USER_AGENT,
            ],
        ];
        $response = wp_safe_remote_get($this->build_url($ip, $this->params), $requestArgs);
        $respCode = wp_remote_retrieve_response_code( $response );
        if (is_wp_error($response)) {
            throw new \RuntimeException($response->get_error_message());
        }
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        if ($respCode !== 200) {
            if (isset($data['error']['message'])) {
                throw new \RuntimeException($data['error']['message']);
            } elseif (isset($data['message'])) {
                throw new \RuntimeException($data['message']);
            } else {
                throw new \RuntimeException('Invalid HTTP Status Code ' . $respCode);
            }
        }
        return $data;
    }

	public function city($ip) {
        try {
            $data = $this->api_call($ip);
		} catch (\Exception $e) {
            return _geoip_detect2_get_new_empty_record($ip, $e->getMessage());
		}
        if (!$data) {
            return _geoip_detect2_get_new_empty_record($ip, 'No data found.');
        }
            
        $r = [];
        $r['extra']['original'] = $data;

        $locale = 'en'; // The REST API only support English at this time

        if (!empty($data['locationData'])) {
            // Continent
            if (!empty($data['locationData']['continentCode'])) {
                $r['continent']['code'] = strtoupper($data['locationData']['continentCode']);
            }
            // Country
            if (!empty($data['locationData']['countryName'])) {
                $r['country']['names'] = $this->locales($locale, $data['locationData']['countryName']);
            }
            if (!empty($data['locationData']['countryCode'])) {
                $r['country']['iso_code'] = strtoupper($data['locationData']['countryCode']);
            }
            // City
            if (!empty($data['locationData']['cityName'])) {
                $r['city']['names'] = $this->locales($locale, $data['locationData']['cityName']);
            }
            if (!empty($data['locationData']['cityGeonamesId'])) {
                $r['city']['geoname_id'] = $data['locationData']['cityGeonamesId'];
            }
            // Lat, Lng
            if (isset($data['locationData']['lat'])) {
                $r['location']['latitude'] = $data['locationData']['lat'];
            }
            if (isset($data['locationData']['lng'])) {
                $r['location']['longitude'] = $data['locationData']['lng'];
            }
            // TZ
            if (isset($data['locationData']['tz'])) {
                $r['location']['time_zone'] = $data['locationData']['tz'];
            }
        }

        // EU flag
        if(isset($data['isEuropeanUnion'])) {
            $r['country']['isInEuropeanUnion'] = $data['isEuropeanUnion'];
        }
        
        if (isset($data['ip'])) {
            $r['traits']['ip_address'] = $data['ip'];
        } else {
            $r['traits']['ip_address'] = $ip;
        }

		$record = new \GeoIp2\Model\City($r, ['en']);

		return $record;
	}

	public function country($ip) {
		return $this->city($ip);
	}

	public function close() {

    }
    
    private function build_url($ip, $params = []) {
        $url = self::URL . $ip;
        return $url . '?' . \http_build_query($params);
    }

}


class FastahSource extends AbstractDataSource {
    protected $params = [];
    // PHP-Curl with functional TLS 1.2 (secure ciphers) and HTTP 1.1 are minimum requirements
    protected $bestAvailHTTP = CURL_HTTP_VERSION_1_1;

    public function __construct() {
        $this->params['key'] = get_option('geoip-detect-fastah_key', '');

        // Set default by probing PHP/Curl capabilities - minimum is HTTP 1.1 over TLSv1.2
        // HTTP/2 ought to be available in PHP-Curl > v7.47.0 @see https://curl.se/docs/http2.html

        if (defined('CURL_HTTP_VERSION_2_0') && curl_version()["features"] & CURL_HTTP_VERSION_2_0  !== 0) {
            $this->bestAvailHTTP = CURL_HTTP_VERSION_2_0;
            if (defined('CURL_HTTP_VERSION_2TLS') && curl_version()["features"] & CURL_HTTP_VERSION_2TLS  !== 0) {
                $this->bestAvailHTTP = CURL_HTTP_VERSION_2TLS;
            }
        }

        if ($this->bestAvailHTTP > CURL_HTTP_VERSION_1_1) {
            $this->params['http2'] = get_option('geoip-detect-fastah_http2', 1);
        } else {
            $this->params['http2'] = 0;
        }
    }

	public function getId() { return 'fastah'; }
	public function getLabel() { return __('Fastah Web API <sup><em>beta</em></sup>', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('Commercial, with enterprise-friendly support and billing. <a href="https://aws.amazon.com/marketplace/pp/prodview-k5gjowexrefl2" target="_blank">Sign-up on AWS Marketplace</a>. <a href="https://docs.getfastah.com/docs/quick-start" target="_blank">API Documentation</a>', 'geoip-detect'); }
	public function getStatusInformationHTML() { 
        $html = '';

        $html .= \sprintf(__('HTTP2: %s', 'geoip-detect'), $this->params['http2'] ? __('Enabled', 'geoip-detect') : __('Disabled', 'geoip-detect')) . '<br />';

        if ($this->bestAvailHTTP === CURL_HTTP_VERSION_1_1) {
            $html .= '<i>' . __('Warning: HTTP2 is not supported by the curl version used by your PHP. This will make lookups slower.', 'geoip-detect') . '</i><br />';
        }

        if (!$this->isWorking()) {
            if (!is_callable('curl_init')) {
                $html .= '<div class="geoip_detect_error">' . __('Error: Fastah requires PHP-Curl for secure HTTPS requests.', 'geoip-detect') . '</div>';
            } else {
                $html .= '<div class="geoip_detect_error">' . __('Error: Fastah only works with an API key.', 'geoip-detect') . '</div>';
            }
        }

        return $html; 
    }

	public function getParameterHTML() { 
        $label_key = __('API Access Key :', 'geoip-detect');
        $label_http2 = __('Use HTTP/2 :', 'geoip-detect');

        $key = esc_attr($this->params['key']);

        $html = <<<HTML
$label_key <input type="text" autocomplete="off" size="20" name="options_fastah[key]" value="$key" /> <br />
<a href="https://aws.amazon.com/marketplace/pp/prodview-k5gjowexrefl2" target="_blank">Sign-up for a 30-day trial key</a>, <a href="https://console.api.getfastah.com" target="_blank">API usage dashboard</a><br><br>
$label_http2 <select name="options_fastah[http2]">
HTML;
        $html .= '<option value="0" ' . (!$this->params['http2'] ? ' selected="selected"' : '') . '">' . __('HTTP/2 is OFF (slower, but more compatible with older PHP versions)', 'geoip-detect') . '</option>';
        $html .= '<option value="1" ' . ($this->params['http2'] ? ' selected="selected"' : '') . '">' . __('HTTP/2 is ON (faster performance)', 'geoip-detect') . '</option>';
        $html .= '</select>';

        return $html; 
    }

    public function saveParameters($post) {
        $message = '';

        if (isset($post['options_fastah']['key'])) {
            $key = sanitize_key($post['options_fastah']['key']);
            update_option('geoip-detect-fastah_key', $key);
            $this->params['key']= $key;
        }

        if (isset($post['options_fastah']['http2'])) {	
            $http2 = (int) $post['options_fastah']['http2'];
            update_option('geoip-detect-fastah_http2', $http2);
            // Show warning if HTTP/2 requested but actually not available via PHP-Curl
            if ($http2 == 1 && $this->bestAvailHTTP == CURL_HTTP_VERSION_1_1) {
                $message .= __('Warning: Turning off HTTP/2 because it is not supported by your server PHP / libcurl.<br>', 'geoip-detect');
                $http2 = 0;
            }
            $this->params['http2'] = $http2;
		}
        
        if (geoip_detect2_is_source_active('fastah') && !$this->isWorking())
            $message .= __('Fastah only works with an API key.', 'geoip-detect');

        return $message;
    }

	public function getReader($locales = ['en'], $options = []) { 
        return new Reader($this->params, $locales, $options);
    }

	public function isWorking() {
        return (!empty($this->params['key']) && is_callable('curl_init'));
    }

}
geoip_detect2_register_source(new FastahSource());

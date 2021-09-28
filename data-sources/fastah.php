<?php
/*
Copyright 2013-2021 Yellow Tree, Siegen, Germany
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

	const URL = 'ep.api.getfastah.com/whereis/v1/json/';
    protected $options = array();
    protected $params = array();

	function __construct($params, $locales, $options) {
        $this->params= $params;
        $this->params['language'] = reset($locales);
        // TODO: Fastah API responses only support 'en' at this time
        //if (empty($this->params['language'])) {
            $this->params['language'] = 'en';
        //}

		$default_options = array(
            'timeout' => 1,
		);
		$this->options = $options + $default_options;
	}

    protected function locales($locale, $value) {
        $locales = array('en' => $value);
        if ($locale != 'en') {
            $locales[$locale] = $value;
        }
        return $locales;
    }

	public function city($ip) {
		$data = $this->api_call($ip);
		if (!$data)
            return _geoip_detect2_get_new_empty_record();
            
        $r = array();

        $r['extra']['original'] = $data;

        // TODO: Check HTTP response code here. 
        if (isset($data['success']) && $data['success'] === false) {
            throw new \RuntimeException($data['error']);
            // Example error:
            /* @see https://docs.getfastah.com/reference/getlocationbyip
            {
                "error": {
                    "message": "You did not specify a valid IP address."
                }
            }
            */
        }

        $locale = $this->params['language'];
		if (!empty($data['continent_name']))
			$r['continent']['names'] = $this->locales($locale, $data['continent_name']);
		if (!empty($data['continent_code']))
			$r['continent']['code'] = strtoupper($data['continent_code']);
		if (!empty($data['country_name']))
			$r['country']['names'] = $this->locales($locale, $data['country_name']);
		if (!empty($data['country_code']))
            $r['country']['iso_code'] = strtoupper($data['country_code']);
            
            if (!empty($data['region_code'])) {
            $r['subdivisions'][0] = array(
                'iso_code' => $data['region_code'],
                'names' => $this->locales($locale, $data['region_name']),
            );
        }
        
		if (!empty($data['city']))
        $r['city']['names'] = $this->locales($locale, $data['city']);
		if (!empty($data['latitude']))
        $r['location']['latitude'] = $data['latitude'];
		if (!empty($data['longitude']))
        $r['location']['longitude'] = $data['longitude'];
        
        if (!empty($data['location']['is_eu'])) {
            $r['country']['is_in_european_union'] = $data['location']['is_eu'];
        }
		if (isset($data['timezone']['id']))
        $r['location']['time_zone'] = $data['timezone']['id'];
        
        if (isset($data['connection']['asn']))
        $r['traits']['autonomous_system_number'] = $data['connection']['asn'];
        if (isset($data['connection']['isp']))
        $r['traits']['isp'] = $data['connection']['isp'];
        if (isset($data['security']['is_proxy']))
        $r['traits']['is_anonymous_vpn'] = $data['security']['is_proxy'] && $data['security']['proxy_type'] == 'vpn';
        if (isset($data['security']['is_tor']))
        $r['traits']['is_tor_exit_node'] = $data['security']['is_tor'];
        
        if (!empty($data['location']['country_flag_emoji']))
            $r['extra']['flag'] = strtoupper($data['location']['country_flag_emoji']);

        if (!empty($data['currency']['code'])) {
            $r['extra']['currency_code'] = $data['currency']['code'];
        }
        

		$r['traits']['ip_address'] = $ip;

		$record = new \GeoIp2\Model\City($r, array('en'));

		return $record;
	}

	public function country($ip) {
		return $this->city($ip); // too much info shouldn't hurt ...
	}

	public function close() {

    }
    
    private function build_url($ip) {
        $url = 'https';
        $url .= '://' . self::URL . $ip;

        $params = [
            'fastah-key' => $this->params['key'],
        ];
        return $url . '?' . \http_build_query($params);
    }

	private function api_call($ip) {

		try {
			// Setting timeout limit to speed up sites
			$context = stream_context_create(
					array(
							'http' => array(
                                    'protocol_version' => $this->params['http2'] ? 2.0 : 1.2,
									'timeout' => $this->options['timeout'],
							),
					)
			);
			// Using @file... to supress errors
            // Example output: {"country_name":"UNITED STATES","country_code":"US","city":"Aurora, TX","ip":"12.215.42.19"}

			$body = @file_get_contents($this->build_url($ip), false, $context);
			$data = json_decode($body, true);

			return $data;
		} catch (\Exception $e) {
            // If the API isn't available, we have to do this
            throw $e;
			return null;
		}
	}
}


class FastahSource extends AbstractDataSource {
    protected $params = array();
    // PHP-Curl with functional TLS 1.2 (secure ciphers) and HTTP 1.1 are minimum requirements
    protected $bestAvailHTTP = CURL_HTTP_VERSION_1_1;

    public function __construct() {
        $this->params['key'] = get_option('geoip-detect-fastah_key', '');

        // Set default by probing PHP/Curl capabilities - minimum is HTTP 1.1 over TLSv1.2
        // HTTP/2 ought to be available in PHP-Curl > v7.47.0 @see https://curl.se/docs/http2.html
        if (curl_version()["features"] & CURL_HTTP_VERSION_2_0  !== 0) {
            $this->bestAvailHTTP = CURL_HTTP_VERSION_2_0;
            if (curl_version()["features"] & CURL_HTTP_VERSION_2TLS  !== 0) {
                $this->bestAvailHTTP = CURL_HTTP_VERSION_2TLS;
            }
        }
        if ($this->bestAvailHTTP < CURL_HTTP_VERSION_2_0) {
            $this->params['http2'] = get_option('geoip-detect-fastah_http2', 0);
        } else {
            $this->params['http2'] = get_option('geoip-detect-fastah_http2', 1);
        }
    }

	public function getId() { return 'fastah'; }
	public function getLabel() { return __('Fastah Web API <sup><em>beta</em></sup>', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('Commercial, with enterprise-friendly support and billing. <a href="https://aws.amazon.com/marketplace/pp/prodview-k5gjowexrefl2" target="_blank">Sign-up on AWS Marketplace</a>. <a href="https://docs.getfastah.com/docs/quick-start" target="_blank">API Documentation</a>', 'geoip-detect'); }
	public function getStatusInformationHTML() { 
        $html = '';

        $html .= \sprintf(__('HTTP2: %s', 'geoip-detect'), $this->params['http2'] ? __('Enabled', 'geoip-detect') : __('Disabled', 'geoip-detect')) . '<br />';

        if (!$this->isWorking()) {
            if (!is_callable('curl_init')) {
                $html .= '<div class="geoip_detect_error">' . __('Fastah requires PHP-Curl for secure HTTPS requests.', 'geoip-detect') . '</div>';
            } else {
                $html .= '<div class="geoip_detect_error">' . __('Fastah only works with an API key.', 'geoip-detect') . '</div>';
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
            if ($http2 == 1 and $this->bestAvailHTTP < CURL_HTTP_VERSION_2_0) {
                $message .= __('Fastah requires PHP-Curl with HTTP/2 support.<br>', 'geoip-detect');
                $http2 = 0; // Turn it OFF forcefully
            }
            $this->params['http2'] = $http2;
		}
        
        if (geoip_detect2_is_source_active('fastah') && !$this->isWorking())
            $message .= __('Fastah only works with an API key.', 'geoip-detect');

        return $message;
    }

	public function getReader($locales = array('en'), $options = array()) { 
        return new Reader($this->params, $locales, $options);
    }

	public function isWorking() {
        return (!empty($this->params['key']) and is_callable('curl_init'));
    }

}
geoip_detect2_register_source(new FastahSource());

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

namespace YellowTree\GeoipDetect\DataSources\Ipstack;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

class Reader implements \YellowTree\GeoipDetect\DataSources\ReaderInterface {

	const URL = 'api.ipstack.com/';
    protected $options = [];
    protected $params = [];
    
	function __construct($params, $locales, $options) {
        $this->params= $params;
        $this->params['language'] = reset($locales);
        if (empty($this->params['language'])) {
            $this->params['language'] = 'en';
        }

		$default_options = array(
            'timeout' => 1,
		);
		$this->options = $options + $default_options;
	}

    protected function locales($locale, $value) {
        $locales = [ 'en' => $value ];
        if ($locale != 'en') {
            $locales[$locale] = $value;
        }
        return $locales;
    }

	public function city($ip) {
		$data = $this->api_call($ip);

		if (!$data)
            return _geoip_detect2_get_new_empty_record();
            
        $r = [];

        $r['extra']['original'] = $data;

        if (isset($data['success']) && $data['success'] === false) {
            throw new \RuntimeException($data['error']['info']);
            // Example error:
            /* @see https://ipstack.com/documentation#errors
            {
                "success": false,
                "error": {
                    "code": 104,
                    "type": "monthly_limit_reached",
                    "info": "Your monthly API request volume has been reached. Please upgrade your plan."    
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

		$record = new \GeoIp2\Model\City($r, [ 'en' ]);

		return $record;
	}

	public function country($ip) {
		return $this->city($ip); // too much info shouldn't hurt ...
	}

	public function close() {

    }
    
    private function build_url($ip) {
        $url = $this->params['ssl'] ? 'https' : 'http';
        $url .= '://' . self::URL . $ip;

        $params = [
            'access_key' => $this->params['key'],
            'language' => $this->params['language'],
        ];
        return $url . '?' . \http_build_query($params);
    }

	private function api_call($ip) {
        
		try {
			// Setting timeout limit to speed up sites
			$context = stream_context_create(
					array(
							'http' => array(
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


class IpstackSource extends AbstractDataSource {
    protected $params = [];

    public function __construct() {
        $this->params['key'] = get_option('geoip-detect-ipstack_key', '');
        $this->params['ssl'] = get_option('geoip-detect-ipstack_ssl', 0);
    }

	public function getId() { return 'ipstack'; }
	public function getLabel() { return __('Ipstack Web-API', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('<a href="https://ipstack.com/">Ipstack</a>', 'geoip-detect'); }
	public function getStatusInformationHTML() { 
        $html = '';

        $html .= \sprintf(__('SSL: %s', 'geoip-detect'), $this->params['ssl'] ? __('Enabled', 'geoip-detect') : __('Disabled', 'geoip-detect')) . '<br />';


        if (!$this->isWorking())
            $html .= '<div class="geoip_detect_error">' . __('Ipstack only works with an API key.', 'geoip-detect') . '</div>';

        return $html; 
    }

	public function getParameterHTML() { 
        $label_key = __('API Access Key:', 'geoip-detect');
        $label_ssl = __('Access the API via SSL:', 'geoip-detect');

        $key = esc_attr($this->params['key']);

        $html = <<<HTML
$label_key <input type="text" autocomplete="off" size="20" name="options_ipstack[key]" value="$key" /><br />
$label_ssl <select name="options_ipstack[ssl]">
HTML;
        $html .= '<option value="0" ' . (!$this->params['ssl'] ? ' selected="selected"' : '') . '">' . __('HTTP (without encryption, not GDPR-compatible)', 'geoip-detect') . '</option>';
        $html .= '<option value="1" ' . ($this->params['ssl'] ? ' selected="selected"' : '') . '">' . __('HTTPS (with encryption - paid plans only)', 'geoip-detect') . '</option>';
        $html .= '</select>';

        return $html; 
    }

    public function saveParameters($post) {
        $message = '';

        if (isset($post['options_ipstack']['key'])) {
            $key = sanitize_key($post['options_ipstack']['key']);
            update_option('geoip-detect-ipstack_key', $key);
            $this->params['key']= $key;
        }

        if (isset($post['options_ipstack']['ssl'])) {	
            $ssl = (int) $post['options_ipstack']['ssl'];
            update_option('geoip-detect-ipstack_ssl', $ssl);
            $this->params['ssl'] = $ssl;
		}
        
        if (geoip_detect2_is_source_active('ipstack') && !$this->isWorking())
            $message .= __('Ipstack only works with an API key.', 'geoip-detect');
            
        return $message;
    }

	public function getReader($locales = [ 'en' ], $options = []) { 
        return new Reader($this->params, $locales, $options);
    }

	public function isWorking() { 
        return !empty($this->params['key']);
    }

}
geoip_detect2_register_source(new IpstackSource());

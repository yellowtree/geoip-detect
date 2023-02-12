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

namespace YellowTree\GeoipDetect\DataSources\Header;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

class HeaderReader extends \YellowTree\GeoipDetect\DataSources\AbstractReader {
	
	protected $providers = array(
		'aws' => 'Amazon AWS CloudFront',
		'cloudflare' => 'Cloudflare',
	);
	
	public function country($ip) {
		$r = [];

		$isoCode = '';

		$provider = [
			'aws' => 'HTTP_CLOUDFRONT_VIEWER_COUNTRY',
			'cloudflare' => 'HTTP_CF_IPCOUNTRY',
		];

		$httpKey = '';
		if (isset($provider[ $this->options['provider'] ])) {
			$httpKey = $provider[ $this->options['provider'] ];
		};

		/**
		 * Customize which key in the $_SERVER array (Request headers) should be used for Geo-Detection
		 * The country should be given in 2-letter ISO 3166-1 codes, uppercase (DE) or lowercase (de).
		 * 
		 * @param string $httpKey
		 * @param string $selectedProvider
		 * @api
		 * @see https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements
		 * 
		 */
		$httpKey = apply_filters('geoip_detect2_source_header_http_key', $httpKey, $this->options['provider'] );

		if (isset($_SERVER[$httpKey])) {
			$isoCode = $_SERVER[$httpKey];
			if (strtoupper($isoCode) == 'XX' /* Not a country / unknown */) {
				$isoCode = '';	
			}
		}

		if (!$isoCode) {
			return _geoip_detect2_get_new_empty_record($ip);
		}
		if (mb_strlen($isoCode) !== 2) {
			$errorMessage = 'Invalid country code' . (GEOIP_DETECT_DEBUG ? (': "' . $isoCode . '"') : '.');
			return _geoip_detect2_get_new_empty_record($ip, $errorMessage);
		}
		
		$r['country']['iso_code'] = strtoupper($isoCode);
		
		$r['traits']['ip_address'] = $ip;
		
		$record = new \GeoIp2\Model\City($r, [ 'en' ]);
		
		return $record;
	}
}

class HeaderDataSource extends AbstractDataSource {

	public function getId() { return 'header'; }
	public function getLabel() { return __('Special Hosting Providers (Cloudflare, Amazon AWS CloudFront, or other)', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('These servers already do a geodetection of the visitor\'s country.', 'geoip-detect'); }
	
	public function getStatusInformationHTML() {
		$provider = get_option('geoip-detect-header-provider');
		
        $html = '';
        $link = '';
		if ($provider == 'cloudflare') {
			$link = 'https://support.cloudflare.com/hc/en-us/articles/200168236-What-does-CloudFlare-IP-Geolocation-do-';
		} elseif ($provider == 'aws') {
			$link = 'https://aws.amazon.com/blogs/aws/enhanced-cloudfront-customization/';
		} elseif ($provider == 'other') {
			$html = __('If your reverse proxy sends the detected country as a 2-letter ISO 3166-1 code, you can configure the name of the request header via the wordpress filter "geoip_detect2_source_header_http_key".', 'geoip-detect');
		}
        if ($link) {
			$html = sprintf(__('This needs to be enabled in the admin panel: see <a href="%s">Help</a>.', 'geoip-detect'), $link);
			$html .= '<br>' . sprintf(__('Probably you will want to enable "%s".', 'geoip-detect'), __('Add known proxies of this provider:', 'geoip-detect'));
		}
		return $html;
	}

	public function getParameterHTML() { 
		$provider = get_option('geoip-detect-header-provider');
		$checked_cloudflare = $provider === 'cloudflare' ? 'checked' : '';
		$checked_aws =        $provider === 'aws'        ? 'checked' : '';
		$checked_other =      $provider === 'other'      ? 'checked' : '';

		$label = __('Which Hosting Provider:', 'geoip-detect');
		$label_other = __('Custom', 'geoip-detect');
		$html = <<<HTML
		<p>$label<br> 
			<label><input type="radio" name="options_header[provider]" value="cloudflare" $checked_cloudflare /> Cloudflare</label>
			<label><input type="radio" name="options_header[provider]" value="aws" $checked_aws /> Amazon AWS CloudFront</label>
			<label><input type="radio" name="options_header[provider]" value="other" $checked_other /> $label_other</label>
	    </p>
		<br />	
HTML;
		
		return $html;
	}
	
	public function saveParameters($post) {
		$message = '';
		
		$value = isset($post['options_header']['provider']) ? sanitize_key($post['options_header']['provider']) : '';
		if (!empty($value)) {
			update_option('geoip-detect-header-provider', $value);
		}
		
		return $message;
	}
	
	public function getShortLabel() {
		$provider = get_option('geoip-detect-header-provider');
		$labels = array(
			'' => __('None', 'geoip-detect'),
			'aws' => 'Amazon AWS CloudFront',
			'cloudflare' => 'Cloudflare',
			'other' => __('Custom', 'geoip-detect'),
		);
		if (!isset($labels[$provider]))
			$provider = '';
		
		$html = __('Hosting Provider:', 'geoip-detect') . ' ' . $labels[$provider];	
		
		return $html;
	}

	public function getReader($locales = [ 'en' ], $options = []) {
		$reader = null;
		
		$provider = get_option('geoip-detect-header-provider');
		if ($provider) {
			try {
				$reader = new HeaderReader( array(
					'provider' => $provider,
				) );
			} catch ( \Exception $e ) {
				if (GEOIP_DETECT_DEBUG) {
					trigger_error(sprintf(__('Error while creating reader for "%s": %s', 'geoip-detect'), $filename, $e->getMessage ()), E_USER_NOTICE);
				}
			}
		}
		
		return $reader;
	}

	public function isWorking() { 
		$working = (bool) get_option('geoip-detect-header-provider');

		return $working;
	}
	
}

geoip_detect2_register_source(new HeaderDataSource());

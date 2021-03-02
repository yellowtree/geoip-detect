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

namespace YellowTree\GeoipDetect\DataSources\Header;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

class HeaderReader extends \YellowTree\GeoipDetect\DataSources\AbstractReader {
	
	protected $providers = array(
		'aws' => 'Amazon AWS CloudFront',
		'cloudflare' => 'Cloudflare',
	);
	
	public function country($ip) {
		
		$r = array();

		$isoCode = '';
		switch ($this->options['provider']) {
			case 'aws':
				if (isset($_SERVER['CloudFront-Viewer-Country'])) {
					$isoCode = $_SERVER['CloudFront-Viewer-Country'];
				}
				break;
				
			case 'cloudflare';
				if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
					$isoCode = $_SERVER["HTTP_CF_IPCOUNTRY"];
					if ($isoCode == 'xx' /* not a country / unknown */)
						$isoCode = '';	
				}
				break;
		}
		$country = '';
		if (!$isoCode) {
			return _geoip_detect2_get_new_empty_record();
		}
		
		$r['country']['iso_code'] = strtoupper($isoCode);
		
		$r['traits']['ip_address'] = $ip;
		
		$record = new \GeoIp2\Model\City($r, array('en'));
		
		return $record;
	}
}

class HeaderDataSource extends AbstractDataSource {

	public function getId() { return 'header'; }
	public function getLabel() { return __('Special Hosting Providers (Cloudflare, Amazon AWS CloudFront)', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('These servers already do geodetection, but only of the visitor\'s country.', 'geoip-detect'); }
	
	public function getStatusInformationHTML() {
		$provider = get_option('geoip-detect-header-provider');
		
        $html = '';
        $link = '';
		if ($provider == 'cloudflare') {
			$link = 'https://support.cloudflare.com/hc/en-us/articles/200168236-What-does-CloudFlare-IP-Geolocation-do-';
		} elseif ($provider == 'aws') {
			$link = 'https://aws.amazon.com/blogs/aws/enhanced-cloudfront-customization/';
		}
        if ($link)
		  $html = sprintf(__('This needs to be enabled in the admin panel: see <a href="%s">Help</a>.', 'geoip-detect'), $link);
		return $html;
	}

	public function getParameterHTML() { 
		$provider = get_option('geoip-detect-header-provider');
		$checked_cloudflare = $provider == 'cloudflare' ? 'checked' : '';
		$checked_aws =        $provider == 'aws'        ? 'checked' : '';

		$label = __('Which Hosting Provider:', 'geoip-detect');
		$html = <<<HTML
		<p>$label<br> 
			<label><input type="radio" name="options_header[provider]" value="cloudflare" $checked_cloudflare /> Cloudflare</label>
			<label><input type="radio" name="options_header[provider]" value="aws" $checked_aws /> Amazon AWS CloudFront</label>
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
		);
		if (!isset($labels[$provider]))
			$provider = '';
		
		$html = __('Hosting Provider:', 'geoip-detect') . ' ' . $labels[$provider];	
		
		return $html;
	}

	public function getReader($locales = array('en'), $options = array()) {
		$reader = null;
		
		$provider = get_option('geoip-detect-header-provider');
		if ($provider) {
			try {
				$reader = new HeaderReader( array(
					'provider' => $provider,
				) );
			} catch ( \Exception $e ) {
				if (WP_DEBUG) {
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

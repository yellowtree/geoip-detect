<?php

namespace YellowTree\GeoipDetect\DataSources\Header;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

class HeaderReader extends \YellowTree\GeoipDetect\DataSources\AbstractReader {
	
	protected $providers = array(
		'aws' => 'Amazon AWS CloudFront',
		'cloudflare' => 'Cloudflare',
	);
	
	
	public function country($ip) {
		
		$r = array();
		
		switch ($this->options['provider']) {
			case 'aws':
				$r['country']['iso_code'] = strtoupper(@$_SERVER['CloudFront-Viewer-Country']);
				break;
				
			case 'cloudflare';				
				$isoCode = @$_SERVER["HTTP_CF_IPCOUNTRY"];
				if ($isoCode == 'xx')
					$isoCode = '';
				
				$r['country']['iso_code'] = strtoupper($isoCode);
				
				break;
		}
		
		$r['traits']['ip_address'] = $ip;
		
		$record = new \GeoIp2\Model\City($r, array('en'));
		
		return $record;
	}
}

class HeaderDataSource extends AbstractDataSource {

	public function getId() { return 'header'; }
	public function getLabel() { return __('Special Hosting Providers (Cloudflare, Amazon AWS CloudFront)', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('These hosting providers already do geodetection, but only of the visitor\'s country.', 'geoip-detect'); }
	
	public function getStatusInformationHTML() {
		$provider = get_option('geoip-detect-header-provider');
		$labels = array(
			'' => __('None', 'geoip-detect'),
			'aws' => 'Amazon AWS CloudFront',
			'cloudflare' => 'Cloudflare',
		);
		if (!isset($labels[$provider]))
			$provider = '';
		
		$html = __('Hosting Provider: ', 'geoip-detect') . $labels[$provider];
		
		
		if ($provider == 'cloudflare') {
			$html .= '<br><br>';
			$html .= __('This needs to be enabled in the admin panel: see <a href="https://support.cloudflare.com/hc/en-us/articles/200168236-What-does-CloudFlare-IP-Geolocation-do-">Help</a>.', 'geoip-detect');
		} elseif ($provider == 'aws') {
			$html .= '<br><br>';
			$html .= __('This needs to be enabled in the admin panel: see <a href="https://aws.amazon.com/blogs/aws/enhanced-cloudfront-customization/">Help</a>.', 'geoip-detect');
		}
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
		
		$value = @$post['options_header']['provider'];
		if (!empty($value)) {
			update_option('geoip-detect-header-provider', $value);
		}
		
		return $message;
	}
	
	public function getShortLabel() { $this->getLabel(); }

	public function getReader($locales = array('en'), $options = array()) {
		$reader = null;
		
		$provider = get_option('geoip-detect-header-provider');
		if ($provider) {
			try {
				$reader = new HeaderReader( array(
					'provider' => $provider,
				) );
			} catch ( \Exception $e ) {
				if (WP_DEBUG)
					echo printf(__('Error while creating reader for "%s": %s', 'geoip-detect'), $filename, $e->getMessage ());
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

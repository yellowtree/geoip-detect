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

/**
 * maybe TODO:
 * - warn email when counter is low ?
 * - change to hostinfo or maxmind if credit is zero?
 * - exclude spiders?
 * - error logging
 */

namespace YellowTree\GeoipDetect\DataSources\Precision;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

class PrecisionReader extends \GeoIp2\WebService\Client implements \YellowTree\GeoipDetect\DataSources\ReaderInterface 
{
	public function __construct($userId, $licenseKey, $options) {
		parent::__construct($userId, $licenseKey, [ 'en' ], $options);
	}
	
	public function city($ip = 'me') {
		$method = get_option('geoip-detect-precision_api_type', 'city');
		
		$ret = null;
		
		$callback = [ $this, $method ];
		if (!is_callable($callback)) {
			throw new \RuntimeException('Precision API: Unsupported method ' . $method);
		}

		if ($method == 'city')
			$ret = parent::city($ip);
		else
			$ret = call_user_func_array($callback, [ $ip ]);
		
			/* Web-API-specific exceptions:
			} catch (AuthenticationException $e) {
			} catch (OutOfQueriesException $e) {
			}
			*/
		
		if ($ret) {
			$credits = $ret->maxmind->queriesRemaining; // This seems to be approximate.
			update_option('geoip-detect-precision-remaining_credits', $credits);
		}
		return $ret;
	}
	
	public function close() { }
}

class PrecisionDataSource extends AbstractDataSource {
	
	protected $known_api_types = array(
			'country' => [ 'label' => 'Country' ], 
			'city' => [ 'label' => 'City' ], 
			'insights' => [ 'label' => 'Insights' ]);
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getId() { return 'precision'; }
	public function getLabel() { return __('Maxmind Precision Web-API', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('<a href="https://www.maxmind.com/en/geoip2-precision-services">Maxmind Precision Services</a>', 'geoip-detect'); }
	public function getStatusInformationHTML() { 
		$html = '';
		$html .= sprintf(__('API Type: %s', 'geoip-detect'), ucfirst(get_option('geoip-detect-precision_api_type', 'city'))) . '<br />';
		
		$remaining = get_option('geoip-detect-precision-remaining_credits');
		if ($remaining !== false) {
			$html .= sprintf(__('Remaining Credits: ca. %s', 'geoip-detect'), $remaining) . '<br />';
		}
		
		if (!$this->isWorking())
			$html .= '<div class="geoip_detect_error">' . __('Maxmind Precision only works with a given user id and secret.', 'geoip-detect') . '</div>';

		
		return apply_filters('geoip_detect_source_get_status_HTML_maxmind', $html, $this->getId());;
	}
	
	public function getParameterHTML() { 
		$user_id = (int) get_option('geoip-detect-precision-user_id');
		$user_secret = esc_attr(get_option('geoip-detect-precision-user_secret'));
		$current_api_type = get_option('geoip-detect-precision_api_type');
		
		$label_user_id = __('User ID:', 'geoip-detect');
		$label_user_secret = __('License key:', 'geoip-detect');
		$label_api_type = __('API Type:', 'geoip-detect');
		
		$html = <<<HTML
$label_user_id <input type="text" size="10" name="options_precision[user_id]" value="$user_id" /><br />		
$label_user_secret <input type="text" autocomplete="off" size="20" name="options_precision[user_secret]" value="$user_secret" /><br />
$label_api_type <select name="options_precision[api_type]">
HTML;
		
		foreach ($this->known_api_types as $name => $api_type) {
			$html .= '<option ';
			if ($name == $current_api_type)
				$html .= 'selected="selected" ';
			$html .= 'value="' . $name . '">' . $api_type['label'] . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	public function saveParameters($post) {
		$message = '';
		
		if (isset($post['options_precision']['user_id'])) {
			$user_id = (int) $post['options_precision']['user_id'];
			update_option('geoip-detect-precision-user_id', $user_id);
		}
		if (isset($post['options_precision']['user_secret'])) {
			$user_secret = sanitize_text_field($post['options_precision']['user_secret']);
			update_option('geoip-detect-precision-user_secret', $user_secret);	
		}
		if (isset($post['options_precision']['api_type'])) {
			$type = sanitize_text_field($post['options_precision']['api_type']);
			if (isset($this->known_api_types[$type])) {
				update_option('geoip-detect-precision_api_type', $type);
			}
		}
		
		if (geoip_detect2_is_source_active('precision') && !$this->isWorking())
			$message .= __('Maxmind Precision only works with a given user id and secret.', 'geoip-detect');
		
		return $message;
	}
	
	public function getReader($locales = [ 'en' ], $options = []) {
		if (!$this->isWorking())
			return null;

		$user_id = get_option('geoip-detect-precision-user_id');
		$user_secret = get_option('geoip-detect-precision-user_secret');
		
		$client = new PrecisionReader($user_id, $user_secret, $options);

		return $client;
	}

	public function isWorking() { 
		$user_id = get_option('geoip-detect-precision-user_id');
		$user_secret = get_option('geoip-detect-precision-user_secret');
	
		return ! (empty($user_id) || empty($user_secret));
	}

}

geoip_detect2_register_source(new PrecisionDataSource());

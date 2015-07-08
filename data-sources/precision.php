<?php
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
		parent::__construct($userId, $licenseKey, array('en'), $options);
	}
	
	public function city($ip) {
		$method = get_option('geoip-detect-precision_api_type', 'city');
		
		$ret = null;
		
		$callback = array($this, $method);
		if (!is_callable($callback)) {
			throw new \RuntimeException('Precision API: Unsupported method ' . $method);
		}

		if ($method == 'city')
			$ret = parent::city($ip);
		else
			$ret = call_user_func_array($callback, array($ip));
		
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
			'country' => array('label' => 'Country'), 
			'city' => array('label' => 'City'), 
			'insights' => array('label' => 'Insights'));
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getId() { return 'precision'; }
	public function getLabel() { return 'Maxmind Precision Web-API'; }

	public function getDescriptionHTML() { return '<a href="https://www.maxmind.com/en/geoip2-precision-services">Maxmind Precision Services</a>'; }
	public function getStatusInformationHTML() { 
		$html = '';
		$html .= 'API Type: ' . ucfirst(get_option('geoip-detect-precision_api_type', 'city')) . '<br />';
		
		$remaining = get_option('geoip-detect-precision-remaining_credits');
		if ($remaining !== false) {
			$html .= 'Remaining Credits: ca. ' . $remaining . '<br />';
		}
		
		if (!$this->isWorking())
			$html .= '<div class="geoip_detect_error">' . __('Maxmind Precision only works with a given user id and secret.', 'geoip-detect') . '</div>';

		return $html;
	}
	
	public function getParameterHTML() { 
		$user_id = (int) get_option('geoip-detect-precision-user_id');
		$user_secret = esc_attr(get_option('geoip-detect-precision-user_secret'));
		$current_api_type = get_option('geoip-detect-precision_api_type');
		
		$html = <<<HTML
User ID: <input type="text" size="10" name="options_precision[user_id]" value="$user_id" /><br />		
User Secret: <input type="text" autocomplete="off" size="20" name="options_precision[user_secret]" value="$user_secret" /><br />
API Type: <select name="options_precision[api_type]">
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
			$user_secret = $post['options_precision']['user_secret'];
			update_option('geoip-detect-precision-user_secret', $user_secret);	
		}
		if (isset($post['options_precision']['api_type'])) {
			if (isset($this->known_api_types[$post['options_precision']['api_type']]))
				update_option('geoip-detect-precision_api_type', $post['options_precision']['api_type']);
		}
		
		if (!$this->isWorking())
			$message .= __('Maxmind Precision only works with a given user id and secret.', 'geoip-detect');
		
		return $message;
	}
	
	public function getReader($locales = array('en'), $options = array()) {
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

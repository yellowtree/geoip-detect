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
use GeoIp2\Exception\OutOfQueriesException;
use GeoIp2\Exception\AuthenticationException;

class PrecisionReader extends \GeoIp2\WebService\Client implements \YellowTree\GeoipDetect\DataSources\ReaderInterface 
{
	public function city($ip) {
		$method = get_option('geoip-detect-precision_api_type', 'city');
		
		$ret = null;
		if (method_exists($this, $method)) {
			//try {
				$ret = $this->$method($ip);
				
				// Catch only Web-API-specific exceptions
			/*
			} catch (AuthenticationException $e) {
				update_option('geoip-detect-precision-error', $e->getMessage());				
			} catch (OutOfQueriesException $e) {
				update_option('geoip-detect-precision-error', $e->getMessage());
			}
			*/
		}
		
		if ($ret) {
			$credits = $ret->maxmind->remainingCredits;
			update_option('geoip-detect-precision-remaining_credits', $credits);
		}
		return $ret;
	}
	
	public function close() {
		
	}
	
	
}

class PrecisionDataSource extends AbstractDataSource {
	
	protected $user_id;
	protected $user_secret;
	
	protected $known_api_types = array(
			'country' => array('label' => 'Country'), 
			'city' => array('label' => 'City'), 
			'insights' => array('label' => 'Insights'));
	
	public function __construct() {
		parent::__construct();
		
		$this->user_id = get_option('geoip-detect-precision-user_id');
		$this->user_secret = get_option('geoip-detect-precision-user_secret');
	}

	
	public function getId() { return 'precision'; }
	public function getLabel() { return 'Maxmind Precision Web-API'; }

	public function getDescriptionHTML() { return '<a href="https://www.maxmind.com/en/geoip2-precision-services">Maxmind Precision Services</a>'; }
	public function getStatusInformationHTML() { return ''; }
	public function getParameterHTML() { 
		$user_id = get_option('geoip-detect-precision_user_id');
		$user_secret = get_option('geoip-detect-precision_user_secret');
		$current_api_type = get_option('geoip-detect-precision_api_type');
		
		$html = <<<HTML
User ID: <input type="text" size="10" name="options_precision[user_id]" value="$user_id" /><br />		
User Secret: <input type="text" size="20" name="options_precision[user_secret]" value="$user_secret" /><br />
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
			$this->user_id = (int) $post['options_precision']['user_id'];
			update_option('geoip-detect-precision_user_id', $this->user_id);
		}
		if (isset($post['options_precision']['user_secret'])) {
			$this->user_secret = $post['options_precision']['user_secret'];
			update_option('geoip-detect-precision_user_secret', $this->user_secret);	
		}
		if (isset($post['options_precision']['api_type'])) {
			if (isset($this->known_api_types[$post['options_precision']['api_type']]))
				update_option('geoip-detect-precision_api_type', $post['options_precision']['api_type']);
		}
		
		if (!$this->isWorking())
			$message .= __('Maxmind Precision only works with a given user id and secret.');
		
		return $message;
	}
	
	public function getReader() { 
		if (!$this->isWorking())
			return null;

		$client = new PrecisionReader($this->user_id, $this->user_secret);
		
		return $client;
	}

	public function isWorking() { 
		return $this->user_id && $this->user_secret;
	}

}

geoip_detect2_register_source(new PrecisionDataSource());

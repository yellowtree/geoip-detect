<?php
/**
 * - warn email when counter is low ?
 * - change to hostinfo or maxmind if credit is zero?
 * - exclude spiders?
 */

namespace YellowTree\GeoipDetect\DataSources\Precision;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
use GeoIp2\Exception\OutOfQueriesException;
use GeoIp2\Exception\AuthenticationException;

class PrecisionReader extends \GeoIp2\WebService\Client implements \YellowTree\GeoipDetect\DataSources\ReaderInterface 
{
	public function city($ip) {
		$method = get_option('geoip-detect-precision-method');
		
		$ret = null;
		if (method_exists($this, $method)) {
			try {
				$ret = $this->$method($ip);
				
				// Catch only Web-API-specific exceptions
			} catch (AuthenticationException $e) {
				update_option('geoip-detect-precision-error', $e->getMessage());				
			} catch (OutOfQueriesException $e) {
				update_option('geoip-detect-precision-error', $e->getMessage());
			}
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
	public function __construct() {
		parent::__construct();
		
		$this->user_id = get_option('geoip-detect-precision-user_id');
		$this->user_secret = get_option('geoip-detect-precision-user_secret');
	}

	
	public function getId() { return 'precision'; }
	public function getLabel() { return 'Maxmind Precision Web-API'; }

	public function getDescriptionHTML() {  }
	public function getStatusInformationHTML() { return ''; }
	public function getParameterHTML() { return ''; }
	
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
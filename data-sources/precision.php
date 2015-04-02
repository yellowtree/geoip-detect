<?php
/**
 * - save credit counter
 * - warn email when counter is low ?
 * - change to hostinfo or maxmind if credit is zero
 * - exclude spiders?
 */

namespace YellowTree\GeoipDetect\DataSources\Precision;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

class PrecisionDataSource extends AbstractDataSource {
	public function __construct() {
		$this->user_id = get_option('geoip-detect-precision-user_id');
		$this->user_secret = get_option('geoip-detect-precision-user_secret');		
	}
	
	public function getId() { return 'precision'; }
	public function getLabel() {  }

	public function getDescriptionHTML() {  }
	public function getStatusInformationHTML() { return ''; }
	public function getParameterHTML() { return ''; }
	
	public function getReader() { 
		if (!$this->isWorking())
			return null;

		$client = new \GeoIp2\WebService\Client($user_id, $user_secret);
		
		return $client;
	}

	public function isWorking() { 
		return $this->user_id && $this->user_secret;
	}

}

geoip_detect2_register_source(new PrecisionDataSource());
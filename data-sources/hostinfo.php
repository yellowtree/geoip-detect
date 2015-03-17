<?php

class GeoIP_HostInfo_Reader {
	
	const URL = 'http://api.hostip.info/get_json.php?ip=';
	
	public function city($ip) {	
		$data = $this->api_call($ip);
		
		if (!$data)
			return null;
		
		$r = new stdClass();
		$r->country = new stdClass();
		
		if ($data['country_name'])
			$r->country->names = array('en' => $data['country_name']);
		if ($data['country_code'])
			$r->country->isoCode = strtoupper($data['country_code']);
		
		if ($data['city']) {
			$r->city = new stdClass();
			$r->city->names = array('en' => $data['city']);
		}
		
		$record = new \GeoIp2\Model\City($r, array('en'));
		
		return $record;
	}
	
	public function country($ip) {
		return $this->city($ip); // too much info shouldn't hurt ...
	}
	
	public function api_call($ip) {
		try {
			// Setting timeout limit to speed up sites
			$context = stream_context_create(
					array(
							'http' => array(
									'timeout' => 1,
							),
					)
			);
			// Using @file... to supress errors
			// Example output: {"country_name":"UNITED STATES","country_code":"US","city":"Aurora, TX","ip":"12.215.42.19"}
			$data = json_decode(@file_get_contents(self::URL . $ip, false, $context));
			
			$hasInfo = false;
			if ($data) {
				foreach ($data as $key => &$value) {
					if (stripos($value, '(unknown'))
						$value = '';
					if (stripos($value, '(private'))
						$value = '';
					if ($key == 'country' && $value = 'XX')
						$value = '';
				}
				$hasInfo = $data->country_name || $data->country_code || $data->city;
			}
			
			if ($hasInfo)
				return $data;
			return null;
		} catch (Exception $e) {
			// If the API isn't available, we have to do this
			return null;
		}
	}
}

function geoip_detect2_hostinfo_reader() {
	return new GeoIP_HostInfo_Reader();
}
add_filter('geoip_detect2_reader', 'geoip_detect2_hostinfo_reader');

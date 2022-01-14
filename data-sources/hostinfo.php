<?php

namespace YellowTree\GeoipDetect\DataSources\HostInfo;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

class Reader implements \YellowTree\GeoipDetect\DataSources\ReaderInterface {

	const URL = 'http://api.hostip.info/get_json.php?ip=';
	protected $options;

	function __construct($options) {
		$default_options = array(
			'timeout' => 1,
		);
		$this->options = $options + $default_options;
	}

	public function city($ip) {
		if (!geoip_detect_is_ip($ip, true))
			throw new \Exception('The Hostip.info-Database only contains IPv4 adresses.');

		$data = $this->api_call($ip);

		if (!$data)
			return _geoip_detect2_get_new_empty_record();

		$r = [];

		$r['traits']['original'] = $data;

		if ($data['country_name'])
			$r['country']['names'] = [ 'en' => $data['country_name'] ];
		if ($data['country_code'])
			$r['country']['iso_code'] = strtoupper($data['country_code']);

		if ($data['city']) {
			$r['city']['names'] = [ 'en' => $data['city'] ];
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
			$body = @file_get_contents(self::URL . $ip, false, $context);
			$data = json_decode($body);

			$hasInfo = false;
			if ($data) {
				$data = get_object_vars($data);
				foreach ($data as $key => &$value) {
					if (stripos($value, '(unknown') !== false)
						$value = '';
					if (stripos($value, '(private') !== false)
						$value = '';
					if ($key == 'country_code' && $value == 'XX')
						$value = '';
				}
				$hasInfo = $data['country_name'] || $data['country_code'] || $data['city'];
			}

			if ($hasInfo)
				return $data;
			return null;
		} catch (\Exception $e) {
			// If the API isn't available, we have to do this
			return null;
		}
	}
}


class HostInfoDataSource extends AbstractDataSource {
	public function getId() { return 'hostinfo'; }
	public function getLabel() { return __('HostIP.info Web-API', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('Free (Licence: GPL)<br />(only English names, does only have some fields)', 'geoip-detect'); }
	public function getStatusInformationHTML() { return __('You can choose a different data source below.', 'geoip-detect'); }
	public function getParameterHTML() { return ''; }

	public function getReader($locales = [ 'en' ], $options = []) { return new Reader($options); }

	public function isWorking() { return true; }

}

geoip_detect2_register_source(new HostInfoDataSource());

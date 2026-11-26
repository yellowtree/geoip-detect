<?php

namespace YellowTree\GeoipDetect\DataSources\Ip2LocationIo;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

class Reader implements \YellowTree\GeoipDetect\DataSources\ReaderInterface
{
	public const URL = 'api.ip2location.io/';
	protected $options = [];
	protected $params = [];

	public function __construct($params, $locales, $options)
	{
		$this->params = $params;
		$this->params['language'] = reset($locales);
		if (empty($this->params['language'])) {
			$this->params['language'] = 'en';
		}

		$default_options = [
			'timeout' => 1,
		];
		$this->options = $options + $default_options;
	}

	protected function locales($locale, $value)
	{
		$locales = ['en' => $value];
		if ($locale != 'en') {
			$locales[$locale] = $value;
		}

		return $locales;
	}

	public function city($ip)
	{
		$data = $this->api_call($ip);

		if (!$data) {
			return _geoip_detect2_get_new_empty_record();
		}

		$r = [];

		$r['extra']['original'] = $data;

		if (isset($data['error']['error_message'])) {
			throw new \RuntimeException($data['error']['error_message']);
			// Example error:
			/* @see https://www.ip2location.io/ip2location-documentation
			{
                "error":{
                    "error_code":10000,
                    "error_message":"Invalid API key or insufficient credit."
                }
            }
			*/
		}

		$locale = $this->params['language'];

		if (!empty($data['country_name'])) {
			$r['country']['names'] = $this->locales($locale, $data['country_name']);
		}
		if (!empty($data['country_code'])) {
			$r['country']['iso_code'] = strtoupper($data['country_code']);
		}
		if (!empty($data['region_name'])) {
			$r['subdivisions'][0] = [
				'iso_code' => '',
				'names'    => $this->locales($locale, $data['region_name']),
			];
		}
		if (!empty($data['city_name'])) {
			$r['city']['names'] = $this->locales($locale, $data['city_name']);
		}
		if (!empty($data['latitude'])) {
			$r['location']['latitude'] = $data['latitude'];
		}
		if (!empty($data['longitude'])) {
			$r['location']['longitude'] = $data['longitude'];
		}
		if (isset($data['asn'])) {
			$r['traits']['autonomous_system_number'] = $data['asn'];
		}
		if (isset($data['as'])) {
			$r['traits']['isp'] = $data['as'];
		}
		if (!empty($data['is_proxy'])) {
			$r['traits']['is_anonymous_vpn'] = $data['is_proxy'];
		}

		$r['traits']['ip_address'] = $ip;

		$record = new \GeoIp2\Model\City($r, ['en']);

		return $record;
	}

	public function country($ip)
	{
		return $this->city($ip); // too much info shouldn't hurt ...
	}

	public function close()
	{
	}

	private function build_url($ip)
	{
		$url = 'https://' . self::URL . $ip;

		$params = [
			'key' => $this->params['key'],
		];

		return $url . '?' . \http_build_query($params);
	}

	private function api_call($ip)
	{
		try {
			// Setting timeout limit to speed up sites
			$context = stream_context_create(
				[
					'http' => [
						'timeout' => $this->options['timeout'],
					],
				]
			);
			// Using @file... to supress errors
			// Example output: {"country_name":"UNITED STATES","country_code":"US","city":"Aurora, TX","ip":"12.215.42.19"}

			$body = @file_get_contents($this->build_url($ip), false, $context);
			$data = json_decode($body, true);

			return $data;
		} catch (\Exception $e) {
			// If the API isn't available, we have to do this
			throw $e;

			return null;
		}
	}
}

class Ip2LocationIoSource extends AbstractDataSource
{
	protected $params = [];

	public function __construct()
	{
		$this->params['key'] = get_option('geoip-detect-ip2locationio_key', '');
	}

	public function getId()
	{
		return 'ip2locationio';
	}

	public function getLabel()
	{
		return __('IP2Location.io Geolocation API Service', 'geoip-detect');
	}

	public function getDescriptionHTML()
	{
		return __('Free 30,000 queries per month. Register API key as <a href="https://www.ip2location.io/">IP2Location.io</a>.', 'geoip-detect');
	}

	public function getStatusInformationHTML()
	{
		$html = '';

		if (!$this->isWorking()) {
			$html .= '<div class="geoip_detect_error">' . __('IP2Location.io only works with an API key.', 'geoip-detect') . '</div>';
		}

		return $html;
	}

	public function getParameterHTML()
	{
		$label_key = __('API Key:', 'geoip-detect');
		$key = esc_attr($this->params['key']);

		$html = <<<HTML
$label_key <input type="text" autocomplete="off" size="20" name="options_ip2locationio[key]" value="$key" /><br />
HTML;

		return $html;
	}

	public function saveParameters($post)
	{
		$message = '';

		if (isset($post['options_ip2locationio']['key'])) {
			$key = sanitize_key($post['options_ip2locationio']['key']);
			update_option('geoip-detect-ip2locationio_key', $key);
			$this->params['key'] = $key;
		}

		if (geoip_detect2_is_source_active('ip2locationio') && !$this->isWorking()) {
			$message .= __('IP2Location.io only works with an API key.', 'geoip-detect');
		}

		return $message;
	}

	public function getReader($locales = ['en'], $options = [])
	{
		return new Reader($this->params, $locales, $options);
	}

	public function isWorking()
	{
		return !empty($this->params['key']);
	}
}
geoip_detect2_register_source(new Ip2LocationIoSource());

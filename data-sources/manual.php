<?php

namespace YellowTree\GeoipDetect\DataSources\Manual;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

define('GEOIP_DETECT_DATA_FILENAME', 'GeoLite2-City.mmdb');

class ManualDataSource extends AbstractDataSource {

	public function getId() { return 'manual'; }
	public function getLabel() { return ''; }

	public function getDescriptionHTML() { return ''; }
	public function getStatusInformationHTML() { return ''; }
	public function getParameterHTML() { return ''; }

	public function activate() { }

	public function getReader() { return null; }

	public function isWorking() { 
		$filename = $this->maxmindGetFile();
		if (!is_readable($filename))
			return false;

		return true;
	}
	
	public function maxmindGetFile() {
		$data_filename = get_option('geoip-detect-manual_file_validated');

		// Allow placing the file in the plugin folder for backwards compat
		if (!$data_filename || !file_exists($data_filename)) {
			$data_filename = GEOIP_PLUGIN_DIR . '/' . GEOIP_DETECT_DATA_FILENAME;
		}
		
		if (!file_exists($data_filename)) {
			$data_filename = '';
		}
		
		$data_filename = apply_filters('geoip_detect_get_abs_db_filename', $data_filename);
	}
}

DataSourceRegistry::getInstance()->register(new ManualDataSource());
<?php

namespace YellowTree\GeoipDetect\DataSources\Manual;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

define('GEOIP_DETECT_DATA_FILENAME', 'GeoLite2-City.mmdb');

class ManualDataSource extends AbstractDataSource {

	public function getId() { return 'manual'; }
	public function getLabel() { return 'Manual download & update of a Maxmind City or Country database'; }

	public function getDescriptionHTML() { return ''; }
	public function getStatusInformationHTML() { return ''; }
	public function getParameterHTML() { return ''; }
	
	public function getShortLabel() { return $this->maxmindGetFileDescription(); }

	public function getReader($locales = array('en')) {
		$reader = null;
		
		$data_file = $this->maxmindGetFilename();
		if ($data_file) {
			try {
				$reader = new \GeoIp2\Database\Reader ( $data_file, $locales );
			} catch ( \Exception $e ) {
				if (WP_DEBUG)
					echo 'Error while creating reader for "' . $data_file . '": ' . $e->getMessage ();
			}
		}
		
		return $reader;
	}

	public function isWorking() { 
		$filename = $this->maxmindGetFilename();
		if (!is_readable($filename))
			return false;

		return true;
	}
	
	public function maxmindGetFilename() {
		$data_filename = get_option('geoip-detect-manual_file_validated');

		// Allow placing the file in the plugin folder for backwards compat
		if (!$data_filename || !file_exists($data_filename)) {
			$data_filename = GEOIP_PLUGIN_DIR . '/' . GEOIP_DETECT_DATA_FILENAME;
		}
		
		if (!file_exists($data_filename)) {
			$data_filename = '';
		}
		
		$data_filename = apply_filters('geoip_detect_get_abs_db_filename', $data_filename);
		
		return $data_filename;
	}
	
	public static function maxmindValidateFilename($filename) {
		if (file_exists(ABSPATH . $filename))
			$filename = ABSPATH . $filename;
		
		if (!is_readable($filename))
			return '';
	
		try {
			$reader = new \GeoIp2\Database\Reader ($filename);
			$metadata = $reader->metadata();
			$reader->close();
		} catch ( \Exception $e ) {
			if (WP_DEBUG)
				echo 'Error while creating reader for "' . $data_file . '": ' . $e->getMessage ();
			return '';
		}
	
		return $filename;
	}
	
	protected function maxmindGetFileDescription() {
		$reader = $this->getReader();
		
		if (!method_exists($reader, 'metadata'))
			return 'Unknown';
		
		try {
			$metadata = $reader->metadata();
		} catch (\Exception $e) {
			return 'Unknown';
		}
		
		$desc = $metadata->description;
		return $desc['en'];
	} 
}

geoip_detect2_register_source(new ManualDataSource());

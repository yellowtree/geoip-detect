<?php

namespace YellowTree\GeoipDetect\DataSources\Manual;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

define('GEOIP_DETECT_DATA_FILENAME', 'GeoLite2-City.mmdb');

class ManualDataSource extends AbstractDataSource {

	public function getId() { return 'manual'; }
	public function getLabel() { return 'Manual download & update of a Maxmind City or Country database'; }

	public function getDescriptionHTML() { return '<a href="http://dev.maxmind.com/geoip/geoip2/geolite2/" target="_blank">Free version</a> - <a href="https://www.maxmind.com/en/geoip2-country-database" target="_blank">Commercial Version</a>'; }
	public function getStatusInformationHTML() {
		$built = $last_update = 0;
		$date_format = get_option('date_format') . ' ' . get_option('time_format');
		$file = $this->maxmindGetFilename();
		
		if (!$file)
			return '<b>No Maxmind database found.</b>';
		
		$relative_file = geoip_detect_get_relative_path(ABSPATH, $file);
		
		$html[] = sprintf(__('Database file: %s', 'geoip-detect'), $relative_file);
		
		try { 
			$reader = $this->getReader();
			if ($reader) {
				$metadata = $reader->metadata();
				$built = $metadata->buildEpoch;
				$last_update = @filemtime($file);
			}
		} catch (\Exception $e) { }
		
		$html[] = sprintf(__('Last updated: %s', 'geoip-detect'), $last_update ? date_i18n($date_format, $last_update) : __('Never', 'geoip-detect'));
		$html[] = sprintf(__('Database data from: %s', 'geoip-detect'), date_i18n($date_format, $built) );
		
		return implode('<br>', $html);
	}

	public function getParameterHTML() { 
		$manual_file = get_option('geoip-detect-manual_file');
		$html = <<<HTML
Filepath to mmdb-file: <input type="text" size="40" name="options_manual[manual_file]" value="$manual_file" /><br />		
HTML;
		
		return $html;
	}
	
	public function saveParameters($post) {
		$message = '';
		
		if (!empty($post['options_manual']['manual_file'])) {
			$validated_filename = self::maxmindValidateFilename($post['options_manual']['manual_file']);
			update_option('geoip-detect-manual_file_validated', $validated_filename);
		
			if (empty($validated_filename) || !$this->isWorking()) {
				$message .= __('The manual datafile has not been found or is not a mmdb-File. ', 'geoip-detect');
			}
		}
		
		return $message;
	}
	
	public function getShortLabel() { return $this->maxmindGetFileDescription(); }

	public function getReader($locales = array('en'), $options = array()) {
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
				echo 'Error while creating reader for "' . $filename . '": ' . $e->getMessage ();
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
			$desc = $metadata->description;
			return $desc['en'];
		} catch (\Exception $e) {
			return 'Unknown';
		}
	} 
}

geoip_detect2_register_source(new ManualDataSource());

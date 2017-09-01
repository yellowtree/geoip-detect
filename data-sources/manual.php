<?php
/*
Copyright 2013-2016 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (info@yellowtree.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace YellowTree\GeoipDetect\DataSources\Manual;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;

define('GEOIP_DETECT_DATA_FILENAME', 'GeoLite2-City.mmdb');

class ManualDataSource extends AbstractDataSource {
	
	public function getId() { return 'manual'; }
	public function getLabel() { return __('Manual download & update of a Maxmind City or Country database', 'geoip-detect'); }

	public function getDescriptionHTML() { return __('<a href="http://dev.maxmind.com/geoip/geoip2/geolite2/" target="_blank">Free version</a> - <a href="https://www.maxmind.com/en/geoip2-country-database" target="_blank">Commercial Version</a>', 'geoip-detect'); }
	public function getStatusInformationHTML() {
		$built = $last_update = 0;
		$date_format = get_option('date_format') . ' ' . get_option('time_format');
		$file = $this->maxmindGetFilename();
		
		if (!$file)
			return '<b>' . __('No Maxmind database found.', 'geoip-detect') . '</b>';
		
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
		$manual_file = esc_attr(get_option('geoip-detect-manual_file'));
		$current_value = '';
		
		if (	get_option('geoip-detect-manual_file_validated') && 
				get_option('geoip-detect-manual_file') 				!= get_option('geoip-detect-manual_file_validated') &&
				ABSPATH . get_option('geoip-detect-manual_file') 	!= get_option('geoip-detect-manual_file_validated')
			) {
			$current_value = '<br >' . sprintf(__('Current value: %s', 'geoip-detect'), get_option('geoip-detect-manual_file_validated'));
		} 

		$label = __('Filepath to mmdb-file:', 'geoip-detect');
		$desc = __('e.g. wp-content/uploads/GeoLite2-Country.mmdb or absolute filepath', 'geoip-detect');
		$html = <<<HTML
		<p>$label <input type="text" size="40" name="options_manual[manual_file]" value="$manual_file" /></p>
		<span class="detail-box">$desc $current_value</span>
		<br />	
HTML;
		
		return $html;
	}
	
	public function saveParameters($post) {
		$message = '';
		
		$file = @$post['options_manual']['manual_file'];
		if (!empty($file)) {
			update_option('geoip-detect-manual_file', $file);
			
			$validated_filename = self::maxmindValidateFilename($file);
			if (empty($validated_filename)) {
				$message .= __('The manual datafile has not been found or is not a mmdb-File. ', 'geoip-detect');
			} else {
				update_option('geoip-detect-manual_file_validated', $validated_filename);
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
					echo printf(__('Error while creating reader for "%s": %s', 'geoip-detect'), $filename, $e->getMessage ());
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
		// Maybe make path absolute
		if (file_exists(ABSPATH . $filename))
			$filename = ABSPATH . $filename;
		
		if (!is_readable($filename))
			return '';
	
		try {
			$reader = new \GeoIp2\Database\Reader($filename);
			$metadata = $reader->metadata();
			$reader->close();
		} catch ( \Exception $e ) {
			if (WP_DEBUG)
				echo printf(__('Error while creating reader for "%s": %s', 'geoip-detect'), $filename, $e->getMessage ());
			return '';
		}
	
		return $filename;
	}
	
	protected function maxmindGetFileDescription() {
		$reader = $this->getReader();
		
		if (!method_exists($reader, 'metadata'))
			return __('Maxmind File Database (file does not exist or is not readable)', 'geoip-detect');
		
		try {
			$metadata = $reader->metadata();
			$desc = $metadata->description;
			return $desc['en'];
		} catch (\Exception $e) {
			return __('Maxmind File Database (file does not exist or is not readable)', 'geoip-detect');
		}
	} 
}

geoip_detect2_register_source(new ManualDataSource());

<?php
/*
Copyright 2013-2021 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (wp-geoip-detect| |posteo.de)

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
		$html = array();

		$file = $this->maxmindGetFilename();

		if (!$file)
			return '<b>' . __('No Maxmind database found.', 'geoip-detect') . '</b>';

		$relative_file = geoip_detect_get_relative_path(ABSPATH, $file);

		$html[] = sprintf(__('Database file: %s', 'geoip-detect'), $relative_file);

		$reader = $this->getReader();
		if ($reader) {
			$metadata = $reader->metadata();
			$built = $metadata->buildEpoch;
			$last_update = is_readable($file) ? filemtime($file) : '';
			$html[] = sprintf(__('Database last updated: %s', 'geoip-detect'), geoip_detect_format_localtime($last_update) );
			$html[] = sprintf(__('Database generated: %s', 'geoip-detect'), geoip_detect_format_localtime($built) );
		}

		$html[] = $this->getStatusInformationHTMLMaxmindAccount();


		return implode('<br>', $html);
	}

	protected function getStatusInformationHTMLMaxmindAccount() {
		$html = '';
		$last_update = get_option('geoip_detect2_maxmind_ccpa_blacklist_last_updated', 0);
		$entries = get_option('geoip_detect2_maxmind_ccpa_blacklist');
		$next_update = wp_next_scheduled('geoipdetectccpaupdate');

		$html .= sprintf(__('Privacy Exclusions last updated: %s', 'geoip-detect'), geoip_detect_format_localtime($last_update) );
		if ($entries) {
			$html .= ' ' . sprintf(__('(has %d entries)', 'geoip-detect'), count($entries));
		}
		if (WP_DEBUG) {
			$html .= '<br>' . sprintf(__('Privacy Exclusions next Update: %s', 'geoip-detect'), geoip_detect_format_localtime($next_update) );
		}

		return $html;
	}

	protected function getParameterHTMLMaxmindAccount() {
		$key = esc_attr(get_option('geoip-detect-auto_license_key', ''));
		$id = esc_attr((int) get_option('geoip-detect-auto_license_id', ''));

		$label_id = __('Account ID:', 'geoip-detect');
		$label_key = __('License key:', 'geoip-detect');


		$html = <<<HTML
$label_id <input type="number" autocomplete="off" size="10" name="options_auto[license_id]" value="$id" /><br />
$label_key <input type="text" autocomplete="off" size="20" name="options_auto[license_key]" value="$key" /><br />
HTML;
		return $html;
	}

	protected function scheduleCcpa($forceRunNow = false) {
		if (!class_exists('\\YellowTree\\GeoipDetect\\Lib\\CcpaBlacklistCron')) {
			return;
		}
		$ccpaCronScheduler = new \YellowTree\GeoipDetect\Lib\CcpaBlacklistCron;
		$ccpaCronScheduler->schedule(true);
	}

	protected function unscheduleCcpa() {
		wp_clear_scheduled_hook('geoipdetectccpaupdate');
	}

	public function activate() {
		$this->scheduleCcpa();
	}
	public function deactivate() {
		$this->unscheduleCcpa();
	}

	protected function saveParametersMaxmindAccount($post) {
		$message = '';

		if (isset($post['options_auto']['license_key'])) {
			$key = sanitize_text_field($post['options_auto']['license_key']);
			$validationResult = $this->validateApiKey($key);
			if (\is_string($validationResult)) {
				$message .= $validationResult;
			}
			$keyChanged = update_option('geoip-detect-auto_license_key', $key);
		}

		if (isset($post['options_auto']['license_id'])) {
			$id = (int) $post['options_auto']['license_id'];
			if ($id <= 0) {
				$message .= __('This is not a valid Maxmind Account ID.', 'geoip-detect');
			}
			$idChanged = update_option('geoip-detect-auto_license_id', $id);
			$forceRunNow = $idChanged || $keyChanged;
			if ($id) {
				$this->scheduleCcpa($forceRunNow);
			}
		}

		return $message;
	}

	public function validateApiKey($key) {
		$message = '';
		$key = trim($key);
		if (mb_strlen($key) != 16) {
			$message = __('The license key usually is a 16-char alphanumeric string. Are you sure this is the right key?', 'geoip-detect');
			if (mb_strlen($key) < 16) {
				$message .= ' ' . __('Do not use the "unhashed format" when generating the license key.', 'geoip-detect');
				// Unhashed: 13char alphanumeric
			}
			$message .= ' ' . sprintf(__('This key is %d chars long.', 'geoip-detect'), mb_strlen($key));
		} else if (1 !== preg_match('/^[a-z0-9]+$/i', $key)) {
			$message = __('The license key usually is a 16-char alphanumeric string. Are you sure this is the right key?', 'geoip-detect');
			$message .= ' ' . __('This key contains characters other than a-z and 0-9.', 'geoip-detect');
		}
		if ($message) return $message;

		return true;
	}



	public function getParameterHTML() {
		$html = $this->getParameterHTMLMaxmindAccount();

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
		$html .= <<<HTML
		<p>$label <input type="text" size="40" name="options_manual[manual_file]" value="$manual_file" /></p>
		<span class="detail-box">$desc $current_value</span>
		<br />
HTML;

		return $html;
	}

	public function saveParameters($post) {
		$message = '';

		$file = isset($post['options_manual']['manual_file']) ? sanitize_text_field($post['options_manual']['manual_file']) : '';
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
				// ToDo this needs to be wrapped?
				$reader = new \GeoIp2\Database\Reader ( $data_file, $locales );
			} catch ( \Exception $e ) {
				if (WP_DEBUG)
					trigger_error(sprintf(__('Error while creating reader for "%s": %s', 'geoip-detect'), $data_file, $e->getMessage()), E_USER_NOTICE);
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
			// Maybe site root changed?
			$data_filename = $this->maxmindValidateFilename(get_option('geoip-detect-manual_file'));
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
			$reader->metadata(); /* Try to read from it ... the result doesn't actually interest me. */
			$reader->close();
		} catch ( \Exception $e ) {
			// Not readable, so do not accept this filename
			return '';
		}

		return $filename;
	}

	protected function maxmindGetFileDescription() {
		$reader = $this->getReader();

		if (!is_object($reader) || !method_exists($reader, 'metadata'))
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

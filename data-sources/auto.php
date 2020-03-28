<?php
/*
Copyright 2013-2020 Yellow Tree, Siegen, Germany
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

namespace YellowTree\GeoipDetect\DataSources\Auto;

use YellowTree\GeoipDetect\DataSources\AbstractMmdbDataSource;

require_once(__DIR__ . '/_mmdb.php');

class AutoDataSource extends AbstractMmdbDataSource
{
	public function getId() { return 'auto'; }
	public function getLabel() { return __('Automatic download & update of Maxmind GeoIP Lite City', 'geoip-detect'); }
	public function getShortLabel() { return sprintf(__('%s (updated weekly)', 'geoip-detect'), parent::getShortLabel()); }
	public function getDescriptionHTML() { 
		return __('(License: See <a href="https://www.maxmind.com/en/site-license-overview" target="_blank">Site Licence Overview</a> or <a href="https://www.maxmind.com/en/end-user-license-agreement" target="_blank">End User Licence Agreement</a>.)', 'geoip-detect'); }

	protected function maxmindGetUploadFilename() {
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'];

		$filename = $dir . '/GeoLite2-City.mmdb';
		return $filename;
	}

	protected function getDownloadUrl() {
		$download_url = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&suffix=tar.gz';
		//$download_url = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&suffix=tar.gz';
		
		$download_url = apply_filters('geoip_detect2_download_url', $download_url);
		if (strpos($download_url, 'license_key=') === false) {
			$key = get_option('geoip-detect-auto_license_key', '');
			if (!$key) {
				return __('Error: Before updating, you need to enter a license key from maxmind.com.', 'geoip-detect');
			}
			$download_url = add_query_arg('license_key', $key, $download_url);
		}
		return $download_url;
	}

	protected function updateTreatError($tmpFile) {
		if(substr($tmpFile->get_error_message(), 0, 4) == '401:') {
			return __('Error: The license key is invalid. If you have created this license key just now, please wait for some minutes and try again.', 'geoip-detect');
		}
		return parent::updateTreatError($tmpFile);
	}


	public function getParameterHTML() {
		$key = esc_attr(get_option('geoip-detect-auto_license_key', ''));

		$label_key = __('License key:', 'geoip-detect');

		$html = <<<HTML
$label_key <input type="text" autocomplete="off" size="20" name="options_auto[license_key]" value="$key" /><br />
HTML;

		return $html;
	}

	protected function updateHTMLvalidation(&$error, &$disabled) {
		$keyAvailable = !! get_option('geoip-detect-auto_license_key', '');
		if (!$keyAvailable) {
			$error .= 
				__('Maxmind Automatic Download only works with a given license key.', 'geoip-detect') .
				'<p>' . sprintf(__('You can signup for a free Maxmind-Account here: <a href="%s" target="_blank">Sign Up</a>.', 'geoip-detect'), 'https://www.maxmind.com/en/geolite2/signup') . '<br>' .
				__('After logging in, generate a license key and copy it to the options below.', 'geoip-detect') . '</p>';
			$disabled = ' disabled="disabled"';
		} else {
			$keyValidationMessage = $this->validateApiKey(get_option('geoip-detect-auto_license_key', ''));
			if ($keyValidationMessage !== true) {
				$error .= $keyValidationMessage;
			}
		}
    }

	public function saveParameters($post) {
		$message = '';

		if (isset($post['options_auto']['license_key'])) {
			$key = sanitize_text_field($post['options_auto']['license_key']);
			$validationResult = $this->validateApiKey($key);
			if (\is_string($validationResult)) {
				$message .= $validationResult;
			}
			update_option('geoip-detect-auto_license_key', $key);
		}

		return $message;
	}

	public function validateApiKey($key) {
		$message = '';
		$key = trim($key);
		if (!$key) {
			return __('As the license key is missing, updates are disabled now.');
		}
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

}

geoip_detect2_register_source(new AutoDataSource());


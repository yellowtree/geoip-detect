<?php
/*
Copyright 2013-2023 Yellow Tree, Siegen, Germany
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

use YellowTree\GeoipDetect\DataSources\Manual\ManualDataSource;
use YellowTree\GeoipDetect\Logger;

define('GEOIP_DETECT_DATA_UPDATE_FILENAME', 'GeoLite2-City.mmdb');

class AutoDataSource extends ManualDataSource
{
	public function getId() { return 'auto'; }
	public function getLabel() { return __('Maxmind GeoIP Lite City (Automatic download & update)', 'geoip-detect'); }
	public function getShortLabel() { return sprintf(__('%s (updated weekly)', 'geoip-detect'), parent::getShortLabel()); }
	public function getDescriptionHTML() { 
		return __('(License: See <a href="https://www.maxmind.com/en/site-license-overview" target="_blank">Site Licence Overview</a> or <a href="https://www.maxmind.com/en/end-user-license-agreement" target="_blank">End User Licence Agreement</a>.)', 'geoip-detect'); }

	public function getStatusInformationHTML() {
		$html = parent::getStatusInformationHTML();

		$rescheduled = '';
		$next_cron_update = wp_next_scheduled( 'geoipdetectupdate' );
		if ($next_cron_update === false) {
			$rescheduled = ' ' . __('(Was rescheduled just now)', 'geoip-detect');
			$this->set_cron_schedule();
			$next_cron_update = wp_next_scheduled( 'geoipdetectupdate' );
		}
		$html .= '<br /><br />' . sprintf(__('Next database update: %s', 'geoip-detect'), geoip_detect_format_localtime($next_cron_update) );
		$html .= $rescheduled;

		$html .= $this->updateHTML();

		return $html;
	}

	public function getParameterHTML() {
		$html = $this->getParameterHTMLMaxmindAccount();

		return $html;
	}

	protected function updateHTML() {
		$html = $error = '';
		$disabled = '';

		$keyAvailable = !! get_option('geoip-detect-auto_license_key', '');
		if (!$keyAvailable) {
			$error .= 
				__('Maxmind Automatic Download only works with a given license key.', 'geoip-detect') .
				'<p>' . sprintf(__('You can signup for a free Maxmind-Account here: <a href="%s" target="_blank">Sign Up</a>.', 'geoip-detect'), 'https://www.maxmind.com/en/geolite2/signup') . '<br>' .
				__('After logging in, generate a license key and copy the Account ID and the key to the options below.', 'geoip-detect') . '</p>';
			$disabled = ' disabled="disabled"';
		} else {
			$keyValidationMessage = $this->validateApiKey(get_option('geoip-detect-auto_license_key', ''));
			if ($keyValidationMessage !== true) {
				$error .= $keyValidationMessage;
			}

			$idAvailable = get_option('geoip-detect-auto_license_id', '') > 0;
			if (!$idAvailable) {
				$error .= 
					__('Please add your Maxmind Account ID to the options. You find it in your Maxmind Account under "My License Key". This will enable the Maxmind Privacy Exclusions API.', 'geoip-detect');
			}
		}


		$text_update = __('Update now', 'geoip-detect');
		$nonce_field = wp_nonce_field( 'geoip_detect_update' );
		if (current_user_can('manage_options')) {
			$html .= <<<HTML
<form method="post" action="options-general.php?page=geoip-detect%2Fgeoip-detect.php">
		$nonce_field
		<input type="hidden" name="action" value="update" />
		<input type="submit" class="button button-secondary" value="$text_update" $disabled />
</form>
HTML;
		}

		if ($error) {
			$error = '<div class="geoip_detect_error" style="margin-top: 10px;">' . $error . '</div>';
		}

		return $html . $error;
	}


	public function __construct() {
		parent::__construct();
		add_action('geoipdetectupdate', [ $this, 'hook_cron' ], 10, 1);
		add_action('plugins_loaded', [ $this, 'on_plugins_loaded' ]);
	}

	public function on_plugins_loaded() {
		if (!defined('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED')) {
			define('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED', false);
		}
	}

	public function maxmindGetFilename() {
		$data_filename = $this->maxmindGetUploadFilename();
		if (!is_readable($data_filename) || !is_file($data_filename))
			$data_filename = '';

		/**
		 * @deprecated - use `geoip_detect_get_abs_mmdb_filename` instead
		 */
		$data_filename = apply_filters('geoip_detect_get_abs_db_filename', $data_filename);
		return $data_filename;
	}

	protected function maxmindGetUploadFilename() {
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'];

		$filename = $dir . '/' . GEOIP_DETECT_DATA_UPDATE_FILENAME;
		$filename = apply_filters('geoip_detect_get_abs_mmdb_filename', $filename);
		return $filename;
	}

	public function saveParameters($post) {
		$message = '';
		$message .= $this->saveParametersMaxmindAccount($post);
		return $message;
	}

	protected function download_url($url, $modified = 0) {
		// Similar to wordpress download_url, but with custom UA
		$url_filename = basename( parse_url( $url, PHP_URL_PATH ) );

		$tmpfname = wp_tempnam( $url_filename );
		if ( ! $tmpfname )
			return new \WP_Error('http_no_file', __('Could not create temporary file.', 'geoip-detect'));

		$headers = [];
		$headers['User-Agent'] = GEOIP_DETECT_USER_AGENT;
		if ($modified) {
			$headers['If-Modified-Since'] = date('r', $modified);
		}

		$response = wp_safe_remote_get( $url, [ 'timeout' => 300, 'stream' => true, 'filename' => $tmpfname, 'headers' => $headers  ] );
		$http_response_code = wp_remote_retrieve_response_code( $response );
		if (304 === $http_response_code) {
			return new \WP_Error( 'http_304', __('It has not changed since the last update.', 'geoip-detect') );
		}
		if (is_wp_error( $response ) || 200 !=  $http_response_code) {
			unlink($tmpfname);
			$body = wp_remote_retrieve_body($response);
			return new \WP_Error( 'http_404', $http_response_code . ': ' . trim( wp_remote_retrieve_response_message( $response ) ) . ' ' . $body );
		}

		return $tmpfname;
	}

	public function maxmindUpdate($forceUpdate = false)
	{
		require_once(ABSPATH.'/wp-admin/includes/file.php');

		$download_url = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&suffix=tar.gz';
		//$download_url = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&suffix=tar.gz';
		
		$download_url = apply_filters('geoip_detect2_download_url', $download_url);
		if (!\str_contains($download_url, 'license_key=')) {
			$key = get_option('geoip-detect-auto_license_key', '');
			if (!$key) {
				return __('Error: Before updating, you need to enter a license key from maxmind.com.', 'geoip-detect');
			}
			$download_url = add_query_arg('license_key', $key, $download_url);
		}

		$outFile = $this->maxmindGetUploadFilename();
		$modified = 0;
		if (\is_readable($outFile) && !$forceUpdate) {
			$modified = filemtime($outFile);
		} 

		// Check if existing download should be resumed
		$tmpFile = get_option('geoip-detect-auto_downloaded_file');
		if (!$tmpFile || !file_exists($tmpFile)) {
			// Download file
			$tmpFile = $this->download_url($download_url, $modified);
		}

		if (is_wp_error($tmpFile)) {
			if(substr($tmpFile->get_error_message(), 0, 4) == '401:') {
				return __('Error: The license key is invalid. If you have created this license key just now, please wait for some minutes and try again.', 'geoip-detect');
			}
			return $tmpFile->get_error_message();
		}
		update_option('geoip-detect-auto_downloaded_file', $tmpFile);

		// Unpack tar.gz
		$ret = $this->unpackArchive($tmpFile, $outFile);
		if (is_string($ret)) {
			return $ret;
		}

		if (!is_readable($outFile)) {
			return 'Error: Something went wrong: the unpacked file cannot be found.';
		}
		if (!is_file($outFile)) {
			return 'Error: Something went wrong: the unpacked file is a folder.';
		}

		update_option('geoip-detect-auto_downloaded_file', '');
		unlink($tmpFile);

		return true;
	}

	// Ungzip File
	protected function unpackArchive($downloadedFilename, $outFile) {
		if (!is_readable($downloadedFilename) || !is_file($downloadedFilename))
			return __('Downloaded file could not be opened for reading.', 'geoip-detect');
		if (!\is_writable(dirname($outFile)))
			return sprintf(__('Database could not be written (%s).', 'geoip-detect'), $outFile);

		$outDir = get_temp_dir() . 'geoip-detect/';

		global $wp_filesystem;
		if (! $wp_filesystem) {
			$ret = \WP_Filesystem(false, get_temp_dir(), true /* allow group/world-writeable folder */);
			if (!$ret) {
				return __('WP Filesystem could not be initialized (does not support FTP credential access. Can you upload files to the media library?).', 'geoip-detect');
			}
		}
		if (\is_dir($outDir)) {
			$wp_filesystem->rmdir($outDir, true);
		}

		mkdir($outDir);

		try {
			$phar = new \PharData( $downloadedFilename );
			$phar->extractTo($outDir, null, true);
		} catch(\Throwable $e) {
			// Fallback method of unpacking?
			unlink($downloadedFilename); // Do not try to unpack this file again, instead re-download
			return __('The downloaded file seems to be corrupt. Try again ...', 'geoip-detect');
		}


		$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($outDir));

		$inFile = '';
		foreach($files as $file) {
			if (!$file->isDir() && mb_substr($file->getFilename(), -5) == '.mmdb') {
				$inFile = $file->getPathname();
				break;
			}
		}

		if (!\is_readable($inFile) || !\is_file($inFile))
			return __('Downloaded file could not be opened for reading.', 'geoip-detect');
	
		$ret = copy($inFile, $outFile);
		if (!$ret)
			return sprintf(__('Downloaded file could not write or overwrite %s.', 'geoip-detect'), $outFile);

		$wp_filesystem->rmdir($outDir, true);

		return true;
	}

	public function hook_cron() {
		/**
		 * Filter:
		 * Cron has fired.
		 * Find out if file should be updated now.
		 *
		 * @param $do_it False if deactivated by define
		 * @param $immediately_after_activation True if this is fired because the plugin was recently activated (deprecated, will now always be false)
		 */
		$do_it = apply_filters('geoip_detect_cron_do_update', !GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED, false);

		$this->set_cron_schedule();

		if ($do_it) {
			$ret = $this->maxmindUpdate();
			Logger::logIfError($ret, 'cron');
		}
	}

	public function set_cron_schedule()
	{
		$next = wp_next_scheduled( 'geoipdetectupdate' );
		if ( $next === false ) {
			$this->schedule_next_cron_run();
		}
	}

	public function schedule_next_cron_run() {
		// Try to update every 1-2 weeks
		$next = time() + WEEK_IN_SECONDS;
		$next += mt_rand(1, WEEK_IN_SECONDS);

		wp_schedule_single_event($next, 'geoipdetectupdate');
	}

	public function activate() {
		parent::activate();
		$this->set_cron_schedule();
	}

	public function deactivate()
	{
		parent::deactivate();
		wp_clear_scheduled_hook('geoipdetectupdate');
	}

	public function uninstall() {
		// Delete the automatically downloaded file, if it exists
		$filename = $this->maxmindGetFilename();
		if ($filename) {
			unlink($filename);
		}
	}
}

/*
if (GEOIP_DETECT_DEBUG && !empty($_GET['test_auto_update_now'])) {
	add_filter('plugins_loaded', function() {
		if (current_user_can('manage_options')) {
			do_action('geoipdetectupdate');
		}
	}, 60);
};
*/

geoip_detect2_register_source(new AutoDataSource());


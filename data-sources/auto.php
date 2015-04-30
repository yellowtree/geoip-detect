<?php

namespace YellowTree\GeoipDetect\DataSources\Auto;

use YellowTree\GeoipDetect\DataSources\Manual\ManualDataSource;

define('GEOIP_DETECT_DATA_UPDATE_FILENAME', 'GeoLite2-City.mmdb');

class AutoDataSource extends ManualDataSource
{	
	public function getId() { return 'auto'; }
	public function getLabel() { return 'Automatic download & update of Maxmind GeoIP Lite City'; }
	public function getShortLabel() { return parent::getShortLabel() . ' (updated monthly)'; }
	public function getDescriptionHTML() { return '(License: Creative Commons Attribution-ShareAlike 3.0 Unported. See <a href="https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ#the-maxmind-lite-databases-are-licensed-creative-commons-sharealike-attribution-when-do-i-need-to-give-attribution" target="_blank">Licensing FAQ</a> for more details.)'; }

	public function getStatusInformationHTML() {
		$html = parent::getStatusInformationHTML();
		$date_format = get_option('date_format') . ' ' . get_option('time_format');
		
		$next_cron_update = wp_next_scheduled( 'geoipdetectupdate' );
		$html .= '<br />' . sprintf(__('Next update: %s', 'geoip-detect'), $next_cron_update ? date_i18n($date_format, $next_cron_update) : __('Never', 'geoip-detect'));
		
		return $html;
	}

	public function getParameterHTML() {
		$text_update = __('Update now', 'geoip-detect');
		$html = <<<HTML
<form method="post" action="#">
		<input type="hidden" name="action" value="update" />
		<input type="submit" class="button button-secondary" value="$text_update" />
</form>
HTML;
		return $html;
	}
	
	public function saveParameters($post) {}
	
	public function __construct() {
		parent::__construct();
		add_action('geoipdetectupdate', array($this, 'hook_cron'), 10, 1);
		add_action('plugins_loaded', array($this, 'on_plugins_loaded'));
	}
	
	public function on_plugins_loaded() {
		if (!defined('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED'))
			define('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED', false);
	}
	
	public function maxmindGetFilename() {
		$data_filename = $this->maxmindGetUploadFilename();
		if (!is_readable($data_filename))
			$data_filename = '';
		
		$data_filename = apply_filters('geoip_detect_get_abs_db_filename', $data_filename);
		return $data_filename;
	}
	
	protected function maxmindGetUploadFilename() {
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'];
		
		$filename = $dir . '/' . GEOIP_DETECT_DATA_UPDATE_FILENAME;
		return $filename;
	}
	
	public function maxmindUpdate()
	{
		require_once(ABSPATH.'/wp-admin/includes/file.php');
		
		$download_url = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';
		$download_url = apply_filters('geoip_detect2_download_url', $download_url);
	
		$outFile = $this->maxmindGetUploadFilename();
	
		// Download
		$tmpFile = download_url($download_url);
		if (is_wp_error($tmpFile))
			return $tmpFile->get_error_message();
	
		// Ungzip File
		$zh = gzopen($tmpFile, 'r');
		$h = fopen($outFile, 'w');
	
		if (!$zh)
			return __('Downloaded file could not be opened for reading.', 'geoip-detect');
		if (!$h)
			return sprintf(__('Database could not be written (%s).', 'geoip-detect'), $outFile);
	
		while ( ($string = gzread($zh, 4096)) != false )
			fwrite($h, $string, strlen($string));
	
		gzclose($zh);
		fclose($h);
	
		unlink($tmpFile);
	
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
	
		if ($do_it)
			$this->maxmindUpdate();
	
		$this->schedule_next_cron_run();
	}
	
	public function set_cron_schedule()
	{
		$next = wp_next_scheduled( 'geoipdetectupdate' );
		if ( !$next ) {
			$this->schedule_next_cron_run();
		}
	}
	
	public function schedule_next_cron_run() {
		// The Lite databases are updated on the first tuesday of each month. Maybe not at midnight, so we schedule it for the night afterwards.
		$next = strtotime('first tuesday of next month + 1 day');
		wp_schedule_single_event($next, 'geoipdetectupdate');
	}
	
	public function activate() {
		$this->set_cron_schedule();
	}
		
	public function deactivate()
	{
		wp_clear_scheduled_hook('geoipdetectupdate');
	}
}

geoip_detect2_register_source(new AutoDataSource());
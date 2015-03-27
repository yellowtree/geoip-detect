<?php
namespace YellowTree\GeoipDetect\DataSources\Auto;

use YellowTree\GeoipDetect\DataSources\AbstractDataSource;
use YellowTree\GeoipDetect\DataSources\Manual\ManualDataSource;


define('GEOIP_DETECT_DATA__UPDATE_FILENAME', 'GeoLite2-City.mmdb');

class AutoDataSource extends ManualDataSource
{	
	public function getId() { return 'auto'; }
	public function getLabel() { return 'Automatic download & update of Maxmind GeoIP Lite City'; }
	
	public function getDescriptionHTML() { return '(License: Creative Commons Attribution-ShareAlike 3.0 Unported. See <a href="https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ#the-maxmind-lite-databases-are-licensed-creative-commons-sharealike-attribution-when-do-i-need-to-give-attribution" target="_blank">Licensing FAQ</a> for more details.)'; }
	public function getParameterHTML() { return ''; }
	
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
		
		$filename = $dir . '/' . GEOIP_DETECT_DATA__UPDATE_FILENAME;
		return $filename;
	}
	
	public function maxmindUpdate()
	{
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
}

\YellowTree\GeoipDetect\DataSources\DataSourceRegistry::getInstance()->register(new AutoDataSource());

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

define('GEOIP_DETECT_DATA_UPDATE_FILENAME_DB_IP', 'GeoLite2-City.mmdb');

require_once(__DIR__ . '/_mmdb.php');

class DbIpDataSource extends AbstractMmdbDataSource {

    public function getId() { return 'dp-ip'; }
	public function getLabel() { return __('Automatic download & update of DB-IP IP to City Lite', 'geoip-detect'); }
	public function getShortLabel() { return sprintf(__('DB-IP IP to City Lite (updated monthly)', 'geoip-detect'), parent::getShortLabel()); }
	public function getDescriptionHTML() { 
		return __('(License: <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">Creative Commons Attribution 4.0 International License</a>. You must include a link back to DB-IP.com on pages that display or use results from the database, see <a href="https://db-ip.com/db/download/ip-to-city-lite">Download page</a>)', 'geoip-detect'); }

	protected function maxmindGetUploadFilename() {
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'];

		$filename = $dir . '/DB-IP-City.mmdb';
		return $filename;
	}

	protected function getDownloadUrl() {
		$today = new \DateTime;
		$download_url = 'https://download.db-ip.com/free/dbip-city-lite-' . $today->format('Y-m') . '.mmdb.gz';
		
		$download_url = apply_filters('geoip_detect2_db-ip_download_url', $download_url);
		return $download_url;
	}

	public function getParameterHTML() {
		return '';
	}

	// Todo schedule begin of month
}

geoip_detect2_register_source(new DbIpDataSource());
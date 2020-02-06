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
	public function getLabel() { return __('Automatic download & update of dbip IP to City Lite', 'geoip-detect'); }
	public function getShortLabel() { return sprintf(__('%s (updated monthly)', 'geoip-detect'), parent::getShortLabel()); }
	public function getDescriptionHTML() { 
		return __('(License: See <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">Creative Commons Attribution 4.0 International License</a>. You must include a link back to DB-IP.com on pages that display or use results from the database.)', 'geoip-detect'); }

	protected function maxmindGetUploadFilename() {
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'];

		$filename = $dir . '/DB-IP-City.mmdb';
		return $filename;
	}
}
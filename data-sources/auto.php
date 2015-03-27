<?php
/* extends manual */
define('GEOIP_DETECT_DATA__UPDATE_FILENAME', 'GeoLite2-City.mmdb');


function geoip_detect_get_database_upload_filename()
{
	$upload_dir = wp_upload_dir();
	$dir = $upload_dir['basedir'];

	$filename = $dir . '/' . GEOIP_DETECT_DATA__UPDATE_FILENAME;
	return $filename;
}


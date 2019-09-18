<?php

if (!defined('GEOIP_PLUGIN_DIR')) {
    define('GEOIP_PLUGIN_DIR', dirname(dirname(dirname(__DIR__))));
}

function output_to_stderr($text) {
	fwrite(STDERR, $text);
}

function array_to_php($data) {
	$date_now = date('r');
	$data = var_export($data, true);

	$file = <<<PHP
<?php
// Generated at {$date_now} 
return $data;
PHP;
	$data = '';
	return $file;
}
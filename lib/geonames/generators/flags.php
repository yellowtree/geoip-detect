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

// $ yarn install
// Usage: $ php lib/geonames/generators/flags.php
// Requires PHP 7.0

if (php_sapi_name() != "cli")
	die('This can only be run from command line.');

require_once(__DIR__ . '/helper.php');

define('FLAG_FILE', GEOIP_PLUGIN_DIR . '/node_modules/emoji-flags/data.json');
define('OUTPUT_FILE', __DIR__ . '/../data/country-flags.php');

if (!file_exists(FLAG_FILE)) {
    die('Flag data ' . FLAG_FILE . ' does not exist. Did you do "yarn install" (or "npm install") in the plugin directory first?' . "\n");
}

$data = json_decode(file_get_contents(FLAG_FILE), true);

$ret = [];
foreach($data as $country) {
    $id = $country['code'];
    var_dump($country);
    $ret[$id] = [
        'emoji' => $country['emoji'],
        'tel' => $country['dialCode'] ?? ''
    ];
}

file_put_contents(OUTPUT_FILE, array_to_php($ret));

output_to_stderr('OK.' . PHP_EOL);


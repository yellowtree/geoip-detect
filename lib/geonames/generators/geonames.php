<?php
/*
Copyright 2013-2019 Yellow Tree, Siegen, Germany
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

// Usage: $ php lib/geonames/generators/geonames.php {api_username}
// Requires PHP 5.4

if (php_sapi_name() != "cli")
	die('This can only be run from command line.');

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/helper.php');

define('OUTPUT_FILE_NAMES', __DIR__ . '/../data/country-names.php');
define('OUTPUT_FILE_INFO', __DIR__ . '/../data/country-info.php');

$username = @$argv[1];
if (!$username)
	die("1st parameter missing: You need to get a free geonames.org-User here: http://www.geonames.org/login\n");

// List of languages that the Maxmind Database support
$langs = ['en', 'de', 'it', 'es', 'fr', 'ja', 'pt-BR', 'ru', 'zh-CN'];

$lang_geonames = array_combine($langs, $langs);
$lang_geonames['pt-BR'] = 'pt';
$lang_geonames['zh-CN'] = 'zh';
//$langs = ['en', 'de'];

$continents = [];
$all_records = [];
output_to_stderr("Getting Country Information from geonames.org with API username " . $username . ":" . PHP_EOL);
foreach ($lang_geonames as $lang_maxmind => $lang_geoname) {
	// Load country information of all countries
	$client = new \spacedealer\geonames\api\Geonames($username, $lang_geoname);

	try {
		$records = [];
		$response = $client->countryInfo();

		if ($response->isOk()) {
			$count = $response->count();
			output_to_stderr("Lang " . $lang_geoname . ": Found countries: $count" . PHP_EOL);
			
			foreach ($response as $row) {
				$r = [];
				$id = $row['countryCode'];
				if (!$id)
					continue;
				
				// Country data
				$r['country']['iso_code'] = $id;
				if (!empty($row['isoAlpha3']))
					$r['country']['iso_code3'] = $row['isoAlpha3'];
				if (!empty($row['geonameId']))
					$r['country']['geoname_id'] = $row['geonameId'];
				if (!empty($row['countryName']))
					$r['country']['names'][$lang_maxmind] = $row['countryName'];
				
				// Continent data
				if ($row['continent'])
					$r['continent'] = $row['continent'];
				if ($row['continentName']) {
					$continents[$row['continent']]['code'] = $row['continent'];
					$continents[$row['continent']]['names'][$lang_maxmind] = $row['continentName'];
				}
				
				// Special country data
				if (isset($row['north']) && isset($row['south']))
					$r['location']['latitude'] = ($row['north'] + $row['south']) / 2.0;
				if (isset($row['west']) && isset($row['east']))
					$r['location']['longitude'] = ($row['west'] + $row['east']) / 2.0;
				
				$records[$id] = $r;
			}
		} else {
			output_to_stderr('Fehler: ' . $response['message'] . PHP_EOL);
		}
	} catch (\RuntimeException $e) {
		output_to_stderr('Fehler:' . $e->getMessage() . PHP_EOL);
	}
	
	// Merge the languages together
	$all_records = array_replace_recursive($all_records, $records);
}

ksort($all_records);


output_to_stderr("Writing country-info.php...");
file_put_contents(OUTPUT_FILE_INFO, array_to_php(['countries' => $all_records, 'continents' => $continents]));
output_to_stderr('OK.' . PHP_EOL);


output_to_stderr('Writing country-names.php...');

$all_names = [];
foreach ($all_records as $id => $r) {
	foreach ($lang_geonames as $lang_maxmind => $lang_geoname) {
		if (!empty( $r['country']['names'][$lang_geoname]))
			$all_names[$lang_geoname][$id] = $r['country']['names'][$lang_geoname];
	}
}
// Sort by label, not by ISO Code
foreach ($all_names as $lang_maxmind => $names) {
	asort($all_names[$lang_maxmind]);
}

file_put_contents(OUTPUT_FILE_NAMES, array_to_php($all_names));

output_to_stderr('OK.' . PHP_EOL);


/* Takes up as much memory (around 700kb)
$getDataOfCountrySwitchCases = '';
foreach ($all_records as $id => $r) {
	$id_exported = var_export($id, true);
	$r_exported = var_export($r, true);
	$getDataOfCountrySwitchCases .= "case $id_exported: return $r_exported;" . PHP_EOL;
}

echo <<<'PHP'
namespace YellowTree\GeoipDetect\Geonames;

if (!class_exists('CountryInformationData')) {
	class CountryInformationData {
		public function getDataOfCountry($country) {
			switch($country) {
			
PHP;
echo $getDataOfCountrySwitchCases;
echo <<<'PHP'
			}
			return ''; // Country not found
		}
	}


PHP;
*/
output_to_stderr("Done. You should now run 'phpunit' now to see if the file data is valid." . PHP_EOL);

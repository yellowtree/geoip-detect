<?php
// Usage: $ php lib/geonames/generators/geonames.php api_username lib/geonames/data
if (php_sapi_name() != "cli")
	die('This can only be run from command line.');

function output_to_stderr($text) {
	fwrite(STDERR, $text);
}

require_once(__DIR__ . '/vendor/autoload.php');

$username = @$argv[1];
if (!$username)
	die('1st parameter missing: You need to get a free geonames.org-User here: http://www.geonames.org/login');

$output_dir = @$argv[2];
if (!$output_dir)
	$output_dir = '.';
if (!is_dir($output_dir))
	die('2nd parameter: You need to specify an existing directory');

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
				if ($row['geonameId'])
					$r['country']['geoname_id'] = $row['geonameId'];
				if ($row['countryName'])
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

function geonames_array_to_php($data) {
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

output_to_stderr("Writing country-info.php...");
file_put_contents($output_dir . '/country-info.php', geonames_array_to_php(['countries' => $all_records, 'continents' => $continents]));
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

file_put_contents($output_dir . '/country-names.php', geonames_array_to_php($all_names));

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
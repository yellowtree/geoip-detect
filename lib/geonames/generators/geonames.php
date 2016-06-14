<?php

if (php_sapi_name() != "cli")
	die('This can only be run from command line.');

require_once(__DIR__ . '/vendor/autoload.php');

$username = 't3dgewp';

// List of languages that the Maxmind Database support
$langs = ['en', 'de', 'it'];

$client = new \spacedealer\geonames\api\Geonames($username, 'de');
try {
    $response = $client->countryInfo();

    if ($response->isOk()) {
        $count = $response->count();
        echo "Found entries: $count" . PHP_EOL;
        $placeName = $response->getPath('0/placeName'); // TODO geht noch nicht
        echo "Place name   : " . $placeName . PHP_EOL;
    } else {
        echo $response->getPath('message') . PHP_EOL;
    }
} catch (\RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}
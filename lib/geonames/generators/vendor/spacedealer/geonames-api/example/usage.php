<?php
/**
 * usage.php file.
 *
 * @author Dirk Adler <adler@spacedealer.de>
 * @link http://www.spacedealer.de
 * @copyright Copyright &copy; 2014 spacedealer GmbH
 */

require __DIR__ . '/../vendor/autoload.php';

$client = new \spacedealer\geonames\api\Geonames('your_username');
try {
    $response = $client->postalCodeSearch([
        'postalcode' => '10997',
        'country' => 'de',
    ]);

    if ($response->isOk()) {
        $count = $response->count();
        echo "Found entries: $count" . PHP_EOL;
        $placeName = $response->getPath('0/placeName');
        echo "Place name   : " . $placeName . PHP_EOL;
    } else {
        echo $response->getPath('message') . PHP_EOL;
    }
} catch (\RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}



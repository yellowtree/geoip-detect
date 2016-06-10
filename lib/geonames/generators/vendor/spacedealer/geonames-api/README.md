# Geonames.org REST api client PHP library

**Attention**: Please do not use in production environments. It‘s WIP.

This is a simple [geonames API](http://www.geonames.org/export/web-services.html) client based on [Guzzle 5](http://docs.guzzlephp.org/en/latest/).

[![Build Status](https://travis-ci.org/spacedealer/geonames-api.svg)](https://travis-ci.org/spacedealer/geonames-api)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/131220d9-7a2d-41c2-aa28-f08a7c89bcff/mini.png)](https://insight.sensiolabs.com/projects/131220d9-7a2d-41c2-aa28-f08a7c89bcff)
[![Dependency Status](https://www.versioneye.com/user/projects/54748bd898b2e85e67000142/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54748bd898b2e85e67000142)

## Requirements

 - php >= 5.4
 - guzzle 5
 - guzzle services 0.5
 - guzzle command 0.7
 
## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist spacedealer/geonames-api "*"
```

or add

```
"spacedealer/geonames-api": "*"
```

to the require section of your `composer.json` file.

## Usage

```php
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
```
## Todos

 - complete unit tests
 - improve response model handling
 - add support for xml only command: extendedFindNearby

## Resources

 - [Source](https://github.com/spacedealer/geonames-api)
 - [Issues](https://github.com/spacedealer/geonames-api/issues)

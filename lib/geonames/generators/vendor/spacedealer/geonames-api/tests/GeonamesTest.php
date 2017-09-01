<?php
/**
 * GeonamesTest.php file.
 *
 * @author Dirk Adler <adler@spacedealer.de>
 * @link http://www.spacedealer.de
 * @copyright Copyright &copy; 2014 spacedealer GmbH
 */

namespace spacedealer\tests\geonames\api;

use GuzzleHttp\Subscriber\Mock;
use spacedealer\geonames\api\Geonames;

/**
 * Class GeonamesTest
 */
class GeonamesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string Official demo user name
     */
    public $username = 'demo';

    /**
     * @dataProvider dataProvider
     * \spacedealer\geonames\api\Response $response
     */
    public function testCommands($command, $params, $responseFile = null)
    {
        // init api client class
        $client = new Geonames($this->username);

        // load mock response data
        if (!$responseFile) {
            $responseFile = 'geonames-' . $command . '.txt';
        }
        $mockResponse = file_get_contents(__DIR__ . '/responses/' . $responseFile);

        // create mock response
        $mock = new Mock([
            $mockResponse,
        ]);

        // add the mock subscriber to the client
        $client->getHttpClient()->getEmitter()->attach($mock);

        // add history
        // $client->getHttpClient()->getEmitter()->attach($history = new History());

        // execute request
        $response = $client->$command($params);

        $this->assertTrue($response->isOk());
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'postalCodeSearch',
                [
                    'postalcode' => '10997',
                    'country' => 'de',
                ]
            ]
        ];
    }

    public function testBaseUrl()
    {
        // init api client class
        $client = new Geonames($this->username, 'en', null);
        $this->assertEquals('http://api.geonames.org/', (string)$client->getDescription()->getBaseUrl());

        // init api client class
        $client = new Geonames($this->username, 'en', '');
        $this->assertEquals('', (string)$client->getDescription()->getBaseUrl());

        // init api client class
        $client = new Geonames($this->username, 'en', 'https://example.com');
        $this->assertEquals('https://example.com', (string)$client->getDescription()->getBaseUrl());
    }
}

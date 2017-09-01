<?php
/**
 * Geonames.php file.
 *
 * @author Dirk Adler <adler@spacedealer.de>
 * @link http://www.spacedealer.de
 * @copyright Copyright &copy; 2014 spacedealer GmbH
 */

namespace spacedealer\geonames\api;

use GuzzleHttp\Client;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;

/**
 * Class Geonames
 *
 * @method Response astergdem() astergdem(array $params)
 * @method Response children() children(array $params)
 * @method Response cities() cities(array $params)
 * @method Response countryCode() countryCode(array $params)
 * @method Response countryInfo() countryInfo(array $params)
 * @method Response countrySubdivision() countrySubdivision(array $params)
 * @method Response earthquakes() earthquakes(array $params)
 * @method Response findNearby() findNearby(array $params)
 * @method Response findNearbyPlaceName() findNearbyPlaceName(array $params)
 * @method Response findNearbyPostalCodes() findNearbyPostalCodes(array $params)
 * @method Response findNearbyStreets() findNearbyStreets(array $params)
 * @method Response findNearbyStreetsOSM() findNearbyStreetsOSM(array $params)
 * @method Response findNearByWeather() findNearByWeather(array $params)
 * @method Response findNearbyWikipedia() findNearbyWikipedia(array $params)
 * @method Response findNearestAddress() findNearestAddress(array $params)
 * @method Response findNearestIntersection() findNearestIntersection(array $params)
 * @method Response findNearestIntersectionOSM() findNearestIntersectionOSM(array $params)
 * @method Response get() get(array $params)
 * @method Response gtopo30() gtopo30(array $params)
 * @method Response hierarchy() hierarchy(array $params)
 * @method Response neighbourhoud() neighbourhoud(array $params)
 * @method Response neighbours() neighbours(array $params)
 * @method Response postalCodeCountryInfo() postalCodeCountryInfo(array $params)
 * @method Response postalCodeLookup() postalCodeLookup(array $params)
 * @method Response postalCodeSearch() postalCodeSearch(array $params)
 * @method Response search() search(array $params)
 * @method Response siblings() siblings(array $params)
 * @method Response srtm3() srtm3(array $params)
 * @method Response timezone() timezone(array $params)
 * @method Response weather() weather(array $params)
 * @method Response weatherIcao() weatherIcao(array $params)
 * @method Response wikipediaBoundingBox() wikipediaBoundingBox(array $params)
 * @method Response wikipediaSearch() wikipediaSearch(array $params)
 *
 * Not support: Response extendedFindNearby() extendedFindNearby(array $params)
 *
 * @package spacedealer\geonames\api
 */
class Geonames extends GuzzleClient
{
    /**
     * @param string $username registered username
     * @param string $lang language code for responses
     * @param string|null $baseUrl override default base url
     */
    public function __construct($username, $lang = 'en', $baseUrl = null)
    {
        $client = new Client();

        // load description config file
        $descriptionConfig = require(__DIR__ . '/resources/config.php');

        // overwrite default base url
        if ($baseUrl !== null) {
            $descriptionConfig['baseUrl'] = $baseUrl;
        }

        // create description
        $description = new Description($descriptionConfig);

        // init client and set default values
        parent::__construct($client, $description, [
            'defaults' => [
                'username' => $username,
                'lang' => $lang,
            ]
        ]);
    }

    /**
     * @param CommandInterface $command
     * @return Response
     */
    public function execute(CommandInterface $command)
    {
        $result = parent::execute($command);
        return new Response($result ?: []);
    }
} 

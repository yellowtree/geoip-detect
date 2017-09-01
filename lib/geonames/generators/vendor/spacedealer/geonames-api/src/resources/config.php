<?php
/**
 * config.php file.
 *
 * @author Dirk Adler <adler@spacedealer.de>
 * @link http://www.spacedealer.de
 * @copyright Copyright &copy; 2014 spacedealer GmbH
 */
return [
    'baseUrl' => 'http://api.geonames.org/',
    'operations' => [
        // Elevation - Aster Global Digital Elevation Model
        // Info: http://www.geonames.org/export/web-services.html#astergdem
        // Expected Results: view-source:http://api.geonames.org/astergdemJSON?lat=50.01&lng=10.2&username=demo
        'astergdem' => [
            'httpMethod' => 'GET',
            'uri' => 'astergdemJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'float',
                    'location' => 'query'
                ],
                'lng' => [
                    'type' => 'float',
                    'location' => 'query'
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Returns the children (admin divisions and populated places) for a given geonameId
        // Info: http://www.geonames.org/export/place-hierarchy.html#children
        // Expected Results: view-source:http://api.geonames.org/children?geonameId=3175395&username=demo
        'children' => [
            'httpMethod' => 'GET',
            'uri' => 'childrenJSON',
            'responseModel' => 'default',
            'parameters' => [
                'geonameId' => [
                    'type' => 'integer',
                    'location' => 'query',
                    'required' => true,
                ],
                'maxRows' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'hierarchy' => [
                    'type' => 'string',
                    'location' => 'query'
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // returns a list of cities and placenames in the bounding box, ordered by relevancy (capital/population)
        // Info: http://www.geonames.org/export/JSON-webservices.html#citiesJSON
        // Expected Results: view-source:http://api.geonames.org/citiesJSON?north=44.1&south=-9.9&east=-22.4&west=55.2&lang=de&username=demo
        'cities' => [
            'httpMethod' => 'GET',
            'uri' => 'citiesJSON',
            'responseModel' => 'default',
            'parameters' => [
                'north' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'south' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'east' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'west' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the iso country code for the given latitude/longitude
        // Info: http://www.geonames.org/export/web-services.html#countrycode
        // Expected Results: view-source:http://api.geonames.org/countryCodeJSON?formatted=true&lat=47.03&lng=10.2&username=demo&style=full
        'countryCode' => [
            'httpMethod' => 'GET',
            'uri' => 'countryCodeJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : Country information : Capital, Population, Area in square km, Bounding Box of mainland (excluding offshore islands)
        // Info: http://www.geonames.org/export/web-services.html#countryInfo
        // Expected Results: view-source:http://api.geonames.org/countryInfoJSON?formatted=true&lang=it&country=DE&username=demo&style=full
        'countryInfo' => [
            'httpMethod' => 'GET',
            'uri' => 'countryInfoJSON',
            'responseModel' => 'default',
            'parameters' => [
                'country' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the country and the administrative subdivison (state, province,...) for the given latitude/longitude
        // Info: http://www.geonames.org/export/web-services.html#countrysubdiv
        // Expected Results: view-source:http://api.geonames.org/countrySubdivisionJSON?formatted=true&lat=47.03&lng=10.2&username=demo&style=full
        'countrySubdivision' => [
            'httpMethod' => 'GET',
            'uri' => 'countrySubdivisionJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'level' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns a list of earthquakes, ordered by magnitude
        // Info: http://www.geonames.org/export/JSON-webservices.html#earthquakesJSON
        // Expected Results: view-source:http://api.geonames.org/earthquakesJSON?formatted=true&north=44.1&south=-9.9&east=-22.4&west=55.2&username=demo&style=full
        'earthquakes' => [
            'httpMethod' => 'GET',
            'uri' => 'earthquakesJSON',
            'responseModel' => 'default',
            'parameters' => [
                'north' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'south' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'east' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'west' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'minMagnitude' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'date' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the closest toponym for the lat/lng query as xml document
        // Info: http://www.geonames.org/export/web-services.html#findNearby
        // Expected Results: view-source:http://api.geonames.org/findNearbyJSON?formatted=true&lat=48.865618158309374&lng=2.344207763671875&fclass=P&fcode=PPLA&fcode=PPL&fcode=PPLC&username=demo&style=full
        'findNearby' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearbyJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'featureClass' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'featureCode' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'style' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'localCountry' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the closest populated place for the lat/lng query as xml document. The unit of the distance element is 'km'.
        // Info: http://www.geonames.org/export/web-services.html#findNearbyPlaceName
        // Expected Results: view-source:http://api.geonames.org/findNearbyPlaceNameJSON?formatted=true&lat=47.3&lng=9&username=demo&style=full
        'findNearbyPlaceName' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearbyPlaceNameJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'style' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'localCountry' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns a list of postalcodes and places for the lat/lng query as xml document.
        // Info: http://www.geonames.org/export/web-services.html#findNearbyPostalCodes
        // Expected Results: view-source:http://api.geonames.org/findNearbyPostalCodesJSON?formatted=true&postalcode=8775&country=CH&radius=10&username=demo&style=full
        'findNearbyPostalCodes' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearbyPostalCodesJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'style' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'localCountry' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'country' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'postalcode' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // US Only
        // Result : returns the nearest street segments for the given latitude/longitude
        // Info: http://www.geonames.org/maps/us-reverse-geocoder.html#findNearbyStreets
        // Expected Results: view-source:http://api.geonames.org/findNearbyStreetsJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearbyStreets' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearbyStreetsJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the nearest street segments for the given latitude/longitude
        // Info: http://www.geonames.org/maps/osm-reverse-geocoder.html#findNearbyStreetsOSM
        // Expected Results: view-source:http://api.geonames.org/findNearbyStreetsOSMJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearbyStreetsOSM' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearbyStreetsOSMJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns a weather station with the most recent weather observation
        // Info: http://www.geonames.org/export/JSON-webservices.html#findNearByWeatherJSON
        // Expected Results
        'findNearByWeather' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearByWeatherJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns a list of wikipedia entries
        // Info: http://www.geonames.org/export/wikipedia-webservice.html#findNearbyWikipedia
        // Expected Resuls: view-source:http://api.geonames.org/findNearbyWikipediaJSON?formatted=true&lat=47&lng=9&username=demo&style=full
        'findNearbyWikipedia' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearbyWikipediaJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'postalcode' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'country' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // US Only
        // Result : returns the nearest address for the given latitude/longitude, the street number is an 'educated guess' using an interpolation of street number at the end of a street segment.
        // Info: http://www.geonames.org/maps/us-reverse-geocoder.html#findNearestAddress
        // Expected Results: view-source:http://api.geonames.org/findNearestAddressJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearestAddress' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearestAddressJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // US Only
        // Result : returns the nearest intersection for the given latitude/longitude
        // Info: http://www.geonames.org/maps/us-reverse-geocoder.html#findNearestIntersection
        // Expected Results: view-source:http://api.geonames.org/findNearestIntersectionJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearestIntersection' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearestIntersectionJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the nearest intersection for the given latitude/longitude
        // Info: http://www.geonames.org/maps/osm-reverse-geocoder.html#findNearestIntersectionOSM
        // Expected Results: view-source:http://api.geonames.org/findNearestIntersectionOSMJSON?formatted=true&lat=37.451&lng=-122.18&username=demo&style=full
        'findNearestIntersectionOSM' => [
            'httpMethod' => 'GET',
            'uri' => 'findNearestIntersectionOSMJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Returns : geoname information for the given geonameId
        // Info: none
        // Expected Results: view-source:http://api.geonames.org/getJSON?formatted=true&geonameId=6295610&username=demo&style=full
        'get' => [
            'httpMethod' => 'GET',
            'uri' => 'getJSON',
            'responseModel' => 'default',
            'parameters' => [
                'geonameId' => [
                    'type' => 'integer',
                    'location' => 'query',
                    'required' => true,
                ],
                'style' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // GTOPO30 is a global digital elevation model (DEM) with a horizontal grid spacing of 30 arc seconds (approximately 1 kilometer). GTOPO30 was derived from several raster and vector sources of topographic information
        // Info: http://www.geonames.org/export/web-services.html#gtopo30
        // Expected Results: view-source:http://api.geonames.org/gtopo30JSON?formatted=true&lat=47.01&lng=10.2&username=demo&style=full
        'gtopo30' => [
            'httpMethod' => 'GET',
            'uri' => 'gtopo30JSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns a list of GeoName records, ordered by hierarchy level. The top hierarchy (continent) is the first element in the list
        // Info: http://www.geonames.org/export/place-hierarchy.html#hierarchy
        // Expected Results: view-source:http://api.geonames.org/hierarchyJSON?formatted=true&geonameId=2657896&username=demo&style=full
        'hierarchy' => [
            'httpMethod' => 'GET',
            'uri' => 'hierarchyJSON',
            'responseModel' => 'default',
            'parameters' => [
                'geonameId' => [
                    'type' => 'integer',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // US Only
        // Result : returns the neighbourhood for the given latitude/longitude
        // Info: http://www.geonames.org/export/web-services.html#neighbourhood
        // Expected Results: view-source:http://api.geonames.org/neighbourhoodJSON?formatted=true&lat=40.78343&lng=-73.96625&username=demo&style=full
        'neighbourhoud' => [
            'httpMethod' => 'GET',
            'uri' => 'neighbourhoodJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the neighbours of a toponym, currently only implemented for countries
        // Info: http://www.geonames.org/export/place-hierarchy.html#neighbours
        // Expected Results: view-source:http://api.geonames.org/neighboursJSON?formatted=true&geonameId=2658434&username=demo&style=full
        'neighbours' => [
            'httpMethod' => 'GET',
            'uri' => 'neighboursJSON',
            'responseModel' => 'default',
            'parameters' => [
                'geonameId' => [
                    'type' => 'integer',
                    'location' => 'query',
                    'required' => true,
                ],
                'country' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the ocean or sea for the given latitude/longitude
        // Info: http://www.geonames.org/export/web-services.html#ocean
        // Expected Results: view-source:http://api.geonames.org/oceanJSON?formatted=true&lat=40.78343&lng=-43.96625&username=demo&style=full
        'ocean' => [
            'httpMethod' => 'GET',
            'uri' => 'neighbourhoodJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : countries for which postal code geocoding is available.
        // Info: http://www.geonames.org/export/web-services.html#postalCodeCountryInfo
        // Expected Results: view-source:http://api.geonames.org/postalCodeCountryInfoJSON?formatted=true&&username=demo&style=full
        'postalCodeCountryInfo' => [
            'httpMethod' => 'GET',
            'uri' => 'postalCodeCountryInfoJSON',
            'responseModel' => 'default',
            'parameters' => [
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns a list of places for the given postalcode in JSON format
        // Info: /web-services.html#postalCodeLookupJSON
        //  postalcode,country ,maxRows (default = 20),callback, charset (default = UTF-8)
        // Expected Results: view-source:http://api.geonames.org/postalCodeLookupJSON?formatted=true&postalcode=6600&country=AT&username=demo&style=full
        'postalCodeLookup' => [
            'httpMethod' => 'GET',
            'uri' => 'postalCodeLookupJSON',
            'responseModel' => 'default',
            'parameters' => [
                'postalcode' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'country' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'charset' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns a list of postal codes and places for the placename/postalcode query as xml document
        // Info: http://www.geonames.org/export/web-services.html#postalCodeSearch
        // Expected Results: view-source:http://api.geonames.org/postalCodeSearchJSON?formatted=true&postalcode=9011&maxRows=10&username=demo&style=full
        'postalCodeSearch' => [
            'httpMethod' => 'GET',
            'uri' => 'postalCodeSearchJSON',
            'responseModel' => 'default',
            'parameters' => [
                'postalcode' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'postalcode_startsWith' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'placename' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'placename_startsWith' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'country' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'countryBias' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'style' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'operator' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'charset' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'isReduced' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the names found for the searchterm as xml or json document, the search is using an AND operator
        // Info: http://www.geonames.org/export/geonames-search.html
        // Expected Results: view-source:http://api.geonames.org/searchJSON?formatted=true&q=london&maxRows=10&lang=es&username=demo&style=full
        'search' => [
            'httpMethod' => 'GET',
            'uri' => 'searchJSON',
            'responseModel' => 'default',
            'parameters' => [
                'q' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'name' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'name_startsWith' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'name_equals' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'country' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'countryBias' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'continentCode' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'adminCode1' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'adminCode2' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'adminCode3' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'featureClass' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'featureCode' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'cities' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'startRow' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'style' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'tag' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'operator' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'charset' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'fuzzy' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'east' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'west' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'north' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'south' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'isNameRequired' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'searchlang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'orderby' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : Returns all siblings of a GeoNames toponym.
        // Info: http://www.geonames.org/export/place-hierarchy.html#siblings
        // Expected Results: view-source:http://api.geonames.org/siblingsJSON?formatted=true&geonameId=3017382&username=demo&style=full
        'siblings' => [
            'httpMethod' => 'GET',
            'uri' => 'siblingsJSON',
            'responseModel' => 'default',
            'parameters' => [
                'geonameId' => [
                    'type' => 'integer',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : This web service is using Shuttle Radar Topography Mission (SRTM) data with data points located every 3-arc-second (approximately 90 meters) on a latitude/longitude grid
        // Info: http://www.geonames.org/export/web-services.html#srtm3
        // Expected Results: view-source:http://api.geonames.org/srtm3JSON?formatted=true&lat=50.01&lng=10.2&username=demo&style=full
        'srtm3' => [
            'httpMethod' => 'GET',
            'uri' => 'srtm3JSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : the timezone at the lat/lng with gmt offset (1. January) and dst offset (1. July)
        // Info: http://www.geonames.org/export/web-services.html#timezone
        // Expected Results:  view-source:http://api.geonames.org/timezoneJSON?formatted=true&lat=47.01&lng=10.2&username=demo&style=full
        'timezone' => [
            'httpMethod' => 'GET',
            'uri' => 'timezoneJSON',
            'responseModel' => 'default',
            'parameters' => [
                'lat' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'lng' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'radius' => [
                    'type' => 'numeric',
                    'location' => 'query',
                ],
                'date' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns a list of weather stations with the most recent weather observation
        // Info: http://www.geonames.org/export/JSON-webservices.html#weatherJSON
        // Expected Results: view-source:http://api.geonames.org/weatherJSON?formatted=true&north=44.1&south=-9.9&east=-22.4&west=55.2&username=demo&style=full
        'weather' => [
            'httpMethod' => 'GET',
            'uri' => 'weatherJSON',
            'responseModel' => 'default',
            'parameters' => [
                'north' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'south' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'east' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'west' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the weather station and the most recent weather observation for the ICAO code
        // Info: http://www.geonames.org/export/JSON-webservices.html#weatherIcaoJSON
        // Expected Results: view-source:http://api.geonames.org/weatherIcaoJSON?formatted=true&ICAO=LSZH&username=demo&style=full
        'weatherIcao' => [
            'httpMethod' => 'GET',
            'uri' => 'weatherIcaoJSON',
            'responseModel' => 'default',
            'parameters' => [
                'ICAO' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the wikipedia entries within the bounding box
        // Info: http://www.geonames.org/export/wikipedia-webservice.html#wikipediaBoundingBox
        // Expected Results: view-source:http://api.geonames.org/wikipediaBoundingBoxJSON?formatted=true&north=44.1&south=-9.9&east=-22.4&west=55.2&username=demo&style=full
        'wikipediaBoundingBox' => [
            'httpMethod' => 'GET',
            'uri' => 'wikipediaBoundingBoxJSON',
            'responseModel' => 'default',
            'parameters' => [
                'north' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'south' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'east' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'west' => [
                    'type' => 'numeric',
                    'location' => 'query',
                    'required' => true,
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
        // Result : returns the wikipedia entries found for the searchterm
        // Info: http://www.geonames.org/export/wikipedia-webservice.html#wikipediaSearch
        // Expected Results: view-source:http://api.geonames.org/wikipediaSearchJSON?formatted=true&q=london&maxRows=10&username=demo&style=full
        'wikipediaSearch' => [
            'httpMethod' => 'GET',
            'uri' => 'wikipediaSearchJSON',
            'responseModel' => 'default',
            'parameters' => [
                'q' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ],
                'title' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'maxRows' => [
                    'type' => 'integer',
                    'location' => 'query',
                ],
                'lang' => [
                    'type' => 'string',
                    'location' => 'query',
                ],
                'username' => [
                    'type' => 'string',
                    'location' => 'query',
                    'required' => true,
                ]
            ]
        ],
    ],
    'models' => [
        'default' => [
            'type' => 'object',
            'additionalProperties' => [
                'location' => 'json'
            ],
        ],
    ],
];

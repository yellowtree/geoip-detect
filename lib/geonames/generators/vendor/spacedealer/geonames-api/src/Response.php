<?php
/**
 * Response.php file.
 *
 * @author Dirk Adler <adler@spacedealer.de>
 * @link http://www.spacedealer.de
 * @copyright Copyright &copy; 2014 spacedealer GmbH
 */

namespace spacedealer\geonames\api;

use GuzzleHttp\Ring\Future\CompletedFutureArray;

/**
 * Class Response
 *
 * @package spacedealer\geonames\api
 */
class Response extends CompletedFutureArray
{
    /**
     * @var bool
     */
    protected $ok = true;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        // extract root & check for status/error response
        if (is_array($data) && count($data) == 1) {

            // error or status response
            $key = key($data);
            $this->ok = ($key != 'status');

            $data = reset($data);
        }

        parent::__construct($data);
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->ok;
    }
}

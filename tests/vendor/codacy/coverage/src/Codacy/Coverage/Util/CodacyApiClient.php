<?php

namespace Codacy\Coverage\Util;

/**
 * Class ApiClient
 * @author Jakob Pupke <jakob.pupke@gmail.com>
 */
class CodacyApiClient
{

    //TODO: do it with fsockopen
    /**
     * @param string $url url to post to
     * @param string $data the JSON data
     * @return void
     */
    static function postData($url, $data)
    {
        echo "Sending Coverage Data to Codacy\n\r";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json")
        );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($status < 200 || $status > 201) {
            die("Error: call to URL $url failed with status $status, response $json_response, curl_error "
                . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        } else {
            $json = json_decode($json_response, true);
            echo $json['success'] . "\n\r";
        }

        curl_close($curl);

    }
}

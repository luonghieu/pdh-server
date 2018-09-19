<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class GuzzleHelper
{
    public function __construct()
    {
        //
    }

    public function get($url, $query = [], $token = null)
    {
        $authorization = empty($token) ?: 'Bearer ' . $token;
        $client = new Client([
            'http_errors' => false,
            'debug' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $authorization,
                'Content-Type' => 'application/json',
            ],
        ]);
        $apiRequest = $client->request('GET', $url, [
            'query' => $query,
        ]);
        $result = $apiRequest->getBody();
        $contents = $result->getContents();
        $contents = json_decode($contents, JSON_NUMERIC_CHECK);

        return $contents;
    }
}

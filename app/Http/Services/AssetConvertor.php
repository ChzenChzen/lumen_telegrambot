<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

class AssetConvertor
{
    public function convert(string $asset, int $amount)
    {
        $http_client = new Client();
        $response = $http_client->get('https://google.com');
        return $response;
    }
}

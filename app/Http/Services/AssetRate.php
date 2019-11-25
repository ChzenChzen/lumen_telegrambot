<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

class AssetRate
{
    protected static $base_url = 'https://rest.coinapi.io/v1/exchangerate/BTC/';
    protected static $coinapi_key = '60D79F0F-3AA9-41A3-84CB-2B19DA8B7DBE';

    public static function get(string $asset)
    {
        $asset = strtoupper($asset);
        
        $http_client = new Client([
            'base_uri' => self::$base_url,
        ]);
        
        $response = $http_client->request('GET', $asset, [
            'headers' => [
                'X-CoinAPI-Key' => self::$coinapi_key,
            ]
        ]);

        $content = $response->getBody()->getContents();
        $json_to_array = json_decode($content, true);
        $rate = $json_to_array['rate'];

        return $rate;
    }
}

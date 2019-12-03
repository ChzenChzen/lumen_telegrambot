<?php

namespace App\Services;

use GuzzleHttp\Client;

class AssetQuery
{
    public $amount;
    public $asset;
    public $date; //2019-11-27T16:25:22+0000
    public $coinapi_key;

    public $http_client;

    function __construct($matches)
    {
        $this->amount = $matches[1];
        $this->asset = strtoupper($matches[2]);

        if (count($matches) == 4) {
            $unformatted_date = $matches[3] . ' 12:00:00';
            $format = 'd.m.Y H:i:s';

            $date = \DateTime::createFromFormat($format, $unformatted_date);
            $this->date = $date->format(\DateTime::ATOM);
        }

        $this->http_client = new Client(['base_uri' => env('COIN_API_BASE_URL')]);
        $this->coinapi_key = env('COIN_API_KEY');
    }

    public function get_rate()
    {
        $response = $this->http_client->request('GET', $this->asset, [
            'headers' => ['X-CoinAPI-Key' => $this->coinapi_key],
            'query' => ['time' => $this->date],
        ]);

        $content = $response->getBody()->getContents();
        $json_to_array = json_decode($content, true);
        $rate = $json_to_array['rate'];

        return $rate;
    }

    public function convert()
    {
        return $this->amount / $this->get_rate();
    }
}

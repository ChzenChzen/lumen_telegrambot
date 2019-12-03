<?php

namespace App\Services;

use GuzzleHttp\Client;

class AssetQuery
{
    public $amount;
    public $asset;
    public $date; //2019-11-27T16:25:22+0000
    public $coinAPIKey;

    public $httpClient;

    function __construct($matches)
    {
        $this->amount = $matches[1];
        $this->asset = strtoupper($matches[2]);

        if (count($matches) == 4) {
            $unformattedDate = $matches[3] . ' 12:00:00';
            $format = 'd.m.Y H:i:s';

            $date = \DateTime::createFromFormat($format, $unformattedDate);
            $this->date = $date->format(\DateTime::ATOM);
        }

        $this->httpClient = new Client(['base_uri' => env('COIN_API_BASE_URL')]);
        $this->coinAPIKey = env('COIN_API_KEY');
    }

    public function getRate()
    {
        $response = $this->httpClient->request('GET', $this->asset, [
            'headers' => ['X-CoinAPI-Key' => $this->coinAPIKey],
            'query' => ['time' => $this->date],
        ]);

        $content = $response->getBody()->getContents();
        $jsonToArray = json_decode($content, true);
        $rate = $jsonToArray['rate'];

        return $rate;
    }

    public function convert()
    {
        return $this->amount / $this->getRate();
    }
}

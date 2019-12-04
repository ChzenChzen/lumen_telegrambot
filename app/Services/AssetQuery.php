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
    public $exchanges;

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
        $this->exchanges = env('COIN_API_EXCHANGES');
    }

    public function generateQuotes()
    {
        $quotes = [];

        if ($this->asset == 'BTC') {
            foreach ($this->exchanges as $exchange) {
                $quotes[] = $exchange . '_SPOT_' . '_BTC_' . '_USD';
                $quotes[] = $exchange . '_SPOT_' . '_BTC_' . '_EUR';
            }
        } else {
            foreach ($this->exchanges as $exchange) {
                $quotes[] = $exchange . '_SPOT_' . '_BTC_' . $this->asset;
            }
        }
        return $quotes;
    }

    public function request($quotes)
    {
        $responses = [];

        if (isset($this->date)) {
            //            
        } else {
            foreach ($quotes as $quote) {
                $url = 'quotes/' . $quote . '/current';
                $response = $this->httpClient->request('GET', $url, [
                    'headers' => ['X-CoinAPI-Key' => $this->coinAPIKey],
                ]);

                $json = $response->getBody()->getContents();
                $responses[] = json_decode($json, true);
            }
        }

        return $responses;
    }



    public function getRate()
    {

        $rate = $jsonToArray['rate'];

        return $rate;
    }

    public function convert()
    {
        return $this->amount / $this->getRate();
    }
}

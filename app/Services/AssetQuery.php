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
        $this->exchanges = explode(',', env('COIN_API_EXCHANGES'));
    }

    public function generateQuotes()
    {
        $quotes = [];

        if ($this->asset == 'BTC') {
            foreach ($this->exchanges as $exchange) {
                $quotes[] = $exchange . '_SPOT_BTC_' . '_USD';
                $quotes[] = $exchange . '_SPOT_BTC_' . '_EUR';
            }
        } else {
            foreach ($this->exchanges as $exchange) {
                $quotes[] = $exchange . '_SPOT_' . 'BTC_' . $this->asset;
            }
        }
        
        $debug_res = print_r($quotes, true);
        file_put_contents('log.txt', $debug_res, FILE_APPEND | LOCK_EX);

        return $quotes;
    }

    public function request($quotes)
    {
        $responses = [];

        if (isset($this->date)) {
        //this code for debug!
            foreach ($quotes as $quote) {
                $url = 'quotes/' . $quote . '/current';
                $response = $this->httpClient->request('GET', $url, [
                    'headers' => ['X-CoinAPI-Key' => $this->coinAPIKey],
                ]);

                $json = $response->getBody()->getContents();
                $responses[] = json_decode($json, true);
            }
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
        $quotes = $this->generateQuotes();
        $responses = $this->request($quotes);


        return print_r($responses, true);
        // $rate = $jsonToArray['rate'];

        // return $rate;
    }

    public function convert()
    {
        return $this->amount / $this->getRate();
    }
}

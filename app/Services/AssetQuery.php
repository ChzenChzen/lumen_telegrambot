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
                $quotes[] = $exchange . '_SPOT_BTC_USD';
                $quotes[] = $exchange . '_SPOT_BTC_EUR';
            }
        } else {
            foreach ($this->exchanges as $exchange) {
                $quotes[] = $exchange . '_SPOT_' . 'BTC_' . $this->asset;
            }
        }

        return $quotes;
    }

    public function request($url)
    {
        $response = $this->httpClient->request('GET', $url, [
            'headers' => ['X-CoinAPI-Key' => $this->coinAPIKey],
            'http_errors' => false,
        ]);

        if ($response->getStatusCode() == 200) {
            $json = $response->getBody()->getContents();
            return json_decode($json, true);
        }

        return [
            'error' => 'error',
        ];
    }

    public function getData($quotes)
    {
        $responses = [];

        if (isset($this->date)) {
            //this code for debug!
        } else {
            foreach ($quotes as $quote) {
                $url = 'quotes/' . $quote . '/current';

                $data = $this->request($url);

                if (array_key_exists('error', $data)) {
                    $data['symbol_id'] = $quote;
                }

                $responses[] = $data;
            }
        }

        return $responses;
    }

    public function getExchange($data)
    {
        $splitted = explode('_', $data['symbol_id']);
        return $splitted[0];
    }

    public function getSymbols($data)
    {
        $splitted = explode('_', $data['symbol_id']);
        return [
            'base' => $splitted[2],
            'query' => $splitted[3],
        ];
    }

    public function getLastPrice($data)
    {
        return $data['last_trade']['price'];
    }

    public function getRate()
    {
        $quotes = $this->generateQuotes();
        $data = $this->getData($quotes);

        $output = [];
        foreach ($data as $item) {
            $exchange = $this->getExchange($item);

            if (key_exists('error', $item)) {
                $output[] = $exchange . ': This trading pair doesn\'t exist.';
            } else {
                $symbols = $this->getSymbols($item);

                if ($this->asset == 'BTC') {
                    $value = $this->amount * $this->getLastPrice($item);
                    $output[] = $exchange . ': ' . $value . ' ' . $symbols['query'];
                } else {
                    $value = $this->amount / $this->getLastPrice($item);
                    $output[] = $exchange . ': ' . $value . ' ' . $symbols['base'];
                }
            }
        }

        return print_r($output, true);
    }

    public function convert()
    {
        return $this->amount / $this->getRate();
    }
}

<?php

namespace App\Services;

use GuzzleHttp\Client;
use PDO;

class TelegramHandler
{
    public $httpClient;

    function __construct(string $token)
    {
        $baseURL = env('TELEGRAM_BASE_URL') . $token . '/';
        $this->httpClient = new Client([
            'base_uri' => $baseURL,
        ]);
    }

    public static function regexData($text)
    {
        $text = strtoupper($text);
        $re = '/(-*\0*.*\d+)\s*(RUB|R|\$|EUR|USD|BTC|BITCOIN)\s*(\d{2}\.\d{2}\.\d{4})?/';

        if (!preg_match($re, $text, $matches)) {
            return;
        };

        $unformattedAssets = [
            '$' => 'USD',
            'R' => 'RUB',
            'BITCOIN' => 'BTC',
        ];

        $asset = $matches[2];

        if (array_key_exists($asset, $unformattedAssets)) {
            $matches[2] = $unformattedAssets[$asset];
        }

        return $matches;
    }

    public function sendRequest($method, $params = [])
    {
        $response = $this->httpClient->request('GET', $method, [
            'query' => $params,
            'http_errors' => false,
        ]);

        $json = $response->getBody()->getContents();
        return json_decode($json, true);
    }

    public function sendMessage($chatID, $text)
    {
        $this->sendRequest('sendMessage', [
            'chat_id' => $chatID,
            'text' => $text,
        ]);
    }

    public function formatResult($result)
    {
        $output = '';
        foreach ($result as $item) {
            $output .= $item . "\n";
        }
        return $output;
    }

    public function handle($data)
    {
        $chatID = $data['message']['chat']['id'];
        $text = $data['message']['text'];

        // send 200 OK
        $this->sendRequest('sendMessage', [
            'chat_id' => $chatID,
        ]);

        if ($text == '/start') {
            $this->sendMessage($chatID, 'If you want to convert one asset to another you should send request in the next format: 1btc or 1 btc or 0.5 btc or 400eur. For request with date you have to follow next format: 1btc 20.04.2016 or 1btc20.04.2016.');
        } else {
            $matches = self::regexData($text);

            if ($matches) {
                $asset = new AssetQuery($matches);
                $result = $asset->getRate();
                $formattedResult = $this->formatResult($result);
                $this->sendMessage($chatID, $formattedResult);
            } else {
                $this->sendMessage($chatID, 'Invalid request');
            }
        }
    }
}

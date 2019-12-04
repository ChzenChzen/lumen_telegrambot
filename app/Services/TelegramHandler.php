<?php

namespace App\Services;

use GuzzleHttp\Client;


class TelegramHandler
{
    public $httpClient;

    function __construct(string $token)
    {
        $baseURL = env('TELEGRAM_BASE_URL') . $token . '/';
        $this->httpClient = new Client(['base_uri' => $baseURL]);
    }

    public static function regexData($text)
    {
        $text = strtoupper($text);
        $re = '/(\d+)\s*(RUB|R|\$|EUR|USD|BTC|BITCOIN)\s*(\d{2}\.\d{2}\.\d{4})?/';
        
        if (!preg_match($re, $text, $matches)){
            return;
        };

        $unformattedAssets = [
            '$' => 'USD',
            'R' => 'RUB',
            'BITCOIN' => 'BTC',
        ];

        $asset = $matches[2];

        if (array_key_exists($asset, $unformattedAssets)){
            $matches[2] = $unformattedAssets[$asset];
        }
        
        return $matches;
    }

    public function sendRequest($method, $params = [])
    {
        $response = $this->httpClient->request('GET', $method, ['query' => $params]);
        $json = $response->getBody()->getContents();
        return json_decode($json, true);
    }

    public function handle($data)
    {
        $chatID = $data['message']['chat']['id'];
        $text = $data['message']['text'];

        $matches = self::regexData($text);

        if ($matches) {
            $asset = new AssetQuery($matches);
            $result = $asset->getRate();

            $this->sendRequest('sendMessage', [
                'chat_id' => $chatID,
                'text' => $result,
            ]);
        } else {
            $this->sendRequest('sendMessage', [
                'chat_id' => $chatID,
                'text' => 'Invalid request',
            ]);
        }
        
    }
}

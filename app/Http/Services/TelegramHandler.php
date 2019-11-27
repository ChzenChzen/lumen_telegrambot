<?php

namespace App\Http\Services;

use GuzzleHttp\Client;


class TelegramHandler
{
    public $http_client;

    function __construct(string $token)
    {
        $base_url = 'https://api.telegram.org/bot' . $token . '/';
        $this->http_client = new Client(['base_uri' => $base_url]);
    }

    public static function regex_data($text)
    {
        $re = '/(\d+)\s*(rub|R|r|\$|eur|usd)\s*(\d{2}\.\d{2}\.\d{4})?/';
        
        if (!preg_match($re, $text, $matches)){
            return;
        };

        $unformatted_assets = [
            '$' => 'USD',
            'R' => 'RUB',
            'r' => 'RUB',
        ];

        $asset = $matches[2];

        if (array_key_exists($asset, $unformatted_assets)){
            $matches[2] = $unformatted_assets[$asset];
        }
        
        return $matches;
    }

    public function send_request($method, $params = [])
    {
        $response = $this->http_client->request('GET', $method, ['query' => $params]);
        $json = $response->getBody()->getContents();
        return json_decode($json, true);
    }

    public function handle($data)
    {
        $chat_id = $data['message']['chat']['id'];
        $text = $data['message']['text'];

        $matches = self::regex_data($text);

        if ($matches) {
            $asset = new AssetQuery($matches);
            $result = $asset->convert();

            $this->send_request('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $result,
            ]);
        } else {
            $this->send_request('sendMessage', [
                'chat_id' => $chat_id,
                'text' => 'Invalid request',
            ]);
        }
        
    }
}

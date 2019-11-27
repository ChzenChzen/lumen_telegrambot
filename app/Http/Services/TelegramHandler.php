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
        $re = '/(\d+)\s*(rub|R|\$|eur)\s*(\d{2}\.\d{2}\.\d{4})?/';
        preg_match($re, $text, $matches);
        return $matches;
    }

    public static function is_valid($matches)
    {
        $valid_assets = ['eur', '$', 'R', 'rub', 'usd'];

        if (is_numeric($matches[1]) && in_array($matches[2], $valid_assets)) {
            return true;
        }
        return false;
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

        if (self::is_valid($matches)) {
            $asset = new AssetQuery($matches);
            $result = $asset->convert();

            $response = $this->send_request('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $result,
            ]);
        }
    }
}

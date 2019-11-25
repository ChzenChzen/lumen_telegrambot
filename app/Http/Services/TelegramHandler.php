<?php

namespace App\Http\Services;


class TelegramHandler
{
    public static function send_request($method, $params = [])
    {
        $telegram_token = '784355426:AAEgw0aJ01hx3PAr3AtAQSff6FF-c6T_bfw';
        $base_url = 'https://api.telegram.org/bot' . $telegram_token . '/';

        if (!empty($params)) {
            $url = $base_url . $method . '?' . http_build_query($params);
        } else {
            $url = $base_url . $method;
        }

        return json_decode(
            file_get_contents($url),
            true
        );
    }

    public function handle($data)
    {
        $chat_id = $data['message']['chat']['id'];
        $text = $data['message']['text'];

        $re = '/(\d+)\s*(rub|R|\$|eur)\s*(\d{2}\.\d{2}\.\d{4})?/';

        preg_match($re, $text, $matches);

        (int) $amount = $matches[1];
        (string) $asset = $matches[2];

        $asset_rate = new AssetRate;
        $rate = $asset_rate->get($asset);

        $answer = $amount / $rate;

        $response = self::send_request('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $answer,
        ]);

        // if (count($matches) == 4) {
        //     // make request with date 
        // } else {
        //     // make request without date
        // }



        // debug

        $result = print_r($response, true);
        file_put_contents('log.txt', $result, FILE_APPEND | LOCK_EX);
    }
}

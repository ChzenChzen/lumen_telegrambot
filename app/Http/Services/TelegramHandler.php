<?php

namespace App\Http\Services;


class TelegramHandler
{
    public function handle($data)
    {
        $coin_api = '60D79F0F-3AA9-41A3-84CB-2B19DA8B7DBE';

        

        $user_id = $data['message']['from']['id'];
        $text = $data['message']['text'];

        $re = '/(\d+)\s*(rub|R|\$|eur)\s*(\d{2}\.\d{2}\.\d{4})?/';

        preg_match($re, $text, $matches);

        $convertor = new AssetConvertor;
        $response = $convertor->convert("EUR", 400);

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

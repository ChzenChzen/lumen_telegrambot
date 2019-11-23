<?php

namespace App\Http\Services;

class TelegramHandler
{
    public function handle($data)
    {


        $user_id = $data['message']['from']['id'];
        $text = $data['message']['text'];

        $re = '/(\d+)\s*(rub|R|\$|eur)\s*(\d{2}\.\d{2}\.\d{4})?/';

        preg_match($re, $text, $matches);


        // debug
        $result = print_r($matches, true);
        file_put_contents('log.txt', $result, FILE_APPEND | LOCK_EX);
    }
}

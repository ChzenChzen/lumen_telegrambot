<?php

namespace App\Http\Controllers;

class SetWebhook extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function set()
    {
        $TOKEN = '784355426:AAEgw0aJ01hx3PAr3AtAQSff6FF-c6T_bfw';
        $method = 'setWebhook';


        $url = 'https://api.telegram.org/bot' . $TOKEN . '/' . $method;
        $options = [
            'url' => 'https://fomotoshi.dev/telegrambot',
        ];

        $response = file_get_contents($url . '?' . http_build_query($options));
        dd($response);
    }
}

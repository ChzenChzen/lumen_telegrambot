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
        $TOKEN = env('TELEGRAM_TOKEN');
        $method = 'setWebhook';


        $url = env('TELEGRAM_BASE_URL') . $TOKEN . '/' . $method;
        $options = [
            'url' => 'https://fomotoshi.dev/telegrambot',
        ];

        $response = file_get_contents($url . '?' . http_build_query($options));
        dd($response);
    }
}

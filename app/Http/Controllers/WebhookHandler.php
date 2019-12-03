<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TelegramHandler;


class WebhookHandler extends Controller
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

    public function handle(Request $request)
    {   
        $data = $request->json()->all();

        $telegramToken = env('TELEGRAM_TOKEN');
        $telegramHandler = new TelegramHandler($telegramToken);

        $telegramHandler->handle($data);
    }
}

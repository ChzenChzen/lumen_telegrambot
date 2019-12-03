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

        $telegram_token = env('TELEGRAM_TOKEN');
        $telegram_handler = new TelegramHandler($telegram_token);

        $telegram_handler->handle($data);
    }
}

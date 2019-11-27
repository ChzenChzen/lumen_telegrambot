<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\TelegramHandler;


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

        $telegram_token = '784355426:AAEgw0aJ01hx3PAr3AtAQSff6FF-c6T_bfw';
        $telegram_handler = new TelegramHandler($telegram_token);

        $telegram_handler->handle($data);
    }
}

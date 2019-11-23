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
        $telegram_handler = new TelegramHandler;
        $telegram_handler->handle($data);
    }
}

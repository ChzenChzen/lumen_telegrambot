<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


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
        
        file_put_contents('/var/www/logs_for_test/log.txt', $request);
    }

    //
}

<?php

const TOKEN = '784355426:AAEgw0aJ01hx3PAr3AtAQSff6FF-c6T_bfw';
$method = 'setWebhook';


$url = 'https://api.telegram.org/bot' . TOKEN . '/' . $method;
$options = [
    'url' => 'https://fomotoshi.dev/index.php',
];
echo($url . '?' . http_build_query($options));

// $response = file_get_contents($url . '?' . http_build_query($options));

// var_dump($response);

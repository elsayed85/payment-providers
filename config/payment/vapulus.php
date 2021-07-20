<?php
return [
    "base_url" => "https://api.vapulus.com:1338/",
    "fail_url" => env("VAPULUS_FAIL_URL"),
    "success_url" => env("VAPULUS_SUCCESS_URL"),
    'app_id' => env('VAPULUS_APPID'),
    'password' => env('VAPULUS_PASSWORD'),
    'hash' => env('VAPULUS_HASH')
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'transbank'=>[
        'webpay_plus_cc'=>env('WEB_PAY_COMMERCE_CODE'),
        'webpay_plus_api_key'=>env('WEB_PAY_API_KEY')
    ],
    'recorrido'=>[
        'url' => env('API_RECORRIDO_URL'),
        'agency_username' => env('API_AGENCY_USERNAME'),
        'agency_password' => env('API_AGENCY_PASSWORD'),
        'basic_username' => env('API_BASIC_USERNAME'),
        'basic_password' => env('API_BASIC_PASSWORD'),
        'client_id' => env('API_CLIENT_ID'),
        'client_secret' => env('API_CLIENT_SECRET'),
    ]

];

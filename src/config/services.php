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
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'main_app'      => [
        'base_url' => env('MAINAPP_BASE_URL'),
        'token'    => env('MAINAPP_TOKEN'),
    ],
    'asan_pardakht' => [
        'asan_pardakht_refund_auth_url' => env('ASAN_PARDAKHT_REFUND_AUTH_URL'),
        'asan_pardakht_refund_api_url'  => env('ASAN_PARDAKHT_REFUND_API_URL'),
        'asan_pardakht_refund_client_id' => env('ASAN_PARDAKHT_REFUND_CLIENTID'),
        'asan_pardakht_refund_secret'   => env('ASAN_PARDAKHT_REFUND_SECRET')
    ],

    'zarinpal' => [
        'next' => [
            'url'         => env('ZARINPAL_NEXT_URL'),
            'token'       => env('ZARINPAL_NEXT_TOKEN'),
            'terminal_id' => env('ZARINPAL_NEXT_TERMINAL_ID'),
            'merchant_id' => env('ZARINPAL_NEXT_MERCHANT_ID'),
        ],
    ],
];

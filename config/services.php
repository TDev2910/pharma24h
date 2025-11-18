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

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'vnpay' => [
        'tmn_code'    => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
    ],

    'ghn' => [
        'token' => env('GHN_TOKEN'),
        'shop_id' => env('GHN_SHOP_ID'),
        'base_url' => env('GHN_BASE_URL', 'https://dev-online-gateway.ghn.vn/shiip/public-api'),
        'from_district_id' => env('GHN_FROM_DISTRICT_ID'),
        'from_ward_code' => env('GHN_FROM_WARD_CODE'),
        'default_service_type' => env('GHN_DEFAULT_SERVICE_TYPE', 2),
        'webhook_secret' => env('GHN_WEBHOOK_SECRET'),
    ],
];
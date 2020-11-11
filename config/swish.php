<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Certificate and key
    |--------------------------------------------------------------------------
    |
    | 1. Generate CSR
    | 2. fi
    etc
    |
    */
    'currency' => env('SWISH_CURRENCY', 'SEK'),
    'merchant_number' => env('SWISH_MERCHANT_NUMBER', '123456789'),
    'cert' => env('SWISH_CERT', storage_path('swish' . DIRECTORY_SEPARATOR . 'cert.pem')),
    'key' => env('SWISH_KEY', storage_path('swish' . DIRECTORY_SEPARATOR . 'key.pem')),
    'callback_base_url' => env('SWISH_CALLBACK_BASE_URL', 'https://please-replace-this-option'),
    'broadcast_events' => env('SWISH_BROADCAST_EVENTS', true)
];
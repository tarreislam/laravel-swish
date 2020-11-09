<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default configuration
    |--------------------------------------------------------------------------
    |
    | What swish
    |
    */
    'swish_number' => env('SWISH_NUMBER', '123456789'),
    'ca' => env('SWISH_CA', storage_path('swish' . DIRECTORY_SEPARATOR . 'ca.pem')),
    'key' => env('SWISH_KEY', storage_path('swish' . DIRECTORY_SEPARATOR . 'key.pem')),
];
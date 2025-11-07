<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Server key untuk autentikasi ke Midtrans API.
    | JANGAN share atau commit ke public repository!
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Client key untuk frontend integration (jika pakai Snap).
    | Untuk Core API, tidak terlalu dipakai.
    |
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Environment Midtrans: sandbox atau production
    |
    */
    'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    |
    | Base URL untuk Midtrans API
    |
    */
    'sandbox_url' => env('MIDTRANS_SANDBOX_URL', 'https://api.sandbox.midtrans.com'),
    'production_url' => env('MIDTRANS_PRODUCTION_URL', 'https://api.midtrans.com'),

    /*
    |--------------------------------------------------------------------------
    | Is Production
    |--------------------------------------------------------------------------
    */
    'is_production' => env('MIDTRANS_ENVIRONMENT') === 'production',

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Automatically select based on environment
    |
    */
    'base_url' => env('MIDTRANS_ENVIRONMENT') === 'production' 
        ? env('MIDTRANS_PRODUCTION_URL', 'https://api.midtrans.com')
        : env('MIDTRANS_SANDBOX_URL', 'https://api.sandbox.midtrans.com'),
];

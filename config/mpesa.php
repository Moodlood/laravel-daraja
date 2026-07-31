<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | M-Pesa Environment
    |--------------------------------------------------------------------------
    |
    | Set the environment for the Daraja API. Use 'sandbox' for testing
    | and 'production' for live transactions.
    |
    | Supported: "sandbox", "production"
    |
    */

    'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Consumer Credentials
    |--------------------------------------------------------------------------
    |
    | Your Daraja API consumer key and secret. These are used for
    | OAuth authentication and STK Push / C2B operations.
    |
    */

    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Lipa Na M-Pesa Online (STK Push)
    |--------------------------------------------------------------------------
    |
    | The passkey and shortcode used for STK Push (Lipa Na M-Pesa Online)
    | requests. The shortcode is your PayBill or Till number.
    |
    */

    'shortcode' => env('MPESA_SHORTCODE'),
    'passkey' => env('MPESA_PASSKEY'),

    /*
    |--------------------------------------------------------------------------
    | Till Number (Buy Goods)
    |--------------------------------------------------------------------------
    |
    | If you use a Till Number (Buy Goods) for receiving payments,
    | specify it here. Defaults to the shortcode if not set.
    |
    */

    'till_number' => env('MPESA_TILL_NUMBER'),

    /*
    |--------------------------------------------------------------------------
    | B2C Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials for Business to Customer (B2C) transactions.
    | These may differ from your C2B/STK Push credentials.
    |
    */

    'b2c' => [
        'consumer_key' => env('MPESA_B2C_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_B2C_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_B2C_SHORTCODE'),
        'initiator_name' => env('MPESA_INITIATOR_NAME'),
        'security_credential' => env('MPESA_SECURITY_CREDENTIAL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | B2B Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials for Business to Business (B2B) transactions.
    |
    */

    'b2b' => [
        'consumer_key' => env('MPESA_B2B_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_B2B_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_B2B_SHORTCODE'),
        'initiator_name' => env('MPESA_B2B_INITIATOR_NAME'),
        'security_credential' => env('MPESA_B2B_SECURITY_CREDENTIAL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback URLs
    |--------------------------------------------------------------------------
    |
    | The URLs that Safaricom will send callback data to. All URLs
    | must be publicly accessible via HTTPS in production.
    |
    */

    'callbacks' => [
        'stk_push' => env('MPESA_STK_CALLBACK_URL'),
        'c2b_validation' => env('MPESA_C2B_VALIDATION_URL'),
        'c2b_confirmation' => env('MPESA_C2B_CONFIRMATION_URL'),
        'b2c_result' => env('MPESA_B2C_RESULT_URL'),
        'b2c_timeout' => env('MPESA_B2C_TIMEOUT_URL'),
        'b2b_result' => env('MPESA_B2B_RESULT_URL'),
        'b2b_timeout' => env('MPESA_B2B_TIMEOUT_URL'),
        'balance_result' => env('MPESA_BALANCE_RESULT_URL'),
        'balance_timeout' => env('MPESA_BALANCE_TIMEOUT_URL'),
        'status_result' => env('MPESA_STATUS_RESULT_URL'),
        'status_timeout' => env('MPESA_STATUS_TIMEOUT_URL'),
        'reversal_result' => env('MPESA_REVERSAL_RESULT_URL'),
        'reversal_timeout' => env('MPESA_REVERSAL_TIMEOUT_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the webhook route prefix and middleware. The package
    | registers webhook routes automatically under this prefix.
    |
    */

    'webhooks' => [
        'prefix' => env('MPESA_WEBHOOK_PREFIX', 'api/mpesa/webhooks'),
        'middleware' => ['api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | OAuth tokens are cached to avoid unnecessary API calls. Configure
    | the cache store and TTL buffer (seconds subtracted from token expiry).
    |
    */

    'cache' => [
        'store' => env('MPESA_CACHE_STORE', config('cache.default', 'file')),
        'prefix' => 'mpesa_',
        'ttl_buffer' => 30, // seconds before expiry to refresh
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Configuration
    |--------------------------------------------------------------------------
    |
    | Configure timeouts, retries, and other HTTP client behavior.
    |
    */

    'http' => [
        'timeout' => env('MPESA_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('MPESA_CONNECT_TIMEOUT', 10),
        'retries' => env('MPESA_HTTP_RETRIES', 3),
        'retry_delay' => env('MPESA_RETRY_DELAY', 100), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | The log channel used for M-Pesa related logs. Set to null to
    | use the default Laravel log channel. Secrets are never logged.
    |
    */

    'log_channel' => env('MPESA_LOG_CHANNEL'),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, additional debug information will be logged.
    | Never enable this in production as it may log sensitive data.
    |
    */

    'debug' => env('MPESA_DEBUG', false),

];

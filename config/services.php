<?php

return [

    'erp_gateway' => [
        'identity_url' => env('IDENTITY_SERVICE_URL'),

        'services' => [
            'identity' => env('IDENTITY_SERVICE_URL'),
            'core' => env('CORE_SERVICE_URL'),
            'master_data' => env('MASTER_DATA_SERVICE_URL'),
            'sales' => env('SALES_SERVICE_URL'),
            'inventory' => env('INVENTORY_SERVICE_URL'),
            'finance' => env('FINANCE_SERVICE_URL'),
            'hr' => env('HR_SERVICE_URL'),
            'purchasing' => env('PURCHASING_SERVICE_URL'),
            'analytics' => env('ANALYTICS_SERVICE_URL'),
        ],

        'shared_secret' => env('GATEWAY_SHARED_SECRET'),

        'secrets' => [
            'identity' => env('IDENTITY_SERVICE_SECRET'),
            'core' => env('CORE_SERVICE_SECRET'),
            'master_data' => env('MASTER_DATA_SERVICE_SECRET'),
            'sales' => env('SALES_SERVICE_SECRET'),
            'inventory' => env('INVENTORY_SERVICE_SECRET'),
            'finance' => env('FINANCE_SERVICE_SECRET'),
            'hr' => env('HR_SERVICE_SECRET'),
            'purchasing' => env('PURCHASING_SERVICE_SECRET'),
            'analytics' => env('ANALYTICS_SERVICE_SECRET'),
        ],

        'timeout' => (int) env('MICROSERVICE_TIMEOUT', 5),
        'connect_timeout' => (int) env('MICROSERVICE_CONNECT_TIMEOUT', 2),
        'retry_times' => (int) env('MICROSERVICE_RETRY_TIMES', 2),
        'retry_sleep' => (int) env('MICROSERVICE_RETRY_SLEEP', 100),
    ],


    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];

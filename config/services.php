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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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


    /*
    |--------------------------------------------------------------------------
    | Maya Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Maya (formerly PayMaya) payment gateway integration.
    | Get your API keys from: https://developers.maya.ph/
    |
    */

    'maya' => [
        // API Keys
        'public_key' => env('MAYA_PUBLIC_KEY'),
        'secret_key' => env('MAYA_SECRET_KEY'),
        
        // Environment (sandbox or production)
        'environment' => env('MAYA_ENVIRONMENT', 'sandbox'),
        
        // Webhook Secret for signature verification
        'webhook_secret' => env('MAYA_WEBHOOK_SECRET'),
        
        // API Base URL (determined by environment)
        'api_url' => env('MAYA_ENVIRONMENT', 'sandbox') === 'production' 
            ? 'https://pg.paymaya.com' 
            : 'https://pg-sandbox.paymaya.com',
        
        // Webhook Configuration
        'webhook_url' => env('APP_URL') . '/maya/qr/webhook',
        
        // Checkout Settings
        'checkout_timeout' => 900, // 15 minutes in seconds
    ],

];

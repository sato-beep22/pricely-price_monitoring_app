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

    'semaphore' => [
        'api_key' => env('SEMAPHORE_API_KEY'),
        'sender_name' => env('SEMAPHORE_SENDER_NAME', 'Pricely'),
        'sender_name_active' => env('SEMAPHORE_SENDER_NAME_ACTIVE', false),
        'api_url' => env('SEMAPHORE_API_URL', 'https://api.semaphore.co/api/v4/messages'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
    ],

    'infobip' => [
        'api_key' => env('INFOBIP_API_KEY'),
        'base_url' => env('INFOBIP_BASE_URL', 'https://6zw45r.api.infobip.com'),
        'test_mode' => env('INFOBIP_TEST_MODE', true),
        'sender' => env('INFOBIP_SENDER', 'ServiceSMS'),
    ],

    'iprog' => [
        'api_url' => env('IPROG_API_URL', 'https://www.iprogsms.com/api/v1/sms_messages'),
        'api_token' => env('IPROG_API_TOKEN'),
    ],

];

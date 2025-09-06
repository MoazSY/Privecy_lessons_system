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

    'jitsi' => [
    'base_url' => env('JITSI_BASE_URL', 'https://meet.jit.si'),
    'enable_recording' => env('JITSI_ENABLE_RECORDING', false),
    'recording_storage' => env('JITSI_RECORDING_STORAGE', 'local'),
    'auto_create_minutes' => env('SESSION_AUTO_CREATE_MINUTES', 15),
],



'daily' => [
    'api_key'   => env('DAILY_API_KEY'),
    'base_url'  => env('DAILY_BASE_URL', 'https://api.daily.co/v1'),
    'subdomain' => env('DAILY_SUBDOMAIN'),
    'domain_url'=> env('DAILY_DOMAIN_URL'), // https://<subdomain>.daily.co
],

'zoom' => [
    'account_id' => env('ZOOM_ACCOUNT_ID'),
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'user_id' => env('ZOOM_USER_ID', 'me'),
    'base_url' => 'https://api.zoom.us/v2',
         'verification_token' => env('ZOOM_VERIFICATION_TOKEN'),
    'secret_token'       => env('ZOOM_SECRET_TOKEN'),

    // هام: لا يوجد تسجيل سحابي بالخطة المجانية
],

];

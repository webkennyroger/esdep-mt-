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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | SSO institucional da Defensoria. O Keycloak responde apenas "quem e" o
    | usuario; as permissoes ficam integralmente no Spatie, dentro da aplicacao.
    */
    'keycloak' => [
        'client_id' => env('KEYCLOAK_CLIENT_ID'),
        'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
        'redirect' => env('KEYCLOAK_REDIRECT_URI'),
        'base_url' => env('KEYCLOAK_BASE_URL'),
        'realms' => env('KEYCLOAK_REALM'),
        'logout_redirect' => env('KEYCLOAK_LOGOUT_REDIRECT', env('APP_URL')),
    ],

    'google_calendar' => [
        'enabled' => env('GOOGLE_CALENDAR_ENABLED', false),
        'calendar_id' => env('GOOGLE_CALENDAR_ID', 'primary'),
        'credentials_path' => env('GOOGLE_CALENDAR_CREDENTIALS_PATH'),
        'credentials_json' => env('GOOGLE_CALENDAR_CREDENTIALS_JSON'),
        'impersonate' => env('GOOGLE_CALENDAR_IMPERSONATE'),
        'timezone' => env('GOOGLE_CALENDAR_TIMEZONE', 'America/Cuiaba'),
    ],

];

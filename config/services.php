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

    /* App Mobile */
    'oauth' => [
            'client_id' => env('OAUTH_CLIENT_ID'),
            'client_secret' => env('OAUTH_CLIENT_SECRET'),
            'redirect_uri' => env('OAUTH_REDIRECT_URI'),
            'redirect_uri_mobile' => env('OAUTH_REDIRECT_URI_MOBILE'),
            'scopes' => explode(' ', env('OAUTH_SCOPES', 'users-infos read-assos read-memberships')),
            'authorize_url' => env('OAUTH_AUTHORIZE_URL', 'https://auth.assos.utc.fr/oauth/authorize'),
            'access_token_url' => env('OAUTH_ACCESS_TOKEN_URL', 'https://auth.assos.utc.fr/oauth/token'),
            'owner_details_url' => env('OAUTH_RESOURCE_OWNER_DETAILS', 'https://auth.assos.utc.fr/api/user'),
            'logout_url' => env('OAUTH_LOGOUT_URL', 'https://auth.assos.utc.fr/logout'),
    ],

    'mobile' => [
        'deeplink_scheme' => env('MOBILE_DEEPLINK_SCHEME', 'picmobile'),
    ],


];

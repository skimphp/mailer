<?php declare(strict_types=1);

return [
    'driver' => env('MAIL_DRIVER', 'smtp'),

    'from' => [
        'address' => env('MAIL_FROM', 'hello@example.com'),
        'name'    => env('MAIL_FROM_NAME', 'Example'),
    ],

    'smtp' => [
        'host'       => env('MAIL_HOST', 'localhost'),
        'port'       => (int) env('MAIL_PORT', 587),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'username'   => env('MAIL_USERNAME'),
        'password'   => env('MAIL_PASSWORD'),
    ],

    'mailgun' => [
        'key'    => env('MAILGUN_KEY'),
        'domain' => env('MAILGUN_DOMAIN'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sendgrid' => [
        'key' => env('SENDGRID_KEY'),
    ],

    'log' => [
        'path' => storagePath('logs/mail.log'),
    ],
];

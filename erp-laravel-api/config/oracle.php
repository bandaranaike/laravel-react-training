<?php
return [
    'oracle' => [
        'driver' => 'oracle', 'tns' => env('DB_TNS', ''),
        'host' => env('DB_HOST', ''), 'port' => env('DB_PORT', '1521'),
        'database' => env('DB_DATABASE', ''),
        'service_name' => env('DB_SERVICE_NAME', ''),
        'username' => env('DB_USERNAME', ''), 'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'AL32UTF8'), 'prefix' => '',
        'prefix_schema' => env('DB_SCHEMA_PREFIX', ''),
    ],
    'legacy_oracle' => [
        'driver' => 'oracle', 'tns' => env('LEGACY_DB_TNS', ''),
        'host' => env('LEGACY_DB_HOST', ''), 'port' => env('LEGACY_DB_PORT', '1521'),
        'database' => env('LEGACY_DB_DATABASE', ''),
        'service_name' => env('LEGACY_DB_SERVICE_NAME', ''),
        'username' => env('LEGACY_DB_USERNAME', ''), 'password' => env('LEGACY_DB_PASSWORD', ''),
        'charset' => 'AL32UTF8', 'prefix' => '',
    ],
];


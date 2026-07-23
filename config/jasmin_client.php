<?php
return [
    'url' => env('JASMIN_BASE_URL', 'http://127.0.0.1:1404'),
    'http_url' => env('JASMIN_HTTP_URL', env('JASMIN_BASE_URL')), // Backward Compatible
    'rest_url' => env('JASMIN_REST_URL', env('JASMIN_BASE_URL')), // Backward Compatible
    'smpp_url' => env('JASMIN_SMPP_URL', env('JASMIN_BASE_URL')), // Backward Compatible
    'username' => env('JASMIN_USERNAME', "jasminadmin"),
    'password' => env('JASMIN_PASSWORD', "jasminpwd"),
    'dlr_callback_url' => env('JASMIN_DLR_CALLBACK_URL', 'http://localhost/jasmin-callback'),
    'batch_callback_url' => env('JASMIN_BATCH_CALLBACK_URL', 'http://localhost/jasmin-batch-callback'),
    'batch_errback_url' => env('JASMIN_BATCH_ERRBACK_URL', 'http://localhost/jasmin-batch-callback'),

    'default_dlr_method' => env('JASMIN_DEFAULT_DLR_METHOD', 'POST'),
    'default_dlr_level' => env('JASMIN_DEFAULT_DLR_LEVEL', 2),
    'batch_chunk_size' => env('JASMIN_BATCH_CHUNK_SIZE', 10000),

    'log_http_failures' => env('JASMIN_LOG_HTTP_FAILURES', true),

    'logging' => [
        'enabled' => env('JASMIN_LOGGING_ENABLED', true),
    ],

    'smpp' => [
        'hosts' => array_values(array_filter(explode(',', env('JASMIN_SMPP_HOSTS', '127.0.0.1:2775')))),
        'username' => env('JASMIN_SMPP_USERNAME'),
        'password' => env('JASMIN_SMPP_PASSWORD'),
        'receiver' => [
            'reconnect_delay_seconds' => (int) env('JASMIN_SMPP_RECONNECT_DELAY', 5),
            'max_reconnect_delay_seconds' => (int) env('JASMIN_SMPP_MAX_RECONNECT_DELAY', 60),
        ],
    ],
];

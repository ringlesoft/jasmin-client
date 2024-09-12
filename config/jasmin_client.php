<?php
return [
    'base_url' => env('JASMIN_BASE_URL', 'http://127.0.0.1:1404'),
    'username' => env('JASMIN_USERNAME', "jasminadmin"),
    'password' => env('JASMIN_PASSWORD', "jasminpwd"),
    'default_callback_url' => env('JASMIN_CALLBACK_URL', 'http://localhost/jasmin-callback'),
    'batch_callback_url' => env('JASMIN_BATCH_CALLBACK_URL', 'http://localhost/jasmin-batch-callback'),
    'batch_errback_url' => env('JASMIN_BATCH_ERRBACK_URL', 'http://localhost/jasmin-batch-callback'),

    'batch_chunk_size' => env('JASMIN_BATCH_CHUNK_SIZE', 10000),
];

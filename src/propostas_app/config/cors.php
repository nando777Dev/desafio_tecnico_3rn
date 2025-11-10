<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',   // seu front Vue
        'http://127.0.0.1:5173',   // caso o navegador use 127.0.0.1
        'http://localhost:8000',   // swagger local
        'http://127.0.0.1:8000',   // fallback swagger local
        'null'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];


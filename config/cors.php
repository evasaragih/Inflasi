<?php

return [
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // Ganti '*' dengan daftar domain klien bila API sudah dipakai publik,
    // contoh: ['https://tpid.padang.go.id', 'http://localhost:5173']
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];

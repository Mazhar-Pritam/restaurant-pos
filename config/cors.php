<?php

return [
    // Which URLs the React app is allowed to call from another origin
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    // Only our React app, never '*', because cookies are used
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Needed so the browser sends and accepts the session cookie
    'supports_credentials' => true,
];

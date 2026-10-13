<?php

return ['paths' => ['api/*'], 'allowed_methods' => ['GET', 'OPTIONS'], 'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')], 'allowed_origins_patterns' => [], 'allowed_headers' => ['Accept', 'Content-Type'], 'exposed_headers' => [], 'max_age' => 600, 'supports_credentials' => false];

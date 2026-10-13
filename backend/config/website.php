<?php

return [
    'locales' => ['ar', 'en'], 'cache_ttl' => (int) env('CONTENT_CACHE_TTL', 600),
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
    'revalidation_url' => env('NEXT_REVALIDATION_URL'), 'revalidation_token' => env('NEXT_REVALIDATION_TOKEN'),
];

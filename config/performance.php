<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for performance monitoring
    | and optimization settings.
    |
    */

    'monitoring' => [
        'enabled' => env('PERFORMANCE_MONITORING', false),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 100), // milliseconds
        'slow_page_threshold' => env('SLOW_PAGE_THRESHOLD', 500), // milliseconds
    ],

    'caching' => [
        'dashboard_cache_ttl' => env('DASHBOARD_CACHE_TTL', 300), // 5 minutes
        'profile_image_cache_ttl' => env('PROFILE_IMAGE_CACHE_TTL', 3600), // 1 hour
    ],

    'optimization' => [
        'eager_loading' => true,
        'query_logging' => env('QUERY_LOGGING', false),
    ],
]; 
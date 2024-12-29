<?php

return [
    'retention_days' => env('ACTIVITY_LOG_RETENTION_DAYS', 30),

    // Add more settings as needed
    'cleanup_chunk_size' => 1000,
    'excluded_types' => [
        // Add activity types that should never be deleted
        'login',
        'critical_action'
    ],
];
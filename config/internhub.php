<?php

return [
    'tracking' => [
        'start_hour' => (int) env('TRACKING_START_HOUR', 8),
        'end_hour' => (int) env('TRACKING_END_HOUR', 18),
        'interval_seconds' => (int) env('TRACKING_INTERVAL_SECONDS', 120),
    ],
];

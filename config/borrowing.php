<?php

return [
    'default_days' => (int) env('BORROWING_DEFAULT_DAYS', 7),
    'min_days'     => (int) env('BORROWING_MIN_DAYS', 3),
    'max_days'     => (int) env('BORROWING_MAX_DAYS', 7),
    'daily_fine'   => (int) env('BORROWING_DAILY_FINE', 2000),
];
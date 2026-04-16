<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Date and Time Formats
    |--------------------------------------------------------------------------
    |
    | These values determine the format strings used throughout the application
    | for displaying and storing dates and times.
    |
    */
    'formats' => [
        'time' => 'H:i:s',
        'date' => 'Y-m-d',
        'datetime' => 'Y-m-d H:i:s',
        'display' => [
            'time' => 'g:i A',
            'date' => 'M d, Y',
            'datetime' => 'M d, Y g:i A',
            'week' => 'M d, D',
            'day' => 'D',
            'weekday' => 'M j',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Times
    |--------------------------------------------------------------------------
    |
    | Default time values used when no specific time is provided
    |
    */
    'defaults' => [
        'midnight' => '00:00:00',
        'end_of_day' => '23:59:59',
        'start' => '09:00:00',
    ],

    /*
    |--------------------------------------------------------------------------
    | Timezone Settings
    |--------------------------------------------------------------------------
    |
    | Default timezone settings and common timezone identifiers
    |
    */
    'timezones' => [
        'default' => env('DEFAULT_TIMEZONE', 'America/New_York'),
        'utc' => 'UTC',
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Duration Settings
    |--------------------------------------------------------------------------
    |
    | Default job duration and available duration options
    |
    */
    'durations' => [
        'default' => env('DEFAULT_JOB_DURATION', 5), // Default duration in minutes
        'options' => [
            5 => '5 minutes',
            30 => '30 minutes',
            60 => '1 hour',
            90 => '1.5 hours',
            120 => '2 hours',
            180 => '3 hours',
        ],
    ],
];

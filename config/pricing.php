<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Subscription Prices
    |--------------------------------------------------------------------------
    |
    | These values define the default monthly prices for different user types
    | in the system. These are used as starting points in the pricing settings.
    |
    */

    'defaults' => [
        'admin' => [
            'monthly_price' => 10.00,
        ],
        'technician' => [
            'monthly_price' => 20.00,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Discount Percentages
    |--------------------------------------------------------------------------
    |
    | These values define the default discount percentages for different
    | subscription periods.
    |
    */

    'discounts' => [
        'half_yearly' => 5, // Default half-yearly discount percentage
        'yearly' => 10,      // Default yearly discount percentage
    ],
];

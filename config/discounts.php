<?php


return [
    /*
|--------------------------------------------------------------------------
| Default stacking order
|--------------------------------------------------------------------------
|
| Options: 'priority', 'percentage_first', 'fixed_first'
|
*/
    'stacking_order' => env('DISCOUNTS_STACKING_ORDER', 'priority'),


    // Maximum total percentage discount allowed (0-100). Null = no global cap
    'max_total_percentage' => env('DISCOUNTS_MAX_TOTAL_PERCENTAGE', null),


    // Rounding options
    'rounding' => [
        'precision' => 2, // decimal places
        'mode' => 'half_up', // supported: half_up, half_down, up, down
        'apply_each_step' => true, // if true round after each discount application
    ],


    // Default per-user usage cap if discount doesn't define one
    'default_per_user_cap' => env('DISCOUNTS_DEFAULT_PER_USER_CAP', 0),


    // Auditing
    'audit_table' => 'discount_audits',


    // Idempotency key required for apply() (recommended to prevent duplicates)
    'require_idempotency_key' => false,
];

<?php

return [
    'name' => 'AdamTicketAgingColors',

    // Defaults used when a mailbox has no overrides.
    'options' => [

        'enabled'   => ['default' => true],

        // Baseline for aging:
        // - status_change (recommended): last status-changed line item, falls back to ticket creation time
        // - waiting_since: uses folder's "waiting since" field when available
        // - last_activity: conversation updated_at (legacy)
        'baseline'  => ['default' => 'status_change'],

        // Thresholds (value + unit). Units: minutes, hours, calendar_days, business_days
        // Level 0 (green) is "new" and applies when elapsed time is LESS THAN OR EQUAL to its threshold.
        'green_value'       => ['default' => 1],
        'green_unit'        => ['default' => 'business_days'],
        'green_intensity'   => ['default' => 15],
        'yellow_value'   => ['default' => 2],
        'yellow_unit'    => ['default' => 'business_days'],
        'yellow_intensity' => ['default' => 20],
        'orange_value'      => ['default' => 4],
        'orange_unit'       => ['default' => 'business_days'],
        'orange_intensity'  => ['default' => 25],
        'red_value'         => ['default' => 6],
        'red_unit'          => ['default' => 'business_days'],
        'red_intensity'     => ['default' => 30],

        // Fixed palette (Green → Yellow → Orange → Red)
        'green_color'    => ['default' => '#4CAF50'],  // Material Green 500
        'yellow_color'   => ['default' => '#FFC107'],  // Material Yellow 500
        'orange_color'   => ['default' => '#FF9800'],  // Material Orange 500
        'red_color'      => ['default' => '#B71C1C'],  // Material Red 900
    ],
];

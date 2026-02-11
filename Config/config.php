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
        'yellow_value'   => ['default' => 2],
        'yellow_unit'    => ['default' => 'business_days'],
        'yellow_intensity' => ['default' => 20],
        'orange_value'      => ['default' => 4],
        'orange_unit'       => ['default' => 'business_days'],
        'orange_intensity'  => ['default' => 25],
        'red_value'         => ['default' => 6],
        'red_unit'          => ['default' => 'business_days'],
        'red_intensity'     => ['default' => 30],

        // Legacy keys (kept for backwards compatibility with earlier builds)
        'yellow_days'   => ['default' => 2],
        'red_days'      => ['default' => 4],        // legacy level 2 (now: orange)
        'deep_red_days' => ['default' => 6],        // legacy level 3 (now: red)

        // Fixed palette (Yellow → Orange → Red)
        'yellow_color'   => ['default' => '#FFC107'],  // Material Yellow 500
        'orange_color'   => ['default' => '#FF9800'],  // Material Orange 500
        'red_color'      => ['default' => '#B71C1C'],  // Material Red 900

        // Legacy palette keys (kept for backward compatibility)
        'legacy_level2_color' => ['default' => '#FF9800'], // stored as red_color in older versions
        'legacy_level3_color' => ['default' => '#B71C1C'], // stored as deep_red_color in older versions
    ],
];

<?php

namespace Modules\AdamTicketAgingColors\Support;

use Carbon\Carbon;

class MailboxSettings
{
    const ALIAS = 'adamticketagingcolors';

    protected static $cache = [];
    protected static $mergedCache = [];

    public static function defaults(): array
    {
        return [

            'enabled'        => (bool)\Config::get(self::ALIAS.'.options.enabled.default', true),
            'baseline'       => \Config::get(self::ALIAS.'.options.baseline.default', 'status_change'),

            // Level 1/2/3 escalation (value + unit + intensity). Default units are business days.
            'yellow_value'   => (int)\Config::get(self::ALIAS.'.options.yellow_value.default', 2),
            'yellow_unit'    => \Config::get(self::ALIAS.'.options.yellow_unit.default', 'business_days'),
            'yellow_intensity' => (int)\Config::get(self::ALIAS.'.options.yellow_intensity.default', 20),
            'orange_value'      => (int)\Config::get(self::ALIAS.'.options.orange_value.default', 4),
            'orange_unit'       => \Config::get(self::ALIAS.'.options.orange_unit.default', 'business_days'),
            'orange_intensity'  => (int)\Config::get(self::ALIAS.'.options.orange_intensity.default', 25),
            'red_value'         => (int)\Config::get(self::ALIAS.'.options.red_value.default', 6),
            'red_unit'          => \Config::get(self::ALIAS.'.options.red_unit.default', 'business_days'),
            'red_intensity'     => (int)\Config::get(self::ALIAS.'.options.red_intensity.default', 30),

            // Colors (Yellow → Orange → Red)
            'yellow_color'   => \Config::get(self::ALIAS.'.options.yellow_color.default', '#FFC107'),
            'orange_color'   => \Config::get(self::ALIAS.'.options.orange_color.default', '#FF9800'),
            'red_color'      => \Config::get(self::ALIAS.'.options.red_color.default', '#B71C1C'),

        ];
    }

    public static function optionKey(int $mailboxId): string
    {
        return self::ALIAS.'.mailbox_settings.'.$mailboxId;
    }

    public static function getMailboxSettings(int $mailboxId): array
    {
        if (isset(self::$cache[$mailboxId])) {
            return self::$cache[$mailboxId];
        }
        // FreeScout's Option::get may return a string, array, or null depending on version/storage.
        $raw = \Option::get(self::optionKey($mailboxId));

        if (is_array($raw)) {
            $data = $raw;
        } elseif (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            $data = is_array($decoded) ? $decoded : [];
        } else {
            $data = [];
        }

        self::$cache[$mailboxId] = $data;
        return self::$cache[$mailboxId];
    }

    public static function getMergedForMailbox(int $mailboxId): array
    {
        if (isset(self::$mergedCache[$mailboxId])) {
            return self::$mergedCache[$mailboxId];
        }

        $merged = array_merge(self::defaults(), self::getMailboxSettings($mailboxId));

        // Backward compatibility: older versions stored *_days instead of *_value/unit.
        if (!isset($merged['yellow_value']) && isset($merged['yellow_days'])) {
            $merged['yellow_value'] = (int)$merged['yellow_days'];
        }
        // Legacy: "red_*" was level 2 (orange), "deep_red_*" was level 3 (red)
        if (!isset($merged['orange_value']) && isset($merged['red_value'])) {
            $merged['orange_value'] = (int)$merged['red_value'];
        }
        if (!isset($merged['red_value']) && isset($merged['deep_red_value'])) {
            $merged['red_value'] = (int)$merged['deep_red_value'];
        }
        if (!isset($merged['orange_value']) && isset($merged['red_days'])) {
            $merged['orange_value'] = (int)$merged['red_days'];
        }
        if (!isset($merged['red_value']) && isset($merged['deep_red_days'])) {
            $merged['red_value'] = (int)$merged['deep_red_days'];
        }

        // Units defaults.
        foreach (['yellow_unit','orange_unit','red_unit'] as $k) {
            if (!isset($merged[$k]) || !$merged[$k]) {
                $merged[$k] = 'business_days';
            }
        }

        // Legacy intensities.
        if (!isset($merged['orange_intensity']) && isset($merged['red_intensity'])) {
            $merged['orange_intensity'] = (int)$merged['red_intensity'];
        }
        if (!isset($merged['red_intensity']) && isset($merged['deep_red_intensity'])) {
            $merged['red_intensity'] = (int)$merged['deep_red_intensity'];
        }

        // Legacy palette keys.
        if (!isset($merged['orange_color']) && !empty($merged['red_color'])) {
            // old "red_color" was orange
            $merged['orange_color'] = (string)$merged['red_color'];
        }
        if (!isset($merged['red_color']) && !empty($merged['deep_red_color'])) {
            $merged['red_color'] = (string)$merged['deep_red_color'];
        }

        // Sanitize numbers
        foreach (['yellow_value','orange_value','red_value'] as $k) {
            $merged[$k] = max(0, (int)($merged[$k] ?? 0));
        }

        // Sanitize intensity values (5-100%)
        foreach (['yellow_intensity','orange_intensity','red_intensity'] as $k) {
            $val = (int)($merged[$k] ?? 0);
            if ($val < 5) {
                $val = ($k === 'yellow_intensity') ? 20 : (($k === 'orange_intensity') ? 25 : 30);
            }
            $merged[$k] = max(5, min(100, $val));
        }

        // Sanitize units
        $allowedUnits = ['minutes','hours','business_days','calendar_days'];
        foreach (['yellow_unit','orange_unit','red_unit'] as $k) {
            $u = (string)($merged[$k] ?? 'business_days');
            $merged[$k] = in_array($u, $allowedUnits) ? $u : 'business_days';
        }

        // Reduce to only the settings used by the current module version (keeps read-compat but avoids writing legacy junk).
        $merged = array_intersect_key($merged, array_flip([
            'enabled','baseline',
            'yellow_value','yellow_unit','yellow_intensity','yellow_color',
            'orange_value','orange_unit','orange_intensity','orange_color',
            'red_value','red_unit','red_intensity','red_color',
        ]));

        self::$mergedCache[$mailboxId] = $merged;
        return self::$mergedCache[$mailboxId];
    }


    public static function saveMailboxSettings(int $mailboxId, array $data): void
    {
        self::$cache[$mailboxId] = $data;
        unset(self::$mergedCache[$mailboxId]);
        // Store as JSON for portability. Some installs may still return an array when reading.
        \Option::set(self::optionKey($mailboxId), json_encode($data));
    }

    /**
     * Count business days (Mon–Fri) between two datetimes.
     * - Same-day => 0
     * - Weekend days are skipped
     */
    public static function businessDaysBetween(Carbon $from, Carbon $to): int
    {
        if ($to->lessThanOrEqualTo($from)) {
            return 0;
        }

        // O(1) weekday calculation (Mon–Fri), counting full days elapsed since the baseline day.
        $start = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        $days = $start->diffInDays($end);
        if ($days <= 0) {
            return 0;
        }

        $fullWeeks = intdiv($days, 7);
        $business = $fullWeeks * 5;
        $remainder = $days % 7;

        // ISO: 1 (Mon) .. 7 (Sun)
        $startIso = (int)$start->dayOfWeekIso;
        for ($i = 1; $i <= $remainder; $i++) {
            $iso = $startIso + $i;
            $iso = $iso % 7;
            if ($iso === 0) {
                $iso = 7;
            }
            if ($iso <= 5) {
                $business++;
            }
        }

        return $business;
    }
}

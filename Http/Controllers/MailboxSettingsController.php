<?php

namespace Modules\AdamTicketAgingColors\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mailbox;
use Illuminate\Http\Request;
use Modules\AdamTicketAgingColors\Support\MailboxSettings;

class MailboxSettingsController extends Controller
{
    public function edit($id)
    {
        $mailbox = Mailbox::findOrFail($id);
        $this->authorize('update', $mailbox);

        $settings = MailboxSettings::getMergedForMailbox((int)$mailbox->id);

        return view('adamticketagingcolors::mailboxes.settings', [
            'mailbox' => $mailbox,
            'settings' => $settings,
        ]);
    }

    /**
     * Persist mailbox aging color settings from the request and redirect to the mailbox settings page.
     *
     * Validates input, normalizes legacy payload keys, saves merged settings for the mailbox,
     * and flashes a success message before redirecting.
     *
     * @param int $id The mailbox ID to update.
     * @param \Illuminate\Http\Request $request Incoming request containing settings payload.
     * @return \Illuminate\Http\RedirectResponse Redirect to the mailbox settings route for the given mailbox.
     */
    public function save($id, Request $request)
    {
        $mailbox = Mailbox::findOrFail($id);
        $this->authorize('update', $mailbox);

        // Validate user input (keep permissive upper bounds to avoid surprising admins).
        $request->validate([
            'baseline' => 'nullable|in:status_change,waiting_since,last_activity',
            'green_value' => 'nullable|integer|min:0|max:100000',
            'yellow_value' => 'nullable|integer|min:0|max:100000',
            'orange_value' => 'nullable|integer|min:0|max:100000',
            'red_value' => 'nullable|integer|min:0|max:100000',
            'green_unit' => 'nullable|in:minutes,hours,business_days,calendar_days',
            'yellow_unit' => 'nullable|in:minutes,hours,business_days,calendar_days',
            'orange_unit' => 'nullable|in:minutes,hours,business_days,calendar_days',
            'red_unit' => 'nullable|in:minutes,hours,business_days,calendar_days',
            'green_intensity' => 'nullable|integer|min:5|max:100',
            'yellow_intensity' => 'nullable|integer|min:5|max:100',
            'orange_intensity' => 'nullable|integer|min:5|max:100',
            'red_intensity' => 'nullable|integer|min:5|max:100',
        ]);

        $defaults = MailboxSettings::defaults();

        $data = [

            'enabled' => (bool)($request->input('enabled') ?? false),
            // Laravel version compatibility: Request::boolean() may not exist on older FreeScout stacks.
            'baseline' => $request->get('baseline', 'status_change'),

            // Level 0 (green/new) applies when elapsed time is <= threshold
            'green_value' => max(0, (int)$request->get('green_value', 1)),
            'green_unit'  => $request->get('green_unit', 'business_days'),
            'green_intensity' => max(5, min(100, (int)$request->get('green_intensity', 15))),

            // Level thresholds (value + unit + intensity)
            'yellow_value' => max(0, (int)$request->get('yellow_value', (int)$request->get('yellow_days', 2))),
            'yellow_unit'  => $request->get('yellow_unit', 'business_days'),
            'yellow_intensity' => max(5, min(100, (int)$request->get('yellow_intensity', 20))),
            // Level 2 (orange): modern key is orange_*. Legacy level-2 payloads only provided red_days.
            // Do not fall back to modern red_* keys here; those now belong to Level 3.
            'orange_value'    => max(0, (int)$request->get('orange_value', (int)$request->get('red_days', (int)$defaults['orange_value']))),
            'orange_unit'     => $request->get('orange_unit', $defaults['orange_unit']),
            'orange_intensity' => max(5, min(100, (int)$request->get('orange_intensity', (int)$defaults['orange_intensity']))),

            // Level 3 (red): modern key is red_*. Legacy level-3 payloads used deep_red_* / deep_red_days.
            'red_value' => max(0, (int)$request->get('red_value', (int)$request->get('deep_red_value', (int)$request->get('deep_red_days', 6)))),
            'red_unit'  => $request->get('red_unit', $request->get('deep_red_unit', 'business_days')),
            'red_intensity' => max(5, min(100, (int)$request->get('red_intensity', (int)$request->get('deep_red_intensity', 30)))),

            // Fixed palette (UI no longer exposes color pickers)
            'green_color' => $defaults['green_color'],
            'yellow_color' => $defaults['yellow_color'],
            'orange_color' => $defaults['orange_color'],
            'red_color' => $defaults['red_color'],

        ];

        // Normalize baseline values (defensive; validation already covers the common cases).
        $allowed = ['status_change','last_activity','waiting_since'];
        if (!in_array($data['baseline'], $allowed)) {
            $data['baseline'] = 'status_change';
        }

        MailboxSettings::saveMailboxSettings((int)$mailbox->id, $data);

        \Session::flash('flash_success_floating', __('Settings saved'));

        return redirect()->route('adamticketagingcolors.mailboxes.settings', ['id' => $mailbox->id]);
    }
}

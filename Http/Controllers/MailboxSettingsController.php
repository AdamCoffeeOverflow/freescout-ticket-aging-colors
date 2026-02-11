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

    public function save($id, Request $request)
    {
        $mailbox = Mailbox::findOrFail($id);
        $this->authorize('update', $mailbox);

        $data = [

            'enabled' => (bool)$request->get('enabled'),
            'baseline' => $request->get('baseline', 'status_change'),

            // Level thresholds (value + unit + intensity)
            'yellow_value' => max(0, (int)$request->get('yellow_value', (int)$request->get('yellow_days', 2))),
            'yellow_unit'  => $request->get('yellow_unit', 'business_days'),
            'yellow_intensity' => max(5, min(100, (int)$request->get('yellow_intensity', 20))),
            // Level 2 (orange) - legacy form fields: red_* or red_days
            'orange_value'    => max(0, (int)$request->get('orange_value', (int)$request->get('red_value', (int)$request->get('red_days', 4)))),
            'orange_unit'     => $request->get('orange_unit', $request->get('red_unit', 'business_days')),
            'orange_intensity' => max(5, min(100, (int)$request->get('orange_intensity', (int)$request->get('red_intensity', 25)))),

            // Level 3 (red) - legacy form fields: deep_red_* or deep_red_days
            'red_value' => max(0, (int)$request->get('red_value', (int)$request->get('deep_red_value', (int)$request->get('deep_red_days', 6)))),
            'red_unit'  => $request->get('red_unit', $request->get('deep_red_unit', 'business_days')),
            'red_intensity' => max(5, min(100, (int)$request->get('red_intensity', (int)$request->get('deep_red_intensity', 30)))),

            // Fixed palette (UI no longer exposes color pickers)
            'yellow_color' => MailboxSettings::defaults()['yellow_color'],
            'orange_color' => MailboxSettings::defaults()['orange_color'],
            'red_color' => MailboxSettings::defaults()['red_color'],

        ];
        // Validate units
        $allowedUnits = ['minutes','hours','business_days','calendar_days'];
        foreach (['yellow_unit','orange_unit','red_unit'] as $key) {
            if (!in_array($data[$key], $allowedUnits)) {
                $data[$key] = 'business_days';
            }
        }

// Normalize baseline values
        $allowed = ['status_change','last_activity','waiting_since'];
        if (!in_array($data['baseline'], $allowed)) {
            $data['baseline'] = 'status_change';
        }

        MailboxSettings::saveMailboxSettings((int)$mailbox->id, $data);

        \Session::flash('flash_success_floating', __('Settings saved'));

        return redirect()->route('adamticketagingcolors.mailboxes.settings', ['id' => $mailbox->id]);
    }
}

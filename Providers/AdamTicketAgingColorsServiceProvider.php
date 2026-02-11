<?php

namespace Modules\AdamTicketAgingColors\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AdamTicketAgingColors\Support\MailboxSettings;
use App\Thread;
use Carbon\Carbon;

// Module alias constant (FreeScout best practice)
define('ADAMTICKETAGINGCOLORS_MODULE', 'adamticketagingcolors');

class AdamTicketAgingColorsServiceProvider extends ServiceProvider
{
    const ALIAS = ADAMTICKETAGINGCOLORS_MODULE;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', self::ALIAS);

        // Routes
        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');

        // Assets
        // Assets (use Minify helpers per FreeScout dev guide)
        \Eventy::addAction('layout.head', function() {
            try {
                echo \Minify::stylesheet([
                    \Module::getPublicPath(self::ALIAS).'/css/module.css',
                ]);
            } catch (\Exception $e) {
                // Fail silently to avoid breaking the UI if Minify is unavailable
            }
        });
        \Eventy::addAction('layout.footer', function() {
            try {
                echo \Minify::javascript([
                    \Module::getPublicPath(self::ALIAS).'/js/module.js',
                ]);
            } catch (\Exception $e) {
                // Fail silently
            }
        });

        // Add a mailbox left menu entry (separate page like other mailbox settings).
        \Eventy::addAction('mailboxes.settings.menu', function($mailbox) {
            // Permission check: mimic core menu behavior
            if (!Auth::check() || !Auth::user()->can('update', $mailbox)) {
                return;
            }
            echo view(self::ALIAS.'::mailboxes/menu_item', ['mailbox' => $mailbox])->render();
        });

        // Preload data for table rows (last status change timestamps and folder waiting_since field).
        // Preload data for conversations table.
        // Priority 30: run after most modules (ex: CustomFields) so we don't lose preloaded props.
        \Eventy::addFilter('conversations_table.preload_table_data', function($conversations) {
            if (!$conversations || !count($conversations)) {
                return $conversations;
            }

            // Collect conversation IDs
            $ids = [];
            foreach ($conversations as $c) {
                $ids[] = $c->id;
            }

            // Last status change timestamp per conversation (max lineitem status_changed)
            $rows = DB::table('threads')
                ->select('conversation_id', DB::raw('MAX(created_at) as last_status_changed_at'))
                ->whereIn('conversation_id', $ids)
                ->where('type', Thread::TYPE_LINEITEM)
                ->where('action_type', Thread::ACTION_TYPE_STATUS_CHANGED)
                ->groupBy('conversation_id')
                ->get();

            $map = [];
            foreach ($rows as $r) {
                $map[(int)$r->conversation_id] = $r->last_status_changed_at;
            }

            // Waiting-since field for this folder (if any).
            $folderId = request()->route('folder_id') ?? request()->get('folder_id') ?? request()->input('folder_id');
            $waitingSinceField = null;
            if ($folderId) {
                try {
                    $folder = \App\Folder::find($folderId);
                    if ($folder) {
                        $waitingSinceField = $folder->getWaitingSinceField();
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            foreach ($conversations as $c) {
                $c->adamtac_last_status_changed_at = $map[$c->id] ?? null;
                $c->adamtac_waiting_since_field = $waitingSinceField; // same for all in folder view
            }

            return $conversations;
        }, 30, 1);

        // Add a row class based on mailbox settings and thresholds.
        // Add a row class based on mailbox settings and thresholds.
        // Priority 10: run early (and echo a trailing space) to avoid class concatenation with modules
        // that echo without leading space.
        \Eventy::addAction('conversations_table.row_class', function($conversation) {
            try {
                // Only apply to Active tickets
                if (!$conversation || !$conversation->isActive()) {
                    return;
                }

                $mailboxId = (int)$conversation->mailbox_id;
                $settings = MailboxSettings::getMergedForMailbox($mailboxId);

                if (empty($settings['enabled'])) {
                    return;
                }

                $baseline = $settings['baseline'] ?? 'status_change';
                $from = null;

                if ($baseline === 'status_change') {
                    $from = $conversation->adamtac_last_status_changed_at ?? null;
                    if (!$from) {
                        // If no status-change line item exists, fall back to created_at
                        $from = $conversation->created_at;
                    }
                } elseif ($baseline === 'waiting_since') {
                    $field = $conversation->adamtac_waiting_since_field ?? null;
                    if ($field) {
                        $from = $conversation->$field ?: $conversation->updated_at;
                    } else {
                        // If no waiting-since field for this folder, do not guess.
                        return;
                    }
                } else { // last_activity (legacy option)
                    $from = $conversation->updated_at;
                }

                $from = $from ? Carbon::parse($from) : null;
                if (!$from) {
                    return;
                }

                $now = Carbon::now();

                // Determine elapsed time in a given unit.
                $elapsedForUnit = function(string $unit) use ($from, $now): int {
                    switch ($unit) {
                        case 'minutes':
                            return $from->diffInMinutes($now);
                        case 'hours':
                            return $from->diffInHours($now);
                        case 'calendar_days':
                            return $from->diffInDays($now);
                        case 'business_days':
                        default:
                            return MailboxSettings::businessDaysBetween($from, $now);
                    }
                };

                $l1v = (int)($settings['yellow_value'] ?? 2);
                $l1u = (string)($settings['yellow_unit'] ?? 'business_days');
                $l2v = (int)($settings['orange_value'] ?? 4);
                $l2u = (string)($settings['orange_unit'] ?? 'business_days');
                $l3v = (int)($settings['red_value'] ?? 6);
                $l3u = (string)($settings['red_unit'] ?? 'business_days');

                $cls = '';
                if ($l3v > 0 && $elapsedForUnit($l3u) > $l3v) {
                    $cls = 'adamtac-row adamtac-red';
                } elseif ($l2v > 0 && $elapsedForUnit($l2u) > $l2v) {
                    $cls = 'adamtac-row adamtac-orange';
                } elseif ($l1v > 0 && $elapsedForUnit($l1u) > $l1v) {
                    $cls = 'adamtac-row adamtac-yellow';
                }

                // IMPORTANT: this hook is an *Action* used inside the <tr class="..."> attribute.
                // Actions must echo output (return values are ignored).
                if ($cls) {
                    echo ' '.$cls.' ';
                }
            } catch (\Throwable $e) {
                // no-op
            }
        }, 10, 1);

        // Expose settings to the layout so CSS variables can be set per mailbox view.
        \Eventy::addAction('layout.body_bottom', function() {
            // We set CSS vars per mailbox on pages that have mailbox_id in body attrs.
            // This is lightweight; actual values are read from options when needed.
            echo view(self::ALIAS.'::layouts/css_vars')->render();
        }, 10, 1);
    }

    public function register()
    {
        // No-op
    }
}

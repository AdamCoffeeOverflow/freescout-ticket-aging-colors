@php
    // Determine mailbox ID for current page.
    // Conversation list pages may have mailbox_id OR folder_id (folder belongs to a mailbox).
    $mboxId = request()->get('mailbox_id');
    if (!$mboxId) {
        $folderId = request()->route('folder_id') ?? request()->get('folder_id') ?? request()->input('folder_id');
        if ($folderId) {
            try {
                $folder = \App\Folder::find((int)$folderId);
                if ($folder && !empty($folder->mailbox_id)) {
                    $mboxId = (int)$folder->mailbox_id;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
    if (!$mboxId) {
        // On mailbox settings routes, mailbox id is in route param {id}
        $mboxId = request()->route('id');
        if (!$mboxId) {
            $mboxId = request()->route('mailbox_id');
        }
    }
    $mboxId = $mboxId ? (int)$mboxId : 0;

    $vars = null;
    if ($mboxId) {
        try {
            $vars = \Modules\AdamTicketAgingColors\Support\MailboxSettings::getMergedForMailbox($mboxId);
        } catch (\Exception $e) {
            $vars = null;
        }
    }
@endphp

@php
    // Helper: normalize a hex color (#RGB or #RRGGBB) into an "R, G, B" string
    $hexToRgb = function ($hex) {
        $hex = trim((string)$hex);
        if ($hex === '') { return null; }
        if ($hex[0] === '#') { $hex = substr($hex, 1); }
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) { return null; }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return $r.', '.$g.', '.$b;
    };
@endphp

@if (!empty($vars) && !empty($vars['enabled']))
    <style>
        :root {
            --adamtac-yellow: {{ $vars['yellow_color'] ?? '#FFC107' }};
            --adamtac-yellow-rgb: {{ $hexToRgb($vars['yellow_color'] ?? '#FFC107') ?? '255, 193, 7' }};
            --adamtac-orange: {{ $vars['orange_color'] ?? '#FF9800' }};
            --adamtac-orange-rgb: {{ $hexToRgb($vars['orange_color'] ?? '#FF9800') ?? '255, 152, 0' }};
            --adamtac-red: {{ $vars['red_color'] ?? '#B71C1C' }};
            --adamtac-red-rgb: {{ $hexToRgb($vars['red_color'] ?? '#B71C1C') ?? '183, 28, 28' }};
            --adamtac-yellow-intensity: {{ $vars['yellow_intensity'] ?? 20 }};
            --adamtac-orange-intensity: {{ $vars['orange_intensity'] ?? 25 }};
            --adamtac-red-intensity: {{ $vars['red_intensity'] ?? 30 }};
        }
    </style>
@endif

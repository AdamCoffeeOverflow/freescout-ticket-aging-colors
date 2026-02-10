# Ticket Aging Colors (AdamTicketAgingColors)

FreeScout module that adds a **left-side aging accent bar** on each conversation row in the **ticket list** so you can quickly spot **Active** tickets that have been waiting too long.

## What it does

- Applies **only** to tickets in **Active** status.
- Does **not** apply to **Closed** tickets.
- Adds a CSS-driven **accent bar** visible in conversation list.
- Supports **three escalation levels** (Level 1 / Level 2 / Level 3), each with:
  - Threshold (value + unit)
  - Color (Yellow, Orange, Deep Red)
  - Intensity (0–100) controlling the pulse opacity

## Aging baseline (how time is calculated)

Choose one baseline per mailbox:

1. **Last status change (recommended)**  
   Uses the most recent **status changed** line-item on the ticket. If none exists, it falls back to the ticket creation time.

2. **Waiting since**  
   Uses FreeScout’s folder “waiting since” field (when available for the current folder).

Units supported for thresholds: **minutes**, **hours**, **calendar days**, **business days (Mon–Fri)**.

## Where to configure

Go to: **Mailboxes → (select mailbox) → Settings → Ticket Aging Colors**

Settings are stored per mailbox using FreeScout options with the prefix:
- `adamticketagingcolors.mailbox_settings.{mailbox_id}`

## Install

1. Copy the module folder into your FreeScout instance:
   - `Modules/AdamTicketAgingColors`
2. Activate it in **Manage → Modules**.
3. (If needed) clear caches:
   - `php artisan cache:clear`

## Compatibility

- Built using FreeScout’s module hooks and options conventions.
- No database migrations or schema changes.
- Tested against FreeScout 1.8.x (module.json `requiredAppVersion: 1.8.0`).


### UI Compatibility
- This module only styles the **indicator column** (`td.conv-current`) and does not modify `.conv-fader` or other table cells to avoid conflicts with themes and Custom Fields UI.


## License

Licensed under **AGPL-3.0-only**. See `LICENSE` and `NOTICE`.

### Network use (AGPL §13)

If this module is used on a FreeScout instance that users access over a network (HTTP/HTTPS), you must make the corresponding code for the exact version running on the server available to those users, as required by the AGPL.



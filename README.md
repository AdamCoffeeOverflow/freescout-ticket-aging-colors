# Ticket Aging Colors (AdamTicketAgingColors)
Author: AdamCoffeeOverflow

<img width="192" height="192" alt="module-icon" src="https://github.com/user-attachments/assets/fd6564b1-0788-4115-ad1f-a420a8f278da" />

FreeScout module that adds a **left-side aging accent bar** on each conversation row in the **ticket list** so you can quickly spot **Active** tickets that have been waiting too long, with a quick glance!

![Recording2026-02-10122818-ezgif com-loop-count](https://github.com/user-attachments/assets/f58c38de-805b-40bb-8c71-5402ee73ad7d)

## What it does

- Applies **only** to tickets in **Active** status.
- Does **not** apply to **Closed** tickets.
- Adds a CSS-driven **accent bar** visible in conversation list.
- Supports **three escalation levels** (Level 1 / Level 2 / Level 3), each with:
  - Threshold (value + unit)
  - Color (Yellow, Orange, Deep Red)
  - Intensity (0–100) controlling the pulse opacity

<img width="366" height="505" alt="Screenshot 2026-02-10 122918" src="https://github.com/user-attachments/assets/d02295fc-8df9-495d-9f5d-f546030a9810" />

## Aging baseline (how time is calculated)

Choose one baseline per mailbox:

1. **Last status change (recommended)**  
   Uses the most recent **status changed** line-item on the ticket. If none exists, it falls back to the ticket creation time.

2. **Waiting since**  
   Uses FreeScout’s folder “waiting since” field (when available for the current folder).

Units supported for thresholds: **minutes**, **hours**, **calendar days**, **business days (Mon–Fri)**.

<img width="1565" height="657" alt="Screenshot 2026-02-10 122945" src="https://github.com/user-attachments/assets/0d6e1e48-a955-45a8-a265-435317bdbe1f" />

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



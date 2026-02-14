# Changelog

All notable changes to **Ticket Aging Colors** (AdamTicketAgingColors) for FreeScout.

## [1.0.0] - 2026-02-06

### Added
- Left-side **ticket aging accent bar** on the Conversations list for **Active** tickets only.
- Per-mailbox settings:
  - **Enable/Disable**
  - Baseline: **Last status change** (recommended) or **Waiting since**
  - 3 thresholds (Level 1/2/3): value + unit
  - Per-level color + **intensity** (0–100)
- Breathing (pulse) animation for the left accent bar (all three levels).
- Robust selector support for FreeScout’s `td.conv-current` cell.

### Notes
- No database migrations. Settings are stored via FreeScout options, prefixed with `adamticketagingcolors.*`.

## [1.0.1] - 2026-02-06

### Changed
- Enforced the three requested colors only: **Yellow**, **Orange**, **Red** (legacy keys are mapped automatically).
- Improved compatibility with the **CustomFields** module by extending the left bar to the extra row it renders.
- More reliable mailbox detection (supports folder-based conversation lists).
- Reduced inter-module class conflicts by echoing a trailing space in the row class hook.

## [1.0.3] - 2026-02-06

### Fixed

## [1.0.4] - 2026-02-10
- Included auto-update support via FreeScout's Modules UI when hosted on GitHub.
- Unified the left bar rendering for both the main conversation row and the CustomFields extra row
  (both are now drawn using the same `::before` bar implementation).
- Synced the breathing animation phase for the CustomFields extension so it matches the main bar.
- Ensured intensity applies identically to both the main bar and the extended section.

## [1.0.5] - 2026-02-14

## [1.0.7] - 2026-02-14

### Fixed
- Hardened asset injection to avoid fatal errors when `Minify` is unavailable.
- Prevented constant redefinition edge-case.
- Messed up the repo files version, re-organized it, bumped everything to 1.0.7

### Improved
- Faster business-day calculation and reduced per-row repeated computations.
- Removed inline styles from settings UI for stricter CSP compatibility.

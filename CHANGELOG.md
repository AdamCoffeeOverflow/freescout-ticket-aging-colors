# Changelog

## 1.1.10 - 2026-09-10

### Fixed

- Fixed the responsive conversation-table CSS conflict identified from issue #21, targeting the official iOS app portrait report and tablet-width views without changing FreeScout core.
- Scoped the 5px `conv-current` sizing to desktop table layout only so Ticket Aging Colors no longer overrides FreeScout's responsive 100%-width cell geometry.
- Aligned the subject-cell aging-bar fallback with FreeScout's `1000px` conversation-table breakpoint instead of switching only below `767px`.
- Kept the customer cell untouched while preserving animated, static, and reduced-motion aging indicators throughout the responsive range.

## 1.1.9 - 2026-09-09

### Added

- Added Portuguese (Portugal) (`pt-PT`) translations for the module settings UI and confirmation message, following the contribution in issue #23.

## 1.1.8 - 2026-09-09

### Fixed

- Fixed the reported FreeScout mobile/app conversation overview issue where enabling Ticket Aging Colors could hide the customer name.
- Removed positional `td:first-child` aging-bar fallbacks and now target FreeScout's explicit `conv-current` indicator cell. The existing `conversations_table.before_subject` marker remains the compatibility path for layouts that need the subject-cell mobile bar.
- Source-verified the conversation-table hooks and markup against FreeScout 1.8.0, 1.8.238, and 1.8.239.

## 1.1.7 - 2026-07-20

### Added

- Per-mailbox setting to disable the accent bar breathing/pulse animation while keeping a static bar at the configured intensity.
- English and Russian translations for the module settings UI and confirmation message.

### Fixed

- Load versioned module CSS and JavaScript directly from FreeScout's published module asset URL to avoid stale or missing Minify-bundle injection on affected Linux installations.

### Improved

- Reuse the active row color variables for static mode instead of duplicating four color-specific override blocks.
- Apply the animation phase delay after the animation shorthand so dynamically inserted companion rows remain synchronized.

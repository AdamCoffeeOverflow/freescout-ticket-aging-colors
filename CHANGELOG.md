# Changelog

## 1.1.8 - 2026-09-05

### Fixed

- Fixed FreeScout mobile/app conversation overview compatibility where enabling Ticket Aging Colors could collapse the first visible conversation cell and hide the customer name.
- Removed positional `td:first-child` aging-bar fallbacks. Supported FreeScout versions provide the explicit `conv-current` indicator cell, while the existing `conversations_table.before_subject` marker remains the compatibility path for layouts that need the subject-cell mobile bar.
- Verified the conversation-table hooks and markup against FreeScout 1.8.239.

## 1.1.7 - 2026-07-20

### Added

- Per-mailbox setting to disable the accent bar breathing/pulse animation while keeping a static bar at the configured intensity.
- English and Russian translations for the module settings UI and confirmation message.

### Fixed

- Load versioned module CSS and JavaScript directly from FreeScout's published module asset URL to avoid stale or missing Minify-bundle injection on affected Linux installations.

### Improved

- Reuse the active row color variables for static mode instead of duplicating four color-specific override blocks.
- Apply the animation phase delay after the animation shorthand so dynamically inserted companion rows remain synchronized.

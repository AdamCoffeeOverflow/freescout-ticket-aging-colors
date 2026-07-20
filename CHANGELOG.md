# Changelog

## 1.1.7 - 2026-07-20

### Added

- Per-mailbox setting to disable the accent bar breathing/pulse animation while keeping a static bar at the configured intensity.
- English and Russian translations for the module settings UI and confirmation message.

### Fixed

- Load versioned module CSS and JavaScript directly from FreeScout's published module asset URL to avoid stale or missing Minify-bundle injection on affected Linux installations.

### Improved

- Reuse the active row color variables for static mode instead of duplicating four color-specific override blocks.
- Apply the animation phase delay after the animation shorthand so dynamically inserted companion rows remain synchronized.

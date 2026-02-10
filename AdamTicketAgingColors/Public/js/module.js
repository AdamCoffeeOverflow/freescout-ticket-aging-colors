// SPDX-License-Identifier: AGPL-3.0-only
// AdamTicketAgingColors

(function () {
  'use strict';

  // Anchor animation phase so dynamically injected elements stay synced.
  try {
    var root = document.documentElement;
    var t = Date.now() / 1000;
    root.style.setProperty('--adamtac-sync', t.toFixed(3));
  } catch (e) {
    // no-op
  }

  // CSP-safe slider label updates (replaces inline oninput handlers).
  function updateRangeLabel(input) {
    if (!input || !input.id) return;
    // Expected: yellow_intensity -> yellow_intensity_val
    var labelId = input.id + '_val';
    var el = document.getElementById(labelId);
    if (el) {
      el.textContent = String(input.value);
    }
  }

  function initRangeLabels(scope) {
    var rootEl = scope || document;
    var ranges = rootEl.querySelectorAll('input[type="range"][id$="_intensity"]');
    for (var i = 0; i < ranges.length; i++) {
      updateRangeLabel(ranges[i]);
    }
  }

  function onInput(e) {
    var t = e.target;
    if (t && t.tagName === 'INPUT' && t.type === 'range' && /_intensity$/.test(t.id || '')) {
      updateRangeLabel(t);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      initRangeLabels(document);
    });
  } else {
    initRangeLabels(document);
  }

  // Event delegation so it still works if the settings page is rendered later.
  document.addEventListener('input', onInput, true);
})();
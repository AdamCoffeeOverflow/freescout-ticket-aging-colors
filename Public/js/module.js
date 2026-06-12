// SPDX-License-Identifier: AGPL-3.0-only
// AdamTicketAgingColors

(function () {
    'use strict';

    function closestTag(el, tagName) {
        tagName = String(tagName || '').toUpperCase();
        while (el && el.nodeType === 1) {
            if (el.tagName === tagName) {
                return el;
            }
            el = el.parentNode;
        }
        return null;
    }

    function safeAddAgingClasses(row, classValue) {
        if (!row || !classValue || !row.classList) {
            return;
        }

        var allowed = {
            'adamtac-row': true,
            'adamtac-green': true,
            'adamtac-yellow': true,
            'adamtac-orange': true,
            'adamtac-red': true
        };
        var parts = String(classValue).split(/\s+/);

        for (var i = 0; i < parts.length; i++) {
            if (allowed[parts[i]]) {
                row.classList.add(parts[i]);
            }
        }
    }

    function applyAgingMarkers(scope) {
        var rootEl = scope && scope.querySelectorAll ? scope : document;
        var markers = rootEl.querySelectorAll('.adamtac-row-marker[data-adamtac-class]');

        for (var i = 0; i < markers.length; i++) {
            var marker = markers[i];
            var row = closestTag(marker, 'tr');
            safeAddAgingClasses(row, marker.getAttribute('data-adamtac-class'));
            marker.setAttribute('data-adamtac-applied', '1');
        }
    }

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
        if (!input || !input.id) {
            return;
        }

        // Expected: yellow_intensity -> yellow_intensity_val.
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
        var target = e.target;
        if (target && target.tagName === 'INPUT' && target.type === 'range' && /_intensity$/.test(target.id || '')) {
            updateRangeLabel(target);
        }
    }

    function init(scope) {
        applyAgingMarkers(scope || document);
        initRangeLabels(scope || document);
    }

    function scheduleInit() {
        if (scheduleInit.pending) {
            return;
        }

        scheduleInit.pending = true;
        window.setTimeout(function () {
            scheduleInit.pending = false;
            init(document);
        }, 50);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            init(document);
        });
    } else {
        init(document);
    }

    // Event delegation so it still works if the settings page is rendered later.
    document.addEventListener('input', onInput, true);

    // FreeScout refreshes/replaces parts of the conversation table via AJAX.
    if (window.jQuery) {
        window.jQuery(document).ajaxComplete(function () {
            scheduleInit();
        });
    }

    if (window.MutationObserver && document.body) {
        try {
            var observer = new MutationObserver(function () {
                scheduleInit();
            });
            observer.observe(document.body, {childList: true, subtree: true});
        } catch (e) {
            // no-op
        }
    }
})();

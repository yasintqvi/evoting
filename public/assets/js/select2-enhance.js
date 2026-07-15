/**
 * Project-wide select2 enhancement: in every MULTI-select select2, an option
 * that is already selected is removed from the dropdown so it can't be picked
 * again for the next selection.
 *
 * select2 does this by default, but this enforces it everywhere (and survives
 * version/config quirks). It is scoped to `multiple` selects only, so single
 * select2 dropdowns keep showing their current value.
 */
(function () {
    "use strict";

    if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) {
        return;
    }

    var $ = jQuery;

    // Inject the scoping style once.
    if (!document.getElementById("s2-hide-selected-style")) {
        var style = document.createElement("style");
        style.id = "s2-hide-selected-style";
        style.textContent =
            ".s2-hide-selected .select2-results__option[aria-selected=\"true\"]{display:none !important;}";
        document.head.appendChild(style);
    }

    // When a multi-select dropdown opens, mark its results list so the style
    // above hides any option that is already selected.
    $(document).on("select2:open", function (e) {
        if (!e.target || !e.target.multiple) {
            return;
        }
        // The dropdown is rendered at the end of <body>; the currently open one
        // carries the `select2-container--open` class.
        $(".select2-container--open .select2-results").addClass("s2-hide-selected");
    });
})();

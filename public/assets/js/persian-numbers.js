/**
 * Accept Persian/Arabic digits in every input across the project and keep the
 * submitted value in English (Latin) digits, so the backend always stores
 * Latin numerals. Works together with the ConvertPersianNumbers middleware.
 */
(function () {
    "use strict";

    var map = {
        "۰": "0", "۱": "1", "۲": "2", "۳": "3", "۴": "4",
        "۵": "5", "۶": "6", "۷": "7", "۸": "8", "۹": "9",
        "٠": "0", "١": "1", "٢": "2", "٣": "3", "٤": "4",
        "٥": "5", "٦": "6", "٧": "7", "٨": "8", "٩": "9"
    };

    function toEnglish(str) {
        return String(str).replace(/[۰-۹٠-٩]/g, function (d) {
            return map[d];
        });
    }

    function shouldHandle(el) {
        if (!el || (el.tagName !== "INPUT" && el.tagName !== "TEXTAREA")) {
            return false;
        }
        // Never touch passwords — digits there are meaningful as typed.
        return el.type !== "password";
    }

    // 1) type="number" rejects non-Latin characters outright, so the input event
    //    never fires for a Persian digit. Intercept the keystroke and insert the
    //    Latin equivalent ourselves.
    document.addEventListener("keydown", function (e) {
        var el = e.target;
        if (!shouldHandle(el) || el.type !== "number") return;
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        if (Object.prototype.hasOwnProperty.call(map, e.key)) {
            e.preventDefault();
            el.value = el.value + map[e.key];
            el.dispatchEvent(new Event("input", { bubbles: true }));
        }
    });

    // 2) Every other field: convert as the user types (covers paste, mobile and IME).
    document.addEventListener("input", function (e) {
        var el = e.target;
        if (!shouldHandle(el) || el.type === "number") return;
        var converted = toEnglish(el.value);
        if (converted === el.value) return;
        var start = el.selectionStart;
        var end = el.selectionEnd;
        el.value = converted;
        try {
            el.setSelectionRange(start, end);
        } catch (err) {
            /* some input types don't support selection ranges */
        }
    });

    // 3) Safety net: convert anything left right before the form is submitted.
    document.addEventListener("submit", function (e) {
        var fields = e.target.querySelectorAll("input, textarea");
        Array.prototype.forEach.call(fields, function (el) {
            if (!shouldHandle(el)) return;
            var converted = toEnglish(el.value);
            if (converted !== el.value) {
                el.value = converted;
            }
        });
    }, true);
})();

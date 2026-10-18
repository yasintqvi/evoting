(() => {
    'use strict';

    const selector = '[data-bs-toggle="dropdown"]';

    function prepare(toggle) {
        if (!toggle || !window.bootstrap?.Dropdown) return;

        // Keep the menu in its original DOM location so forms, delegated click
        // handlers and Bootstrap's keyboard navigation continue to work.
        bootstrap.Dropdown.getOrCreateInstance(toggle, {
            popperConfig(defaultConfig) {
                return {
                    ...defaultConfig,
                    strategy: 'fixed',
                    modifiers: [
                        ...(defaultConfig.modifiers || []),
                        {
                            name: 'preventOverflow',
                            options: { boundary: 'viewport', rootBoundary: 'viewport', padding: 8 }
                        },
                        {
                            name: 'flip',
                            options: { boundary: 'viewport', rootBoundary: 'viewport', padding: 8 }
                        }
                    ]
                };
            }
        });
    }

    document.querySelectorAll(selector).forEach(prepare);

    // Prepare dynamically inserted table rows before Bootstrap's data API runs.
    document.addEventListener('click', event => prepare(event.target.closest(selector)), true);
    document.addEventListener('keydown', event => {
        if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
            prepare(event.target.closest(selector));
        }
    }, true);
})();


/*
|--------------------------------------------------------------------------
| NAPVCW DCFMS - Global Theme Manager
|--------------------------------------------------------------------------
|
| Features:
| - Light / Dark mode
| - Persists preference using localStorage
| - Supports multiple theme-toggle buttons on the same page
| - Updates icons and labels automatically
| - Respects system preference if no saved preference exists
| - Keeps the splash / landing page permanently dark when
|   <body data-fixed-theme="dark"> is used
|
*/

const THEME_STORAGE_KEY = 'dcfms-theme';

const THEME_LIGHT = 'light';
const THEME_DARK = 'dark';


/*
|--------------------------------------------------------------------------
| Get Saved Theme
|--------------------------------------------------------------------------
*/

function getSavedTheme() {
    try {
        const savedTheme =
            window.localStorage.getItem(
                THEME_STORAGE_KEY
            );

        if (
            savedTheme === THEME_LIGHT ||
            savedTheme === THEME_DARK
        ) {
            return savedTheme;
        }
    } catch (error) {
        /*
         * localStorage may be unavailable in some
         * restricted browser environments.
         */
    }

    return null;
}


/*
|--------------------------------------------------------------------------
| Get System Theme
|--------------------------------------------------------------------------
*/

function getSystemTheme() {
    if (
        window.matchMedia &&
        window.matchMedia(
            '(prefers-color-scheme: light)'
        ).matches
    ) {
        return THEME_LIGHT;
    }

    return THEME_DARK;
}


/*
|--------------------------------------------------------------------------
| Determine Preferred Theme
|--------------------------------------------------------------------------
*/

function getPreferredTheme() {
    return (
        getSavedTheme() ??
        getSystemTheme()
    );
}


/*
|--------------------------------------------------------------------------
| Save Theme
|--------------------------------------------------------------------------
*/

function saveTheme(theme) {
    try {
        window.localStorage.setItem(
            THEME_STORAGE_KEY,
            theme
        );
    } catch (error) {
        /*
         * Ignore storage errors.
         * Theme can still work for the current page.
         */
    }
}


/*
|--------------------------------------------------------------------------
| Check Whether Page Has Fixed Theme
|--------------------------------------------------------------------------
*/

function isFixedThemePage() {
    return (
        document.body?.dataset.fixedTheme ===
        THEME_DARK
    );
}


/*
|--------------------------------------------------------------------------
| Update Theme Toggle Buttons
|--------------------------------------------------------------------------
*/

function updateThemeToggleButtons(theme) {
    const buttons =
        document.querySelectorAll(
            '[data-theme-toggle]'
        );

    buttons.forEach((button) => {

        const sunIcon =
            button.querySelector(
                '[data-theme-sun]'
            );

        const moonIcon =
            button.querySelector(
                '[data-theme-moon]'
            );

        const label =
            button.querySelector(
                '[data-theme-label]'
            );


        /*
         * Current theme = dark
         * Show sun because clicking will switch to light.
         */
        if (sunIcon) {
            sunIcon.classList.toggle(
                'hidden',
                theme !== THEME_DARK
            );
        }


        /*
         * Current theme = light
         * Show moon because clicking will switch to dark.
         */
        if (moonIcon) {
            moonIcon.classList.toggle(
                'hidden',
                theme !== THEME_LIGHT
            );
        }


        /*
         * Update button label.
         */
        if (label) {
            label.textContent =
                theme === THEME_DARK
                    ? 'Light Mode'
                    : 'Dark Mode';
        }


        /*
         * Accessibility labels.
         */
        button.setAttribute(
            'aria-label',
            theme === THEME_DARK
                ? 'Switch to light mode'
                : 'Switch to dark mode'
        );

        button.setAttribute(
            'title',
            theme === THEME_DARK
                ? 'Switch to light mode'
                : 'Switch to dark mode'
        );

        button.setAttribute(
            'aria-pressed',
            theme === THEME_LIGHT
                ? 'true'
                : 'false'
        );
    });
}


/*
|--------------------------------------------------------------------------
| Apply Theme
|--------------------------------------------------------------------------
*/

function applyTheme(
    theme,
    persist = true
) {
    /*
     * Landing / splash page must remain dark.
     */
    if (isFixedThemePage()) {
        document.documentElement.setAttribute(
            'data-theme',
            THEME_DARK
        );

        return;
    }


    const normalizedTheme =
        theme === THEME_LIGHT
            ? THEME_LIGHT
            : THEME_DARK;


    /*
     * Apply to <html>.
     */
    document.documentElement.setAttribute(
        'data-theme',
        normalizedTheme
    );


    /*
     * Optional helper class.
     */
    document.documentElement.classList.toggle(
        'theme-light',
        normalizedTheme === THEME_LIGHT
    );

    document.documentElement.classList.toggle(
        'theme-dark',
        normalizedTheme === THEME_DARK
    );


    /*
     * Persist user preference.
     */
    if (persist) {
        saveTheme(
            normalizedTheme
        );
    }


    /*
     * Synchronize all buttons.
     */
    updateThemeToggleButtons(
        normalizedTheme
    );


    /*
     * Inform other components if needed.
     */
    window.dispatchEvent(
        new CustomEvent(
            'dcfms:theme-changed',
            {
                detail: {
                    theme:
                        normalizedTheme,
                },
            }
        )
    );
}


/*
|--------------------------------------------------------------------------
| Read Current Theme
|--------------------------------------------------------------------------
*/

function getCurrentTheme() {
    const currentTheme =
        document.documentElement
            .getAttribute(
                'data-theme'
            );

    if (
        currentTheme === THEME_LIGHT ||
        currentTheme === THEME_DARK
    ) {
        return currentTheme;
    }

    return getPreferredTheme();
}


/*
|--------------------------------------------------------------------------
| Toggle Theme
|--------------------------------------------------------------------------
*/

function toggleTheme() {
    if (isFixedThemePage()) {
        return;
    }

    const currentTheme =
        getCurrentTheme();

    const nextTheme =
        currentTheme === THEME_DARK
            ? THEME_LIGHT
            : THEME_DARK;

    applyTheme(
        nextTheme,
        true
    );
}


/*
|--------------------------------------------------------------------------
| Bind Theme Buttons
|--------------------------------------------------------------------------
*/

function bindThemeToggleButtons() {
    const buttons =
        document.querySelectorAll(
            '[data-theme-toggle]'
        );

    buttons.forEach((button) => {

        /*
         * Avoid duplicate event listeners.
         */
        if (
            button.dataset.themeBound ===
            'true'
        ) {
            return;
        }

        button.dataset.themeBound =
            'true';


        button.addEventListener(
            'click',
            function () {
                toggleTheme();
            }
        );
    });
}


/*
|--------------------------------------------------------------------------
| Initial Theme Setup
|--------------------------------------------------------------------------
*/

function initializeTheme() {
    /*
     * Splash / landing page always remains dark.
     */
    if (isFixedThemePage()) {
        document.documentElement.setAttribute(
            'data-theme',
            THEME_DARK
        );

        document.documentElement.classList.add(
            'theme-dark'
        );

        document.documentElement.classList.remove(
            'theme-light'
        );

        return;
    }


    const preferredTheme =
        getPreferredTheme();


    /*
     * Do not write again to localStorage during
     * initial load unless user changes the theme.
     */
    applyTheme(
        preferredTheme,
        false
    );


    bindThemeToggleButtons();
}


/*
|--------------------------------------------------------------------------
| Watch System Theme Changes
|--------------------------------------------------------------------------
|
| System preference is followed only when the user has not manually
| saved a DCFMS theme preference.
|
*/

function watchSystemTheme() {
    if (!window.matchMedia) {
        return;
    }

    const mediaQuery =
        window.matchMedia(
            '(prefers-color-scheme: light)'
        );


    const handleSystemThemeChange =
        function (event) {

            /*
             * Do not change fixed-dark splash page.
             */
            if (isFixedThemePage()) {
                return;
            }


            /*
             * User preference has priority.
             */
            if (getSavedTheme()) {
                return;
            }


            applyTheme(
                event.matches
                    ? THEME_LIGHT
                    : THEME_DARK,
                false
            );
        };


    if (
        typeof mediaQuery.addEventListener ===
        'function'
    ) {
        mediaQuery.addEventListener(
            'change',
            handleSystemThemeChange
        );
    } else if (
        typeof mediaQuery.addListener ===
        'function'
    ) {
        /*
         * Older browser fallback.
         */
        mediaQuery.addListener(
            handleSystemThemeChange
        );
    }
}


/*
|--------------------------------------------------------------------------
| DOM Ready
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        initializeTheme();

        watchSystemTheme();

    }
);


/*
|--------------------------------------------------------------------------
| Browser Page Cache Support
|--------------------------------------------------------------------------
|
| Some browsers restore a page from the back-forward cache.
| Refresh the button state when that happens.
|
*/

window.addEventListener(
    'pageshow',
    function () {

        if (isFixedThemePage()) {
            document.documentElement
                .setAttribute(
                    'data-theme',
                    THEME_DARK
                );

            return;
        }


        const theme =
            getPreferredTheme();

        applyTheme(
            theme,
            false
        );

        bindThemeToggleButtons();

    }
);


/*
|--------------------------------------------------------------------------
| Optional Global API
|--------------------------------------------------------------------------
|
| Makes theme functions available if a Blade page or future component
| needs them.
|
*/

window.DCFMSTheme = {

    getCurrentTheme,

    getPreferredTheme,

    setTheme(theme) {

        if (
            theme !== THEME_LIGHT &&
            theme !== THEME_DARK
        ) {
            return;
        }

        applyTheme(
            theme,
            true
        );
    },

    toggleTheme,

};
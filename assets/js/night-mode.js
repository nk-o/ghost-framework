( () => {
    const {
        ghostFrameworkNightMode,
        localStorage,
        matchMedia,
        jQuery: $,
    } = window;

    if ( 'undefined' === typeof ghostFrameworkNightMode ) {
        return;
    }

    const $html = $( 'html' );
    const $doc = $( document );

    function switchMode( toggle = true ) {
        const storedState = localStorage.getItem( ghostFrameworkNightMode.night_class );
        let defaultValue = ghostFrameworkNightMode.default;

        // Get local storage value.
        if ( ghostFrameworkNightMode.use_local_storage && storedState ) {
            defaultValue = storedState;

            // Get system color scheme.
        } else if ( matchMedia && 'auto' === defaultValue ) {
            defaultValue = matchMedia('(prefers-color-scheme: dark)').matches ? 'night' : 'day';
        }

        // Toggle night mode.
        if ( toggle ) {
            defaultValue = 'day' === defaultValue ? 'night' : 'day';
        }

        $html.addClass( ghostFrameworkNightMode.switching_class );

        // Enable Night.
        if ( 'night' === defaultValue ) {
            $html.addClass( ghostFrameworkNightMode.night_class );

            if ( toggle ) {
                localStorage.setItem( ghostFrameworkNightMode.night_class, 'night' );
            }

            // Disable Night.
        } else {
            $html.removeClass( ghostFrameworkNightMode.night_class );

            if ( toggle ) {
                localStorage.setItem( ghostFrameworkNightMode.night_class, 'day' );
            }
        }

        // Trigger a reflow, flushing the CSS changes. This need to apply the changes from the new class added.
        // Info here - https://stackoverflow.com/questions/11131875/what-is-the-cleanest-way-to-disable-css-transition-effects-temporarily
        // eslint-disable-next-line no-unused-expressions
        $html[ 0 ].offsetHeight;

        $html.removeClass( ghostFrameworkNightMode.switching_class );
    }

    // Set default state.
    switchMode( false );

    // Toggle state when switched system color scheme.
    if ( matchMedia ) {
        matchMedia( '(prefers-color-scheme: dark)' ).addEventListener( 'change', () => {
            const storedState = localStorage.getItem( ghostFrameworkNightMode.night_class );
            const defaultValue = ghostFrameworkNightMode.default;

            if ( ! ( ghostFrameworkNightMode.use_local_storage && storedState ) && 'auto' === defaultValue ) {
                switchMode( false );
            }
        } );
    }

    // Click on switch button.
    $doc.on( 'click', ghostFrameworkNightMode.toggle_selector, ( e ) => {
        e.preventDefault();

        switchMode();
    } );
} )();

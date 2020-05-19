( () => {
    const {
        ghostFrameworkNightMode,
        localStorage,
    } = window;

    if ( 'undefined' === typeof ghostFrameworkNightMode ) {
        return;
    }

    const $html = $( 'html' );
    const $doc = $( document );

    function switchMode( toggle = true ) {
        const storedState = localStorage.getItem( ghostFrameworkNightMode.night_class );
        let isNight = !! ghostFrameworkNightMode.is_default_night;

        if ( storedState ) {
            isNight = 'night' === storedState;
        }

        if ( toggle ) {
            isNight = ! isNight;
        }

        $html.addClass( ghostFrameworkNightMode.switching_class );

        // Enable Night.
        if ( isNight ) {
            $html.addClass( ghostFrameworkNightMode.night_class );
            localStorage.setItem( ghostFrameworkNightMode.night_class, 'night' );

            // Disable Night.
        } else {
            $html.removeClass( ghostFrameworkNightMode.night_class );
            localStorage.setItem( ghostFrameworkNightMode.night_class, 'day' );
        }

        // Trigger a reflow, flushing the CSS changes. This need to apply the changes from the new class added.
        // Info here - https://stackoverflow.com/questions/11131875/what-is-the-cleanest-way-to-disable-css-transition-effects-temporarily
        // eslint-disable-next-line no-unused-expressions
        $html[ 0 ].offsetHeight;

        $html.removeClass( ghostFrameworkNightMode.switching_class );
    }

    // Set default state.
    switchMode( false );

    // Click on switch button.
    $doc.on( 'click', ghostFrameworkNightMode.toggle_selector, ( e ) => {
        e.preventDefault();

        switchMode();
    } );
} )();

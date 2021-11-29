import hasLocalStorage from 'has-localstorage';

( () => {
    const {
        ghostFrameworkNightMode,
        matchMedia,
        jQuery: $,
    } = window;

    // We need this to prevent JS error in the iframe in Incognito mode.
    const localStorage = hasLocalStorage() ? window.localStorage : false;

    if ( 'undefined' === typeof ghostFrameworkNightMode || ! localStorage ) {
        return;
    }

    const $doc = $( document );
    let $html = ghostFrameworkNightMode.is_editor ? $( '.editor-styles-wrapper' ) : $( 'html' );

    function switchMode( toggle = true ) {
        if ( ! $html || ! $html.length ) {
            return;
        }

        const storedState = localStorage.getItem( ghostFrameworkNightMode.night_class );
        let defaultValue = ghostFrameworkNightMode.default;

        // Get local storage value.
        if ( ghostFrameworkNightMode.use_local_storage && storedState ) {
            defaultValue = storedState;

            // Get system color scheme.
        } else if ( matchMedia && 'auto' === defaultValue ) {
            defaultValue = matchMedia( '(prefers-color-scheme: dark)' ).matches ? 'night' : 'day';
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
        if ( $html[ 0 ] ) {
            $html[ 0 ].offsetHeight;
        }

        $html.removeClass( ghostFrameworkNightMode.switching_class );
    }

    // Set default state.
    if ( ghostFrameworkNightMode.is_editor ) {
        const {
            Component,
        } = wp.element;

        const {
            registerPlugin,
        } = wp.plugins;

        const {
            withSelect,
        } = wp.data;

        class EditorNightMode extends Component {
            constructor( ...args ) {
                super( ...args );

                this.state = {
                    editorMode: this.props.editorMode,
                };
            }

            componentDidMount() {
                this.switch();
            }

            componentDidUpdate() {
                // Editor mode changed.
                if ( this.state.editorMode !== this.props.editorMode ) {
                    this.setState( {
                        editorMode: this.props.editorMode,
                    } );

                    this.switch();
                }
            }

            switch() {
                $html = $( '.editor-styles-wrapper' );

                switchMode( false );
            }

            render() {
                return null;
            }
        }

        registerPlugin( 'ghost-framework-editor-night-mode', {
            render: withSelect( ( select ) => {
                const {
                    getEditorMode,
                } = select( 'core/edit-post' ) || {};

                return {
                    editorMode: getEditorMode ? getEditorMode() : '',
                };
            } )( EditorNightMode ),
        } );
    } else {
        switchMode( false );
    }

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

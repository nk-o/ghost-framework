/**
 * Aspect Ratio control for Kirki
 */
const {
    jQuery: $,
} = window;

const controlClass = 'customize-control-kirki-aspect-ratio';

$( function() {
    function updateElements( $control, val, keepCustom = false ) {
        const $val = $control.find( `.${ controlClass }-value` );
        const $select = $control.find( `.${ controlClass }-select` );
        const $custom = $control.find( `.${ controlClass }-custom` );
        const $width = $control.find( `.${ controlClass }-custom-width` );
        const $height = $control.find( `.${ controlClass }-custom-height` );

        let width = '';
        let height = '';

        // If custom, find current value from control input.
        if ( 'custom' === val ) {
            val = $val.val();

            if ( ! /:/i.test( val ) ) {
                val = '';
            }
        }

        // Find width and height values.
        if ( /:/i.test( val ) ) {
            const valArr = val.split( ':' );

            width = parseFloat( valArr[ 0 ] );
            height = parseFloat( valArr[ 1 ] );
        }

        // Change width and height inputs.
        if ( 'auto' !== val ) {
            if ( ! width || ! height ) {
                val = '4:3';
                width = 4;
                height = 3;
            }

            if ( $width.val() !== width ) {
                $width.val( width );
            }
            if ( $height.val() !== height ) {
                $height.val( height );
            }
        }

        // Change select value.
        if ( ! keepCustom && $select.find( `option[value="${ val }"]` ).length ) {
            if ( $select.val() !== val ) {
                $select.val( val );
            }
        } else {
            if ( 'custom' !== $select.val() ) {
                $select.val( 'custom' );
            }
        }

        // If custom, we need to show width and height controls.
        if ( 'custom' === $select.val() ) {
            $custom.removeClass( `${ controlClass }-hide` );
        } else {
            $custom.addClass( `${ controlClass }-hide` );
        }

        // Update control value if needed.
        if ( $val.val() !== val ) {
            $val.val( val ).change();
        }
    }

    // Update all aspect ratio controls.
    $( `.${ controlClass }` ).each( function() {
        const $this = $( this );

        updateElements( $this, $this.find( `.${ controlClass }-value` ).val() );
    } );

    // Change select value.
    $( document ).on( 'change', `.${ controlClass }-select`, function() {
        const $this = $( this );
        const val = $this.val();

        updateElements( $this.closest( `.${ controlClass }` ), val, 'custom' === val );
    } );

    // Change custom value.
    $( document ).on( 'input change', `.${ controlClass }-custom-width, .${ controlClass }-custom-height`, function() {
        const $this = $( this ).closest( `.${ controlClass }` );
        const width = $this.find( `.${ controlClass }-custom-width` ).val();
        const height = $this.find( `.${ controlClass }-custom-height` ).val();

        updateElements( $this, `${ width }:${ height }`, true );
    } );

    // Use Select2.
    $(`.${controlClass}-select`).selectWoo({
        // Disable search input.
        minimumResultsForSearch: -1,
    });
} );

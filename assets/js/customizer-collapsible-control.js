/**
 * Collapsible control for Kirki
 */
const {
    jQuery: $,
} = window;

$( function() {
    function collapse( $collapsible, collapse = true ) {
        $collapsible.closest( '.customize-control-collapsible' ).nextUntil( '.customize-control-collapsible, .customize-control-collapsible-end' )[ collapse ? 'addClass' : 'removeClass' ]( 'customize-control-kirki-collapsible-hidden' );

        $collapsible[ collapse ? 'removeClass' : 'addClass' ]( 'customize-control-kirki-collapsible-expanded' );
    }

    // Hide all collapsed controls on page load.
    $( '.customize-control-kirki-collapsible:not(.customize-control-kirki-collapsible-expanded)' ).each( function() {
        collapse( $( this ) );
    } );

    // Collapse on click.
    $( document ).on( 'click', '.customize-control-kirki-collapsible', function() {
        const $collapsible = $( this );
        collapse( $collapsible, $collapsible.hasClass( 'customize-control-kirki-collapsible-expanded' ) );
    } )
} );

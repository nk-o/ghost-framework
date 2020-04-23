( () => {
    const {
        ghostFrameworkWebfontList,
    } = window;

    if ( 'undefined' === typeof ghostFrameworkWebfontList || 'undefined' === typeof ghostFrameworkWebfontList[ 'google-fonts' ] ) {
        return;
    }

    const googleFonts = ghostFrameworkWebfontList[ 'google-fonts' ];
    const googleFamilies = [];

    Object.keys( googleFonts ).forEach( ( key ) => {
        const data = googleFonts[ key ];
        const weights = 'undefined' !== typeof data.widths ? data.widths : false;
        let weightsString = '';

        if ( weights ) {
            weights.forEach( ( weight ) => {
                if ( weightsString ) {
                    weightsString += ',';
                }
                weightsString += weight;
            } );
        }

        googleFamilies.push( `${ key }:${ weightsString }` );
    } );

    window.WebFont.load( {
        google: {
            families: googleFamilies,
        },
    } );
} )();

jQuery(() => {
    const {
        kirkiIcons,
    } = window;

    function isSvg( string ) {
        if ( string ) {
            return /^<svg/i.test( string );
        }

        return false;
    }

    function prepareSvg( string ) {
        return `<span class="kirki-section-icon-svg">${ string }</span>`;
    }

    if (!_.isUndefined(kirkiIcons.section)) {
        // Parse sections and add icons.
        _.each(kirkiIcons.section, (icon, sectionID) => {
            if ( isSvg( icon ) ) {
                // Add icons in list.
                jQuery(`#accordion-section-${sectionID} > h3`).prepend( prepareSvg( icon ) );

                // Add icons on titles when a section is open.
                jQuery(`#sub-accordion-section-${sectionID} .customize-section-title > h3 .customize-action`).after( prepareSvg( icon ) );
            } else {
                // Add icons in list.
                jQuery(`#accordion-section-${sectionID} > h3`).addClass(`dashicons-before ${icon}`);

                // Add icons on titles when a section is open.
                jQuery(`#sub-accordion-section-${sectionID} .customize-section-title > h3`).append(`<span class="dashicons ${icon}" style="float:left;padding-right:.1em;padding-top:2px;"></span>`);
            }
        });
    }

    if (!_.isUndefined(kirkiIcons.panel)) {
        // Add icons in lists & headers.
        _.each(kirkiIcons.panel, (icon, panelID) => {
            if ( isSvg( icon ) ) {
                jQuery(`#accordion-panel-${panelID} > h3, #sub-accordion-panel-${panelID} .panel-title`).prepend( prepareSvg( icon ) );
            } else {
                jQuery(`#accordion-panel-${panelID} > h3, #sub-accordion-panel-${panelID} .panel-title`).addClass(`dashicons-before ${icon}`);
            }
        });
    }
});

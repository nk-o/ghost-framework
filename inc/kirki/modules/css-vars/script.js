const {
    kirkiCssVarFields,
} = window;

const kirkiCssVars = {

    /**
	 * Get styles.
	 *
	 * @since 3.0.28
	 * @returns {Object}
	 */
    getStyles() {
        let style = jQuery('#kirki-css-vars'),
            styles = style.html().replace(':root{', '').replace('}', '').split(';'),
            stylesObj = {};

        // Format styles as a object we can then tweak.
        _.each(styles, (style) => {
            style = style.split(':');
            if (style[0] && style[1]) {
                stylesObj[style[0]] = style[1];
            }
        });
        return stylesObj;
    },

    /**
	 * Builds the styles from an object.
	 *
	 * @since 3.0.28
	 * @param {Object} vars - The vars.
	 * @returns {string}
	 */
    buildStyle(vars) {
        let style = '';

        _.each(vars, (val, name) => {
            style += `${name}:${val};`;
        });
        return `:root{${style}}`;
    },
};

jQuery(() => {
    _.each(kirkiCssVarFields, (field) => {
        wp.customize(field.settings, (value) => {
            value.bind((newVal) => {
                const styles = kirkiCssVars.getStyles();

                _.each(field.css_vars, (cssVar) => {
                    if (typeof newVal === 'object') {
                        if (cssVar[2] && newVal[cssVar[2]]) {
                            styles[cssVar[0]] = cssVar[1].replace('$', newVal[cssVar[2]]);
                        }
                    } else {
                        styles[cssVar[0]] = cssVar[1].replace('$', newVal);
                    }
                });
                jQuery('#kirki-css-vars').html(kirkiCssVars.buildStyle(styles));
            });
        });
    });
});

wp.customize.bind('preview-ready', () => {
    wp.customize.preview.bind('active', () => {
        _.each(kirkiCssVarFields, (field) => {
            wp.customize(field.settings, (value) => {
                let styles = kirkiCssVars.getStyles(),
                    newVal = window.parent.wp.customize(value.id).get();
                _.each(field.css_vars, (cssVar) => {
                    if (typeof newVal === 'object') {
                        if (cssVar[2] && newVal[cssVar[2]]) {
                            styles[cssVar[0]] = cssVar[1].replace('$', newVal[cssVar[2]]);
                        }
                    } else {
                        styles[cssVar[0]] = cssVar[1].replace('$', newVal);
                    }
                });
                jQuery('#kirki-css-vars').html(kirkiCssVars.buildStyle(styles));
            });
        });
    });
});

window.kirkiCssVars = kirkiCssVars;

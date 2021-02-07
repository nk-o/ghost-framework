const {
    kirkiPostMessageFields,
    WebFont,
} = window;

var kirkiPostMessage = {

    /**
	 * The fields.
	 *
	 * @since 3.0.26
	 */
    fields: {},

    /**
	 * A collection of methods for the <style> tags.
	 *
	 * @since 3.0.26
	 */
    styleTag: {

        /**
		 * Add a <style> tag in <head> if it doesn't already exist.
		 *
		 * @since 3.0.26
		 * @param {string} id - The field-ID.
		 * @returns {void}
		 */
        add(id) {
            if (document.getElementById(`kirki-postmessage-${id}`) === null || typeof document.getElementById(`kirki-postmessage-${id}`) === 'undefined') {
                jQuery('head').append(`<style id="kirki-postmessage-${id}"></style>`);
            }
        },

        /**
		 * Add a <style> tag in <head> if it doesn't already exist,
		 * by calling the this.add method, and then add styles inside it.
		 *
		 * @since 3.0.26
		 * @param {string} id - The field-ID.
		 * @param {string} styles - The styles to add.
		 * @returns {void}
		 */
        addData(id, styles) {
            kirkiPostMessage.styleTag.add(id);
            jQuery(`#kirki-postmessage-${id}`).text(styles);
        },
    },

    /**
	 * Common utilities.
	 *
	 * @since 3.0.26
	 */
    util: {

        /**
		 * Processes the value and applies any replacements and/or additions.
		 *
		 * @since 3.0.26
		 * @param {Object} output - The output (js_vars) argument.
		 * @param {mixed}  value - The value.
		 * @param {string} controlType - The control-type.
		 * @returns {string|false} - Returns false if value is excluded, otherwise a string.
		 */
        processValue(output, value) {
            let self = this,
                settings = window.parent.wp.customize.get(),
                excluded = false;

            if (typeof value === 'object') {
                _.each(value, (subValue, key) => {
                    value[key] = self.processValue(output, subValue);
                });
                return value;
            }
            output = _.defaults(output, {
                prefix: '',
                units: '',
                suffix: '',
                value_pattern: '$',
                pattern_replace: {},
                exclude: [],
            });

            if (output.exclude.length >= 1) {
                _.each(output.exclude, (exclusion) => {
                    if (value == exclusion) {
                        excluded = true;
                    }
                });
            }

            if (excluded) {
                return false;
            }

            value = output.value_pattern.replace(new RegExp('\\$', 'g'), value);
            _.each(output.pattern_replace, (id, placeholder) => {
                if (!_.isUndefined(settings[id])) {
                    value = value.replace(placeholder, settings[id]);
                }
            });
            return output.prefix + value + output.units + output.suffix;
        },

        /**
		 * Make sure urls are properly formatted for background-image properties.
		 *
		 * @since 3.0.26
		 * @param {string} url - The URL.
		 * @returns {string}
		 */
        backgroundImageValue(url) {
            return (url.indexOf('url(') === -1) ? `url(${url})` : url;
        },
    },

    /**
	 * A collection of utilities for CSS generation.
	 *
	 * @since 3.0.26
	 */
    css: {

        /**
		 * Generates the CSS from the output (js_vars) parameter.
		 *
		 * @since 3.0.26
		 * @param {Object} output - The output (js_vars) argument.
		 * @param {mixed}  value - The value.
		 * @param {string} controlType - The control-type.
		 * @returns {string}
		 */
        fromOutput(output, value, controlType) {
            let styles = '',
                kirkiParent = window.parent.kirki,
                googleFont = '',
                mediaQuery = false,
                processedValue;

            if (output.js_callback && typeof window[output.js_callback] === 'function') {
                value = window[output.js_callback[0]](value, output.js_callback[1]);
            }
            switch (controlType) {
            default:
                if (controlType === 'kirki-image') {
                    value = (!_.isUndefined(value.url)) ? kirkiPostMessage.util.backgroundImageValue(value.url) : kirkiPostMessage.util.backgroundImageValue(value);
                }
                if (_.isObject(value)) {
                    styles += `${output.element}{`;
                    _.each(value, (val, key) => {
                        if (output.choice && key !== output.choice) {
                            return;
                        }
                        processedValue = kirkiPostMessage.util.processValue(output, val);
                        if (!output.property) {
                            output.property = key;
                        }
                        if (processedValue !== false) {
                            styles += `${output.property}:${processedValue};`;
                        }
                    });
                    styles += '}';
                } else {
                    processedValue = kirkiPostMessage.util.processValue(output, value);
                    if (processedValue !== false) {
                        styles += `${output.element}{${output.property}:${processedValue};}`;
                    }
                }
                break;
            }

            // Get the media-query.
            if (output.media_query && typeof output.media_query === 'string' && !_.isEmpty(output.media_query)) {
                mediaQuery = output.media_query;
                if (mediaQuery.indexOf('@media') === -1) {
                    mediaQuery = `@media ${mediaQuery}`;
                }
            }

            // If we have a media-query, add it and return.
            if (mediaQuery) {
                return `${mediaQuery}{${styles}}`;
            }

            // Return the styles.
            return styles;
        },
    },

    /**
	 * A collection of utilities to change the HTML in the document.
	 *
	 * @since 3.0.26
	 */
    html: {

        /**
		 * Modifies the HTML from the output (js_vars) parameter.
		 *
		 * @since 3.0.26
		 * @param {Object} output - The output (js_vars) argument.
		 * @param {mixed}  value - The value.
		 * @returns {string}
		 */
        fromOutput(output, value) {
            if (output.js_callback && typeof window[output.js_callback] === 'function') {
                value = window[output.js_callback[0]](value, output.js_callback[1]);
            }

            if (_.isObject(value) || _.isArray(value)) {
                if (!output.choice) {
                    return;
                }
                _.each(value, (val, key) => {
                    if (output.choice && key !== output.choice) {
                        return;
                    }
                    value = val;
                });
            }
            value = kirkiPostMessage.util.processValue(output, value);

            if (output.attr) {
                jQuery(output.element).attr(output.attr, value);
            } else {
                jQuery(output.element).html(value);
            }
        },
    },

    /**
	 * A collection of utilities to allow toggling a CSS class.
	 *
	 * @since 3.0.26
	 */
    toggleClass: {

        /**
		 * Toggles a CSS class from the output (js_vars) parameter.
		 *
		 * @since 3.0.21
		 * @param {Object} output - The output (js_vars) argument.
		 * @param {mixed}  value - The value.
		 * @returns {string}
		 */
        fromOutput(output, value) {
            if (typeof output.class === 'undefined' || typeof output.value === 'undefined') {
                return;
            }
            if (value === output.value && !jQuery(output.element).hasClass(output.class)) {
                jQuery(output.element).addClass(output.class);
            } else {
                jQuery(output.element).removeClass(output.class);
            }
        },
    },
};

jQuery(() => {
    _.each(kirkiPostMessageFields, (field) => {
        wp.customize(field.settings, (value) => {
            value.bind((newVal) => {
                let styles = '';
                _.each(field.js_vars, (output) => {
                    if (!output.function || typeof kirkiPostMessage[output.function] === 'undefined') {
                        output.function = 'css';
                    }
                    if (output.function === 'css') {
                        styles += kirkiPostMessage.css.fromOutput(output, newVal, field.type);
                    } else {
                        kirkiPostMessage[output.function].fromOutput(output, newVal, field.type);
                    }
                });
                kirkiPostMessage.styleTag.addData(field.settings, styles);
            });
        });
    });
});

window.kirkiPostMessage = kirkiPostMessage;

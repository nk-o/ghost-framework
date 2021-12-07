/* eslint-disable */
if (_.isUndefined(window.kirkiSetSettingValue)) {
    const kirkiSetSettingValue = {

        /**
		 * Set the value of the control.
		 *
		 * @since 3.0.0
		 * @param string setting The setting-ID.
		 * @param mixed  value   The value.
		 */
        set(setting, value) {
            /**
			 * Get the control of the sub-setting.
			 * This will be used to get properties we need from that control,
			 * and determine if we need to do any further work based on those.
			 */
            let $this = this,
                subControl = wp.customize.settings.controls[setting],
                valueJSON;

            // If the control doesn't exist then return.
            if (_.isUndefined(subControl)) {
                return true;
            }

            // First set the value in the wp object. The control type doesn't matter here.
            $this.setValue(setting, value);

            // Process visually changing the value based on the control type.
            switch (subControl.type) {
            case 'checkbox':
            case 'kirki-toggle':
                value = !!((value === 1 || value === '1' || value === true));
                jQuery($this.findElement(setting, 'input')).prop('checked', value);
                wp.customize.instance(setting).set(value);
                break;

            case 'kirki-select':
                $this.setSelectWoo($this.findElement(setting, 'select'), value);
                break;

            case 'kirki-slider':
                jQuery($this.findElement(setting, 'input')).prop('value', value);
                jQuery($this.findElement(setting, '.kirki_range_value .value')).html(value);
                break;

            case 'kirki-generic':
                if ( _.isUndefined( subControl.choices ) || _.isUndefined( subControl.choices.element ) ) {
                    subControl.choices.element = 'input';
                }
                jQuery( $this.findElement( setting, subControl.choices.element ) ).prop( 'value', value );
                break;

            case 'kirki-color':
                $this.setColorPicker($this.findElement(setting, '.kirki-color-control'), value);
                break;

            case 'kirki-radio-buttonset':
            case 'kirki-radio-image':
            case 'kirki-radio':
            case 'kirki-dashicons':
            case 'kirki-color-palette':
            case 'kirki-palette':
                jQuery( $this.findElement( setting, 'input[value="' + value + '"]' ) ).prop( 'checked', true );
                break;

            case 'kirki-repeater':

                // Not yet implemented.
                break;

            case 'kirki-custom':

                // Do nothing.
                break;
            default:
                jQuery($this.findElement(setting, 'input')).prop('value', value);
            }
        },

        /**
		 * Set the value for colorpickers.
		 * CAUTION: This only sets the value visually, it does not change it in th wp object.
		 *
		 * @since 3.0.0
		 * @param object selector jQuery object for this element.
		 * @param string value    The value we want to set.
		 */
        setColorPicker(selector, value) {
            selector.attr('data-default-color', value).data('default-color', value).wpColorPicker('color', value);
        },

        /**
		 * Sets the value in a selectWoo element.
		 * CAUTION: This only sets the value visually, it does not change it in th wp object.
		 *
		 * @since 3.0.0
		 * @param string selector The CSS identifier for this selectWoo.
		 * @param string value    The value we want to set.
		 */
        setSelectWoo(selector, value) {
            jQuery(selector).selectWoo().val(value).trigger('change');
        },

        /**
		 * Sets the value in textarea elements.
		 * CAUTION: This only sets the value visually, it does not change it in th wp object.
		 *
		 * @since 3.0.0
		 * @param string selector The CSS identifier for this textarea.
		 * @param string value    The value we want to set.
		 */
        setTextarea(selector, value) {
            jQuery(selector).prop('value', value);
        },

        /**
		 * Finds an element inside this control.
		 *
		 * @since 3.0.0
		 * @param string setting The setting ID.
		 * @param string element The CSS identifier.
		 */
        findElement(setting, element) {
            return wp.customize.control(setting).container.find(element);
        },

        /**
		 * Updates the value in the wp.customize object.
		 *
		 * @since 3.0.0
		 * @param string setting The setting-ID.
		 * @param mixed  value   The value.
		 */
        setValue(setting, value, timeout) {
            timeout = (_.isUndefined(timeout)) ? 100 : parseInt(timeout, 10);
            wp.customize.instance(setting).set({});
            setTimeout(() => {
                wp.customize.instance(setting).set(value);
            }, timeout);
        },
    };
    window.kirkiSetSettingValue = kirkiSetSettingValue;
}
let kirki = {

    initialized: false,

    /**
	 * Initialize the object.
	 *
	 * @since 3.0.17
	 * @returns {null}
	 */
    initialize() {
        const self = this;

        // We only need to initialize once.
        if (self.initialized) {
            return;
        }
        // Mark as initialized.
        self.initialized = true;
    },
};

// Initialize the kirki object.
kirki.initialize();
kirki = jQuery.extend(kirki, {

    /**
	 * An object containing definitions for controls.
	 *
	 * @since 3.0.16
	 */
    control: {

        /**
		 * The radio control.
		 *
		 * @since 3.0.17
		 */
        'kirki-radio': {

            /**
			 * Init the control.
			 *
			 * @since 3.0.17
			 * @param {Object} control - The customizer control object.
			 * @returns {null}
			 */
            init(control) {
                const self = this;

                // Render the template.
                self.template(control);

                // Init the control.
                kirki.input.radio.init(control);
            },

            /**
			 * Render the template.
			 *
			 * @since 3.0.17
			 * @param {Object} control - The customizer control object.
			 * @param {Object} control.params - The control parameters.
			 * @param {string} control.params.label - The control label.
			 * @param {string} control.params.description - The control description.
			 * @param {string} control.params.inputAttrs - extra input arguments.
			 * @param {string} control.params.default - The default value.
			 * @param {Object} control.params.choices - Any extra choices we may need.
			 * @param {string} control.id - The setting.
			 * @returns {null}
			 */
            template(control) {
                const template = wp.template('kirki-input-radio');
                control.container.html(template({
                    label: control.params.label,
                    description: control.params.description,
                    'data-id': control.id,
                    inputAttrs: control.params.inputAttrs,
                    default: control.params.default,
                    value: kirki.setting.get(control.id),
                    choices: control.params.choices,
                }));
            },
        },

        /**
		 * The color control.
		 *
		 * @since 3.0.16
		 */
        'kirki-color': {

            /**
			 * Init the control.
			 *
			 * @since 3.0.16
			 * @param {Object} control - The customizer control object.
			 * @returns {null}
			 */
            init(control) {
                const self = this;

                // Render the template.
                self.template(control);

                // Init the control.
                kirki.input.color.init(control);
            },

            /**
			 * Render the template.
			 *
			 * @since 3.0.16
			 * @param {Object}     control - The customizer control object.
			 * @param {Object}     control.params - The control parameters.
			 * @param {string}     control.params.label - The control label.
			 * @param {string}     control.params.description - The control description.
			 * @param {string}     control.params.mode - The colorpicker mode. Can be 'full' or 'hue'.
			 * @param {bool|array} control.params.palette - false if we don't want a palette,
			 *                                              true to use the default palette,
			 *                                              array of custom hex colors if we want a custom palette.
			 * @param {string}     control.params.inputAttrs - extra input arguments.
			 * @param {string}     control.params.default - The default value.
			 * @param {Object}     control.params.choices - Any extra choices we may need.
			 * @param {boolean}    control.params.choices.alpha - should we add an alpha channel?
			 * @param {string}     control.id - The setting.
			 * @returns {null}
			 */
            template(control) {
                const template = wp.template('kirki-input-color');
                control.container.html(template({
                    label: control.params.label,
                    description: control.params.description,
                    'data-id': control.id,
                    mode: control.params.mode,
                    inputAttrs: control.params.inputAttrs,
                    'data-palette': control.params.palette,
                    'data-default-color': control.params.default,
                    'data-alpha': control.params.choices.alpha,
                    value: kirki.setting.get(control.id),
                }));
            },
        },

        /**
         * The generic control.
         *
         * @since 3.0.16
         */
        'kirki-generic': {

            /**
             * Init the control.
             *
             * @since 3.0.17
             * @param {Object} control - The customizer control object.
             * @param {Object} control.params - Control parameters.
             * @param {Object} control.params.choices - Define the specifics for this input.
             * @param {string} control.params.choices.element - The HTML element we want to use ('input', 'div', 'span' etc).
             * @returns {null}
             */
            init: function( control ) {
                var self = this;

                // Render the template.
                self.template( control );

                // Init the control.
                if ( ! _.isUndefined( control.params ) && ! _.isUndefined( control.params.choices ) && ! _.isUndefined( control.params.choices.element ) && 'textarea' === control.params.choices.element ) {
                    kirki.input.textarea.init( control );
                    return;
                }
                kirki.input.genericInput.init( control );
            },

            /**
             * Render the template.
             *
             * @since 3.0.17
             * @param {Object}  control - The customizer control object.
             * @param {Object}  control.params - The control parameters.
             * @param {string}  control.params.label - The control label.
             * @param {string}  control.params.description - The control description.
             * @param {string}  control.params.inputAttrs - extra input arguments.
             * @param {string}  control.params.default - The default value.
             * @param {Object}  control.params.choices - Any extra choices we may need.
             * @param {boolean} control.params.choices.alpha - should we add an alpha channel?
             * @param {string}  control.id - The setting.
             * @returns {null}
             */
            template: function( control ) {
                var args = {
                        label: control.params.label,
                        description: control.params.description,
                        'data-id': control.id,
                        inputAttrs: control.params.inputAttrs,
                        choices: control.params.choices,
                        value: kirki.setting.get( control.id )
                    },
                    template;

                if ( ! _.isUndefined( control.params ) && ! _.isUndefined( control.params.choices ) && ! _.isUndefined( control.params.choices.element ) && 'textarea' === control.params.choices.element ) {
                    template = wp.template( 'kirki-input-textarea' );
                    control.container.html( template( args ) );
                    return;
                }
                template = wp.template( 'kirki-input-generic' );
                control.container.html( template( args ) );
            }
        },

        /**
		 * The number control.
		 *
		 * @since 3.0.26
		 */
        'kirki-number': {

            /**
			 * Init the control.
			 *
			 * @since 3.0.26
			 * @param {Object} control - The customizer control object.
			 * @returns {null}
			 */
            init(control) {
                const self = this;

                // Render the template.
                self.template(control);

                // Init the control.
                kirki.input.number.init(control);
            },

            /**
			 * Render the template.
			 *
			 * @since 3.0.27
			 * @param {Object}  control - The customizer control object.
			 * @param {Object}  control.params - The control parameters.
			 * @param {string}  control.params.label - The control label.
			 * @param {string}  control.params.description - The control description.
			 * @param {string}  control.params.inputAttrs - extra input arguments.
			 * @param {string}  control.params.default - The default value.
			 * @param {Object}  control.params.choices - Any extra choices we may need.
			 * @param {string}  control.id - The setting.
			 * @returns {null}
			 */
            template(control) {
                const template = wp.template('kirki-input-number');
                let args = {};
                control.container.html(template(args = {
                    label: control.params.label,
                    description: control.params.description,
                    'data-id': control.id,
                    inputAttrs: control.params.inputAttrs,
                    choices: control.params.choices,
                    value: kirki.setting.get(control.id),
                }));
            },
        },

        /**
		 * The image control.
		 *
		 * @since 3.0.34
		 */
        'kirki-image': {

            /**
			 * Init the control.
			 *
			 * @since 3.0.34
			 * @param {Object} control - The customizer control object.
			 * @returns {null}
			 */
            init(control) {
                const self = this;

                // Render the template.
                self.template(control);

                // Init the control.
                kirki.input.image.init(control);
            },

            /**
			 * Render the template.
			 *
			 * @since 3.0.34
			 * @param {Object}  control - The customizer control object.
			 * @param {Object}  control.params - The control parameters.
			 * @param {string}  control.params.label - The control label.
			 * @param {string}  control.params.description - The control description.
			 * @param {string}  control.params.inputAttrs - extra input arguments.
			 * @param {string}  control.params.default - The default value.
			 * @param {Object}  control.params.choices - Any extra choices we may need.
			 * @param {string}  control.id - The setting.
			 * @returns {null}
			 */
            template(control) {
                const template = wp.template('kirki-input-image');
                let args = {};
                control.container.html(template(args = {
                    label: control.params.label,
                    description: control.params.description,
                    'data-id': control.id,
                    inputAttrs: control.params.inputAttrs,
                    choices: control.params.choices,
                    value: kirki.setting.get(control.id),
                }));
            },
        },

        'kirki-select': {

            /**
			 * Init the control.
			 *
			 * @since 3.0.17
			 * @param {Object} control - The customizer control object.
			 * @returns {null}
			 */
            init(control) {
                const self = this;

                // Render the template.
                self.template(control);

                // Init the control.
                kirki.input.select.init(control);
            },

            /**
			 * Render the template.
			 *
			 * @since 3.0.17
			 * @param {Object}  control - The customizer control object.
			 * @param {Object}  control.params - The control parameters.
			 * @param {string}  control.params.label - The control label.
			 * @param {string}  control.params.description - The control description.
			 * @param {string}  control.params.inputAttrs - extra input arguments.
			 * @param {Object}  control.params.default - The default value.
			 * @param {Object}  control.params.choices - The choices for the select dropdown.
			 * @param {string}  control.id - The setting.
			 * @returns {null}
			 */
            template(control) {
                const template = wp.template('kirki-input-select');

                control.container.html(template({
                    label: control.params.label,
                    description: control.params.description,
                    'data-id': control.id,
                    inputAttrs: control.params.inputAttrs,
                    choices: control.params.choices,
                    value: kirki.setting.get(control.id),
                    multiple: control.params.multiple || 1,
                    placeholder: control.params.placeholder,
                }));
            },
        },
    },
});
/* global kirkiL10n */
kirki = jQuery.extend(kirki, {

    /**
	 * An object containing definitions for input fields.
	 *
	 * @since 3.0.16
	 */
    input: {

        /**
		 * Radio input fields.
		 *
		 * @since 3.0.17
		 */
        radio: {

            /**
			 * Init the control.
			 *
			 * @since 3.0.17
			 * @param {Object} control - The control object.
			 * @param {Object} control.id - The setting.
			 * @returns {null}
			 */
            init(control) {
                const input = jQuery(`input[data-id="${control.id}"]`);

                // Save the value
                input.on('change keyup paste click', function () {
                    kirki.setting.set(control.id, jQuery(this).val());
                });
            },
        },

        /**
		 * Color input fields.
		 *
		 * @since 3.0.16
		 */
        color: {

            /**
			 * Init the control.
			 *
			 * @since 3.0.16
			 * @param {Object} control - The control object.
			 * @param {Object} control.id - The setting.
			 * @param {Object} control.choices - Additional options for the colorpickers.
			 * @param {Object} control.params - Control parameters.
			 * @param {Object} control.params.choices - alias for control.choices.

			 * @returns {null}
			 */
            init(control) {
                let picker = jQuery(`.kirki-color-control[data-id="${control.id}"]`),
                    clear;

                control.choices = control.choices || {};
                if (_.isEmpty(control.choices) && control.params.choices) {
                    control.choices = control.params.choices;
                }

                // If we have defined any extra choices, make sure they are passed-on to Iris.
                if (!_.isEmpty(control.choices)) {
                    picker.wpColorPicker(control.choices);
                }

                // Tweaks to make the "clear" buttons work.
                setTimeout(() => {
                    clear = jQuery(`.kirki-input-container[data-id="${control.id}"] .wp-picker-clear`);
                    if (clear.length) {
                        clear.click(() => {
                            kirki.setting.set(control.id, '');
                        });
                    }
                }, 200);

                // Saves our settings to the WP API
                picker.wpColorPicker({
                    change() {
                        // Small hack: the picker needs a small delay
                        setTimeout(() => {
                            kirki.setting.set(control.id, picker.val());
                        }, 20);
                    },
                });
            },
        },

        /**
		 * Generic input fields.
		 *
		 * @since 3.0.17
		 */
        genericInput: {

            /**
			 * Init the control.
			 *
			 * @since 3.0.17
			 * @param {Object} control - The control object.
			 * @param {Object} control.id - The setting.
			 * @returns {null}
			 */
            init(control) {
                const input = jQuery(`input[data-id="${control.id}"]`);

                // Save the value
                input.on('change keyup paste click', function () {
                    kirki.setting.set(control.id, jQuery(this).val());
                });
            },
        },

        /**
		 * Generic input fields.
		 *
		 * @since 3.0.17
		 */
        textarea: {

            /**
			 * Init the control.
			 *
			 * @since 3.0.17
			 * @param {Object} control - The control object.
			 * @param {Object} control.id - The setting.
			 * @returns {null}
			 */
            init(control) {
                const textarea = jQuery(`textarea[data-id="${control.id}"]`);

                // Save the value
                textarea.on('change keyup paste click', function () {
                    kirki.setting.set(control.id, jQuery(this).val());
                });
            },
        },

        select: {

            /**
			 * Init the control.
			 *
			 * @since 3.0.17
			 * @param {Object} control - The control object.
			 * @param {Object} control.id - The setting.
			 * @returns {null}
			 */
            init(control) {
                let element = jQuery(`select[data-id="${control.id}"]`),
                    multiple = parseInt(element.data('multiple'), 10),
                    selectValue,
                    selectWooOptions = {
                        // Disable search input.
                        minimumResultsForSearch: -1,
                        escapeMarkup(markup) {
                            return markup;
                        },
                    };
                if (control.params.placeholder) {
                    selectWooOptions.placeholder = control.params.placeholder;
                    selectWooOptions.allowClear = true;
                }

                if (multiple > 1) {
                    selectWooOptions.maximumSelectionLength = multiple;
                }
                jQuery(element).selectWoo(selectWooOptions).on('change', function () {
                    selectValue = jQuery(this).val();
                    selectValue = (selectValue === null && multiple > 1) ? [] : selectValue;
                    kirki.setting.set(control.id, selectValue);
                });
            },
        },

        /**
		 * Number fields.
		 *
		 * @since 3.0.26
		 */
        number: {

            /**
			 * Init the control.
			 *
			 * @since 3.0.17
			 * @param {Object} control - The control object.
			 * @param {Object} control.id - The setting.
			 * @returns {null}
			 */
            init(control) {
                let element = jQuery(`input[data-id="${control.id}"]`),
                    value = control.setting._value,
                    up,
                    down;

                // Make sure we use default values if none are define for some arguments.
                control.params.choices = _.defaults(control.params.choices, {
                    min: 0,
                    max: 100,
                    step: 1,
                });

                // Make sure we have a valid value.
                if (isNaN(value) || value === '') {
                    value = (control.params.choices.min < 0 && control.params.choices.max > 0) ? 0 : control.params.choices.min;
                }
                value = parseFloat(value);

                // If step is 'any', set to 0.001.
                control.params.choices.step = (control.params.choices.step === 'any') ? 0.001 : control.params.choices.step;

                // Make sure choices are properly formtted as numbers.
                control.params.choices.min = parseFloat(control.params.choices.min);
                control.params.choices.max = parseFloat(control.params.choices.max);
                control.params.choices.step = parseFloat(control.params.choices.step);

                up = jQuery(`.kirki-input-container[data-id="${control.id}"] .plus`);
                down = jQuery(`.kirki-input-container[data-id="${control.id}"] .minus`);

                up.click(() => {
                    let oldVal = parseFloat(element.val()),
                        newVal;

                    newVal = (oldVal >= control.params.choices.max) ? oldVal : oldVal + control.params.choices.step;

                    element.val(newVal);
                    element.trigger('change');
                });

                down.click(() => {
                    let oldVal = parseFloat(element.val()),
                        newVal;

                    newVal = (oldVal <= control.params.choices.min) ? oldVal : oldVal - control.params.choices.step;

                    element.val(newVal);
                    element.trigger('change');
                });

                element.on('change keyup paste click', function () {
                    let val = jQuery(this).val();
                    if (isNaN(val)) {
                        val = parseFloat(val, 10);
                        val = (isNaN(val)) ? 0 : val;
                        jQuery(this).attr('value', val);
                    }
                    kirki.setting.set(control.id, val);
                });
            },

        },

        /**
		 * Image fields.
		 *
		 * @since 3.0.34
		 */
        image: {

            /**
			 * Init the control.
			 *
			 * @since 3.0.34
			 * @param {Object} control - The control object.
			 * @returns {null}
			 */
            init(control) {
                let value = kirki.setting.get(control.id),
                    saveAs = (!_.isUndefined(control.params.choices) && !_.isUndefined(control.params.choices.save_as)) ? control.params.choices.save_as : 'url',
                    preview = control.container.find('.placeholder, .thumbnail'),
                    previewImage = (saveAs === 'array') ? value.url : value,
                    removeButton = control.container.find('.image-upload-remove-button'),
                    defaultButton = control.container.find('.image-default-button');

                // Make sure value is properly formatted.
                value = (saveAs === 'array' && _.isString(value)) ? { url: value } : value;

                // Tweaks for save_as = id.
                if ((saveAs === 'id' || saveAs === 'ID') && value !== '') {
                    wp.media.attachment(value).fetch().then(() => {
                        setTimeout(() => {
                            const url = wp.media.attachment(value).get('url');
                            preview.removeClass().addClass('thumbnail thumbnail-image').html(`<img src="${url}" alt="" />`);
                        }, 700);
                    });
                }

                // If value is not empty, hide the "default" button.
                if ((saveAs === 'url' && value !== '') || (saveAs === 'array' && !_.isUndefined(value.url) && value.url !== '')) {
                    control.container.find('image-default-button').hide();
                }

                // If value is empty, hide the "remove" button.
                if ((saveAs === 'url' && value === '') || (saveAs === 'array' && (_.isUndefined(value.url) || value.url === ''))) {
                    removeButton.hide();
                }

                // If value is default, hide the default button.
                if (value === control.params.default) {
                    control.container.find('image-default-button').hide();
                }

                if (previewImage !== '') {
                    preview.removeClass().addClass('thumbnail thumbnail-image').html(`<img src="${previewImage}" alt="" />`);
                }

                control.container.on('click', '.image-upload-button', (e) => {
                    var image = wp.media({ multiple: false }).open().on('select', () => {
                        // This will return the selected image from the Media Uploader, the result is an object.
                        let uploadedImage = image.state().get('selection').first(),
                            jsonImg = uploadedImage.toJSON(),
                            previewImage = jsonImg.url;

                        if (!_.isUndefined(jsonImg.sizes)) {
                            previewImage = jsonImg.sizes.full.url;
                            if (!_.isUndefined(jsonImg.sizes.medium)) {
                                previewImage = jsonImg.sizes.medium.url;
                            } else if (!_.isUndefined(jsonImg.sizes.thumbnail)) {
                                previewImage = jsonImg.sizes.thumbnail.url;
                            }
                        }

                        if (saveAs === 'array') {
                            kirki.setting.set(control.id, {
                                id: jsonImg.id,
                                url: jsonImg.sizes.full.url,
                                width: jsonImg.width,
                                height: jsonImg.height,
                            });
                        } else if (saveAs === 'id') {
                            kirki.setting.set(control.id, jsonImg.id);
                        } else {
                            kirki.setting.set(control.id, ((!_.isUndefined(jsonImg.sizes)) ? jsonImg.sizes.full.url : jsonImg.url));
                        }

                        if (preview.length) {
                            preview.removeClass().addClass('thumbnail thumbnail-image').html(`<img src="${previewImage}" alt="" />`);
                        }
                        if (removeButton.length) {
                            removeButton.show();
                            defaultButton.hide();
                        }
                    });

                    e.preventDefault();
                });

                control.container.on('click', '.image-upload-remove-button', (e) => {
                    let preview,
                        removeButton,
                        defaultButton;

                    e.preventDefault();

                    kirki.setting.set(control.id, '');

                    preview = control.container.find('.placeholder, .thumbnail');
                    removeButton = control.container.find('.image-upload-remove-button');
                    defaultButton = control.container.find('.image-default-button');

                    if (preview.length) {
                        preview.removeClass().addClass('placeholder').html(kirkiL10n.noFileSelected);
                    }
                    if (removeButton.length) {
                        removeButton.hide();
                        if (jQuery(defaultButton).hasClass('button')) {
                            defaultButton.show();
                        }
                    }
                });

                control.container.on('click', '.image-default-button', (e) => {
                    let preview,
                        removeButton,
                        defaultButton;

                    e.preventDefault();

                    kirki.setting.set(control.id, control.params.default);

                    preview = control.container.find('.placeholder, .thumbnail');
                    removeButton = control.container.find('.image-upload-remove-button');
                    defaultButton = control.container.find('.image-default-button');

                    if (preview.length) {
                        preview.removeClass().addClass('thumbnail thumbnail-image').html(`<img src="${control.params.default}" alt="" />`);
                    }
                    if (removeButton.length) {
                        removeButton.show();
                        defaultButton.hide();
                    }
                });
            },
        },
    },
});
kirki = jQuery.extend(kirki, {

    /**
	 * An object containing definitions for settings.
	 *
	 * @since 3.0.16
	 */
    setting: {

        /**
		 * Gets the value of a setting.
		 *
		 * This is a helper function that allows us to get the value of
		 * control[key1][key2] for example, when the setting used in the
		 * customizer API is "control".
		 *
		 * @since 3.0.16
		 * @param {string} setting - The setting for which we're getting the value.
		 * @returns {mixed} Depends on the value.
		 */
        get(setting) {
            let parts = setting.split('['),
                foundSetting = '',
                foundInStep = 0,
                currentVal = '';

            _.each(parts, (part, i) => {
                part = part.replace(']', '');

                if (i === 0) {
                    foundSetting = part;
                } else {
                    foundSetting += `[${part}]`;
                }

                if (!_.isUndefined(wp.customize.instance(foundSetting))) {
                    currentVal = wp.customize.instance(foundSetting).get();
                    foundInStep = i;
                }

                if (foundInStep < i) {
                    if (_.isObject(currentVal) && !_.isUndefined(currentVal[part])) {
                        currentVal = currentVal[part];
                    }
                }
            });

            return currentVal;
        },

        /**
		 * Sets the value of a setting.
		 *
		 * This function is a bit complicated because there any many scenarios to consider.
		 * Example: We want to save the value for my_setting[something][3][something-else].
		 * The control's setting is my_setting[something].
		 * So we need to find that first, then figure out the remaining parts,
		 * merge the values recursively to avoid destroying my_setting[something][2]
		 * and also take into account any defined "key" arguments which take this even deeper.
		 *
		 * @since 3.0.16
		 * @param {object|string} element - The DOM element whose value has changed,
		 *                                  or an ID.
		 * @param {mixed}         value - Depends on the control-type.
		 * @param {string}        key - If we only want to save an item in an object
		 *                                  we can define the key here.
		 * @returns {null}
		 */
        set(element, value, key) {
            let setting,
                parts,
                currentNode = '',
                foundNode = '',
                subSettingObj = {},
                currentVal,
                subSetting,
                subSettingParts;

            // Get the setting from the element.
            setting = element;
            if (_.isObject(element)) {
                if (jQuery(element).attr('data-id')) {
                    setting = element.attr('data-id');
                } else {
                    setting = element.parents('[data-id]').attr('data-id');
                }
            }

            if (typeof wp.customize.control(setting) !== 'undefined') {
                wp.customize.control(setting).setting.set(value);
                return;
            }

            parts = setting.split('[');

            // Find the setting we're using in the control using the customizer API.
            _.each(parts, (part, i) => {
                part = part.replace(']', '');

                // The current part of the setting.
                currentNode = (i === 0) ? part : `[${part}]`;

                // When we find the node, get the value from it.
                // In case of an object we'll need to merge with current values.
                if (!_.isUndefined(wp.customize.instance(currentNode))) {
                    foundNode = currentNode;
                    currentVal = wp.customize.instance(foundNode).get();
                }
            });

            // Get the remaining part of the setting that was unused.
            subSetting = setting.replace(foundNode, '');

            // If subSetting is not empty, then we're dealing with an object
            // and we need to dig deeper and recursively merge the values.
            if (subSetting !== '') {
                if (!_.isObject(currentVal)) {
                    currentVal = {};
                }
                if (subSetting.charAt(0) === '[') {
                    subSetting = subSetting.replace('[', '');
                }
                subSettingParts = subSetting.split('[');
                _.each(subSettingParts, (subSettingPart, i) => {
                    subSettingParts[i] = subSettingPart.replace(']', '');
                });

                // If using a key, we need to go 1 level deeper.
                if (key) {
                    subSettingParts.push(key);
                }

                // Converting to a JSON string and then parsing that to an object
                // may seem a bit hacky and crude but it's efficient and works.
                subSettingObj = `{"${subSettingParts.join('":{"')}":"${value}"${'}'.repeat(subSettingParts.length)}`;
                subSettingObj = JSON.parse(subSettingObj);

                // Recursively merge with current value.
                jQuery.extend(true, currentVal, subSettingObj);
                value = currentVal;
            } else if (key) {
                currentVal = (!_.isObject(currentVal)) ? {} : currentVal;
                currentVal[key] = value;
                value = currentVal;
            }
            wp.customize.control(foundNode).setting.set(value);
        },
    },
});
/* global ajaxurl */

/* global kirki */
/**
 * The majority of the code in this file
 * is derived from the wp-customize-posts plugin
 * and the work of @westonruter to whom I am very grateful.
 *
 * @see https://github.com/xwp/wp-customize-posts
 */

(function () {
    /**
	 * A dynamic color-alpha control.
	 *
	 * @class
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
    wp.customize.kirkiDynamicControl = wp.customize.Control.extend({

        initialize(id, options) {
            let control = this,
                args = options || {};

            args.params = args.params || {};

            if (!args.params.content) {
                args.params.content = jQuery('<li></li>');
                args.params.content.attr('id', `customize-control-${id.replace(/]/g, '').replace(/\[/g, '-')}`);
                args.params.content.attr('class', `customize-control customize-control-${args.params.type}`);
            }

            control.propertyElements = [];
            wp.customize.Control.prototype.initialize.call(control, id, args);
        },

        /**
		 * Add bidirectional data binding links between inputs and the setting(s).
		 *
		 * This is copied from wp.customize.Control.prototype.initialize(). It
		 * should be changed in Core to be applied once the control is embedded.
		 *
		 * @private
		 * @returns {null}
		 */
        _setUpSettingRootLinks() {
            let control = this,
                nodes = control.container.find('[data-customize-setting-link]');

            nodes.each(function () {
                const node = jQuery(this);

                wp.customize(node.data('customizeSettingLink'), (setting) => {
                    const element = new wp.customize.Element(node);
                    control.elements.push(element);
                    element.sync(setting);
                    element.set(setting());
                });
            });
        },

        /**
		 * Add bidirectional data binding links between inputs and the setting properties.
		 *
		 * @private
		 * @returns {null}
		 */
        _setUpSettingPropertyLinks() {
            let control = this,
                nodes;

            if (!control.setting) {
                return;
            }

            nodes = control.container.find('[data-customize-setting-property-link]');

            nodes.each(function () {
                let node = jQuery(this),
                    element,
                    propertyName = node.data('customizeSettingPropertyLink');

                element = new wp.customize.Element(node);
                control.propertyElements.push(element);
                element.set(control.setting()[propertyName]);

                element.bind((newPropertyValue) => {
                    let newSetting = control.setting();
                    if (newPropertyValue === newSetting[propertyName]) {
                        return;
                    }
                    newSetting = _.clone(newSetting);
                    newSetting[propertyName] = newPropertyValue;
                    control.setting.set(newSetting);
                });
                control.setting.bind((newValue) => {
                    if (newValue[propertyName] !== element.get()) {
                        element.set(newValue[propertyName]);
                    }
                });
            });
        },

        /**
		 * @inheritdoc
		 */
        ready() {
            const control = this;

            control._setUpSettingRootLinks();
            control._setUpSettingPropertyLinks();

            wp.customize.Control.prototype.ready.call(control);

            control.deferred.embedded.done(() => {
                control.initKirkiControl(control);
            });
        },

        /**
		 * Embed the control in the document.
		 *
		 * Override the embed() method to do nothing,
		 * so that the control isn't embedded on load,
		 * unless the containing section is already expanded.
		 *
		 * @returns {null}
		 */
        embed() {
            let control = this,
                sectionId = control.section();

            if (!sectionId) {
                return;
            }

            wp.customize.section(sectionId, (section) => {
                if (section.params.type === 'kirki-expanded' || section.expanded() || wp.customize.settings.autofocus.control === control.id) {
                    control.actuallyEmbed();
                } else {
                    section.expanded.bind((expanded) => {
                        if (expanded) {
                            control.actuallyEmbed();
                        }
                    });
                }
            });
        },

        /**
		 * Deferred embedding of control when actually
		 *
		 * This function is called in Section.onChangeExpanded() so the control
		 * will only get embedded when the Section is first expanded.
		 *
		 * @returns {null}
		 */
        actuallyEmbed() {
            const control = this;
            if (control.deferred.embedded.state() === 'resolved') {
                return;
            }
            control.renderContent();
            control.deferred.embedded.resolve(); // This triggers control.ready().
        },

        /**
		 * This is not working with autofocus.
		 *
		 * @param {object} [args] Args.
		 * @returns {null}
		 */
        focus(args) {
            const control = this;
            control.actuallyEmbed();
            wp.customize.Control.prototype.focus.call(control, args);
        },

        /**
		 * Additional actions that run on ready.
		 *
		 * @param {object} [args] Args.
		 * @returns {null}
		 */
        initKirkiControl(control) {
            if (typeof kirki.control[control.params.type] !== 'undefined') {
                kirki.control[control.params.type].init(control);
                return;
            }

            // Save the value
            this.container.on('change keyup paste click', 'input', function () {
                control.setting.set(jQuery(this).val());
            });
        },
    });
}());
_.each(kirki.control, (obj, type) => {
    wp.customize.controlConstructor[type] = wp.customize.kirkiDynamicControl.extend({});
});
/* global kirkiControlLoader */
wp.customize.controlConstructor['kirki-dashicons'] = wp.customize.kirkiDynamicControl.extend({});

/* global tinyMCE */
wp.customize.controlConstructor['kirki-editor'] = wp.customize.kirkiDynamicControl.extend({

    initKirkiControl() {
        let control = this,
            element = control.container.find('textarea'),
            id = `kirki-editor-${control.id.replace('[', '').replace(']', '')}`,
            editor;

        wp.editor.initialize(id, {
            tinymce: {
                wpautop: true,
            },
            quicktags: true,
            mediaButtons: true,
        });

        editor = tinyMCE.get(id);

        if (editor) {
            editor.onChange.add((ed) => {
                let content;

                ed.save();
                content = editor.getContent();
                element.val(content).trigger('change');
                wp.customize.instance(control.id).set(content);
            });
        }
    },
});
wp.customize.controlConstructor['kirki-multicheck'] = wp.customize.kirkiDynamicControl.extend({

    initKirkiControl() {
        const control = this;

        // Save the value
        control.container.on('change', 'input', () => {
            let value = [],
                i = 0;

            // Build the value as an object using the sub-values from individual checkboxes.
            jQuery.each(control.params.choices, (key) => {
                if (control.container.find(`input[value="${key}"]`).is(':checked')) {
                    control.container.find(`input[value="${key}"]`).parent().addClass('checked');
                    value[i] = key;
                    i++;
                } else {
                    control.container.find(`input[value="${key}"]`).parent().removeClass('checked');
                }
            });

            // Update the value in the customizer.
            control.setting.set(value);
        });
    },
});
/* global kirkiControlLoader */
wp.customize.controlConstructor['kirki-multicolor'] = wp.customize.Control.extend({

    // When we're finished loading continue processing
    ready() {
        const control = this;

        // Init the control.
        if (!_.isUndefined(window.kirkiControlLoader) && _.isFunction(kirkiControlLoader)) {
            kirkiControlLoader(control);
        } else {
            control.initKirkiControl();
        }
    },

    initKirkiControl() {
        let control = this,
            colors = control.params.choices,
            keys = Object.keys(colors),
            value = this.params.value,
            i = 0;

        // Proxy function that handles changing the individual colors
        function kirkiMulticolorChangeHandler(control, value, subSetting) {
            let picker = control.container.find(`.multicolor-index-${subSetting}`),
                args = {
                    change() {
                        // Color controls require a small delay.
                        setTimeout(() => {
                            // Set the value.
                            control.saveValue(subSetting, picker.val());

                            // Trigger the change.
                            control.container.find(`.multicolor-index-${subSetting}`).trigger('change');
                        }, 100);
                    },
                };

            if (_.isObject(colors.irisArgs)) {
                _.each(colors.irisArgs, (irisValue, irisKey) => {
                    args[irisKey] = irisValue;
                });
            }

            // Did we change the value?
            picker.wpColorPicker(args);
        }

        // Colors loop
        while (i < Object.keys(colors).length) {
            kirkiMulticolorChangeHandler(this, value, keys[i]);
            i++;
        }
    },

    /**
	 * Saves the value.
	 */
    saveValue(property, value) {
        let control = this,
            input = control.container.find('.multicolor-hidden-value'),
            val = control.setting._value;

        val[property] = value;

        jQuery(input).attr('value', JSON.stringify(val)).trigger('change');
        control.setting.set(val);
    },
});
/* global kirkiControlLoader */
const RepeaterRow = function (rowIndex, container, label, control) {
    const self = this;
    this.rowIndex = rowIndex;
    this.container = container;
    this.label = label;
    this.header = this.container.find('.repeater-row-header');

    this.header.on('click', () => {
        self.toggleMinimize();
    });

    this.container.on('click', '.repeater-row-remove', () => {
        self.remove();
    });

    this.header.on('mousedown', () => {
        self.container.trigger('row:start-dragging');
    });

    this.container.on('keyup change', 'input, select, textarea', (e) => {
        self.container.trigger('row:update', [self.rowIndex, jQuery(e.target).data('field'), e.target]);
    });

    this.setRowIndex = function (rowIndex) {
        this.rowIndex = rowIndex;
        this.container.attr('data-row', rowIndex);
        this.container.data('row', rowIndex);
        this.updateLabel();
    };

    this.toggleMinimize = function () {
        // Store the previous state.
        this.container.toggleClass('minimized');
        this.header.find('.dashicons').toggleClass('dashicons-arrow-up').toggleClass('dashicons-arrow-down');
    };

    this.remove = function () {
        this.container.slideUp(300, function () {
            jQuery(this).detach();
        });
        this.container.trigger('row:remove', [this.rowIndex]);
    };

    this.updateLabel = function () {
        let rowLabelField,
            rowLabel,
            rowLabelSelector;

        if (this.label.type === 'field') {
            rowLabelField = this.container.find(`.repeater-field [data-field="${this.label.field}"]`);
            if (_.isFunction(rowLabelField.val)) {
                rowLabel = rowLabelField.val();
                if (rowLabel !== '') {
                    if (!_.isUndefined(control.params.fields[this.label.field])) {
                        if (!_.isUndefined(control.params.fields[this.label.field].type)) {
                            if (control.params.fields[this.label.field].type === 'select') {
                                if (!_.isUndefined(control.params.fields[this.label.field].choices) && !_.isUndefined(control.params.fields[this.label.field].choices[rowLabelField.val()])) {
                                    rowLabel = control.params.fields[this.label.field].choices[rowLabelField.val()];
                                }
                            } else if (control.params.fields[this.label.field].type === 'radio' || control.params.fields[this.label.field].type === 'radio-image') {
                                rowLabelSelector = `${control.selector} [data-row="${this.rowIndex}"] .repeater-field [data-field="${this.label.field}"]:checked`;
                                rowLabel = jQuery(rowLabelSelector).val();
                            }
                        }
                    }
                    this.header.find('.repeater-row-label').text(rowLabel);
                    return;
                }
            }
        }
        this.header.find('.repeater-row-label').text(`${this.label.value} ${this.rowIndex + 1}`);
    };
    this.updateLabel();
};

wp.customize.controlConstructor.repeater = wp.customize.Control.extend({

    // When we're finished loading continue processing
    ready() {
        const control = this;

        // Init the control.
        if (!_.isUndefined(window.kirkiControlLoader) && _.isFunction(kirkiControlLoader)) {
            kirkiControlLoader(control);
        } else {
            control.initKirkiControl();
        }
    },

    initKirkiControl() {
        let control = this,
            limit,
            theNewRow;

        // The current value set in Control Class (set in Ghost_Framework_Kirki_Customize_Repeater_Control::to_json() function)
        const settingValue = this.params.value;

        // The hidden field that keeps the data saved (though we never update it)
        this.settingField = this.container.find('[data-customize-setting-link]').first();

        // Set the field value for the first time, we'll fill it up later
        this.setValue([], false);

        // The DIV that holds all the rows
        this.repeaterFieldsContainer = this.container.find('.repeater-fields').first();

        // Set number of rows to 0
        this.currentIndex = 0;

        // Save the rows objects
        this.rows = [];

        // Default limit choice
        limit = false;
        if (!_.isUndefined(this.params.choices.limit)) {
            limit = (this.params.choices.limit <= 0) ? false : parseInt(this.params.choices.limit, 10);
        }

        this.container.on('click', 'button.repeater-add', (e) => {
            e.preventDefault();
            if (!limit || control.currentIndex < limit) {
                theNewRow = control.addRow();
                theNewRow.toggleMinimize();
                control.initColorPicker();
                control.initSelect(theNewRow);
            } else {
                jQuery(`${control.selector} .limit`).addClass('highlight');
            }
        });

        this.container.on('click', '.repeater-row-remove', () => {
            control.currentIndex--;
            if (!limit || control.currentIndex < limit) {
                jQuery(`${control.selector} .limit`).removeClass('highlight');
            }
        });

        this.container.on('click keypress', '.repeater-field-image .upload-button,.repeater-field-cropped_image .upload-button,.repeater-field-upload .upload-button', function (e) {
            e.preventDefault();
            control.$thisButton = jQuery(this);
            control.openFrame(e);
        });

        this.container.on('click keypress', '.repeater-field-image .remove-button,.repeater-field-cropped_image .remove-button', function (e) {
            e.preventDefault();
            control.$thisButton = jQuery(this);
            control.removeImage(e);
        });

        this.container.on('click keypress', '.repeater-field-upload .remove-button', function (e) {
            e.preventDefault();
            control.$thisButton = jQuery(this);
            control.removeFile(e);
        });

        /**
		 * Function that loads the Mustache template
		 */
        this.repeaterTemplate = _.memoize(() => {
            let compiled,

                /*
				 * Underscore's default ERB-style templates are incompatible with PHP
				 * when asp_tags is enabled, so WordPress uses Mustache-inspired templating syntax.
				 *
				 * @see trac ticket #22344.
				 */
                options = {
                    evaluate: /<#([\s\S]+?)#>/g,
                    interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                    escape: /\{\{([^\}]+?)\}\}(?!\})/g,
                    variable: 'data',
                };

            return function (data) {
                compiled = _.template(control.container.find('.customize-control-repeater-content').first().html(), null, options);
                return compiled(data);
            };
        });

        // When we load the control, the fields have not been filled up
        // This is the first time that we create all the rows
        if (settingValue.length) {
            _.each(settingValue, (subValue) => {
                theNewRow = control.addRow(subValue);
                control.initColorPicker();
                control.initSelect(theNewRow, subValue);
            });
        }

        // Once we have displayed the rows, we cleanup the values
        this.setValue(settingValue, true, true);

        this.repeaterFieldsContainer.sortable({
            handle: '.repeater-row-header',
            update() {
                control.sort();
            },
        });
    },

    /**
	 * Open the media modal.
	 */
    openFrame(event) {
        if (wp.customize.utils.isKeydownButNotEnterEvent(event)) {
            return;
        }

        if (this.$thisButton.closest('.repeater-field').hasClass('repeater-field-cropped_image')) {
            this.initCropperFrame();
        } else {
            this.initFrame();
        }

        this.frame.open();
    },

    initFrame() {
        const libMediaType = this.getMimeType();

        this.frame = wp.media({
            states: [
                new wp.media.controller.Library({
                    library: wp.media.query({ type: libMediaType }),
                    multiple: false,
                    date: false,
                }),
            ],
        });

        // When a file is selected, run a callback.
        this.frame.on('select', this.onSelect, this);
    },

    /**
	 * Create a media modal select frame, and store it so the instance can be reused when needed.
	 * This is mostly a copy/paste of Core api.CroppedImageControl in /wp-admin/js/customize-control.js
	 */
    initCropperFrame() {
        // We get the field id from which this was called
        let currentFieldId = this.$thisButton.siblings('input.hidden-field').attr('data-field'),
            attrs = ['width', 'height', 'flex_width', 'flex_height'], // A list of attributes to look for
            libMediaType = this.getMimeType();

        // Make sure we got it
        if (_.isString(currentFieldId) && currentFieldId !== '') {
            // Make fields is defined and only do the hack for cropped_image
            if (_.isObject(this.params.fields[currentFieldId]) && this.params.fields[currentFieldId].type === 'cropped_image') {
                // Iterate over the list of attributes
                attrs.forEach((el) => {
                    // If the attribute exists in the field
                    if (!_.isUndefined(this.params.fields[currentFieldId][el])) {
                        // Set the attribute in the main object
                        this.params[el] = this.params.fields[currentFieldId][el];
                    }
                });
            }
        }

        this.frame = wp.media({
            button: {
                text: 'Select and Crop',
                close: false,
            },
            states: [
                new wp.media.controller.Library({
                    library: wp.media.query({ type: libMediaType }),
                    multiple: false,
                    date: false,
                    suggestedWidth: this.params.width,
                    suggestedHeight: this.params.height,
                }),
                new wp.media.controller.CustomizeImageCropper({
                    imgSelectOptions: this.calculateImageSelectOptions,
                    control: this,
                }),
            ],
        });

        this.frame.on('select', this.onSelectForCrop, this);
        this.frame.on('cropped', this.onCropped, this);
        this.frame.on('skippedcrop', this.onSkippedCrop, this);
    },

    onSelect() {
        const attachment = this.frame.state().get('selection').first().toJSON();

        if (this.$thisButton.closest('.repeater-field').hasClass('repeater-field-upload')) {
            this.setFileInRepeaterField(attachment);
        } else {
            this.setImageInRepeaterField(attachment);
        }
    },

    /**
	 * After an image is selected in the media modal, switch to the cropper
	 * state if the image isn't the right size.
	 */

    onSelectForCrop() {
        const attachment = this.frame.state().get('selection').first().toJSON();

        if (this.params.width === attachment.width && this.params.height === attachment.height && !this.params.flex_width && !this.params.flex_height) {
            this.setImageInRepeaterField(attachment);
        } else {
            this.frame.setState('cropper');
        }
    },

    /**
	 * After the image has been cropped, apply the cropped image data to the setting.
	 *
	 * @param {object} croppedImage Cropped attachment data.
	 */
    onCropped(croppedImage) {
        this.setImageInRepeaterField(croppedImage);
    },

    /**
	 * Returns a set of options, computed from the attached image data and
	 * control-specific data, to be fed to the imgAreaSelect plugin in
	 * wp.media.view.Cropper.
	 *
	 * @param {wp.media.model.Attachment} attachment
	 * @param {wp.media.controller.Cropper} controller
	 * @returns {Object} Options
	 */
    calculateImageSelectOptions(attachment, controller) {
        let control = controller.get('control'),
            flexWidth = !!parseInt(control.params.flex_width, 10),
            flexHeight = !!parseInt(control.params.flex_height, 10),
            realWidth = attachment.get('width'),
            realHeight = attachment.get('height'),
            xInit = parseInt(control.params.width, 10),
            yInit = parseInt(control.params.height, 10),
            ratio = xInit / yInit,
            xImg = realWidth,
            yImg = realHeight,
            x1,
            y1,
            imgSelectOptions;

        controller.set('canSkipCrop', !control.mustBeCropped(flexWidth, flexHeight, xInit, yInit, realWidth, realHeight));

        if (xImg / yImg > ratio) {
            yInit = yImg;
            xInit = yInit * ratio;
        } else {
            xInit = xImg;
            yInit = xInit / ratio;
        }

        x1 = (xImg - xInit) / 2;
        y1 = (yImg - yInit) / 2;

        imgSelectOptions = {
            handles: true,
            keys: true,
            instance: true,
            persistent: true,
            imageWidth: realWidth,
            imageHeight: realHeight,
            x1,
            y1,
            x2: xInit + x1,
            y2: yInit + y1,
        };

        if (flexHeight === false && flexWidth === false) {
            imgSelectOptions.aspectRatio = `${xInit}:${yInit}`;
        }
        if (flexHeight === false) {
            imgSelectOptions.maxHeight = yInit;
        }
        if (flexWidth === false) {
            imgSelectOptions.maxWidth = xInit;
        }

        return imgSelectOptions;
    },

    /**
	 * Return whether the image must be cropped, based on required dimensions.
	 *
	 * @param {bool} flexW
	 * @param {bool} flexH
	 * @param {int}  dstW
	 * @param {int}  dstH
	 * @param {int}  imgW
	 * @param {int}  imgH
	 * @return {bool}
	 */
    mustBeCropped(flexW, flexH, dstW, dstH, imgW, imgH) {
        if ((flexW === true && flexH === true) || (flexW === true && dstH === imgH) || (flexH === true && dstW === imgW) || (dstW === imgW && dstH === imgH) || (imgW <= dstW)) {
            return false;
        }

        return true;
    },

    /**
	 * If cropping was skipped, apply the image data directly to the setting.
	 */
    onSkippedCrop() {
        const attachment = this.frame.state().get('selection').first().toJSON();
        this.setImageInRepeaterField(attachment);
    },

    /**
	 * Updates the setting and re-renders the control UI.
	 *
	 * @param {object} attachment
	 */
    setImageInRepeaterField(attachment) {
        const $targetDiv = this.$thisButton.closest('.repeater-field-image,.repeater-field-cropped_image');

        $targetDiv.find('.kirki-image-attachment').html(`<img src="${attachment.url}">`).hide().slideDown('slow');

        $targetDiv.find('.hidden-field').val(attachment.id);
        this.$thisButton.text(this.$thisButton.data('alt-label'));
        $targetDiv.find('.remove-button').show();

        // This will activate the save button
        $targetDiv.find('input, textarea, select').trigger('change');
        this.frame.close();
    },

    /**
	 * Updates the setting and re-renders the control UI.
	 *
	 * @param {object} attachment
	 */
    setFileInRepeaterField(attachment) {
        const $targetDiv = this.$thisButton.closest('.repeater-field-upload');

        $targetDiv.find('.kirki-file-attachment').html(`<span class="file"><span class="dashicons dashicons-media-default"></span> ${attachment.filename}</span>`).hide().slideDown('slow');

        $targetDiv.find('.hidden-field').val(attachment.id);
        this.$thisButton.text(this.$thisButton.data('alt-label'));
        $targetDiv.find('.upload-button').show();
        $targetDiv.find('.remove-button').show();

        // This will activate the save button
        $targetDiv.find('input, textarea, select').trigger('change');
        this.frame.close();
    },

    getMimeType() {
        // We get the field id from which this was called
        const currentFieldId = this.$thisButton.siblings('input.hidden-field').attr('data-field');

        // Make sure we got it
        if (_.isString(currentFieldId) && currentFieldId !== '') {
            // Make fields is defined and only do the hack for cropped_image
            if (_.isObject(this.params.fields[currentFieldId]) && this.params.fields[currentFieldId].type === 'upload') {
                // If the attribute exists in the field
                if (!_.isUndefined(this.params.fields[currentFieldId].mime_type)) {
                    // Set the attribute in the main object
                    return this.params.fields[currentFieldId].mime_type;
                }
            }
        }
        return 'image';
    },

    removeImage(event) {
        let $targetDiv,
            $uploadButton;

        if (wp.customize.utils.isKeydownButNotEnterEvent(event)) {
            return;
        }

        $targetDiv = this.$thisButton.closest('.repeater-field-image,.repeater-field-cropped_image,.repeater-field-upload');
        $uploadButton = $targetDiv.find('.upload-button');

        $targetDiv.find('.kirki-image-attachment').slideUp('fast', function () {
            jQuery(this).show().html(jQuery(this).data('placeholder'));
        });
        $targetDiv.find('.hidden-field').val('');
        $uploadButton.text($uploadButton.data('label'));
        this.$thisButton.hide();

        $targetDiv.find('input, textarea, select').trigger('change');
    },

    removeFile(event) {
        let $targetDiv,
            $uploadButton;

        if (wp.customize.utils.isKeydownButNotEnterEvent(event)) {
            return;
        }

        $targetDiv = this.$thisButton.closest('.repeater-field-upload');
        $uploadButton = $targetDiv.find('.upload-button');

        $targetDiv.find('.kirki-file-attachment').slideUp('fast', function () {
            jQuery(this).show().html(jQuery(this).data('placeholder'));
        });
        $targetDiv.find('.hidden-field').val('');
        $uploadButton.text($uploadButton.data('label'));
        this.$thisButton.hide();

        $targetDiv.find('input, textarea, select').trigger('change');
    },

    /**
	 * Get the current value of the setting
	 *
	 * @return Object
	 */
    getValue() {
        // The setting is saved in JSON
        return JSON.parse(decodeURI(this.setting.get()));
    },

    /**
	 * Set a new value for the setting
	 *
	 * @param newValue Object
	 * @param refresh If we want to refresh the previewer or not
	 */
    setValue(newValue, refresh, filtering) {
        // We need to filter the values after the first load to remove data requrired for diplay but that we don't want to save in DB
        let filteredValue = newValue,
            filter = [];

        if (filtering) {
            jQuery.each(this.params.fields, (index, value) => {
                if (value.type === 'image' || value.type === 'cropped_image' || value.type === 'upload') {
                    filter.push(index);
                }
            });
            jQuery.each(newValue, (index, value) => {
                jQuery.each(filter, (ind, field) => {
                    if (!_.isUndefined(value[field]) && !_.isUndefined(value[field].id)) {
                        filteredValue[index][field] = value[field].id;
                    }
                });
            });
        }

        this.setting.set(encodeURI(JSON.stringify(filteredValue)));

        if (refresh) {
            // Trigger the change event on the hidden field so
            // previewer refresh the website on Customizer
            this.settingField.trigger('change');
        }
    },

    /**
	 * Add a new row to repeater settings based on the structure.
	 *
	 * @param data (Optional) Object of field => value pairs (undefined if you want to get the default values)
	 */
    addRow(data) {
        let control = this,
            template = control.repeaterTemplate(), // The template for the new row (defined on Ghost_Framework_Kirki_Customize_Repeater_Control::render_content() ).
            settingValue = this.getValue(), // Get the current setting value.
            newRowSetting = {}, // Saves the new setting data.
            templateData, // Data to pass to the template
            newRow,
            i;

        if (template) {
            // The control structure is going to define the new fields
            // We need to clone control.params.fields. Assigning it
            // ould result in a reference assignment.
            templateData = jQuery.extend(true, {}, control.params.fields);

            // But if we have passed data, we'll use the data values instead
            if (data) {
                for (i in data) {
                    if (data.hasOwnProperty(i) && templateData.hasOwnProperty(i)) {
                        templateData[i].default = data[i];
                    }
                }
            }

            templateData.index = this.currentIndex;

            // Append the template content
            template = template(templateData);

            // Create a new row object and append the element
            newRow = new RepeaterRow(
                control.currentIndex,
                jQuery(template).appendTo(control.repeaterFieldsContainer),
                control.params.row_label,
                control,
            );

            newRow.container.on('row:remove', (e, rowIndex) => {
                control.deleteRow(rowIndex);
            });

            newRow.container.on('row:update', (e, rowIndex, fieldName, element) => {
                control.updateField.call(control, e, rowIndex, fieldName, element);
                newRow.updateLabel();
            });

            // Add the row to rows collection
            this.rows[this.currentIndex] = newRow;

            for (i in templateData) {
                if (templateData.hasOwnProperty(i)) {
                    newRowSetting[i] = templateData[i].default;
                }
            }

            settingValue[this.currentIndex] = newRowSetting;
            this.setValue(settingValue, true);

            this.currentIndex++;

            return newRow;
        }
    },

    sort() {
        let control = this,
            $rows = this.repeaterFieldsContainer.find('.repeater-row'),
            newOrder = [],
            settings = control.getValue(),
            newRows = [],
            newSettings = [];

        $rows.each((i, element) => {
            newOrder.push(jQuery(element).data('row'));
        });

        jQuery.each(newOrder, (newPosition, oldPosition) => {
            newRows[newPosition] = control.rows[oldPosition];
            newRows[newPosition].setRowIndex(newPosition);

            newSettings[newPosition] = settings[oldPosition];
        });

        control.rows = newRows;
        control.setValue(newSettings);
    },

    /**
	 * Delete a row in the repeater setting
	 *
	 * @param index Position of the row in the complete Setting Array
	 */
    deleteRow(index) {
        let currentSettings = this.getValue(),
            row,
            i,
            prop;

        if (currentSettings[index]) {
            // Find the row
            row = this.rows[index];
            if (row) {
                // Remove the row settings
                delete currentSettings[index];

                // Remove the row from the rows collection
                delete this.rows[index];

                // Update the new setting values
                this.setValue(currentSettings, true);
            }
        }

        // Remap the row numbers
        i = 1;
        for (prop in this.rows) {
            if (this.rows.hasOwnProperty(prop) && this.rows[prop]) {
                this.rows[prop].updateLabel();
                i++;
            }
        }
    },

    /**
	 * Update a single field inside a row.
	 * Triggered when a field has changed
	 *
	 * @param e Event Object
	 */
    updateField(e, rowIndex, fieldId, element) {
        let type,
            row,
            currentSettings;

        if (!this.rows[rowIndex]) {
            return;
        }

        if (!this.params.fields[fieldId]) {
            return;
        }

        type = this.params.fields[fieldId].type;
        row = this.rows[rowIndex];
        currentSettings = this.getValue();

        element = jQuery(element);

        if (_.isUndefined(currentSettings[row.rowIndex][fieldId])) {
            return;
        }

        if (type === 'checkbox') {
            currentSettings[row.rowIndex][fieldId] = element.is(':checked');
        } else {
            // Update the settings
            currentSettings[row.rowIndex][fieldId] = element.val();
        }
        this.setValue(currentSettings, true);
    },

    /**
	 * Init the color picker on color fields
	 * Called after AddRow
	 *
	 */
    initColorPicker() {
        let control = this,
            colorPicker = control.container.find('.color-picker-hex'),
            options = {},
            fieldId = colorPicker.data('field');

        // We check if the color palette parameter is defined.
        if (!_.isUndefined(fieldId) && !_.isUndefined(control.params.fields[fieldId]) && !_.isUndefined(control.params.fields[fieldId].palettes) && _.isObject(control.params.fields[fieldId].palettes)) {
            options.palettes = control.params.fields[fieldId].palettes;
        }

        // When the color picker value is changed we update the value of the field
        options.change = function (event, ui) {
            let currentPicker = jQuery(event.target),
                row = currentPicker.closest('.repeater-row'),
                rowIndex = row.data('row'),
                currentSettings = control.getValue();

            currentSettings[rowIndex][currentPicker.data('field')] = ui.color.toString();
            control.setValue(currentSettings, true);
        };

        // Init the color picker
        if (colorPicker.length !== 0) {
            colorPicker.wpColorPicker(options);
        }
    },

    /**
	 * Init the dropdown-pages field with selectWoo
	 * Called after AddRow
	 *
	 * @param {object} theNewRow the row that was added to the repeater
	 * @param {object} data the data for the row if we're initializing a pre-existing row
	 *
	 */
    initSelect(theNewRow, data) {
        let control = this,
            dropdown = theNewRow.container.find('.repeater-field select'),
            $select,
            dataField,
            multiple,
            selectWooOptions = {
                // Disable search input.
                minimumResultsForSearch: -1,
            };

        if (dropdown.length === 0) {
            return;
        }

        dataField = dropdown.data('field');
        multiple = jQuery(dropdown).data('multiple');
        if (multiple !== 'undefed' && jQuery.isNumeric(multiple)) {
            multiple = parseInt(multiple, 10);
            if (multiple > 1) {
                selectWooOptions.maximumSelectionLength = multiple;
            }
        }

        data = data || {};
        data[dataField] = data[dataField] || '';

        $select = jQuery(dropdown).selectWoo(selectWooOptions).val(data[dataField] || jQuery(dropdown).val());

        this.container.on('change', '.repeater-field select', function (event) {
            let currentDropdown = jQuery(event.target),
                row = currentDropdown.closest('.repeater-row'),
                rowIndex = row.data('row'),
                currentSettings = control.getValue();

            currentSettings[rowIndex][currentDropdown.data('field')] = jQuery(this).val();
            control.setValue(currentSettings);
        });
    },
});
wp.customize.controlConstructor['kirki-slider'] = wp.customize.kirkiDynamicControl.extend({

    initKirkiControl() {
        let control = this,
            changeAction = (control.setting.transport === 'postMessage') ? 'mousemove change' : 'change',
            rangeInput = control.container.find('input[type="range"]'),
            textInput = control.container.find('input[type="text"]'),
            value = control.setting._value;

        // Set the initial value in the text input.
        textInput.attr('value', value);

        // If the range input value changes copy the value to the text input.
        rangeInput.on('mousemove change', () => {
            textInput.attr('value', rangeInput.val());
        });

        // Save the value when the range input value changes.
        // This is separate from the above because of the postMessage differences.
        // If the control refreshes the preview pane,
        // we don't want a refresh for every change
        // but 1 final refresh when the value is changed.
        rangeInput.on(changeAction, () => {
            control.setting.set(rangeInput.val());
        });

        // If the text input value changes,
        // copy the value to the range input
        // and then save.
        textInput.on('input paste change', () => {
            rangeInput.attr('value', textInput.val());
            control.setting.set(textInput.val());
        });

        // If the reset button is clicked,
        // set slider and text input values to default
        // and hen save.
        control.container.find('.slider-reset').on('click', () => {
            textInput.attr('value', control.params.default);
            rangeInput.attr('value', control.params.default);
            control.setting.set(textInput.val());
        });
    },
});
wp.customize.controlConstructor['kirki-switch'] = wp.customize.kirkiDynamicControl.extend({

    initKirkiControl() {
        let control = this,
            checkboxValue = control.setting._value;

        // Save the value
        this.container.on('change', 'input', function () {
            checkboxValue = !!(jQuery(this).is(':checked'));
            control.setting.set(checkboxValue);
        });
    },
});
wp.customize.controlConstructor['kirki-toggle'] = wp.customize.kirkiDynamicControl.extend({

    initKirkiControl() {
        let control = this,
            checkboxValue = control.setting._value;

        // Save the value
        this.container.on('change', 'input', function () {
            checkboxValue = !!(jQuery(this).is(':checked'));
            control.setting.set(checkboxValue);
        });
    },
});
/* global kirkiL10n, kirki */
/* eslint-enable */

window.kirki = kirki;

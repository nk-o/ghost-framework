const kirkiDependencies = {

    listenTo: {},

    init() {
        const self = this;
        wp.customize.control.each((control) => {
            self.showKirkiControl(control);
        });
        _.each(self.listenTo, (slaves, master) => {
            _.each(slaves, (slave) => {
                wp.customize(master, (setting) => {
                    const setupControl = function (control) {
                        let setActiveState,
                            isDisplayed;

                        isDisplayed = function () {
                            return self.showKirkiControl(wp.customize.control(slave));
                        };
                        setActiveState = function () {
                            control.active.set(isDisplayed());
                        };

                        setActiveState();
                        setting.bind(setActiveState);
                        control.active.validate = isDisplayed;
                    };
                    wp.customize.control(slave, setupControl);
                });
            });
        });
    },

    /**
	 * Should we show the control?
	 *
	 * @since 3.0.17
	 * @param {string|object} control - The control-id or the control object.
	 * @returns {bool}
	 */
    showKirkiControl(control) {
        let self = this,
            show = true,
            isOption = (
                control.params && // Check if control.params exists.
				control.params.kirkiOptionType && // Check if option_type exists.
				control.params.kirkiOptionType === 'option' && // We're using options.
				control.params.kirkiOptionName && // Check if option_name exists.
				!_.isEmpty(control.params.kirkiOptionName) // Check if option_name is not empty.
            ),
            i;

        if (_.isString(control)) {
            control = wp.customize.control(control);
        }

        // Exit early if control not found or if "required" argument is not defined.
        if (typeof control === 'undefined' || (control.params && _.isEmpty(control.params.required))) {
            return true;
        }

        // Loop control requirements.
        for (i = 0; i < control.params.required.length; i++) {
            if (!self.checkCondition(control.params.required[i], control, isOption, 'AND')) {
                show = false;
            }
        }
        return show;
    },

    /**
	 * Check a condition.
	 *
	 * @param {Object} requirement - The requirement, inherited from showKirkiControl.
	 * @param {Object} control - The control object.
	 * @param {bool}   isOption - Whether it's an option or not.
	 * @param {string} relation - Can be one of 'AND' or 'OR'.
	 */
    checkCondition(requirement, control, isOption, relation) {
        let self = this,
            childRelation = (relation === 'AND') ? 'OR' : 'AND',
            nestedItems,
            i;

        // Tweak for using active callbacks with serialized options instead of theme_mods.
        if (isOption && requirement.setting) {
            // Make sure we don't already have the option_name in there.
            if (requirement.setting.indexOf(`${control.params.kirkiOptionName}[`) === -1) {
                requirement.setting = `${control.params.kirkiOptionName}[${requirement.setting}]`;
            }
        }

        // If an array of other requirements nested, we need to process them separately.
        if (typeof requirement[0] !== 'undefined' && typeof requirement.setting === 'undefined') {
            nestedItems = [];

            // Loop sub-requirements.
            for (i = 0; i < requirement.length; i++) {
                nestedItems.push(self.checkCondition(requirement[i], control, isOption, childRelation));
            }

            // OR relation. Check that true is part of the array.
            if (childRelation === 'OR') {
                return (nestedItems.indexOf(true) !== -1);
            }

            // AND relation. Check that false is not part of the array.
            return (nestedItems.indexOf(false) === -1);
        }

        // Early exit if setting is not defined.
        if (typeof wp.customize.control(requirement.setting) === 'undefined') {
            return true;
        }

        self.listenTo[requirement.setting] = self.listenTo[requirement.setting] || [];
        if (self.listenTo[requirement.setting].indexOf(control.id) === -1) {
            self.listenTo[requirement.setting].push(control.id);
        }

        return self.evaluate(
            requirement.value,
            wp.customize.control(requirement.setting).setting._value,
            requirement.operator,
        );
    },

    /**
	 * Figure out if the 2 values have the relation we want.
	 *
	 * @since 3.0.17
	 * @param {mixed} value1 - The 1st value.
	 * @param {mixed} value2 - The 2nd value.
	 * @param {string} operator - The comparison to use.
	 * @returns {bool}
	 */
    evaluate(value1, value2, operator) {
        let found = false;

        if (operator === '===') {
            return value1 === value2;
        }
        if (operator === '==' || operator === '=' || operator === 'equals' || operator === 'equal') {
            return value1 == value2;
        }
        if (operator === '!==') {
            return value1 !== value2;
        }
        if (operator === '!=' || operator === 'not equal') {
            return value1 != value2;
        }
        if (operator === '>=' || operator === 'greater or equal' || operator === 'equal or greater') {
            return value2 >= value1;
        }
        if (operator === '<=' || operator === 'smaller or equal' || operator === 'equal or smaller') {
            return value2 <= value1;
        }
        if (operator === '>' || operator === 'greater') {
            return value2 > value1;
        }
        if (operator === '<' || operator === 'smaller') {
            return value2 < value1;
        }
        if (operator === 'contains' || operator === 'in') {
            if (_.isArray(value1) && _.isArray(value2)) {
                _.each(value2, (value) => {
                    if (value1.includes(value)) {
                        found = true;
                        return false;
                    }
                });
                return found;
            }
            if (_.isArray(value2)) {
                _.each(value2, (value) => {
                    if (value == value1) { // jshint ignore:line
                        found = true;
                    }
                });
                return found;
            }
            if (_.isObject(value2)) {
                if (!_.isUndefined(value2[value1])) {
                    found = true;
                }
                _.each(value2, (subValue) => {
                    if (value1 === subValue) {
                        found = true;
                    }
                });
                return found;
            }
            if (_.isString(value2)) {
                if (_.isString(value1)) {
                    return (value1.indexOf(value2) > -1 && value2.indexOf(value1) > -1);
                }
                return value1.indexOf(value2) > -1;
            }
        }
        return value1 == value2;
    },
};

jQuery(() => {
    kirkiDependencies.init();
});

window.kirkiDependencies = kirkiDependencies;

const {
    kirkiSetSettingValue,
} = window;

jQuery(() => {
    // Loop Controls.
    wp.customize.control.each((control) => {
        // Check if we have a preset defined.
        if (control.params && control.params.preset && !_.isEmpty(control.params.preset)) {
            wp.customize(control.id, (value) => {
                // Listen to value changes.
                value.bind((to) => {
                    // Loop preset definitions.
                    _.each(control.params.preset, (preset, valueToListen) => {
                        // Check if the value set want is the same as the one we're looking for.
                        if (valueToListen === to) {
                            // Loop settings defined inside the preset.
                            _.each(preset.settings, (controlValue, controlID) => {
                                // Set the value.
                                kirkiSetSettingValue.set(controlID, controlValue);
                            });
                        }
                    });
                });
            });
        }
    });
});

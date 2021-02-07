jQuery(() => {
    wp.customize.section.each((section) => {
        // Get the pane element.
        let pane = jQuery(`#sub-accordion-section-${section.id}`),
            sectionLi = jQuery(`#accordion-section-${section.id}`);

        // Check if the section is expanded.
        if (sectionLi.hasClass('control-section-kirki-expanded')) {
            // Move element.
            pane.appendTo(sectionLi);
        }
    });
});

/**
 * See https://github.com/justintadlock/trt-customizer-pro
 */
(function () {
    wp.customize.sectionConstructor['kirki-link'] = wp.customize.Section.extend({
        attachEvents() {},
        isContextuallyActive() {
            return true;
        },
    });
}());

/**
 * @see https://wordpress.stackexchange.com/a/256103/17078
 */
(function () {
    let _panelEmbed,
        _panelIsContextuallyActive,
        _panelAttachEvents,
        _sectionEmbed,
        _sectionIsContextuallyActive,
        _sectionAttachEvents;

    wp.customize.bind('pane-contents-reflowed', () => {
        let panels = [],
            sections = [];

        // Reflow Sections.
        wp.customize.section.each((section) => {
            if (section.params.type !== 'kirki-nested' || _.isUndefined(section.params.section)) {
                return;
            }
            sections.push(section);
        });

        sections.sort(wp.customize.utils.prioritySort).reverse();

        jQuery.each(sections, (i, section) => {
            const parentContainer = jQuery(`#sub-accordion-section-${section.params.section}`);

            parentContainer.children('.section-meta').after(section.headContainer);
        });

        // Reflow Panels.
        wp.customize.panel.each((panel) => {
            if (panel.params.type !== 'kirki-nested' || _.isUndefined(panel.params.panel)) {
                return;
            }
            panels.push(panel);
        });

        panels.sort(wp.customize.utils.prioritySort).reverse();

        jQuery.each(panels, (i, panel) => {
            const parentContainer = jQuery(`#sub-accordion-panel-${panel.params.panel}`);

            parentContainer.children('.panel-meta').after(panel.headContainer);
        });
    });

    // Extend Panel.
    _panelEmbed = wp.customize.Panel.prototype.embed;
    _panelIsContextuallyActive = wp.customize.Panel.prototype.isContextuallyActive;
    _panelAttachEvents = wp.customize.Panel.prototype.attachEvents;

    wp.customize.Panel = wp.customize.Panel.extend({
        attachEvents() {
            let panel;

            if (this.params.type !== 'kirki-nested' || _.isUndefined(this.params.panel)) {
                _panelAttachEvents.call(this);
                return;
            }

            _panelAttachEvents.call(this);

            panel = this;

            panel.expanded.bind((expanded) => {
                const parent = wp.customize.panel(panel.params.panel);

                if (expanded) {
                    parent.contentContainer.addClass('current-panel-parent');
                } else {
                    parent.contentContainer.removeClass('current-panel-parent');
                }
            });

            panel.container.find('.customize-panel-back').off('click keydown').on('click keydown', (event) => {
                if (wp.customize.utils.isKeydownButNotEnterEvent(event)) {
                    return;
                }
                event.preventDefault(); // Keep this AFTER the key filter above

                if (panel.expanded()) {
                    wp.customize.panel(panel.params.panel).expand();
                }
            });
        },

        embed() {
            let panel = this,
                parentContainer;
            if (this.params.type !== 'kirki-nested' || _.isUndefined(this.params.panel)) {
                _panelEmbed.call(this);
                return;
            }

            _panelEmbed.call(this);

            parentContainer = jQuery(`#sub-accordion-panel-${this.params.panel}`);

            parentContainer.append(panel.headContainer);
        },

        isContextuallyActive() {
            let panel = this,
                children,
                activeCount = 0;

            if (this.params.type !== 'kirki-nested') {
                return _panelIsContextuallyActive.call(this);
            }

            children = this._children('panel', 'section');

            wp.customize.panel.each((child) => {
                if (!child.params.panel) {
                    return;
                }

                if (child.params.panel !== panel.id) {
                    return;
                }

                children.push(child);
            });

            children.sort(wp.customize.utils.prioritySort);

            _(children).each((child) => {
                if (child.active() && child.isContextuallyActive()) {
                    activeCount += 1;
                }
            });
            return (activeCount !== 0);
        },
    });

    // Extend Section.
    _sectionEmbed = wp.customize.Section.prototype.embed;
    _sectionIsContextuallyActive = wp.customize.Section.prototype.isContextuallyActive;
    _sectionAttachEvents = wp.customize.Section.prototype.attachEvents;

    wp.customize.Section = wp.customize.Section.extend({
        attachEvents() {
            const section = this;

            if (this.params.type !== 'kirki-nested' || _.isUndefined(this.params.section)) {
                _sectionAttachEvents.call(section);
                return;
            }

            _sectionAttachEvents.call(section);

            section.expanded.bind((expanded) => {
                const parent = wp.customize.section(section.params.section);

                if (expanded) {
                    parent.contentContainer.addClass('current-section-parent');
                } else {
                    parent.contentContainer.removeClass('current-section-parent');
                }
            });

            section.container.find('.customize-section-back').off('click keydown').on('click keydown', (event) => {
                if (wp.customize.utils.isKeydownButNotEnterEvent(event)) {
                    return;
                }
                event.preventDefault(); // Keep this AFTER the key filter above
                if (section.expanded()) {
                    wp.customize.section(section.params.section).expand();
                }
            });
        },

        embed() {
            let section = this,
                parentContainer;

            if (this.params.type !== 'kirki-nested' || _.isUndefined(this.params.section)) {
                _sectionEmbed.call(section);
                return;
            }

            _sectionEmbed.call(section);

            parentContainer = jQuery(`#sub-accordion-section-${this.params.section}`);

            parentContainer.append(section.headContainer);
        },

        isContextuallyActive() {
            let section = this,
                children,
                activeCount = 0;
            if (this.params.type !== 'kirki-nested') {
                return _sectionIsContextuallyActive.call(this);
            }

            children = this._children('section', 'control');

            wp.customize.section.each((child) => {
                if (!child.params.section) {
                    return;
                }

                if (child.params.section !== section.id) {
                    return;
                }
                children.push(child);
            });

            children.sort(wp.customize.utils.prioritySort);

            _(children).each((child) => {
                if (typeof child.isContextuallyActive !== 'undefined') {
                    if (child.active() && child.isContextuallyActive()) {
                        activeCount += 1;
                    }
                } else if (child.active()) {
                    activeCount += 1;
                }
            });

            return (activeCount !== 0);
        },
    });
}(jQuery));

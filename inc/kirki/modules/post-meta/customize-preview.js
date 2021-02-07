jQuery(() => {
    const {
        wp,
        _customizePostPreviewedQueriedObject,
    } = window;

    const self = {
        queriedPost: (!_.isUndefined(_customizePostPreviewedQueriedObject)) ? _customizePostPreviewedQueriedObject : null,
    };

    // Send the queried post object to the Customizer pane when ready.
    wp.customize.bind('preview-ready', () => {
        wp.customize.preview.bind('active', () => {
            wp.customize.preview.send('queried-post', self.queriedPost);
        });
    });
});

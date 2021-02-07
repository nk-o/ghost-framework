jQuery(() => {
    let self;

    self = {
        queriedPost: new wp.customize.Value(),
    };

    // Listen for queried-post messages from the preview.
    wp.customize.bind('ready', () => {
        wp.customize.previewer.bind('queried-post', (queriedPost) => {
            self.queriedPost.set(queriedPost || false);
        });
    });

    // Listen for post
    self.queriedPost.bind((newPost, oldPost) => {
        window.kirkiPost = false;
        if (newPost || oldPost) {
            window.kirkiPost = (newPost) || oldPost;
        }
    });
});

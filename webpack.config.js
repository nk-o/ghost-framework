const path = require( 'path' );
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

const assets = [
    // JS
    './assets/js/night-mode.js',
    './assets/js/customizer-aspect-ratio-control.js',
    './assets/js/customizer-collapsible-control.js',
    './inc/kirki/controls/js/script.js',
    './inc/kirki/modules/css-vars/script.js',
    './inc/kirki/modules/custom-sections/sections.js',
    './inc/kirki/modules/field-dependencies/field-dependencies.js',
    './inc/kirki/modules/icons/icons.js',
    './inc/kirki/modules/post-meta/customize-controls.js',
    './inc/kirki/modules/post-meta/customize-preview.js',
    './inc/kirki/modules/postmessage/postmessage.js',
    './inc/kirki/modules/preset/preset.js',
    './inc/kirki/modules/tooltips/tooltip.js',

    // SCSS
    './assets/css/admin.scss',
    './assets/css/customizer-aspect-ratio-control.scss',
    './assets/css/customizer-collapsible-control.scss',
    './inc/kirki/assets/css/customizer.scss',
    './inc/kirki/controls/css/styles.scss',
    './inc/kirki/modules/custom-sections/sections.scss',
    './inc/kirki/modules/icons/icons.scss',
    './inc/kirki/modules/tooltips/tooltip.scss',
];

const assetsEntry = {};

assets.forEach( ( path ) => {
    assetsEntry[ path ] = path;
} );

module.exports = {
    mode: 'production',
    stats: 'minimal',
    entry: assetsEntry,
    module: {
        rules: [
            {
                test: /\.(jsx|js)$/i,
                loader: 'babel-loader',
                options: {
                    presets: [ '@babel/env' ],
                },
            }, {
                test: /\.(css|scss)/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                      loader: 'postcss-loader',
                      options: {
                            postcssOptions: {
                                plugins: [ 'autoprefixer' ],
                            },
                      },
                    },
                    'sass-loader',
                ],
            },
        ],
    },
    output: {
        path: path.resolve( __dirname, './' ),
        filename( { chunk } ) {
            return `${ chunk.name.replace( /\.js$/i, '' ) }.min.js`;
        },
    },
    plugins: [
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin( {
            filename( { chunk } ) {
                return `${ chunk.name.replace( /\.scss$/i, '' ) }.min.css`;
            },
        } ),
    ]
};

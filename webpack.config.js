const path = require( 'path' );
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

const assets = [
    // JS
    './assets/js/night-mode.js',
    './assets/js/customizer-collapsible-control.js',
    './inc/kirki/controls/js/script.js',

    // CSS
    './assets/css/admin.scss',
    './assets/css/customizer-collapsible-control.scss',
    './inc/kirki/assets/css/customizer.scss',
];

const assetsEntry = {};

assets.forEach( ( path ) => {
    assetsEntry[ path.replace( /\.(js|scss)$/i, '' ) ] = path;
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
        filename: '[name].min.js',
    },
    plugins: [
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin( {
            filename: '[name].min.css',
        } ),
    ]
};

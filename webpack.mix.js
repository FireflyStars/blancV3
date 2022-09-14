const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
const webpack = require('webpack')
mix.webpackConfig ({
    plugins: [
        new webpack.DefinePlugin({
            // __VUE_OPTIONS_API__: false,
            // __VUE_PROD_DEVTOOLS__: true,
        }),
    ],
})
mix.js('resources/js/app.js', 'public/js')
    // .sass('resources/css/app.scss', 'public/css').vue();
    .sass('resources/css/app.scss', 'public/css').vue().browserSync({
        proxy:'http://blanc2.local',
        files: [
            'resources/views/**/*.php',
            'public/js/**/*.js',
            'public/css/**/*.css'
        ]
    }).version();;

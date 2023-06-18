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

 mix.webpackConfig({
    stats: {
        hash: true,
        version: true,
        timings: true,
        children: true,
        errors: true,
        errorDetails: true,
        warnings: true,
        chunks: true,
        modules: false,
        reasons: true,
        source: true,
        publicPath: true,
    }
});

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/admin.js', 'public/js')
    .js('resources/js/datatables.js', 'public/js')
    .js('resources/js/datatime.js', 'public/js')
    .js('resources/js/posts.js', 'public/js')
    .js('resources/js/laravel-echo-setup.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/datatime.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/bootstrap.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/afont.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/adstyle.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/adcustom.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/post.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/home.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/style.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/slider.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/single.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ])
    .postCss('resources/css/user.css', 'public/css', [
        require('postcss-import'),
        require('postcss-nested'),
    ]);

if (mix.inProduction()) {
    mix.version();
}

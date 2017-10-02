const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/rv.js', 'public/js')
   .js('resources/assets/js/mr.js', 'public/js')
   .js('resources/assets/js/rr.js', 'public/js')
   .js('resources/assets/js/canvass.js', 'public/js')
   .js('resources/assets/js/AccountManagement.js', 'public/js')
   .js('resources/assets/js/mirs.js', 'public/js')
   .js('resources/assets/js/mct.js', 'public/js')
   .js('resources/assets/js/item.js', 'public/js')
   .js('resources/assets/js/mrt.js', 'public/js')
   .js('resources/assets/js/po.js', 'public/js')
   .js('resources/assets/js/master.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');

let mix = require('laravel-mix');

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

mix.styles([
    'public/assets/admin/css/login/login.css',
    'public/assets/admin/css/user/admin.css',
    'public/assets/admin/css/chatroom/chatroom.css',
], 'public/bundle/css/all.css').version();

mix.styles([
'public/assets/web/css/style.css',
'public/assets/web/css/custom.css',
], 'public/assets/web/css/web.css').version();

mix.styles('public/assets/web/css/ge_1.css', 'public/assets/web/css/pages/ge_1.css').version();

mix.js("resources/assets/js/app.js", "public/js").version();
mix.js("resources/assets/js/web.js", "public/js").version();



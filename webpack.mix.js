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

mix.js("resources/assets/js/app.js", "public/js");
mix.js("resources/assets/js/web.js", "public/js");


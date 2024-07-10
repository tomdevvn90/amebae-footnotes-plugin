const mix = require('laravel-mix');
mix.setPublicPath('dist');
mix.setResourceRoot('../');
mix.autoload({
    jquery: ['$', 'jQuery', 'window.jQuery']
});
mix.copyDirectory('assets/images', 'dist/images');
mix.js('assets/js/app.js', 'dist/js')
    .sass('assets/scss/styles.scss', 'dist/css')
    .sass('assets/scss/block-editor-button.scss', 'dist/css')
    .extract()
    .version();

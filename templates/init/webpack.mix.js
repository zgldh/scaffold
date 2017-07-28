const {mix} = require('laravel-mix');
var path = require('path');

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

var webpackConfig = {
  resolve: {
    alias: {
      'resources': path.resolve(__dirname, 'resources'),
      'bower_components': path.resolve(__dirname, 'bower_components'),
      '$NAME$': path.resolve(__dirname, '$NAME$'),
    }
  },
  devServer: {
    contentBase: path.join(__dirname, "public"),
    noInfo: false,
    quiet: false
  }
};

mix.webpackConfig(webpackConfig);

mix.sass('resources/assets/sass/app.scss', 'public/css')
  .sass('resources/assets/sass/admin.scss', 'public/css')
  .js('resources/assets/js/entries/app.js', 'public/js')
  .js('resources/assets/js/entries/admin.js', 'public/js');

mix.extract(['vue', 'vuex', 'vue-router', 'element-ui', 'jquery', 'lodash', 'axios', 'nprogress', 'materialize-css', 'babel-polyfill']);
mix.sourceMaps();
mix.browserSync('$HOST$');
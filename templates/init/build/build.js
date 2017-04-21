var webpack = require("webpack");
var webpackConfig = require('../webpack.config.js');
require('shelljs/global');

const notifier = require('node-notifier');

webpackConfig.plugins = [
  new webpack.LoaderOptionsPlugin({
    minimize: true
  }),
  new webpack.optimize.CommonsChunkPlugin({
    names: ['vendor', 'static']
  })
];

rm('-rf', webpackConfig.output.path);
mkdir('-p', webpackConfig.output.path);

var compiler = webpack(webpackConfig, function (err, stats) {
  console.log('[webpack:build]', stats.toString({
    chunks: false, // Makes the build much quieter
    colors: true
  }));
  var duration = (stats.endTime - stats.startTime).toString() + ' ms';
  notifier.notify('Build complete in ' + duration);
});

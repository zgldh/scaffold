var webpack = require("webpack");
var webpackConfig = require('../webpack.config.js');
require('shelljs/global');

const notifier = require('node-notifier');

webpackConfig.devtool = 'source-map';
webpackConfig.output.pathinfo = true;
webpackConfig.watch = true;
webpackConfig.watchOptions = {
  aggregateTimeout: 300,
  poll: 1000
};
webpackConfig.plugins = [
  new webpack.LoaderOptionsPlugin({
    debug: true
  }),
  new webpack.optimize.CommonsChunkPlugin({
    names: ['vendor','static']
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

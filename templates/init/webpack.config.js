var path = require('path');
var glob = require('glob');
var webpack = require('webpack');

function assetsPath (_path) {
  return 'static/' + _path;
}

function allEntries () {
  var files = glob.sync('./resources/assets/js/entries/*.js');
  var allEntries = {};
  for (var i = 0; i < files.length; i++) {
    var entryName = path.basename(files[i], '.js');
    allEntries[entryName] = files[i];
  }
  console.log(allEntries);
  return allEntries;
}

module.exports = {
  entry: allEntries(),
  output: {
    path: path.resolve(__dirname, 'public/dist'),
    filename: '[name].js',
    publicPath: "/dist/",
    chunkFilename: '[id].[chunkhash].js',
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        exclude: /node_modules|bower_components/,
        options: {
          // vue-loader options go here
          js: 'babel-loader'
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: '/node_modules/',
        query: {
          presets: ['es2015']
        }
      },
      {
        test: /\.(sass|scss|css)/,
        loader: "style-loader!css-loader!sass-loader"
      },
      {
        test: /\.json$/,
        loader: 'json-loader'
      },
      {
        test: /\.html$/,
        loader: 'html-loader'
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'file-loader',
        options: {
          name: assetsPath('images/[name].[ext]?[hash]')
        }
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        query: {
          limit: 10000,
          name: assetsPath('fonts/[name].[hash:7].[ext]')
        }
      }
      // ,{
      //   test: require.resolve('./bower_components/tinymce-dist/tinymce'),
      //   loaders: [
      //     'imports?this=>window',
      //     'exports?window.tinymce'
      //   ]
      // },
      // {
      //   test: /bower_components[\\/]tinymce-dist[\\/](themes|plugins)[\\/]/,
      //   loader: 'imports?this=>window'
      // }
    ]
  },
  resolve: {
    extensions: ['.js', '.vue', '.css', '.sass', '.scss'],
    alias: {
      '/': path.resolve(__dirname),
      'resources': path.resolve(__dirname, 'resources'),
      'bower_components': path.resolve(__dirname, 'bower_components'),
      'vue$': 'vue/dist/vue.common.js'
    }
  },
  resolveLoader: {
    alias: {
      'scss-loader': 'sass-loader'
    }
  }
};
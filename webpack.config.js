const path = require('path');
const CleanObsoleteChunks = require('webpack-clean-obsolete-chunks');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCssAssetsWebpackPlugin = require('optimize-css-assets-webpack-plugin');
const TerserWebpackPlugin = require('terser-webpack-plugin');
const SpriteLoaderPlugin = require('svg-sprite-loader/plugin');
const AssetsManifest = require('webpack-assets-manifest');

const isDev = process.env.NODE_ENV === 'development';
const isProd = !isDev;

const filename = (ext) => (isDev ? `[hash].[name].${ext}` : `[name].[hash].${ext}`);

const optimization = () => {
  const config = {};

  if (isProd) {
    config.minimizer = [
      new OptimizeCssAssetsWebpackPlugin(),
      new TerserWebpackPlugin({
        cache: true,
      }),
    ];
  }

  return config;
};

const cssLoaders = (extra) => {
  const loaders = [
    {
      loader: MiniCssExtractPlugin.loader,
      options: {
        hmr: isDev,
        reloadAll: true,
      },
    },
    {
      loader: 'css-loader',
      options: {
        sourceMap: isDev,
      },
    },
  ];

  if (extra) {
    loaders.push(extra);
  }

  return loaders;
};

const jsLoaders = () => {
  const loaders = [
    {
      loader: 'babel-loader',
      options: {
        presets: [
          '@babel/preset-env',
        ],
      },
    },
  ];

  if (isDev) {
    loaders.push({
      loader: 'eslint-loader',
      options: {
        fix: true,
      },
    });
  }

  return loaders;
};

const plugins = () => {
  const base = [
    new CleanObsoleteChunks(),
    new AssetsManifest({
      output: `${path.resolve(__dirname, 'assets/templates')}/manifest.json`,
      publicPath: true,
    }),
    new CopyWebpackPlugin(),
    new MiniCssExtractPlugin({
      filename: filename('css'),
    }),
    new SpriteLoaderPlugin(),
  ];

  return base;
};

module.exports = {
  context: path.resolve(__dirname, 'assets/dev'),
  mode: 'development',
  entry: {
    main: ['@babel/polyfill', './js/main.js'],
  },
  output: {
    filename: filename('js'),
    path: path.resolve(__dirname, 'assets/templates'),
  },
  resolve: {
    extensions: ['.js', '.json'],
    alias: {
      '@modules': path.resolve(__dirname, 'assets/dev/js/modules'),
      '@dev': path.resolve(__dirname, 'assets/dev'),
    },
  },
  optimization: optimization(),
  devtool: isDev ? 'source-map' : '',
  plugins: plugins(),
  module: {
    rules: [
      {
        test: /\.css$/,
        use: cssLoaders(),
      },
      {
        test: /\.scss$/,
        use: cssLoaders(
          {
            loader: 'sass-loader',
            options: {
              sourceMap: isDev,
            },
          },
        ),
      },
      {
        test: /\.(png|jpg|gif)$/,
        use: ['file-loader'],
      },
      {
        test: /\.(ttf|woff|woff2|eot)$/,
        use: ['file-loader'],
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: jsLoaders(),
      },
      {
        test: /\.svg$/,
        use: [
          {
            loader: 'svg-sprite-loader',
            options: {
              extract: true,
              spriteFilename: 'sprite.svg',
            },
          },
          'svgo-loader',
        ],
      },
    ],
  },
};

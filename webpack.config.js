const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
module.exports = {
  mode: 'development',
  entry: {
    'js/app' : './src/js/app.js',
    'js/inicio' : './src/js/inicio.js',
    'js/tipodotacion/index' : './src/js/tipodotacion/index.js',
    'js/usuario/index' : './src/js/usuario/index.js',
    'js/login/index' : './src/js/login/index.js',
    'js/aplicacion/index' : './src/js/aplicacion/index.js',
    'js/talla/index' : './src/js/talla/index.js',
    'js/permiso/index' : './src/js/permiso/index.js',
    'js/empleado/index' : './src/js/empleado/index.js',
    'js/dotacioninventario/index' : './src/js/dotacioninventario/index.js',
    'js/asignacion_permiso/index' : './src/js/asignacion_permiso/index.js',
    'js/dashboard/index' : './src/js/dashboard/index.js',
    'js/dashboard/index' : './src/js/dashboard/index.js',
    'js/dotacionsolicitud/index' : './src/js/dotacionsolicitud/index.js',
    'js/dotacionentrega/index' : './src/js/dotacionentrega/index.js',
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'public/build')
  },
  plugins: [
    new MiniCssExtractPlugin({
        filename: 'styles.css'
    })
  ],
  module: {
    rules: [
      {
        test: /\.(c|sc|sa)ss$/,
        use: [
            {
                loader: MiniCssExtractPlugin.loader
            },
            'css-loader',
            'sass-loader'
        ]
      },
      {
        test: /\.(png|svg|jpe?g|gif)$/,
        type: 'asset/resource',
      },
    ]
  }
};
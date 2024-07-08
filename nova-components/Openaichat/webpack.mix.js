let mix = require('laravel-mix')
let path = require('path');

require('./nova.mix')

mix
  .setPublicPath('dist')
  .js('resources/js/tool.js', 'js')
  .vue({ version: 3 })
  .nova('acme/openaichat')
  .webpackConfig({
    resolve: {
        alias: {
            '@': path.join(__dirname, 'resources/js'),
        },
        fallback: {
            "buffer": require.resolve("buffer/")
        }
    }
});
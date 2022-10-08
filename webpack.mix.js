const mix = require('laravel-mix');

const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

mix.webpackConfig({
    plugins: [ new DependencyExtractionWebpackPlugin() ]
});

mix.options({
	terser: {
		extractComments: false,
	}
});

mix
	.js('src/index.js', 'dist')
	.react()
	.setPublicPath('dist');
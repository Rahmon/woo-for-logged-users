let mix = require('laravel-mix');

mix.options({
	terser: {
		extractComments: false,
	}
});

mix
	.js('src/index.js', 'dist')
	.react()
	.setPublicPath('dist');
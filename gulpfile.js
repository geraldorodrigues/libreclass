process.env.DISABLE_NOTIFIER = true;


var elixir       = require('laravel-elixir'),
		flex         = require('postcss-flexibility'),
		autoprefixer = require('autoprefixer'),
		postStylus   = require('poststylus');
		mqpacker     = require('css-mqpacker');

		require('laravel-elixir-stylus');
elixir(function(mix) {

	var configStylus = [
		// mqpacker(),
		// autoprefixer(),
		// flex(),
		// bootstrap(),
	];

	// Tarefas para o framework bootstrap
	mix.stylus('configBootstrap.styl', 'public/assets/css/bootstrap.min.css');
	mix.scripts('./resources/assets/lib/bootstrap/js/*.js', 'public/assets/js/bootstrap.min.js');

	//Bibliotecas
	mix.copy('node_modules/lealjs/dist', 'public/assets/lealjs');

	// mix.stylus('bootstrap.styl', 'public/css/bootstrap.min.css');

	// //Libs
	// mix.copy('bower_components/jquery/dist/jquery.min.js', 'public/lib/js');
	//
	// //CSS
	// mix.copy('resources/assets/bootstrap/bootstrap.min.css', 'public/css/bootstrap.min.css');
	//
	// mix.version(['lib/js/jquery.min.js', 'lib/js/typed.min.js', 'js/script.min.js', 'css/bootstrap.min.css', 'css/master.min.css']);
	//
	// mix.browserSync({
	// 	proxy: 'localhost:8000'
	// });

});

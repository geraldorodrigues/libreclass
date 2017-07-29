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
	mix.scripts(
		[
			'./resources/assets/lib/bootstrap/js/modal.js',
			'./resources/assets/lib/bootstrap/js/tooltip.js',
			'./resources/assets/lib/bootstrap/js/popover.js',
			'./resources/assets/lib/bootstrap/js/dropdown.js'
		], 'public/assets/js/bootstrap.min.js');

	//Bibliotecas
	mix.copy('node_modules/lealjs/dist', 'public/assets/lealjs');
	mix.copy('node_modules/jquery/dist/jquery.min.js', 'public/assets/js/jquery.min.js');

	//summernote
	mix.scripts([
		'./node_modules/summernote/dist/summernote.js',
		'./node_modules/summernote/dist/lang/summernote-pt-BR.js'
	], 'public/assets/js/summernote.min.js');

	mix.copy('node_modules/summernote/dist/summernote.css', 'public/assets/css/summernote/summernote.min.css');
	mix.copy('node_modules/summernote/dist/font', 'public/assets/css/summernote/font');

	//Controllers
	//Instituição
	mix.scripts('controllers/institution/*.js', 'public/assets/js/institutionControllers.min.js');


	//Tarefas para compilação dos arquivos de layout
	mix.stylus('layout/index.styl', 'public/assets/css/master.min.css');

	//Scripts
	mix.scripts('*.js', 'public/assets/js/scripts.min.js');

	// //Libs
	// mix.copy('bower_components/jquery/dist/jquery.min.js', 'public/lib/js');
	//
	// //CSS
	// mix.copy('resources/assets/bootstrap/bootstrap.min.css', 'public/css/bootstrap.min.css');
	//
	// mix.version(['lib/js/jquery.min.js', 'lib/js/typed.min.js', 'js/script.min.js', 'css/bootstrap.min.css', 'css/master.min.css']);
	//
	mix.browserSync({
		proxy: 'localhost:8001'
	});

});

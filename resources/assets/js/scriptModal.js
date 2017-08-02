$('.modal').on('hidden.bs.modal', function (e) {

		if($('.modal:visible').length) {
			var visible = $('.modal:visible');
			visible.css('z-index', '1050');
		}
		$(e.currentTarget).css('z-index', '1050');
		triggerResetForm($(e.currentTarget).find('form'));
	});

	$('.modal').on('show.bs.modal', function (e) {
		var visible = $('.modal:visible');
		visible.css('z-index', '1040');
	});

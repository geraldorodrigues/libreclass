controller('periods', function() {
	var view = "#view-periods";

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddPeriod', this.openModalAddPeriod);
		view.on('click', '.ev-redirectToDiscipline', this.redirectToDiscipline);
	};

	this.show = function() {
		view.show();
		$("#page-title").text('Nome do curso');
	};

	this.openModalAddPeriod = function(e) {
		var modal = $('#modalAddPeriod');

		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			var id = $(e.currentTarget).closest('.item-period').attr('data-id');
			this.getPeriod(id);
			modal.find('.modal-title').text('Editar período');
		}
		else {
			modal.find('.modal-title').text('Cadastrar período');
		}

		modal.modal();
	};

	this.getPeriod = function(id) {
		console.log(id);
	};

	this.redirectToDiscipline = function(e) {
		redirect('disciplines/'+$(e.currentTarget).attr('data-id'));
	};
});

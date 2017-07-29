controller('disciplines', function() {
	var view = "#view-disciplines";

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddDiscipline', this.openModalAddDiscipline);
	};

	this.show = function() {
		view.show();
		$("#page-title").text('Nome do período');
	};

	this.openModalAddDiscipline = function(e) {
		var modal = $('#modalAddDiscipline');

		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			var id = $(e.currentTarget).closest('.item-discipline').attr('data-id');
			this.getDiscipline(id);
			modal.find('.modal-title').text('Editar disciplina');
		}
		else {
			modal.find('.modal-title').text('Cadastrar disciplina');
		}

		modal.modal();
	};

	this.getDiscipline = function(id) {
		console.log(id);
	};
});

controller('classes', function() {
	var view = "#view-classes";

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddClasse', this.openModalAddClasse);
		view.on('click', '.ev-redirectToDiscipline', this.redirectToDiscipline);
	};

	this.show = function() {
		view.show();
		$("#page-title").text('Turmas');
	};

	this.openModalAddClasse = function(e) {
		e.stopPropagation();
		var modal = $('#modalAddClasse');

		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			var id = $(e.currentTarget).closest('.item-classe').attr('data-id');
			this.getClasse(id);
			modal.find('.modal-title').text('Editar período');
		}
		else {
			modal.find('.modal-title').text('Cadastrar período');
		}

		modal.modal();
	};

	this.getClasse = function(id) {
		console.log(id);
	};

	this.redirectToDiscipline = function(e) {
		redirect('disciplines/'+$(e.currentTarget).attr('data-id'));
	};
});

controller('courses', function() {
	var view = "#view-courses";

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddCourse', this.openModalAddCourse);
		view.on('click', '.ev-redirectToPeriod', this.redirectToPeriod);
	};

	this.show = function() {
		view.show();
		$("#page-title").text('Cursos');
	};

	this.openModalAddCourse = function(e) {
		e.stopPropagation();
		var modal = $('#modalAddCourse');

		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			var id = $(e.currentTarget).closest('.item-course').attr('data-id');
			this.getCourse(id);
			modal.find('.modal-title').text('Editar curso');
		}
		else {
			modal.find('.modal-title').text('Cadastrar curso');
		}

		modal.modal();
	};

	this.redirectToPeriod = function(e) {
		redirect('periods/'+$(e.currentTarget).attr('data-id'));
	};

	this.getCourse = function(id) {
		console.log(id);
	};
});

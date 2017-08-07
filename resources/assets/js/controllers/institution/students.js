controller('students', function() {
	var view = "#view-students";

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddStudent', this.modalAddStudents);

	};

	this.show = function() {
		view.show();
		$("#page-title").text('Estudantes');

	};

	this.openModalAddStudents = function(e) {
		e.stopPropagation();
		var modal = $('#modalAddStudents');
		modal.find('form')[0].reset();

		var form = $('#form-student')

		//Verifica se o elemento do evento tem o atributo edit
		/*if($(e.currentTarget).is('[edit]')) {
			modal.find('.modal-title').text('Editar estudante');
			var id = $(e.currentTarget).closest('.item-student').attr('data-id');
			$.post('student/read', { 'discipline_id': id}, function(data){
				if(data.status){
					console.log(data);
					$('input[name="discipline_id"]',form).val(id);
					$('input[name="name"]',form).val(data.discipline.name);
					$('input[name="timetable"]',form).val(data.discipline.timetable);
					$('textarea[name="syllabus"]',form).summernote('code', data.discipline.syllabus);

				}
			})

			modal.find('.modal-title').text('Editar disciplina');
			modal.modal();
		}
		else {
			modal.find('.modal-title').text('Cadastrar disciplina');
			modal.modal();
		}
*/
	};
});

controller('courses', function() {
	var view = "#view-courses";

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddCourse', this.openModalAddCourse);
		view.on('click', '.ev-redirectToPeriod', this.redirectToPeriod);
		view.on('submit', '.ev-saveCourse', this.saveCourse);
	};

	this.show = function() {
		view.show();
		$("#page-title").text('Cursos');
		this.getListCourses();
	};

	this.getListCourses = function() {
		$.post('/course/list', {}, function(data) {

			if(!data.status) {
				$.dialog.info(data.message);
				return;
			}

			var coursesList = view.find('.courses-list').empty();
			data.courses.forEach(function(item) {
				coursesList.prepend(this.templateItemCourse(item));
			}.bind(this));

		}.bind(this));
	};

	this.openModalAddCourse = function(e) {
		e.stopPropagation();
		var modal = $('#modalAddCourse');
		var form = $('#form-course');

		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			var id = $(e.currentTarget).closest('.item-course').attr('data-id');
			$.post('/course/read', { 'course_id': id } , function(data){
				if(data.status == 1) {
					$('input[name="course_id"]', form).val(id);
					$('input[name="name"]', form).val(data.course.name);
					$('input[name="type"]', form).val(data.course.type);
					$('input[name="modality"]', form).val(data.course.modality);
					$('input[name="absent_percent"]', form).val(data.course.absent_percent);
					$('input[name="max_value"]', form).val(data.course.max_value);
					$('input[name="average"]', form).val(data.course.average);
					$('input[name="final_average"]', form).val(data.course.final_average);
					$('input[name="curricular_profile"]', form).val(data.course.curricular_profile);

					modal.find('.modal-title').text('Editar curso');
					modal.modal();
				}
				else {
					$.dialog.info('Erro', 'Não foi possível carregar os dados do curso. Se o erro persistir contate o suporte');
				}

			});
		}
		else {
			modal.find('.modal-title').text('Cadastrar curso');
			modal.modal();
		}

	};

	this.redirectToPeriod = function(e) {
		redirect('periods/'+$(e.currentTarget).closest('.item-course').attr('data-id'));
	};

	// Salvar curso
	this.saveCourse = function(e) {
		e.preventDefault();
		var form = $('#form-course');

		if (!form.validation()) {
			return false;
		}
		var _data = form.serializeObject();

		//Necessário usar ajax para enviar o objeto FormData
		$.ajax({
			type: "POST",
			url: '/course/save',
			data: new FormData(document.getElementById('form-course')),
			processData: false,
			contentType: false,
			success: function(data) {
				if(data.status == 1) {
					$('#modalAddCourse').modal('hide');
					var courseList = $('.courses-list', view);
					if(_data.course_id) {
						courseList.find('.item-course[data-id="'+ _data.course_id +'"]').replaceWith(this.templateItemCourse(data.course));
						$.alert('Curso editado com sucesso');
					}
					else {
						courseList.prepend(this.templateItemCourse(data.course));
						$.alert('Novo curso criado com sucesso');
					}
				}
				else {
					$.dialog.info('Erro', data.message);
				}
			}.bind(this),
			error: errorDialog
		});
	};

	this.templateItemCourse = function(item){
		var html =
		'<div class="col-xs-4 mb item-course" data-id="'+ item.id +'">'+
			'<div class="ck card card--shadow ev-redirectToPeriod">'+
				'<div class="card__header">'+
					'<div class="flex">'+
						'<span class="grow text-bold text-md">'+ item.name +'</span>'+
						'<i class="ck material-icons icon ev-openModalAddCourse" edit>&#xE254;</i>'+
					'</div>'+
				'</div>'+
				'<div class="card__body">'+
				'</div>'+
			'</div>'+
		'</div>';
		return html;
	};

});

controller('courses', function() {
	var view = "#view-courses";

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddCourse', this.openModalAddCourse);
		view.on('click', '.ev-redirectToPeriod', this.redirectToPeriod);
		view.on('click', '.ev-addCourse', this.addCourse);
		//view.on('click', '.ev-openModalAddCourse'), this.editCourse);
	};

	this.show = function() {
		view.show();
		$("#page-title").text('Cursos');
		this.getListCourse();
	};

	this.getListCourse = function() {
		$.post('/course/list', {}, function(data) {

			if(!data.status) {
				$.dialog.info(data.message);
				return;
			}

			var coursesList = view.find('.courses-list').empty();
			data.courses.forEach(function(item) {
				console.log(this);
				coursesList.prepend(this.templateItemCourse(item));
			}.bind(this));
		}.bind(this));
	};

	this.openModalAddCourse = function(e) {
		e.stopPropagation();
		var modal = $('#modalAddCourse');
		var form = $('#form-course');
		console.log(form);

		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			var id = $(e.currentTarget).closest('.item-course').attr('data-id');
			$.post('/course/read', { 'course_id': id } , function(data){
				console.log(data.status);
				if(data.status == 1) {
					console.log();
					$('input[name="course_id"]', form).val(id);
					$('input[name="name"]', form).val(data.course.name);
					$('input[name="type"]', form).val(data.course.type);
					$('input[name="modality"]', form).val(data.course.modality);
					$('input[name="absent_percent"]', form).val(data.course.absent_percent);
					$('input[name="max_value"]', form).val(data.course.max_value);
					$('input[name="average"]', form).val(data.course.average);
					$('input[name="final_average"]', form).val(data.course.final_average);
					$('input[name="curricular_profile"]', form).val(data.course.curricular_profile);

					}

			});
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

	// adicionar curso
	this.addCourse = function(e) {
		e.preventDefault();
		var form = $('#form-course');

		if (!form.validation()) {
			return false;
		}
		var data = form.serializeObject();
		$.post('/course/save', data, function(data) {
			if(data.status == 1) {
				$.dialog.info('Curso adicionado com sucesso!');
				$('#modalAddCourse').modal('hide');
			}
			else {
				$.dialog.info(data.message);
			}
		});


	};
	this.templateItemCourse = function(item){
		var html =
		'<div class="col-xs-4 mb">'+
			'<div class="ck card card--shadow item-course ev-redirectToPeriod" data-id="'+item.id+'">'+
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

controller('disciplines', function() {
	var view = "#view-disciplines";

	this.initialize = function() {
		view = $(view);
		view.on('submit', '.ev-saveDiscipline', this.saveDiscipline);
		view.on('click', '.ev-openModalAddDiscipline', this.openModalAddDiscipline);
		view.on('click', '.ev-deleteDiscipline', this.deleteDiscipline);
		//view.functionName: function(arguments) {
			// body...
		//}

	};

	this.show = function() {
		view.show();
		$("#page-title").text('Disciplinas');
		this.getListDisciplines();
	};
	this.getListDisciplines = function() {
		$.post('discipline/list', { period_id: argument(1) }, function(data) {

			if(!data.status) {
				$.dialog.info(data.message);
				return;
			}

			var disciplinesList = view.find('.disciplines-list').empty();
			data.disciplines.forEach(function(item) {
				disciplinesList.prepend(this.templateItemDiscipline(item));
			}.bind(this));

		}.bind(this));
	};

	this.openModalAddDiscipline = function(e) {
		e.stopPropagation();
		var modal = $('#modalAddDiscipline');
		var form = $('#form-discipline')

		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			modal.find('.modal-title').text('Editar disciplina');
			var id = $(e.currentTarget).closest('.item-discipline').attr('data-id');
			$.post('discipline/read', { 'discipline_id': id}, function(data){
				if(data.status){
					$('input[name="discipline_id"]',form).val(id);
					$('input[name="name"]',form).val(data.discipline.name);
					$('input[name="timetable"]',form).val(data.discipline.timetable);
					$('input[name="syllabus"]',form).val(data.discipline.syllabus);

				}
			})

			modal.find('.modal-title').text('Editar disciplina');
			modal.modal();
		}
		else {
			modal.find('.modal-title').text('Cadastrar disciplina');
			modal.modal();
		}

	};

	this.getDiscipline = function(id) {
		console.log(id);
	};
	this.saveDiscipline = function(e) {
		e.preventDefault();
		e.stopPropagation();

		var form = $(e.currentTarget);


		if(!form.validation()) {
			$.alert('Existem campos inválidos ou não preenchidos');
			return false;
		}
		var _data = form.serializeObject();
		_data.period_id = argument(1);
		_data.syllabus = form.find('[name="syllabus"]').summernote('code');
		dialogWaiting('Salvando. Aguarde.');
		$.post('discipline/save', _data, function(data) {
			if(!data.status) {
				$.dialog.info('Não foi possível salvar. Se o erro persistir contate o suporte');
			}
			else {
				$.dialog.close();
				$('#modalAddDiscipline').modal('hide');
				var disciplinesList = $('.disciplines-list', view);

				//Se for edição
				if(_data.discipline_id) {
					disciplinesList.find('.item-discipline[data-id="'+ _data.discipline_id +'"]').replaceWith(this.templateItemDiscipline(data.discipline));
					$.alert('Disciplina editada com sucesso');
				}
				else {
					disciplinesList.prepend(this.templateItemDiscipline(data.discipline));
					$.alert('Nova Disciplina criada com sucesso');
				}
			}

		}.bind(this));

	};

	this.templateItemDiscipline = function(data) {
		console.log(data);
		var html =
		'<div class="col-xs-6 col-sm-3 mb item-discipline" data-id="'+ data.id +'">'+
			'<div class="card card--shadow">'+
				'<div class="card__header">'+
					'<div class="flex">'+
						'<span class="grow text-bold text-md">'+ data.name +'</span>'+
						'<i class="ck material-icons icon mr-xs ev-openModalAddDiscipline" edit title="Editar">&#xE254;</i>'+
						'<i class="ck material-icons icon ev-deleteDiscipline"title="Deletar">&#xE872;</i>'+
					'</div>'+
				'</div>'+
				'<div class="card__body">'+
				'</div>'+
			'</div>'+
		'</div>';
		return html;
	};
	this.deleteDiscipline = function(e) {
		e.stopPropagation();
		var item = $(e.currentTarget).closest('.item-discipline');

		$.dialog.confirm('Confirmar', 'Deseja excluir a disciplina? Essa operação é irreversível.', function() {
			$.post('discipline/delete', { discipline_id: item.attr('data-id') }, function(data) {
				if(!data.status) {
					$.dialog.info('', data.message);
				}
				else {
					item.fadeOut(300, function() {
						item.remove();
						$.alert('Disciplina deletada com sucesso');
					});
				}
			}, errorDialog);
		});
	};
});

controller('periods', function() {
	var view = "#view-periods";

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddPeriod', this.openModalAddPeriod);
		view.on('click', '.ev-redirectToDiscipline', this.redirectToDiscipline);
		view.on('submit', '.ev-savePeriod', this.savePeriod);
	};

	this.show = function() {
		view.show();
		$("#page-title").text('Nome do curso');
		this.getListPeriods();
	};

	this.getListPeriods = function() {
		$.post('period/list', { course_id: argument(1) }, function(data) {
			if(!data.status) {
				$.dialog.info('Erro', data.message);
				return;
			}
			var periodsList = view.find('.periods-list').empty();
			data.periods.forEach(function(item) {
				periodsList.prepend(this.templateItemPeriod(item));
			}.bind(this));
		}.bind(this));
	};

	this.openModalAddPeriod = function(e) {
		e.stopPropagation();
		var modal = $('#modalAddPeriod');

		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			modal.find('.modal-title').text('Editar período');
			var id = $(e.currentTarget).closest('.item-period').attr('data-id');
			$.post('period/read', { period_id: id }, function(data) {
				if(data.status) {
					modal.find('input[name="period_id"]').val(id);
					modal.find('input[name="name"]').val(data.period.name);
					modal.modal();
				} else {
					$.dialog.info('Erro', data.message);
				}
			}, errorDialog);
		}
		else {
			modal.find('.modal-title').text('Cadastrar período');
			modal.modal();
		}

	};

	this.savePeriod = function(e) {
		alert();
		e.preventDefault();
		e.stopPropagation();

		var form = $(e.currentTarget);

		if(!form.validation()) {
			$.alert('Existem campos inválidos ou não preenchidos');
			return false;
		}
		var _data = form.serializeObject();
		_data.course_id = argument(1);

		dialogWaiting('Salvando. Aguarde.');
		$.post('period/save', _data, function(data) {
			if(!data.status) {
				$.dialog.info('Não foi possível salvar. Se o erro persistir contate o suporte');
			}
			else {
				$.dialog.close();
				$('#modalAddPeriod').modal('hide');
				var periodsList = $('.periods-list', view);

				//Se for edição
				if(_data.period_id) {
					periodsList.find('.item-period[data-id="'+ _data.period_id +'"]').replaceWith(this.templateItemPeriod(data.period));
					$.alert('Período editado com sucesso');
				}
				else {
					periodsList.prepend(this.templateItemPeriod(data.period));
					$.alert('Novo período criado com sucesso');
				}
			}

		}.bind(this));

	};

	this.templateItemPeriod = function(data) {
		console.log(data);
		var template =
		'<div class="col-xs-6 col-sm-3 mb item-period" data-id="'+ data.id +'">'+
			'<div class="card card--shadow ev-redirectToDiscipline">'+
				'<div class="card__header">'+
					'<div class="flex">'+
						'<span class="grow text-bold text-md">'+ data.name +'</span>'+
						'<i class="material-icons icon ev-openModalAddPeriod ck" edit title="Editar">&#xE254;</i>'+
					'</div>'+
				'</div>'+
				'<div class="card__body">'+
				'</div>'+
			'</div>'+
		'</div>';

		return template;
	};

	this.redirectToDiscipline = function(e) {
		redirect('disciplines/'+$(e.currentTarget).closest('.item-period').attr('data-id'));
	};
});

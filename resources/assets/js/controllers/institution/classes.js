controller('classes', function() {
	var view = "#view-classes";

	this.classeData = null;
	this.coursesData = null;

	this.initialize = function() {
		view = $(view);
		view.on('click', '.ev-openModalAddClasse', this.openModalAddClasse);
		view.on('click', '.ev-redirectToDiscipline', this.redirectToDiscipline);
		view.on('submit', '.ev-saveClasse', this.saveClasse);
		view.on('change', '.ev-getPeriods', this.getPeriods);
		view.on('change', '.ev-filterByCourse', this.filterByCourse);

	};

	this.show = function() {
		$("#page-title").text('Turmas');
		this.getCourses(function() {
			this.setCoursesFilter();
		}.bind(this));
		view.show();
	};

	this.openModalAddClasse = function(e) {
		e.stopPropagation();
		var modal = $('#modalAddClasse');
		//Verifica se o elemento do evento tem o atributo edit
		if($(e.currentTarget).is('[edit]')) {
			this.classeData = null;
			var id = $(e.currentTarget).closest('.item-classe').attr('data-id');
			$.post('/classe/read', { classe_id: id }, function(data) {

				if(!data.status) {
					$.dialog.info(data.message);
					return;
				}

				this.classeData = data.classe;
				$('input[name="classe_id"]', form).val(id);
				$('input[name="name"]', form).val(data.classe.name);
				//Obtém os cursos e coloca no
				// this.getCourses();
			}.bind(this));
			modal.find('.modal-title').text('Editar período');
		}
		else {
			modal.find('.modal-title').text('Cadastrar período');
		}

		//Obtém os cursos e seta em courses Modal.
		this.getCourses(function() {
			this.setCoursesModal();
		}.bind(this));

		modal.modal();
	};

	this.filterByCourse = function(e) {
		var id = $(e.currentTarget).val();
		var listGroupClasses = view.find('.list-group-classes').empty().hide();
		var targetCallback = view.find('.callback-list-group-classes').empty().hide();

		$.post('/classe/list-grouped', { course_id: id }, function(data) {

			if(!data.status) {
				$.dialog.info(data.message);
				return;
			}

			var noClasses = true;
			if(data.periods.length) {
				data.periods.forEach(function(item) {
					if(item.classes.length) {
						noClasses = false;
						listGroupClasses.append(this.templateGroupClasses(item));
					}
				}.bind(this));
				if(!noClasses) {
					listGroupClasses.fadeIn();
				}
			}

			if(!data.periods.length || noClasses) {
				targetCallback.html(
					'<div class="alert__title">Nenhum resultado</div>'+
					'<div>Nenhuma turma encontrada</div>'
				).fadeIn();
			}


		}.bind(this));
	};

	this.getCourses = function(callback) {
		$.post('/course/list', {}, function(data) {

			if(!data.status) {
				$.dialog.info(data.message);
				return;
			}

			data.courses = data.courses.sort(function(a,b) {
				return a.name < b.name;
			});

			this.coursesData = data.courses;

			if(typeof callback == 'function') {
				callback();
			}

		}.bind(this));
	};

	this.setCoursesModal = function() {
		var selectCourseForm = $("#modalAddClasse").find('[name="course_id"]').empty();
		var courses = this.coursesData;

		if(this.coursesData && this.coursesData.length) {
			courses.forEach(function(item) {
				selectCourseForm.prepend('<option value="'+ item.id +'">'+ item.name +'</option>');
			}.bind(this));
			selectCourseForm.find('option:eq(0)').prop('selected', true).trigger('change');
		}
	};

	this.setCoursesFilter = function() {
		var select = view.find('[name="course_filter"]').empty();
		var courses = this.coursesData;

		if(this.coursesData && this.coursesData.length) {
			courses.forEach(function(item) {
				select.prepend('<option value="'+ item.id +'">'+ item.name +'</option>');
			}.bind(this));
			select.find('option:eq(0)').prop('selected', true).trigger('change');
		}
	};

	this.getPeriods = function(e) {
		$.post('/period/list', { course_id: $(e.currentTarget).val()}, function(data) {

			if(!data.status) {
				$.dialog.info(data.message);
				return;
			}

			data.periods = data.periods.sort(function(a,b) {
				return a.name < b.name;
			});

			var selectPeriods = $("#modalAddClasse").find('[name="period_id"]').empty();
			data.periods.forEach(function(item) {
				selectPeriods.prepend('<option value="'+ item.id +'">'+ item.name +'</option>');
			}.bind(this));
			selectPeriods.find('option:eq(0)').prop('selected', true);

		}.bind(this));
	};

	/**
	 * Salva os dados informados no formulário.
	 * @param  {object} e Objeto do evento 'submit' da classe .ev-saveClasse
	 */
	this.saveClasse = function(e) {
		e.preventDefault();
		e.stopPropagation();

		var form = $(e.currentTarget);

		if(!form.validation()) {
			$.alert('Existem campos inválidos ou não preenchidos');
			return false;
		}
		var _data = form.serializeObject();

		dialogWaiting('Salvando. Aguarde.');
		$.post('classe/save', _data, function(data) {
			if(!data.status) {
				$.dialog.info('Não foi possível salvar. Se o erro persistir contate o suporte');
			}
			else {
				$.dialog.close();
				$('#modalAddClasse').modal('hide');
				var classesList = $('.classe-list', view);

				//Se for edição
				if(_data.classe_id) {
					classeList.find('.item-classe[data-id="'+ _data.classe_id +'"]').replaceWith(this.templateItemClasse(data.classe));
					$.alert('Turma editada com sucesso');
				}
				else {
					classeList.prepend(this.templateItemPeriod(data.classe));
					$.alert('Nova turma criada com sucesso');
				}
			}

		}.bind(this));

	};

	/**
	 * Template base para o cartão do período
	 * @param  {object} data - Objeto com as propriedades do período
	 * @return {string}      Template HTML
	 */
	this.templateGroupClasses = function(data) {

		var classesTemplate = '';
		var templateClasse = '';

		if(!data.classes.length) {
			templateClasse = '<li>Nenhuma turma cadastrada</li>';
		}
		else {
			data.classes.forEach(function(item) {
				templateClasse += this.templateItemClasse(item);
			}.bind(this));
		}


		var template =
		'<li class="mt item-period" data-id="'+ data.id +'">'+
			'<div class="text-bold text-md text-upper"> '+ data.name +' </div>'+
			'<div class="row mt" >'+
				templateClasse +
			'</div>'+
		'</li>';

		return template;
	};

	/**
	 * Template base para o cartão da classe
	 * @param  {object} data - Objeto com as propriedades do classe
	 * @return {string}      Template HTML
	 */
	this.templateItemClasse = function(data) {
		var template =
			'<div class="col-xs-4 mb item-classe" data-id="'+ data.id +'">'+
				'<div class="card card--shadow item-classe ev-redirectToOffers">'+
					'<div class="card__header">'+
						'<div class="flex">'+
							'<span class="grow text-bold text-md">'+ data.name +'</span>'+
							'<i class="material-icons icon mr-xs ck ev-openModalAddClasse" edit title="Editar">&#xE254;</i>'+
							'<i class="ck material-icons icon ck ev-deleteClasse" title="Deletar">&#xE872;</i>'+
						'</div>'+
					'</div>'+
					'<div class="card__body">'+
					'</div>'+
				'</div>'+
			'</div>'+
		'</div>';

		return template;
	};

	this.redirectToDiscipline = function(e) {
		redirect('disciplines/'+$(e.currentTarget).attr('data-id'));
	};
});

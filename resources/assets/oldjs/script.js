$(function() {
	$('.modal').on('hidden.bs.modal', function (e) {

		if($('.modal:visible').length) {
			var visible = $('.modal:visible');
			visible.css('z-index', '1050');
		}
		$(e.currentTarget).css('z-index', '1050');
		triggerResetForm($(e.currentTarget).find('form'));
		// $(e.currentTarget).find('form')[0].reset();
		// $(e.currentTarget).find('form .form-group.has-error').removeClass('has-error');
		// $(e.currentTarget).find('form .form-group .callback').hide();
		// $(e.currentTarget).find('form .form-group[dependency-plus]').hide().find('input').prop('disabled', true);
	});


	$('.modal').on('show.bs.modal', function (e) {
		var visible = $('.modal:visible');
		visible.css('z-index', '1040');
	});

	//Inicializa o chosen select
	$('[data-module="chosen-select"]').chosen({
		width: "100%",
		allow_single_deselect: true,
		placeholder_text_single: "Selecione um opção",
		no_results_text: "Não possui resultados para",
		search_contains: true
	});

	//Evento para datepicker Pikaday
	triggerDatePicker($('.datepicker'));

	$(document).on('focusin', '.pika-single', function (e) { e.stopPropagation(); });

	$('body').on('blur', '.datepicker', validateInputDate);
	$('.mask-monthYear').unmask().mask('00/0000', {placeholder: 'MM/YYYY'});
	$('.mask-cei').unmask().mask('00.00000.0-00', {placeholder: '__._____._-__/YYYY'});

	$('[href="#associateds"]').click(function() {
		$('[name="search"]', 'associateds').val('');
		$.ctrl.associateds.dropFilter = { situation: 'all', delegacy_id: ''};
		$('[data-name="drop-delegacy-label"]').text("Todas");
		$('[data-name="drop-situation-label"]').text("Todas");
	});

	$('.sidebar').on('click', '.side-item', function(e) {
		$('.sidebar').find('.side-item').removeClass('active');
		$(e.currentTarget).addClass('active');
	});

	$('.menu-nav').on('click teste', '[data-nav-href]', function(e) {
		var navArea = $(e.currentTarget).closest('.menu-nav');

		if(navArea.find('[data-nav-target="'+ $(e.currentTarget).attr('data-nav-href') +'"]').is(':visible')) {
			return false;
		}
		else {
			navArea.find('[data-nav-target]').fadeOut(0);
			navArea.find('[data-nav-target="'+ $(e.currentTarget).attr('data-nav-href') +'"]').fadeIn(0);
		}

		navArea.find('[data-nav-href]').removeClass('active');
		$(e.currentTarget).addClass('active');
	});

	//Recalcula tamanho do texarea automaticamente
	$('body').on('keyup', 'textarea', function(e) {
		e.currentTarget.style.height = "1px";
		e.currentTarget.style.height = (22+e.currentTarget.scrollHeight)+"px";
	});

	//Implementa expandir e comprimir uma div
	$('body').on('click', '[data-action="expand"]', function(e) {
		var target = $('[data-expand="'+ $(e.currentTarget).attr('data-expand-target') +'"]');
		// console.log(target);
		if(target.is(':visible')) {
			target.slideUp();
			$(e.currentTarget).find('[data-expand-arrow]').html('<i class="material-icons icon no-pad">&#xE5CF;</i>');
		}
		else {
			target.slideDown();
			$(e.currentTarget).find('[data-expand-arrow]').html('<i class="material-icons icon no-pad">&#xE5CE;</i>');
		}
	});

	triggerMaskMoney();


});

function triggerDatePicker(input) {
	// var picker = new Pikaday({ field: document.getElementById('date') });

	input.each(function(i, item) {
		// console.log($(item));
		var config = {
			maxDate: moment()._d,
			yearRange: [ moment().year() - 100, moment().year() ]
		};
		if($(item).is('[datepicker-future]')) {
			config.maxDate = null;
			config.yearRange = [ moment().year() - 100, moment().year() + 30 ];
		}

		$(item).pikaday({
			firstDay: 1,
			minDate: new Date(1920, 0, 1),
			maxDate: config.maxDate,
			yearRange: config.yearRange,
			format: 'DD/MM/YYYY',
			i18n: {
				previousMonth : 'Anterior',
				nextMonth     : 'Próximo',
				months        : ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				weekdays      : ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				weekdaysShort : ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb']
			},
			onSelect: function() {
				var field = $(this._o.field);
				if(field.hasClass('datepicker-range')) {
					var target = $(field).closest('form').find('[name="'+field.attr('data-target')+'"]');
					if(field.attr('data-range') == 'min') {
						var startDate = moment(field.val(), 'DD/MM/YYYY')._d;
						field.pikaday('setStartRange', startDate);
						target.pikaday('setStartRange', startDate);
						target.pikaday('setMinDate', startDate);
					}
					else if(field.attr('data-range') == 'max') {
						var endDate = moment(field.val(), 'DD/MM/YYYY')._d;
						field.pikaday('setEndRange', endDate);
						target.pikaday('setEndRange', endDate);
						target.pikaday('setMaxDate', endDate);

					}
				}
			}
		});
	});

}

function update_masks() {
	var arr = [
		'.mask-cpf', '.mask-phone', '.mask-date', '.mask-time', '.mask-money',
		'.mask-number', '.mask-monthYear', '.mask-monthYear', '.mask-cei'
	];

	arr.forEach(function(selector) {
		$(selector).each(function(i, item) {

			if(typeof $(item).data().mask != 'undefined') {
				if(!$(item).is('input')) {
					var val = $(item).text();
					$(item).text(function() {
						return $(item).masked(val);
					});
				}
				else {
					var val = $(item).val();
					$(item).val(function() {
						return $(item).masked(val);
					});
				}
			}

		});
	});
	// triggerMaskMoney();
}

function dialogWaiting(message) {
	$.dialog.waiting('<div class="circle--small"></div><div class="text-center br-top-sm">'+ (message || "") +'</div>');
}

function validaCPF(cpf) {
	cpf = cpf.replace(/\D/g, '');
	if(cpf.length != 11 || cpf.replace(eval('/'+cpf.charAt(1)+'/g'),'') == '') {
		return false;
	}
	else
	{
		for(n = 9; n < 11; n++)
		{
			for(d = 0, c = 0; c < n; c++) d += cpf.charAt(c) * ((n + 1) - c);
				d = ((10 * d) % 11) % 10;

			if(cpf.charAt(c) != d) return false;
		}
		return true;
	}
}

validateInputCpf = function(e) {
	var group = $(e.currentTarget).closest('.form-group');
	group.removeClass('has-error').find('.callback').hide();
	if($(e.currentTarget).val().length && !validaCPF($(e.currentTarget).val())) {
		group.addClass('has-error').find('.callback').show().text('CPF inválido');
		return false;
	}
	return true;
};

validateInputDate = function(e, format) {
	var group = $(e.currentTarget).closest('.form-group');
	group.removeClass('has-error').find('.callback').hide();
	if($(e.currentTarget).val().length && !moment($(e.currentTarget).val(), (format || "DD/MM/YYYY")).isValid()) {
		group.addClass('has-error').find('.callback').show().text('Informe uma data válida');
		return false;
	}
	return true;
};

function animation() {
  return $('<div>', {'class': 'circle' });
}

function animationSmall() {
  return $('<div>', {'class': 'circle--small' });
}

errorDialog = function(xhr, ajaxOptions, thrownError) {
	$.dialog.info('Erro '+ xhr.status , '<div class="text-center">Não foi possível completar sua requisição</div>');
};

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

Number.prototype.formatMoney = function(c, d, t){
var n = this,
    c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "," : d,
    t = t == undefined ? "." : t,
    s = n < 0 ? "-" : "",
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

parseTextToFloat = function(arg) {
	var tmp = arg.replace(/\./g, '');
	tmp = tmp.replace(',', '.');
	return parseFloat(tmp);
};

triggerChosen = function(input) {
	$(input).chosen({
		width: "100%",
		allow_single_deselect: true,
		placeholder_text_single: "Selecione um opção",
		no_results_text: "Não possui resultados para",
		search_contains: true
	});
};

triggerResetForm = function(target) {
	$(target).trigger('reset');
	$(target).find('.form-group.has-error').removeClass('has-error');
	$(target).find('.form-group .callback').hide();
	$(target).find('.form-group[dependency-plus]').hide().find('input').prop('disabled', true);
	$(target).find('[data-module="chosen-select"]').trigger('chosen:updated');
};

triggerMaskMoney = function(target) {
	var config = { allowNegative: true, thousands:'.', decimal:',', allowZero: true };
	if(target) {
		$(target).maskMoney(config);
	}
	else {
		$('.mask-money2').maskMoney(config);
	}
};

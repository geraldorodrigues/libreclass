eventSelect2 = function(select) {
	console.log(select);
	// var modal = $('#modalAddAssociateRole');
	// var select = modal.find('select[name="associated_id"]');
	$(select.target).select2({
		placeholder: select.placeholder,
		selectOnClose: true,
		width: '100%',
		ajax: {
			url: select.url,
			dataType: 'json',
			method: 'POST',
			delay: 500,
			data: function (params) {
				return $.extend(select.params, {
					search: params.term, // search term
				});
			},
			processResults: function (data, params) {
				return {
					results: data[select.results]
				};
			},
			cache: true
		},
		escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		minimumInputLength: 3,
		templateResult: select.templateResult,
		templateSelection: function(data) {
			console.log('selection',data);
			if(data.register) {
				return data.register + ' - ' + data.name;
			}
			return data.text;
		},
		language: {
			inputTooShort: function(arg) {
				return 'Informe '+ (arg.minimum - arg.input.length) +' ou mais caracteres';
			},
			noResults: function() {
				return "Nenhum resultado encontrado";
			},
			searching: function() {
				return "Pesquisando...";
			},
		}
	});
};

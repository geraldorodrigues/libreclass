$(function() {

	$("body").on("change", ".dependency-plus", function(e) {

		var name = $(e.currentTarget).attr("name");
		var form = {};
		if($(e.currentTarget).closest('.dependency-plus-container').length) {
			form = $(e.currentTarget).closest('.dependency-plus-container');
		}
		else {
			form = $(e.currentTarget).closest('form');
		}
		var inputDependency = $("[dependency-plus~='"+ name +"']", form);

		console.log(inputDependency);
		$.each(inputDependency, function(i, item) {
			var plus = $.makeArray($(item).attr('dependency-plus').split(' '));
			var plusValues = $(item).attr('dependency-plus-value').split(' ');
			var dependency = [];

			plus.forEach(function(item, j) {
				dependency.push({ 'name': item, 'value': plusValues[j].split(",") });
			});

			var showBlock = false;

			for(var i in dependency) {
				for(var j in dependency[i].value) {

					showBlock = false;
					var value = "";
					var parent = $('.dependency-plus[name="'+ dependency[i].name +'"]', form);
					if(parent.prop('localName') == 'input') { // Necessário para input do tipo radio e checkbox

						if(parent.prop('type') == 'radio') {
							if(!parent.filter(':checked').length) break;
							value = parent.filter(':checked').val();
						}
						else if(parent.prop('type') == 'checkbox') {
							if(!parent.filter(':checked').length) break;
							var arr = [];
							parent.filter(':checked').each(function(i, item) {
								arr.push($(item).val());
							});
							value = arr;
						}
						else { // Para type text;
							if(!parent.val()) break;
							value = parent.val();
						}
					}
					else {
						if(!parent.val()) break;
						value = parent.val();
					}
					if(dependency[i].value[j][0] == "!") {
						if($.makeArray(value).indexOf(dependency[i].value[j].slice(1)) != -1) {
							showBlock = false;
						}
					}

					if($.makeArray(value).indexOf(dependency[i].value[j]) != -1) {
						showBlock = true;
						break;
					}
				}
				if(showBlock == false) { break; }
			}

			$(item).find('input, select').each(function(i, input) {
				if($(input).closest('[dependency-plus]').attr('dependency-plus').split(' ').indexOf(name) != -1) {
					if(showBlock) $(input).prop('disabled', false);
					else $(input).prop('disabled', true);
				}
			});

			if(showBlock) $(item).show();
			else if(!$(item).is('[data-disabled]')) $(item).hide();
		});
	});
});

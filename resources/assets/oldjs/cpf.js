$(function() {
	$('body').on('blur', '.valida-cpf-hover', function(e) {
		validateInputCpf(e);
	});
});

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

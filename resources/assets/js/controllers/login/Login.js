initial('in');

controller('in', function() {
	var view = "#view-login";

	this.initialize = function() {
		view = $(view);

		$('#form-login', view).submit(this.submitLogin);
		$('#form-login').find('.showPassword').on('click', this.togglePassword);

		redirect('in');
	};

	this.show = function() {
		view.show();
	};

	this.submitLogin = function(e) {
		e.preventDefault();
		var form = $(e.currentTarget);

		if(!form.validation()) {
			$.alert('Existem campos inválidos');
			return false;
		}

		var message = $.alert('Enviando...');
		$.post("/auth/in", form.serializeObject(), function(data) {
			if (data.status) {
				window.location.href = "/";
			}
			else {
				$(message).remove();
				$.dialog.info('Erro', data.message);
			}
		}, error_send);
	};

	this.forgotPassword = function(e) {
		e.preventDefault();
		$.alert('Enviando...');
		$.post($(e.currentTarget).attr('action'), $(e.currentTarget).serialize(), function(data) {
			if ( data.status ) {
				$.dialog.info('Operação realizada', data.message);
				$(".login-forgot").eq(0).click();
			}
			else {
				$.dialog.info('Erro', data.message);
			}
		}, error_send);
	};

	this.togglePassword = function(e) {
		var input = $(e.currentTarget).closest('.form-group').find('input[name="password"]');
		if($(e.currentTarget).attr('show') == "0") {
			input.attr('type', 'text');
			$(e.currentTarget).attr('show', '1');
		}
		else {
			input.attr('type', 'password');
			$(e.currentTarget).attr('show', '0');
		}
	};

});

//# sourceMappingURL=controllers.login.js.map

function dialogWaiting(message) {
	$.dialog.waiting('<div class="circle--small"></div><div class="text-center br-top-sm">'+ (message || "") +'</div>');
}

function errorDialog(xhr, ajaxOptions, thrownError) {
	$.dialog.info('Erro '+ xhr.status , '<div class="text-center">Não foi possível completar sua requisição</div>');
}

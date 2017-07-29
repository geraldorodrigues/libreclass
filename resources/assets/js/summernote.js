$(function() {
	$('.editor').summernote({
		lang: 'pt-BR',
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['height', ['height']]
		],
		minHeight: 100
	});
});

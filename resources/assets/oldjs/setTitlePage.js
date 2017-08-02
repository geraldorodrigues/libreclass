setTitlePage = function(obj) {
	obj.title = obj.title || "";
	obj.icon = obj.icon || "";
	$('#page-title').html(obj.title);
	$('#page-title-icon').attr('src', obj.icon);
};

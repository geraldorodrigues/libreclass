controller('courses', function() {
	var view = "#view-courses";

	this.initialize = function() {
		view = $(view);
	};

	this.show = function() {
		view.show();
	};
});

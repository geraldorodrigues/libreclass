triggerResetForm = function(target) {
	$(target).trigger('reset');
	$(target).find('.form-group.has-error').removeClass('has-error');
	$(target).find('.form-group .callback').hide();
	$(target).find('.form-group[dependency-plus]').hide().find('input').prop('disabled', true);
	$(target).find('[data-module="chosen-select"]').trigger('chosen:updated');
};

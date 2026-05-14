window.Camagru.register.validateField = (name) => {
	const field = window.Camagru.register.state.fields[name];
	if (!field) {
		return true;
	}
	const error = window.Camagru.register.validators[name](field.value);
	if (error) {
		window.Camagru.register.showError(field, error);
		return false;
	}
	window.Camagru.register.clearError(field);
	return true;
};

window.Camagru.register.clearError = (field) => {
	field.classList.remove("brutal-input--invalid");
	field.removeAttribute("aria-invalid");
	const helper = field.parentElement.querySelector(".field-error");
	if (helper) {
		helper.remove();
	}
};

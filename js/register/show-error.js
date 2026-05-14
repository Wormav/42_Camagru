window.Camagru.register.showError = (field, message) => {
	field.classList.add("brutal-input--invalid");
	field.setAttribute("aria-invalid", "true");

	let helper = field.parentElement.querySelector(".field-error");
	if (!helper) {
		helper = document.createElement("span");
		helper.className = "field-error";
		helper.setAttribute("role", "alert");
		field.parentElement.appendChild(helper);
	}
	helper.textContent = message;
};

document.addEventListener("DOMContentLoaded", () => {
	const form = document.getElementById("register-form");
	if (!form) {
		return;
	}

	window.Camagru.register.state.fields = {
		email: form.querySelector("[name='email']"),
		username: form.querySelector("[name='username']"),
		password: form.querySelector("[name='password']"),
		password_confirmation: form.querySelector("[name='password_confirmation']"),
	};

	Object.keys(window.Camagru.register.state.fields).forEach((name) => {
		const field = window.Camagru.register.state.fields[name];
		if (!field) {
			return;
		}
		window.Camagru.register.state.touched[name] = false;

		field.addEventListener("blur", () => {
			window.Camagru.register.state.touched[name] = true;
			window.Camagru.register.validateField(name);
		});

		field.addEventListener("input", () => {
			if (!window.Camagru.register.state.touched[name]) {
				return;
			}
			window.Camagru.register.validateField(name);
			if (name === "password") {
				window.Camagru.register.validateField("password_confirmation");
			}
		});
	});

	form.addEventListener("submit", (event) => {
		let firstInvalid = null;
		Object.keys(window.Camagru.register.state.fields).forEach((name) => {
			if (!window.Camagru.register.validateField(name) && !firstInvalid) {
				firstInvalid = window.Camagru.register.state.fields[name];
			}
		});
		if (firstInvalid) {
			event.preventDefault();
			firstInvalid.focus();
		}
	});
});

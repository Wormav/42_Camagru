(function () {
	"use strict";

	var form = document.getElementById("register-form");
	if (!form) {
		return;
	}

	var fields = {
		email: form.querySelector('[name="email"]'),
		username: form.querySelector('[name="username"]'),
		password: form.querySelector('[name="password"]'),
		password_confirmation: form.querySelector('[name="password_confirmation"]'),
	};

	var validators = {
		email: function (value) {
			if (value === "") return "Email is required.";
			if (value.length > 255) return "Email is too long (max 255 characters).";
			if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return "Email format is invalid.";
			return null;
		},
		username: function (value) {
			if (value === "") return "Username is required.";
			if (value.length < 3 || value.length > 20) return "Username must be 3 to 20 characters.";
			if (!/^[A-Za-z0-9_]+$/.test(value)) return "Username can only contain letters, digits and underscore.";
			return null;
		},
		password: function (value) {
			if (value.length < 8) return "Password must be at least 8 characters.";
			if (!/[A-Z]/.test(value)) return "Password must contain at least one uppercase letter.";
			if (!/[a-z]/.test(value)) return "Password must contain at least one lowercase letter.";
			if (!/\d/.test(value)) return "Password must contain at least one digit.";
			if (!/[^A-Za-z0-9]/.test(value)) return "Password must contain at least one special character.";
			return null;
		},
		password_confirmation: function (value) {
			if (value !== fields.password.value) return "Passwords do not match.";
			return null;
		},
	};

	function showError(field, message) {
		field.classList.add("brutal-input--invalid");
		field.setAttribute("aria-invalid", "true");

		var helper = field.parentElement.querySelector(".field-error");
		if (!helper) {
			helper = document.createElement("span");
			helper.className = "field-error";
			helper.setAttribute("role", "alert");
			field.parentElement.appendChild(helper);
		}
		helper.textContent = message;
	}

	function clearError(field) {
		field.classList.remove("brutal-input--invalid");
		field.removeAttribute("aria-invalid");
		var helper = field.parentElement.querySelector(".field-error");
		if (helper) {
			helper.remove();
		}
	}

	function validateField(name) {
		var field = fields[name];
		if (!field) return true;
		var error = validators[name](field.value);
		if (error) {
			showError(field, error);
			return false;
		}
		clearError(field);
		return true;
	}

	// Wire each field: validate on blur (don't nag the user while typing
	// the first time), then re-validate on every input once the field has
	// been "touched" so feedback updates as they fix things.
	Object.keys(fields).forEach(function (name) {
		var field = fields[name];
		var touched = false;

		field.addEventListener("blur", function () {
			touched = true;
			validateField(name);
		});

		field.addEventListener("input", function () {
			if (!touched) return;
			validateField(name);
			// Confirmation depends on password, keep them in sync.
			if (name === "password") {
				validateField("password_confirmation");
			}
		});
	});

	// Final gate on submit: block and focus the first invalid field.
	form.addEventListener("submit", function (event) {
		var firstInvalid = null;
		Object.keys(fields).forEach(function (name) {
			if (!validateField(name) && !firstInvalid) {
				firstInvalid = fields[name];
			}
		});
		if (firstInvalid) {
			event.preventDefault();
			firstInvalid.focus();
		}
	});
})();

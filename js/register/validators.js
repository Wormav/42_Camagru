window.Camagru.register.validators = {
	email: (value) => {
		if (value === "") return "Email is required.";
		if (value.length > 255) return "Email is too long (max 255 characters).";
		if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return "Email format is invalid.";
		return null;
	},
	username: (value) => {
		if (value === "") return "Username is required.";
		if (value.length < 3 || value.length > 20) return "Username must be 3 to 20 characters.";
		if (!/^[A-Za-z0-9_]+$/.test(value)) return "Username can only contain letters, digits and underscore.";
		return null;
	},
	password: (value) => {
		if (value.length < 8) return "Password must be at least 8 characters.";
		if (!/[A-Z]/.test(value)) return "Password must contain at least one uppercase letter.";
		if (!/[a-z]/.test(value)) return "Password must contain at least one lowercase letter.";
		if (!/\d/.test(value)) return "Password must contain at least one digit.";
		if (!/[^A-Za-z0-9]/.test(value)) return "Password must contain at least one special character.";
		return null;
	},
	password_confirmation: (value) => {
		const passwordField = window.Camagru.register.state.fields.password;
		if (passwordField && value !== passwordField.value) return "Passwords do not match.";
		return null;
	},
};

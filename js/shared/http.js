window.Camagru.shared.postForm = async (url, formData) => {
	const response = await fetch(url, {
		method: "POST",
		body: formData,
		credentials: "same-origin",
		headers: { "X-Requested-With": "XMLHttpRequest" },
	});

	const payload = await response.json().catch(() => ({}));
	return { response, payload };
};

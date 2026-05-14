window.Camagru.post.loadUpload = (file) => {
	const imageEl = document.querySelector("[data-stage-image]");
	if (!(imageEl instanceof HTMLImageElement)) {
		return;
	}

	if (!window.Camagru.post.ACCEPTED_MIMES.includes(file.type)) {
		window.Camagru.post.showStatus("denied", "// unsupported format — pick JPEG or PNG");
		return;
	}
	if (file.size > window.Camagru.post.MAX_UPLOAD_BYTES) {
		window.Camagru.post.showStatus("denied", "// image too large — 10MB max");
		return;
	}

	const reader = new FileReader();

	reader.addEventListener("load", () => {
		window.Camagru.post.stopStream();
		imageEl.src = typeof reader.result === "string" ? reader.result : "";
		window.Camagru.post.showSource("image");
	});

	reader.addEventListener("error", () => {
		window.Camagru.post.showStatus("denied", "// could not read the file — try another one");
	});

	reader.readAsDataURL(file);
};

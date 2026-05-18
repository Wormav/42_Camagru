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
		const dataUrl = typeof reader.result === "string" ? reader.result : "";
		imageEl.addEventListener("load", () => {
			window.Camagru.post.state.sourceMode = "image";
			window.Camagru.post.redrawUpload();
			window.Camagru.post.showSource("image");
			window.Camagru.post.updateCaptureButton();
		}, { once: true });
		imageEl.addEventListener("error", () => {
			window.Camagru.shared.dispatchToast("Invalid image file — please upload a real JPEG or PNG.", "error");
			window.Camagru.post.showStatus("denied", "// invalid image file");
		}, { once: true });
		imageEl.src = dataUrl;
	});

	reader.addEventListener("error", () => {
		window.Camagru.post.showStatus("denied", "// could not read the file — try another one");
	});

	reader.readAsDataURL(file);
};

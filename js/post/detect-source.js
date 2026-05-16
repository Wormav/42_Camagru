window.Camagru.post.detectSource = () => {
	const mode = window.Camagru.post.state.sourceMode;
	if (mode === "webcam" && window.Camagru.post.state.activeStream !== null) {
		return "webcam";
	}
	if (mode === "image") {
		const imageEl = document.querySelector("[data-stage-image]");
		if (imageEl instanceof HTMLImageElement && imageEl.src !== "") {
			return "image";
		}
	}
	return null;
};

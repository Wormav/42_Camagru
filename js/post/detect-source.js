window.Camagru.post.detectSource = () => {
	const canvas = document.querySelector("[data-stage-canvas]");
	const imageEl = document.querySelector("[data-stage-image]");

	if (!(canvas instanceof HTMLCanvasElement) || !(imageEl instanceof HTMLImageElement)) {
		return null;
	}

	if (!canvas.classList.contains("hidden") && window.Camagru.post.state.activeStream !== null) {
		return "webcam";
	}
	if (!imageEl.classList.contains("hidden") && imageEl.src !== "") {
		return "image";
	}
	return null;
};

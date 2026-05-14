window.Camagru.post.detectSource = () => {
	const video = document.querySelector("[data-stage-video]");
	const imageEl = document.querySelector("[data-stage-image]");

	if (!(video instanceof HTMLVideoElement) || !(imageEl instanceof HTMLImageElement)) {
		return null;
	}

	if (!video.classList.contains("hidden") && window.Camagru.post.state.activeStream !== null) {
		return "webcam";
	}
	if (!imageEl.classList.contains("hidden") && imageEl.src !== "") {
		return "image";
	}
	return null;
};

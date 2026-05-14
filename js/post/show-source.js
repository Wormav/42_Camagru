window.Camagru.post.showSource = (source) => {
	const status = document.querySelector("[data-stage-status]");
	const video = document.querySelector("[data-stage-video]");
	const imageEl = document.querySelector("[data-stage-image]");

	if (status === null || video === null || imageEl === null) {
		return;
	}

	status.dataset.stageState = "ready";
	status.classList.add("hidden");
	video.classList.add("hidden");
	imageEl.classList.add("hidden");

	if (source === "webcam") {
		video.classList.remove("hidden");
	} else if (source === "image") {
		imageEl.classList.remove("hidden");
	}
};

window.Camagru.post.showSource = (source) => {
	const status = document.querySelector("[data-stage-status]");
	const canvas = document.querySelector("[data-stage-canvas]");
	const imageEl = document.querySelector("[data-stage-image]");

	if (status === null || canvas === null || imageEl === null) {
		return;
	}

	status.dataset.stageState = "ready";
	status.classList.add("hidden");
	canvas.classList.add("hidden");
	imageEl.classList.add("hidden");

	if (source === "webcam" || source === "image") {
		canvas.classList.remove("hidden");
	}
};

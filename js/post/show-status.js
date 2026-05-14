window.Camagru.post.showStatus = (name, customLabel) => {
	const status = document.querySelector("[data-stage-status]");
	const label = document.querySelector("[data-stage-status-label]");
	const video = document.querySelector("[data-stage-video]");
	const imageEl = document.querySelector("[data-stage-image]");

	if (status === null || label === null || video === null || imageEl === null) {
		return;
	}

	status.dataset.stageState = name;
	label.textContent = customLabel || window.Camagru.post.STATUS_LABEL[name] || "";
	video.classList.add("hidden");
	imageEl.classList.add("hidden");
	status.classList.remove("hidden");
};

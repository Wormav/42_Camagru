window.Camagru.post.updateCaptureButton = () => {
	const captureBtn = document.querySelector("[data-action='capture']");
	if (captureBtn === null) {
		return;
	}

	if (window.Camagru.post.detectSource() === null) {
		captureBtn.setAttribute("disabled", "");
		captureBtn.setAttribute("aria-disabled", "true");
	} else {
		captureBtn.removeAttribute("disabled");
		captureBtn.setAttribute("aria-disabled", "false");
	}
};

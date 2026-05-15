window.Camagru.post.toggleOverlay = (tile) => {
	const overlayList = document.querySelector("[data-overlay-list]");
	const isActive = tile.getAttribute("aria-pressed") === "true";

	if (overlayList !== null) {
		for (const other of overlayList.querySelectorAll("[aria-pressed='true']")) {
			other.setAttribute("aria-pressed", "false");
		}
	}

	if (isActive) {
		window.Camagru.post.state.selectedOverlayId = null;
		window.Camagru.post.setOverlayImage(null);
	} else {
		tile.setAttribute("aria-pressed", "true");
		window.Camagru.post.state.selectedOverlayId = tile.dataset.overlayId || null;
		window.Camagru.post.setOverlayImage(tile.dataset.overlayPath || null);
	}

	window.Camagru.post.updateCaptureButton();
};

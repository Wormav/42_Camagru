document.addEventListener("DOMContentLoaded", () => {
	const video = document.querySelector("[data-stage-video]");
	const imageEl = document.querySelector("[data-stage-image]");
	const status = document.querySelector("[data-stage-status]");
	const label = document.querySelector("[data-stage-status-label]");

	if (video === null || imageEl === null || status === null || label === null) {
		return;
	}

	const webcamBtn = document.querySelector("[data-action='use-webcam']");
	if (webcamBtn !== null) {
		webcamBtn.addEventListener("click", window.Camagru.post.startWebcam);
	}

	const fileInput = document.querySelector("[data-action='use-upload']");
	if (fileInput !== null) {
		fileInput.addEventListener("change", (event) => {
			const target = event.target;
			if (!(target instanceof HTMLInputElement)) {
				return;
			}
			const file = target.files && target.files[0];
			if (!file) {
				return;
			}
			window.Camagru.post.loadUpload(file);
			target.value = "";
		});
	}

	const overlayList = document.querySelector("[data-overlay-list]");
	if (overlayList !== null) {
		overlayList.addEventListener("click", (event) => {
			const target = event.target;
			if (!(target instanceof Element)) {
				return;
			}
			const tile = target.closest("[data-overlay-id]");
			if (tile === null || !overlayList.contains(tile)) {
				return;
			}
			window.Camagru.post.toggleOverlay(tile);
		});
	}

	const snapsList = document.querySelector("[data-snaps-list]");
	if (snapsList !== null) {
		snapsList.addEventListener("click", (event) => {
			const target = event.target;
			if (!(target instanceof Element)) {
				return;
			}

			const deleteBtn = target.closest("[data-action='delete-snap']");
			if (deleteBtn instanceof HTMLElement && snapsList.contains(deleteBtn)) {
				const figure = deleteBtn.closest("[data-snap-item]");
				if (figure instanceof HTMLElement) {
					window.Camagru.post.deleteSnap(figure, deleteBtn);
				}
				return;
			}

			const trigger = target.closest("[data-action='open-lightbox']");
			if (!(trigger instanceof HTMLElement) || !snapsList.contains(trigger)) {
				return;
			}
			const src = trigger.dataset.imageSrc || "";
			if (src !== "") {
				window.Camagru.post.openLightbox(src);
			}
		});
	}

	const lightbox = document.querySelector("[data-lightbox]");
	if (lightbox !== null) {
		lightbox.addEventListener("click", (event) => {
			const target = event.target;
			if (!(target instanceof Element)) {
				return;
			}
			if (target === lightbox || target.closest("[data-action='close-lightbox']") !== null) {
				window.Camagru.post.closeLightbox();
			}
		});
	}

	document.addEventListener("keydown", (event) => {
		if (event.key === "Escape" && lightbox instanceof HTMLElement && lightbox.dataset.open === "true") {
			window.Camagru.post.closeLightbox();
		}
	});

	const captureBtn = document.querySelector("[data-action='capture']");
	if (captureBtn !== null) {
		captureBtn.addEventListener("click", window.Camagru.post.captureSnap);
	}

	window.addEventListener("pagehide", window.Camagru.post.stopStream);
});

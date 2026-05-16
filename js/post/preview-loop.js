window.Camagru.post.startPreviewLoop = () => {
	const video = document.querySelector("[data-stage-video]");
	const canvas = document.querySelector("[data-stage-canvas]");
	if (!(video instanceof HTMLVideoElement) || !(canvas instanceof HTMLCanvasElement)) {
		return;
	}
	const ctx = canvas.getContext("2d");
	if (ctx === null) {
		return;
	}

	let sized = false;

	const tick = () => {
		if (!sized && video.videoWidth > 0 && video.videoHeight > 0) {
			canvas.width = video.videoWidth;
			canvas.height = video.videoHeight;
			sized = true;
		}
		if (sized) {
			ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
			const overlay = window.Camagru.post.state.overlayImage;
			if (overlay !== null && overlay.complete && overlay.naturalWidth > 0) {
				ctx.drawImage(overlay, 0, 0, canvas.width, canvas.height);
			}
		}
		window.Camagru.post.state.rafId = requestAnimationFrame(tick);
	};

	window.Camagru.post.state.rafId = requestAnimationFrame(tick);
};

window.Camagru.post.redrawUpload = () => {
	const canvas = document.querySelector("[data-stage-canvas]");
	const imageEl = document.querySelector("[data-stage-image]");
	if (!(canvas instanceof HTMLCanvasElement) || !(imageEl instanceof HTMLImageElement)) {
		return;
	}
	if (imageEl.naturalWidth === 0 || imageEl.naturalHeight === 0) {
		return;
	}
	const ctx = canvas.getContext("2d");
	if (ctx === null) {
		return;
	}
	canvas.width = imageEl.naturalWidth;
	canvas.height = imageEl.naturalHeight;
	ctx.drawImage(imageEl, 0, 0, canvas.width, canvas.height);
	const overlay = window.Camagru.post.state.overlayImage;
	if (overlay !== null && overlay.complete && overlay.naturalWidth > 0) {
		ctx.drawImage(overlay, 0, 0, canvas.width, canvas.height);
	}
};

window.Camagru.post.setOverlayImage = (path) => {
	if (path === null || path === "") {
		window.Camagru.post.state.overlayImage = null;
		if (window.Camagru.post.state.sourceMode === "image") {
			window.Camagru.post.redrawUpload();
		}
		return;
	}
	const img = new Image();
	img.addEventListener("load", () => {
		if (window.Camagru.post.state.sourceMode === "image") {
			window.Camagru.post.redrawUpload();
		}
	}, { once: true });
	img.src = path;
	window.Camagru.post.state.overlayImage = img;
};

window.Camagru.post.stopPreviewLoop = () => {
	if (window.Camagru.post.state.rafId !== null) {
		cancelAnimationFrame(window.Camagru.post.state.rafId);
		window.Camagru.post.state.rafId = null;
	}

	const canvas = document.querySelector("[data-stage-canvas]");
	if (canvas instanceof HTMLCanvasElement) {
		const ctx = canvas.getContext("2d");
		if (ctx !== null) {
			ctx.clearRect(0, 0, canvas.width, canvas.height);
		}
	}
};

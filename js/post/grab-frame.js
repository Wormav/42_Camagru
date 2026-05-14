window.Camagru.post.grabFrame = () =>
	new Promise((resolve) => {
		const source = window.Camagru.post.detectSource();
		if (source === null) {
			resolve(null);
			return;
		}

		const video = document.querySelector("[data-stage-video]");
		const imageEl = document.querySelector("[data-stage-image]");
		if (!(video instanceof HTMLVideoElement) || !(imageEl instanceof HTMLImageElement)) {
			resolve(null);
			return;
		}

		const width = source === "webcam" ? video.videoWidth : imageEl.naturalWidth;
		const height = source === "webcam" ? video.videoHeight : imageEl.naturalHeight;

		if (!width || !height) {
			resolve(null);
			return;
		}

		const canvas = document.createElement("canvas");
		canvas.width = width;
		canvas.height = height;

		const ctx = canvas.getContext("2d");
		if (ctx === null) {
			resolve(null);
			return;
		}
		ctx.drawImage(source === "webcam" ? video : imageEl, 0, 0, width, height);

		canvas.toBlob((blob) => resolve(blob), "image/jpeg", 0.92);
	});

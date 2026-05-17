window.Camagru.post.startWebcam = async () => {
	const video = document.querySelector("[data-stage-video]");
	if (!(video instanceof HTMLVideoElement)) {
		return;
	}

	if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== "function") {
		window.Camagru.post.showStatus("denied");
		return;
	}

	window.Camagru.post.showStatus("pending");

	try {
		const stream = await navigator.mediaDevices.getUserMedia({
			video: {
				facingMode: "user",
				width: { ideal: 1280 },
				height: { ideal: 720 },
				aspectRatio: { ideal: 16 / 9 },
			},
			audio: false,
		});

		window.Camagru.post.stopStream();
		window.Camagru.post.state.activeStream = stream;
		video.srcObject = stream;

		window.Camagru.post.state.sourceMode = "webcam";
		window.Camagru.post.showSource("webcam");
		window.Camagru.post.startPreviewLoop();
		window.Camagru.post.updateCaptureButton();
	} catch (error) {
		console.warn("[post] getUserMedia failed:", error);

		const name = error instanceof DOMException ? error.name : "";
		const labels = {
			NotAllowedError: "// camera access denied — check browser permissions",
			NotFoundError: "// no camera found — try upload instead",
			NotReadableError: "// camera busy — close other apps using it",
			OverconstrainedError: "// camera does not match constraints",
			SecurityError: "// camera blocked — page must be served over HTTPS",
			AbortError: "// camera start aborted — try again",
		};
		window.Camagru.post.showStatus("denied", labels[name] || "// camera unavailable — try upload instead");
	}
};

window.Camagru.post.captureSnap = async () => {
	const captureBtn = document.querySelector("[data-action='capture']");
	const selectedOverlayId = window.Camagru.post.state.selectedOverlayId;

	if (!(captureBtn instanceof HTMLButtonElement)) {
		return;
	}

	captureBtn.setAttribute("disabled", "");
	captureBtn.setAttribute("aria-disabled", "true");

	try {
		const blob = await window.Camagru.post.grabFrame();
		if (blob === null) {
			window.Camagru.shared.dispatchToast("No source ready — start the webcam or upload an image.", "error");
			return;
		}

		const formData = new FormData();
		formData.append("csrf_token", window.Camagru.shared.getCsrfToken());
		if (selectedOverlayId !== null) {
			formData.append("overlay_id", selectedOverlayId);
		}
		formData.append("snap", blob, "snap.jpg");

		const { response, payload } = await window.Camagru.shared.postForm("/post/capture", formData);

		if (!response.ok || payload.ok !== true) {
			const message =
				typeof payload.error === "string" ? payload.error : `Capture failed (${response.status}).`;
			window.Camagru.shared.dispatchToast(message, "error");
			return;
		}

		if (typeof payload.image_id === "number" && typeof payload.image_path === "string") {
			window.Camagru.post.prependSnap(payload.image_id, payload.image_path);
		}

		window.Camagru.shared.dispatchToast("Snap captured ✓");
	} catch (error) {
		console.warn("[post] capture failed:", error);
		window.Camagru.shared.dispatchToast("Network error — please try again.", "error");
	} finally {
		window.Camagru.post.updateCaptureButton();
	}
};

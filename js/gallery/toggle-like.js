window.Camagru.gallery.toggleLike = async (button) => {
	const imageId = Number.parseInt(button.dataset.imageId || "", 10);
	if (!Number.isFinite(imageId) || imageId <= 0) {
		return;
	}

	if (button.dataset.busy === "true") {
		return;
	}
	button.dataset.busy = "true";

	try {
		const formData = new FormData();
		formData.append("csrf_token", window.Camagru.shared.getCsrfToken());
		formData.append("image_id", String(imageId));

		const { response, payload } = await window.Camagru.shared.postForm("/likes/toggle", formData);

		if (response.status === 401) {
			window.location.href = "/login";
			return;
		}

		if (!response.ok || payload.ok !== true) {
			const message =
				typeof payload.error === "string" ? payload.error : `Toggle failed (${response.status}).`;
			window.Camagru.shared.dispatchToast(message, "error");
			return;
		}

		const liked = payload.liked === true;
		const count = typeof payload.like_count === "number" ? payload.like_count : 0;
		window.Camagru.gallery.applyLikeState(button, liked, count);
	} catch (error) {
		console.warn("[gallery] like toggle failed:", error);
		window.Camagru.shared.dispatchToast("Network error — please try again.", "error");
	} finally {
		button.dataset.busy = "false";
	}
};

window.Camagru.post.deleteSnap = async (figure, button) => {
	const imageId = Number.parseInt(figure.dataset.imageId || "", 10);
	if (Number.isNaN(imageId) || imageId <= 0) {
		return;
	}

	if (!window.confirm("Delete this snap? This cannot be undone.")) {
		return;
	}

	button.setAttribute("disabled", "");
	button.setAttribute("aria-disabled", "true");

	try {
		const formData = new FormData();
		formData.append("csrf_token", window.Camagru.shared.getCsrfToken());
		formData.append("image_id", String(imageId));

		const { response, payload } = await window.Camagru.shared.postForm("/post/delete", formData);

		if (!response.ok || payload.ok !== true) {
			const message =
				typeof payload.error === "string" ? payload.error : `Delete failed (${response.status}).`;
			window.Camagru.shared.dispatchToast(message, "error");
			button.removeAttribute("disabled");
			button.setAttribute("aria-disabled", "false");
			return;
		}

		window.Camagru.post.removeSnap(figure);
		window.Camagru.shared.dispatchToast("Snap deleted ✓");
	} catch (error) {
		console.warn("[post] delete failed:", error);
		window.Camagru.shared.dispatchToast("Network error — please try again.", "error");
		button.removeAttribute("disabled");
		button.setAttribute("aria-disabled", "false");
	}
};

window.Camagru.gallery.submitComment = async (form) => {
	if (form.dataset.busy === "true") {
		return;
	}
	form.dataset.busy = "true";

	const submitBtn = form.querySelector("button[type='submit']");
	if (submitBtn instanceof HTMLButtonElement) {
		submitBtn.setAttribute("disabled", "");
	}

	try {
		const formData = new FormData(form);
		formData.set("csrf_token", window.Camagru.shared.getCsrfToken());

		const { response, payload } = await window.Camagru.shared.postForm("/comments", formData);

		if (response.status === 401) {
			window.location.href = "/login";
			return;
		}

		if (!response.ok || payload.ok !== true) {
			const message =
				typeof payload.error === "string" ? payload.error : `Post failed (${response.status}).`;
			window.Camagru.shared.dispatchToast(message, "error");
			return;
		}

		const list = document.querySelector("[data-comments-list]");
		if (list !== null && payload.comment !== null && typeof payload.comment === "object") {
			const node = window.Camagru.gallery.buildCommentNode(
				payload.comment,
				payload.is_mine === true,
				window.Camagru.shared.getCsrfToken(),
			);
			if (node !== null) {
				list.appendChild(node);
			}
		}

		if (typeof payload.comment_count === "number") {
			window.Camagru.gallery.updateCommentCount(payload.comment_count);
		}

		window.Camagru.gallery.toggleEmptyState();
		form.reset();
		window.Camagru.shared.dispatchToast("Comment posted ✓");
	} catch (error) {
		console.warn("[gallery] comment submit failed:", error);
		window.Camagru.shared.dispatchToast("Network error — please try again.", "error");
	} finally {
		form.dataset.busy = "false";
		if (submitBtn instanceof HTMLButtonElement) {
			submitBtn.removeAttribute("disabled");
		}
	}
};

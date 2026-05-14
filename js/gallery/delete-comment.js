window.Camagru.gallery.deleteComment = async (form) => {
	if (form.dataset.busy === "true") {
		return;
	}
	if (!window.confirm("Delete this comment?")) {
		return;
	}
	form.dataset.busy = "true";

	try {
		const formData = new FormData(form);
		formData.set("csrf_token", window.Camagru.shared.getCsrfToken());

		const { response, payload } = await window.Camagru.shared.postForm("/comments/delete", formData);

		if (response.status === 401) {
			window.location.href = "/login";
			return;
		}

		if (!response.ok || payload.ok !== true) {
			const message =
				typeof payload.error === "string" ? payload.error : `Delete failed (${response.status}).`;
			window.Camagru.shared.dispatchToast(message, "error");
			form.dataset.busy = "false";
			return;
		}

		const li = form.closest("[data-comment-item]");
		if (li instanceof HTMLElement) {
			li.remove();
		}

		if (typeof payload.comment_count === "number") {
			window.Camagru.gallery.updateCommentCount(payload.comment_count);
		}

		window.Camagru.gallery.toggleEmptyState();
		window.Camagru.shared.dispatchToast("Comment deleted ✓");
	} catch (error) {
		console.warn("[gallery] comment delete failed:", error);
		window.Camagru.shared.dispatchToast("Network error — please try again.", "error");
		form.dataset.busy = "false";
	}
};

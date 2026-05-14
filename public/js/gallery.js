"use strict";
(() => {
	const grid = document.querySelector("[data-gallery-grid]");
	if (grid === null) {
		return;
	}

	const getCsrfToken = () => {
		const meta = document.querySelector("meta[name='csrf-token']");
		return meta instanceof HTMLMetaElement ? meta.content : "";
	};

	const toast = (message, kind = "success") => {
		document.dispatchEvent(
			new CustomEvent("toast:show", {
				detail: { message, kind },
			}),
		);
	};

	const applyLikeState = (button, liked, likeCount) => {
		button.dataset.liked = liked ? "true" : "false";
		button.setAttribute("aria-pressed", liked ? "true" : "false");
		button.setAttribute("aria-label", liked ? "Unlike" : "Like");

		button.classList.remove("bg-pink", "bg-paper", "hover:bg-pink");
		if (liked) {
			button.classList.add("bg-pink");
		} else {
			button.classList.add("bg-paper", "hover:bg-pink");
		}

		const icon = button.querySelector("[data-like-icon]");
		if (icon instanceof SVGElement) {
			icon.setAttribute("fill", liked ? "currentColor" : "none");
		}

		const countEl = button.querySelector("[data-like-count]");
		if (countEl !== null) {
			countEl.textContent = String(likeCount);
		}
	};

	const toggleLike = async (button) => {
		const imageId = Number.parseInt(button.dataset.imageId ?? "", 10);
		if (!Number.isFinite(imageId) || imageId <= 0) {
			return;
		}

		if (button.dataset.busy === "true") {
			return;
		}
		button.dataset.busy = "true";

		try {
			const formData = new FormData();
			formData.append("csrf_token", getCsrfToken());
			formData.append("image_id", String(imageId));

			const response = await fetch("/likes/toggle", {
				method: "POST",
				body: formData,
				credentials: "same-origin",
			});

			const payload = await response.json().catch(() => ({}));

			if (response.status === 401) {
				window.location.href = "/login";
				return;
			}

			if (!response.ok || payload.ok !== true) {
				const message =
					typeof payload.error === "string" ? payload.error : `Toggle failed (${response.status}).`;
				toast(message, "error");
				return;
			}

			const liked = payload.liked === true;
			const count = typeof payload.like_count === "number" ? payload.like_count : 0;
			applyLikeState(button, liked, count);
		} catch (error) {
			console.warn("[gallery] like toggle failed:", error);
			toast("Network error — please try again.", "error");
		} finally {
			button.dataset.busy = "false";
		}
	};

	grid.addEventListener("click", (event) => {
		const target = event.target;
		if (!(target instanceof Element)) {
			return;
		}
		const button = target.closest("[data-action='toggle-like']");
		if (!(button instanceof HTMLButtonElement) || !grid.contains(button)) {
			return;
		}
		event.preventDefault();
		toggleLike(button);
	});

	// --- Comments (AJAX) ---------------------------------------------------
	const commentsList = document.querySelector("[data-comments-list]");
	const commentsEmpty = document.querySelector("[data-comments-empty]");
	const commentForm = document.querySelector("[data-comment-form]");
	const commentTemplate = document.querySelector("[data-comment-template]");

	const updateCommentCount = (count) => {
		const value = String(Number.isFinite(count) ? count : 0);
		for (const el of document.querySelectorAll("[data-comment-count]")) {
			el.textContent = value;
		}
	};

	const toggleEmptyState = () => {
		if (commentsList === null || commentsEmpty === null) {
			return;
		}
		const hasComments = commentsList.querySelector("[data-comment-item]") !== null;
		commentsList.classList.toggle("hidden", !hasComments);
		commentsEmpty.classList.toggle("hidden", hasComments);
	};

	const buildCommentNode = (comment, isMine, csrfToken) => {
		if (!(commentTemplate instanceof HTMLTemplateElement)) {
			return null;
		}
		const fragment = commentTemplate.content.cloneNode(true);
		const li = fragment.querySelector("[data-comment-item]");
		if (!(li instanceof HTMLElement)) {
			return null;
		}
		li.dataset.commentId = String(comment.id);

		// Avatar: prefer image, else colored fallback with first letter.
		const avatarImg = fragment.querySelector("[data-tpl-avatar]");
		const avatarFallback = fragment.querySelector("[data-tpl-avatar-fallback]");
		const avatarPath = typeof comment.avatar_path === "string" ? comment.avatar_path : "";
		if (avatarPath !== "" && avatarImg instanceof HTMLImageElement && avatarFallback instanceof HTMLElement) {
			avatarImg.src = avatarPath;
			avatarImg.classList.remove("hidden");
			avatarFallback.remove();
		} else if (avatarFallback instanceof HTMLElement) {
			avatarFallback.textContent = (comment.username ?? "?").slice(0, 1);
			if (avatarImg instanceof HTMLImageElement) {
				avatarImg.remove();
			}
		}

		// Username (textContent prevents XSS).
		const usernameEl = fragment.querySelector("[data-tpl-username]");
		if (usernameEl !== null) {
			usernameEl.textContent = comment.username ?? "";
		}

		// Timestamp.
		const timeEl = fragment.querySelector("[data-tpl-time]");
		if (timeEl instanceof HTMLElement) {
			const iso = typeof comment.created_iso === "string" ? comment.created_iso : "";
			const human = typeof comment.created_human === "string" ? comment.created_human : "";
			if (iso !== "") {
				timeEl.setAttribute("datetime", iso);
			}
			timeEl.textContent = human;
		}

		// Content (textContent prevents XSS).
		const contentEl = fragment.querySelector("[data-tpl-content]");
		if (contentEl !== null) {
			contentEl.textContent = (comment.content ?? "").trim();
		}

		// Delete form: only kept if it's our own comment.
		const deleteForm = fragment.querySelector("[data-tpl-delete]");
		if (deleteForm instanceof HTMLFormElement) {
			if (isMine === true) {
				const csrfInput = deleteForm.querySelector("[data-tpl-csrf]");
				const idInput = deleteForm.querySelector("[data-tpl-comment-id]");
				if (csrfInput instanceof HTMLInputElement) {
					csrfInput.value = csrfToken;
				}
				if (idInput instanceof HTMLInputElement) {
					idInput.value = String(comment.id);
				}
			} else {
				deleteForm.remove();
			}
		}

		return li;
	};

	const submitComment = async (form) => {
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
			// Make sure CSRF token is fresh from meta (form may have stale one).
			formData.set("csrf_token", getCsrfToken());

			const response = await fetch("/comments", {
				method: "POST",
				body: formData,
				credentials: "same-origin",
				headers: { "X-Requested-With": "XMLHttpRequest" },
			});

			if (response.status === 401) {
				window.location.href = "/login";
				return;
			}

			const payload = await response.json().catch(() => ({}));

			if (!response.ok || payload.ok !== true) {
				const message =
					typeof payload.error === "string" ? payload.error : `Post failed (${response.status}).`;
				toast(message, "error");
				return;
			}

			if (commentsList !== null && payload.comment !== null && typeof payload.comment === "object") {
				const node = buildCommentNode(payload.comment, payload.is_mine === true, getCsrfToken());
				if (node !== null) {
					commentsList.appendChild(node);
				}
			}

			if (typeof payload.comment_count === "number") {
				updateCommentCount(payload.comment_count);
			}

			toggleEmptyState();

			form.reset();
			toast("Comment posted ✓");
		} catch (error) {
			console.warn("[gallery] comment submit failed:", error);
			toast("Network error — please try again.", "error");
		} finally {
			form.dataset.busy = "false";
			if (submitBtn instanceof HTMLButtonElement) {
				submitBtn.removeAttribute("disabled");
			}
		}
	};

	const deleteComment = async (form) => {
		if (form.dataset.busy === "true") {
			return;
		}
		if (!window.confirm("Delete this comment?")) {
			return;
		}
		form.dataset.busy = "true";

		try {
			const formData = new FormData(form);
			formData.set("csrf_token", getCsrfToken());

			const response = await fetch("/comments/delete", {
				method: "POST",
				body: formData,
				credentials: "same-origin",
				headers: { "X-Requested-With": "XMLHttpRequest" },
			});

			if (response.status === 401) {
				window.location.href = "/login";
				return;
			}

			const payload = await response.json().catch(() => ({}));

			if (!response.ok || payload.ok !== true) {
				const message =
					typeof payload.error === "string" ? payload.error : `Delete failed (${response.status}).`;
				toast(message, "error");
				form.dataset.busy = "false";
				return;
			}

			const li = form.closest("[data-comment-item]");
			if (li instanceof HTMLElement) {
				li.remove();
			}

			if (typeof payload.comment_count === "number") {
				updateCommentCount(payload.comment_count);
			}

			toggleEmptyState();
			toast("Comment deleted ✓");
		} catch (error) {
			console.warn("[gallery] comment delete failed:", error);
			toast("Network error — please try again.", "error");
			form.dataset.busy = "false";
		}
	};

	if (commentForm instanceof HTMLFormElement) {
		commentForm.addEventListener("submit", (event) => {
			event.preventDefault();
			submitComment(commentForm);
		});
	}

	if (commentsList !== null) {
		commentsList.addEventListener("submit", (event) => {
			const target = event.target;
			if (!(target instanceof HTMLFormElement)) {
				return;
			}
			if (target.matches("[data-delete-comment-form]")) {
				event.preventDefault();
				deleteComment(target);
			}
		});
	}
})();

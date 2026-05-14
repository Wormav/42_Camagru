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
})();

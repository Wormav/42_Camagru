(function () {
	"use strict";

	const DEFAULT_DURATION_MS = 4000;
	const TRANSITION_MS = 350;

	document.addEventListener("DOMContentLoaded", () => {
		const toasts = document.querySelectorAll("[data-toast]");
		toasts.forEach(setupToast);
	});

	function setupToast(toast) {
		toast.classList.add("is-entering");
		requestAnimationFrame(() => {
			requestAnimationFrame(() => {
				toast.classList.remove("is-entering");
			});
		});

		const durationAttr = toast.getAttribute("data-toast-duration");
		const duration = durationAttr ? parseInt(durationAttr, 10) : DEFAULT_DURATION_MS;
		const timer = window.setTimeout(() => dismiss(toast), duration);

		const closeBtn = toast.querySelector("[data-toast-close]");
		if (closeBtn) {
			closeBtn.addEventListener("click", () => {
				window.clearTimeout(timer);
				dismiss(toast);
			});
		}
	}

	function dismiss(toast) {
		toast.classList.add("is-leaving");
		window.setTimeout(() => toast.remove(), TRANSITION_MS + 50);
	}
})();

"use strict";

(() => {
	const DEFAULT_DURATION_MS = 4000;
	const TRANSITION_MS = 350;

	const CONTAINER_CLASSES =
		"fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-[calc(100%-3rem)] sm:w-auto pointer-events-none";

	document.addEventListener("DOMContentLoaded", () => {
		document.querySelectorAll("[data-toast]").forEach(setupToast);
	});

	document.addEventListener("toast:show", (event) => {
		const detail = event.detail ?? {};
		spawnToast(
			typeof detail.message === "string" ? detail.message : "",
			detail.kind === "error" ? "error" : "success",
			typeof detail.duration === "number" ? detail.duration : undefined,
		);
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

	function spawnToast(message, kind, duration) {
		if (message === "") {
			return;
		}

		const container = getOrCreateContainer();
		const toast = document.createElement("div");
		toast.className = "toast " + (kind === "error" ? "toast--error" : "toast--success");
		toast.dataset.toast = "";
		toast.setAttribute("role", kind === "error" ? "alert" : "status");
		if (typeof duration === "number") {
			toast.dataset.toastDuration = String(duration);
		} else if (kind === "error") {
			toast.dataset.toastDuration = "6000";
		}

		const span = document.createElement("span");
		span.className = "flex-1";
		span.textContent = message;

		const closeBtn = document.createElement("button");
		closeBtn.type = "button";
		closeBtn.className = "toast__close";
		closeBtn.dataset.toastClose = "";
		closeBtn.setAttribute("aria-label", "Dismiss");
		closeBtn.textContent = "×";

		toast.append(span, closeBtn);
		container.append(toast);
		setupToast(toast);
	}

	function getOrCreateContainer() {
		const existing = document.querySelector("[data-toast-container]");
		if (existing instanceof HTMLElement) {
			return existing;
		}
		const layoutContainer = document.querySelector(".fixed.bottom-6.right-6.z-50");
		if (layoutContainer instanceof HTMLElement) {
			layoutContainer.dataset.toastContainer = "";
			return layoutContainer;
		}
		const created = document.createElement("div");
		created.className = CONTAINER_CLASSES;
		created.dataset.toastContainer = "";
		document.body.append(created);
		return created;
	}
})();

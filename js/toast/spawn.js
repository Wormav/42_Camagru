window.Camagru.toast.spawn = (message, kind, duration) => {
	if (message === "") {
		return;
	}

	const container = window.Camagru.toast.getOrCreateContainer();
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
	window.Camagru.toast.setup(toast);
};

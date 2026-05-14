window.Camagru.toast.setup = (toast) => {
	toast.classList.add("is-entering");
	requestAnimationFrame(() => {
		requestAnimationFrame(() => {
			toast.classList.remove("is-entering");
		});
	});

	const durationAttr = toast.getAttribute("data-toast-duration");
	const duration = durationAttr ? parseInt(durationAttr, 10) : window.Camagru.toast.DEFAULT_DURATION_MS;
	const timer = window.setTimeout(() => window.Camagru.toast.dismiss(toast), duration);

	const closeBtn = toast.querySelector("[data-toast-close]");
	if (closeBtn) {
		closeBtn.addEventListener("click", () => {
			window.clearTimeout(timer);
			window.Camagru.toast.dismiss(toast);
		});
	}
};

document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll("[data-toast]").forEach(window.Camagru.toast.setup);
});

document.addEventListener("toast:show", (event) => {
	const detail = event.detail || {};
	window.Camagru.toast.spawn(
		typeof detail.message === "string" ? detail.message : "",
		detail.kind === "error" ? "error" : "success",
		typeof detail.duration === "number" ? detail.duration : undefined,
	);
});

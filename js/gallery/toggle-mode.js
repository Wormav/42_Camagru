window.Camagru.gallery.GALLERY_MODE_KEY = "camagru.galleryMode";

window.Camagru.gallery.getGalleryMode = () => {
	try {
		const stored = localStorage.getItem(window.Camagru.gallery.GALLERY_MODE_KEY);
		return stored === "infinite" ? "infinite" : "pagination";
	} catch (error) {
		return "pagination";
	}
};

window.Camagru.gallery.setGalleryMode = (mode) => {
	try {
		localStorage.setItem(window.Camagru.gallery.GALLERY_MODE_KEY, mode);
	} catch (error) {}
};

window.Camagru.gallery.applyGalleryMode = () => {
	const toggle = document.querySelector("[data-mode-toggle]");
	if (!(toggle instanceof HTMLElement)) {
		return;
	}

	const mode = window.Camagru.gallery.getGalleryMode();

	for (const option of toggle.querySelectorAll("[data-mode-option]")) {
		const isActive = option.getAttribute("data-mode-option") === mode;
		option.setAttribute("data-active", isActive ? "true" : "false");
		option.setAttribute("aria-pressed", isActive ? "true" : "false");
	}

	if (mode === "infinite") {
		window.Camagru.gallery.startInfiniteScroll();
	}
};

window.Camagru.gallery.handleModeClick = (button) => {
	const next = button.getAttribute("data-mode-option");
	if (next !== "pagination" && next !== "infinite") {
		return;
	}
	const current = window.Camagru.gallery.getGalleryMode();
	if (next === current) {
		return;
	}
	window.Camagru.gallery.setGalleryMode(next);
	window.location.assign("/gallery");
};

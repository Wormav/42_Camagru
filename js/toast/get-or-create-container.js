window.Camagru.toast.getOrCreateContainer = () => {
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
	created.className = window.Camagru.toast.CONTAINER_CLASSES;
	created.dataset.toastContainer = "";
	document.body.append(created);
	return created;
};

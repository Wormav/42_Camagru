window.Camagru.toast.dismiss = (toast) => {
	toast.classList.add("is-leaving");
	window.setTimeout(() => toast.remove(), window.Camagru.toast.TRANSITION_MS + 50);
};

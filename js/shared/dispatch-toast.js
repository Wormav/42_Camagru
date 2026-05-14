window.Camagru.shared.dispatchToast = (message, kind = "success") => {
	document.dispatchEvent(
		new CustomEvent("toast:show", {
			detail: { message, kind },
		}),
	);
};

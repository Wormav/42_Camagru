window.Camagru.shared.getCsrfToken = () => {
	const meta = document.querySelector("meta[name='csrf-token']");
	return meta instanceof HTMLMetaElement ? meta.content : "";
};

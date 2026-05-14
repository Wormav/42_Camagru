window.Camagru.post.closeLightbox = () => {
	const lightbox = document.querySelector("[data-lightbox]");
	const lightboxImage = document.querySelector("[data-lightbox-image]");

	if (!(lightbox instanceof HTMLElement) || !(lightboxImage instanceof HTMLImageElement)) {
		return;
	}
	lightbox.dataset.open = "false";
	lightbox.classList.add("hidden");
	lightbox.classList.remove("flex");
	lightboxImage.src = "";
};

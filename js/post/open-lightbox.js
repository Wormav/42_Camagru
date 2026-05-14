window.Camagru.post.openLightbox = (imageSrc) => {
	const lightbox = document.querySelector("[data-lightbox]");
	const lightboxImage = document.querySelector("[data-lightbox-image]");

	if (!(lightbox instanceof HTMLElement) || !(lightboxImage instanceof HTMLImageElement)) {
		return;
	}
	lightboxImage.src = imageSrc;
	lightbox.dataset.open = "true";
	lightbox.classList.remove("hidden");
	lightbox.classList.add("flex");
};

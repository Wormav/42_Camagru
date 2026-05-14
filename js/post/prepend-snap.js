window.Camagru.post.prependSnap = (imageId, imagePath) => {
	const snapsList = document.querySelector("[data-snaps-list]");
	const snapsEmpty = document.querySelector("[data-snaps-empty]");
	const snapsCount = document.querySelector("[data-snaps-count]");
	const snapTemplate = document.querySelector("[data-snap-template]");

	if (snapsList === null || !(snapTemplate instanceof HTMLTemplateElement)) {
		return;
	}

	const fragment = snapTemplate.content.cloneNode(true);
	const figure = fragment.querySelector("[data-snap-item]");
	const thumbBtn = fragment.querySelector("[data-action='open-lightbox']");
	const img = fragment.querySelector("img");

	if (figure instanceof HTMLElement) {
		figure.dataset.imageId = String(imageId);
	}
	if (thumbBtn instanceof HTMLElement) {
		thumbBtn.dataset.imageSrc = imagePath;
	}
	if (img instanceof HTMLImageElement) {
		img.src = imagePath;
	}

	snapsList.prepend(fragment);
	snapsList.classList.remove("hidden");

	if (snapsEmpty !== null) {
		snapsEmpty.classList.add("hidden");
	}

	if (snapsCount !== null) {
		const current = Number.parseInt(snapsCount.textContent || "0", 10);
		snapsCount.textContent = String(Number.isNaN(current) ? 1 : current + 1);
	}
};

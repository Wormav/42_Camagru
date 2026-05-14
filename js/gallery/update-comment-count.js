window.Camagru.gallery.updateCommentCount = (count) => {
	const value = String(Number.isFinite(count) ? count : 0);
	for (const el of document.querySelectorAll("[data-comment-count]")) {
		el.textContent = value;
	}
};

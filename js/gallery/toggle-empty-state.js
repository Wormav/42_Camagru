window.Camagru.gallery.toggleEmptyState = () => {
	const list = document.querySelector("[data-comments-list]");
	const empty = document.querySelector("[data-comments-empty]");
	if (list === null || empty === null) {
		return;
	}
	const hasComments = list.querySelector("[data-comment-item]") !== null;
	list.classList.toggle("hidden", !hasComments);
	empty.classList.toggle("hidden", hasComments);
};

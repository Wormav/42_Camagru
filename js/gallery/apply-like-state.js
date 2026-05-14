window.Camagru.gallery.applyLikeState = (button, liked, likeCount) => {
	button.dataset.liked = liked ? "true" : "false";
	button.setAttribute("aria-pressed", liked ? "true" : "false");
	button.setAttribute("aria-label", liked ? "Unlike" : "Like");

	button.classList.remove("bg-pink", "bg-paper", "hover:bg-pink");
	if (liked) {
		button.classList.add("bg-pink");
	} else {
		button.classList.add("bg-paper", "hover:bg-pink");
	}

	const icon = button.querySelector("[data-like-icon]");
	if (icon instanceof SVGElement) {
		icon.setAttribute("fill", liked ? "currentColor" : "none");
	}

	const countEl = button.querySelector("[data-like-count]");
	if (countEl !== null) {
		countEl.textContent = String(likeCount);
	}
};

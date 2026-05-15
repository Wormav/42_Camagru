document.addEventListener("DOMContentLoaded", () => {
	const grid = document.querySelector("[data-gallery-grid]");
	if (grid !== null) {
		grid.addEventListener("click", (event) => {
			const target = event.target;
			if (!(target instanceof Element)) {
				return;
			}

			const likeBtn = target.closest("[data-action='toggle-like']");
			if (likeBtn instanceof HTMLButtonElement && grid.contains(likeBtn)) {
				event.preventDefault();
				window.Camagru.gallery.toggleLike(likeBtn);
				return;
			}

			const copyBtn = target.closest("[data-action='copy-share-link']");
			if (copyBtn instanceof HTMLButtonElement && grid.contains(copyBtn)) {
				event.preventDefault();
				window.Camagru.gallery.copyShareLink(copyBtn);
				return;
			}
		});
	}

	const commentForm = document.querySelector("[data-comment-form]");
	if (commentForm instanceof HTMLFormElement) {
		commentForm.addEventListener("submit", (event) => {
			event.preventDefault();
			window.Camagru.gallery.submitComment(commentForm);
		});
	}

	const commentsList = document.querySelector("[data-comments-list]");
	if (commentsList !== null) {
		commentsList.addEventListener("submit", (event) => {
			const target = event.target;
			if (!(target instanceof HTMLFormElement)) {
				return;
			}
			if (target.matches("[data-delete-comment-form]")) {
				event.preventDefault();
				window.Camagru.gallery.deleteComment(target);
			}
		});
	}
});

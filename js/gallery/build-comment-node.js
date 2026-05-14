window.Camagru.gallery.buildCommentNode = (comment, isMine, csrfToken) => {
	const template = document.querySelector("[data-comment-template]");
	if (!(template instanceof HTMLTemplateElement)) {
		return null;
	}
	const fragment = template.content.cloneNode(true);
	const li = fragment.querySelector("[data-comment-item]");
	if (!(li instanceof HTMLElement)) {
		return null;
	}
	li.dataset.commentId = String(comment.id);

	const avatarImg = fragment.querySelector("[data-tpl-avatar]");
	const avatarFallback = fragment.querySelector("[data-tpl-avatar-fallback]");
	const avatarPath = typeof comment.avatar_path === "string" ? comment.avatar_path : "";
	if (avatarPath !== "" && avatarImg instanceof HTMLImageElement && avatarFallback instanceof HTMLElement) {
		avatarImg.src = avatarPath;
		avatarImg.classList.remove("hidden");
		avatarFallback.remove();
	} else if (avatarFallback instanceof HTMLElement) {
		avatarFallback.textContent = (comment.username || "?").slice(0, 1);
		if (avatarImg instanceof HTMLImageElement) {
			avatarImg.remove();
		}
	}

	const usernameEl = fragment.querySelector("[data-tpl-username]");
	if (usernameEl !== null) {
		usernameEl.textContent = comment.username || "";
	}

	const timeEl = fragment.querySelector("[data-tpl-time]");
	if (timeEl instanceof HTMLElement) {
		const iso = typeof comment.created_iso === "string" ? comment.created_iso : "";
		const human = typeof comment.created_human === "string" ? comment.created_human : "";
		if (iso !== "") {
			timeEl.setAttribute("datetime", iso);
		}
		timeEl.textContent = human;
	}

	const contentEl = fragment.querySelector("[data-tpl-content]");
	if (contentEl !== null) {
		contentEl.textContent = (comment.content || "").trim();
	}

	const deleteForm = fragment.querySelector("[data-tpl-delete]");
	if (deleteForm instanceof HTMLFormElement) {
		if (isMine === true) {
			const csrfInput = deleteForm.querySelector("[data-tpl-csrf]");
			const idInput = deleteForm.querySelector("[data-tpl-comment-id]");
			if (csrfInput instanceof HTMLInputElement) {
				csrfInput.value = csrfToken;
			}
			if (idInput instanceof HTMLInputElement) {
				idInput.value = String(comment.id);
			}
		} else {
			deleteForm.remove();
		}
	}

	return li;
};

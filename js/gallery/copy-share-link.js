window.Camagru.gallery.copyShareLink = async (button) => {
	const link = button.dataset.shareLink || "";
	if (link === "") {
		return;
	}

	try {
		if (navigator.clipboard && typeof navigator.clipboard.writeText === "function") {
			await navigator.clipboard.writeText(link);
		} else {
			const helper = document.createElement("textarea");
			helper.value = link;
			helper.setAttribute("readonly", "");
			helper.style.position = "fixed";
			helper.style.opacity = "0";
			document.body.appendChild(helper);
			helper.select();
			document.execCommand("copy");
			document.body.removeChild(helper);
		}
		window.Camagru.shared.dispatchToast("Link copied to clipboard ✓");
	} catch (error) {
		console.warn("[gallery] clipboard copy failed:", error);
		window.Camagru.shared.dispatchToast("Could not copy — copy it manually.", "error");
	}
};

window.Camagru.post.removeSnap = (figure) => {
	const snapsList = document.querySelector("[data-snaps-list]");
	const snapsEmpty = document.querySelector("[data-snaps-empty]");
	const snapsCount = document.querySelector("[data-snaps-count]");

	figure.remove();

	if (snapsCount !== null) {
		const current = Number.parseInt(snapsCount.textContent || "0", 10);
		const next = Number.isNaN(current) ? 0 : Math.max(0, current - 1);
		snapsCount.textContent = String(next);

		if (next === 0) {
			if (snapsList !== null) {
				snapsList.classList.add("hidden");
			}
			if (snapsEmpty !== null) {
				snapsEmpty.classList.remove("hidden");
			}
		}
	}
};

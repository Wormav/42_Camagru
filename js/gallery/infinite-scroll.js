window.Camagru.gallery.startInfiniteScroll = () => {
	const grid = document.querySelector("[data-gallery-grid]");
	const sentinel = document.querySelector("[data-infinite-sentinel]");
	const status = document.querySelector("[data-infinite-status]");
	const loader = document.querySelector("[data-infinite-loader]");
	const message = document.querySelector("[data-infinite-message]");
	const paginationNav = document.querySelector("[data-pagination-nav]");

	if (
		!(grid instanceof Element) ||
		!(sentinel instanceof Element) ||
		!(status instanceof Element) ||
		!(loader instanceof Element) ||
		!(message instanceof Element)
	) {
		return;
	}

	if (paginationNav instanceof HTMLElement) {
		paginationNav.classList.add("hidden");
	}

	sentinel.classList.remove("hidden");
	status.classList.remove("hidden");
	status.classList.add("flex", "flex-col", "items-center", "gap-3");

	const totalPages = parseInt(grid.dataset.totalPages || "1", 10);
	const state = {
		currentPage: parseInt(grid.dataset.currentPage || "1", 10),
		totalPages: Number.isFinite(totalPages) ? totalPages : 1,
		loading: false,
		done: false,
	};

	if (state.currentPage >= state.totalPages) {
		state.done = true;
		loader.classList.add("hidden");
		message.textContent = "// no more snaps to load.";
		sentinel.classList.add("hidden");
		return;
	}

	const fetchNext = async () => {
		if (state.loading || state.done) {
			return;
		}
		state.loading = true;
		loader.classList.remove("hidden");
		message.textContent = "// loading more snaps…";

		try {
			const nextPage = state.currentPage + 1;
			const response = await fetch("/gallery/feed?page=" + nextPage, {
				headers: { "Accept": "application/json" },
				credentials: "same-origin",
			});
			if (!response.ok) {
				throw new Error("HTTP " + response.status);
			}
			const payload = await response.json();
			if (payload.ok !== true) {
				throw new Error("Bad payload");
			}

			if (typeof payload.html === "string" && payload.html !== "") {
				grid.insertAdjacentHTML("beforeend", payload.html);
			}

			state.currentPage = nextPage;

			if (payload.hasMore === false) {
				state.done = true;
				loader.classList.add("hidden");
				message.textContent = "// no more snaps to load.";
				sentinel.classList.add("hidden");
				observer.disconnect();
			} else {
				loader.classList.add("hidden");
				message.textContent = "// scroll for more…";
			}
		} catch (error) {
			console.warn("[gallery] infinite scroll fetch failed:", error);
			loader.classList.add("hidden");
			message.textContent = "// could not load more — scroll to retry.";
		} finally {
			state.loading = false;
		}
	};

	const observer = new IntersectionObserver(
		(entries) => {
			for (const entry of entries) {
				if (entry.isIntersecting) {
					fetchNext();
				}
			}
		},
		{ rootMargin: "300px 0px" },
	);

	observer.observe(sentinel);
};

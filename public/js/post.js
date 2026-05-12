"use strict";
(() => {
	const video = document.querySelector("[data-stage-video]");
	const imageEl = document.querySelector("[data-stage-image]");
	const status = document.querySelector("[data-stage-status]");
	const label = document.querySelector("[data-stage-status-label]");
	const webcamBtn = document.querySelector("[data-action='use-webcam']");
	const fileInput = document.querySelector("[data-action='use-upload']");
	const overlayList = document.querySelector("[data-overlay-list]");
	const captureBtn = document.querySelector("[data-action='capture']");

	const snapsList = document.querySelector("[data-snaps-list]");
	const snapsEmpty = document.querySelector("[data-snaps-empty]");
	const snapsCount = document.querySelector("[data-snaps-count]");
	const snapTemplate = document.querySelector("[data-snap-template]");
	const lightbox = document.querySelector("[data-lightbox]");
	const lightboxImage = document.querySelector("[data-lightbox-image]");

	if (!video || !imageEl || !status || !label) {
		return;
	}

	let selectedOverlayId = null;

	const ACCEPTED_MIMES = ["image/jpeg", "image/png"];
	const MAX_UPLOAD_BYTES = 10 * 1024 * 1024; // 10 MB

	const STATUS_LABEL = {
		idle: "// webcam preview goes here",
		pending: "// requesting camera access…",
		denied: "// camera access denied — try upload instead",
		ready: "",
	};

	let activeStream = null;

	const hideAllSources = () => {
		video.classList.add("hidden");
		imageEl.classList.add("hidden");
	};

	const showStatus = (name, customLabel = null) => {
		status.dataset.stageState = name;
		label.textContent = customLabel ?? STATUS_LABEL[name] ?? "";
		hideAllSources();
		status.classList.remove("hidden");
	};

	const showSource = (source) => {
		status.dataset.stageState = "ready";
		status.classList.add("hidden");
		hideAllSources();

		if (source === "webcam") {
			video.classList.remove("hidden");
		} else if (source === "image") {
			imageEl.classList.remove("hidden");
		}
	};

	const stopActiveStream = () => {
		if (activeStream === null) {
			return;
		}
		for (const track of activeStream.getTracks()) {
			track.stop();
		}
		activeStream = null;
		video.srcObject = null;
	};

	const startWebcam = async () => {
		if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== "function") {
			showStatus("denied");
			return;
		}

		showStatus("pending");

		try {
			const stream = await navigator.mediaDevices.getUserMedia({
				video: { facingMode: "user" },
				audio: false,
			});

			stopActiveStream();
			activeStream = stream;
			video.srcObject = stream;

			showSource("webcam");
		} catch (error) {
			console.warn("[post] getUserMedia failed:", error);

			const name = error instanceof DOMException ? error.name : "";
			const labels = {
				NotAllowedError: "// camera access denied — check browser permissions",
				NotFoundError: "// no camera found — try upload instead",
				NotReadableError: "// camera busy — close other apps using it",
				OverconstrainedError: "// camera does not match constraints",
				SecurityError: "// camera blocked — page must be served over HTTPS",
				AbortError: "// camera start aborted — try again",
			};
			showStatus("denied", labels[name] ?? "// camera unavailable — try upload instead");
		}
	};

	const loadUploadedImage = (file) => {
		if (!ACCEPTED_MIMES.includes(file.type)) {
			showStatus("denied", "// unsupported format — pick JPEG or PNG");
			return;
		}
		if (file.size > MAX_UPLOAD_BYTES) {
			showStatus("denied", "// image too large — 10MB max");
			return;
		}

		const reader = new FileReader();

		reader.addEventListener("load", () => {
			stopActiveStream();
			imageEl.src = typeof reader.result === "string" ? reader.result : "";
			showSource("image");
		});

		reader.addEventListener("error", () => {
			showStatus("denied", "// could not read the file — try another one");
		});

		reader.readAsDataURL(file);
	};

	if (webcamBtn !== null) {
		webcamBtn.addEventListener("click", startWebcam);
	}

	if (fileInput !== null) {
		fileInput.addEventListener("change", (event) => {
			const target = event.target;
			if (!(target instanceof HTMLInputElement)) {
				return;
			}
			const file = target.files?.[0];
			if (!file) {
				return;
			}
			loadUploadedImage(file);
			target.value = "";
		});
	}

	const updateCaptureButton = () => {
		if (captureBtn === null) {
			return;
		}
		if (selectedOverlayId === null) {
			captureBtn.setAttribute("disabled", "");
			captureBtn.setAttribute("aria-disabled", "true");
		} else {
			captureBtn.removeAttribute("disabled");
			captureBtn.setAttribute("aria-disabled", "false");
		}
	};

	const toggleOverlay = (tile) => {
		const isActive = tile.getAttribute("aria-pressed") === "true";

		if (overlayList !== null) {
			for (const other of overlayList.querySelectorAll("[aria-pressed='true']")) {
				other.setAttribute("aria-pressed", "false");
			}
		}

		if (isActive) {
			selectedOverlayId = null;
		} else {
			tile.setAttribute("aria-pressed", "true");
			selectedOverlayId = tile.dataset.overlayId ?? null;
		}

		updateCaptureButton();
	};

	if (overlayList !== null) {
		overlayList.addEventListener("click", (event) => {
			const target = event.target;
			if (!(target instanceof Element)) {
				return;
			}
			const tile = target.closest("[data-overlay-id]");
			if (tile === null || !overlayList.contains(tile)) {
				return;
			}
			toggleOverlay(tile);
		});
	}
	const getCsrfToken = () => {
		const meta = document.querySelector("meta[name='csrf-token']");
		return meta instanceof HTMLMetaElement ? meta.content : "";
	};

	const detectCurrentSource = () => {
		if (!video.classList.contains("hidden") && activeStream !== null) {
			return "webcam";
		}
		if (!imageEl.classList.contains("hidden") && imageEl.src !== "") {
			return "image";
		}
		return null;
	};

	const grabCurrentFrame = () =>
		new Promise((resolve) => {
			const source = detectCurrentSource();

			if (source === null) {
				resolve(null);
				return;
			}

			const width = source === "webcam" ? video.videoWidth : imageEl.naturalWidth;
			const height = source === "webcam" ? video.videoHeight : imageEl.naturalHeight;

			if (!width || !height) {
				resolve(null);
				return;
			}

			const canvas = document.createElement("canvas");
			canvas.width = width;
			canvas.height = height;

			const ctx = canvas.getContext("2d");
			if (ctx === null) {
				resolve(null);
				return;
			}
			ctx.drawImage(source === "webcam" ? video : imageEl, 0, 0, width, height);

			canvas.toBlob((blob) => resolve(blob), "image/jpeg", 0.92);
		});

	const toast = (message, kind = "success") => {
		document.dispatchEvent(
			new CustomEvent("toast:show", {
				detail: { message, kind },
			}),
		);
	};

	const captureSnap = async () => {
		if (selectedOverlayId === null || captureBtn === null) {
			return;
		}

		captureBtn.setAttribute("disabled", "");
		captureBtn.setAttribute("aria-disabled", "true");

		try {
			const blob = await grabCurrentFrame();
			if (blob === null) {
				toast("No source ready — start the webcam or upload an image.", "error");
				return;
			}

			const formData = new FormData();
			formData.append("csrf_token", getCsrfToken());
			formData.append("overlay_id", selectedOverlayId);
			formData.append("snap", blob, "snap.jpg");

			const response = await fetch("/post/capture", {
				method: "POST",
				body: formData,
				credentials: "same-origin",
			});

			const payload = await response.json().catch(() => ({}));

			if (!response.ok || payload.ok !== true) {
				const message =
					typeof payload.error === "string" ? payload.error : `Capture failed (${response.status}).`;
				toast(message, "error");
				return;
			}

			if (typeof payload.image_id === "number" && typeof payload.image_path === "string") {
				prependSnap(payload.image_id, payload.image_path);
			}

			toast("Snap captured ✓");
		} catch (error) {
			console.warn("[post] capture failed:", error);
			toast("Network error — please try again.", "error");
		} finally {
			updateCaptureButton();
		}
	};

	if (captureBtn !== null) {
		captureBtn.addEventListener("click", captureSnap);
	}

	window.addEventListener("pagehide", stopActiveStream);
})();

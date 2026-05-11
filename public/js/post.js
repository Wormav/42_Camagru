"use strict";
(() => {
	const video = document.querySelector("[data-stage-video]");
	const imageEl = document.querySelector("[data-stage-image]");
	const status = document.querySelector("[data-stage-status]");
	const label = document.querySelector("[data-stage-status-label]");
	const webcamBtn = document.querySelector("[data-action='use-webcam']");
	const fileInput = document.querySelector("[data-action='use-upload']");

	if (!video || !imageEl || !status || !label) {
		return;
	}

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
		} catch (_error) {
			showStatus("denied");
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

	window.addEventListener("pagehide", stopActiveStream);
})();

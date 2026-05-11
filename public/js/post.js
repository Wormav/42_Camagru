"use strict";

(() => {
	const video = document.querySelector("[data-stage-video]");
	const status = document.querySelector("[data-stage-status]");
	const label = document.querySelector("[data-stage-status-label]");
	const webcamBtn = document.querySelector("[data-action='use-webcam']");

	if (!video || !status || !label) {
		return;
	}

	const STATUS_LABEL = {
		idle: "// webcam preview goes here",
		pending: "// requesting camera access…",
		denied: "// camera access denied — try upload instead",
		ready: "",
	};

	let activeStream = null;

	const setStageState = (name) => {
		status.dataset.stageState = name;
		label.textContent = STATUS_LABEL[name] ?? "";

		if (name === "ready") {
			status.classList.add("hidden");
			video.classList.remove("hidden");
		} else {
			status.classList.remove("hidden");
			video.classList.add("hidden");
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
			setStageState("denied");
			return;
		}

		setStageState("pending");

		try {
			const stream = await navigator.mediaDevices.getUserMedia({
				video: { facingMode: "user" },
				audio: false,
			});

			stopActiveStream();
			activeStream = stream;
			video.srcObject = stream;

			setStageState("ready");
		} catch (_error) {
			setStageState("denied");
		}
	};

	if (webcamBtn !== null) {
		webcamBtn.addEventListener("click", startWebcam);
	}

	window.addEventListener("pagehide", stopActiveStream);
})();

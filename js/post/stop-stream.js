window.Camagru.post.stopStream = () => {
	window.Camagru.post.stopPreviewLoop();

	const stream = window.Camagru.post.state.activeStream;
	if (stream === null) {
		return;
	}
	for (const track of stream.getTracks()) {
		track.stop();
	}
	window.Camagru.post.state.activeStream = null;

	const video = document.querySelector("[data-stage-video]");
	if (video instanceof HTMLVideoElement) {
		video.srcObject = null;
	}
};

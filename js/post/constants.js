window.Camagru.post.ACCEPTED_MIMES = ["image/jpeg", "image/png"];
window.Camagru.post.MAX_UPLOAD_BYTES = 10 * 1024 * 1024;

window.Camagru.post.STATUS_LABEL = {
	idle: "// webcam preview goes here",
	pending: "// requesting camera access…",
	denied: "// camera access denied — try upload instead",
	ready: "",
};

window.Camagru.post.state = {
	selectedOverlayId: null,
	activeStream: null,
};

function switchMode(current) {
	const d = new Date();
	var cvalue = "";

	d.setTime(d.getTime() + 10 * 365 * 24 * 60 * 60 * 1000);
	let expires = "expires=" + d.toUTCString();

	if (current == "GESP") {
		cvalue = "Specify";
	} else {
		cvalue = "GESP";
	}

	document.cookie = "mode=" + cvalue + ";" + expires + ";path=/";
	location.reload();
}

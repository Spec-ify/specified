//Themes. Could be handled in a better, more efficient way but it does it's job.
// IMPORTANT: the variables are intialized and set to *something* before running the script
let themeBody = null;
let themeTextBox = null;

const localStorageTheme = window.localStorage.getItem("theme");
if (localStorageTheme !== null) {
    document.querySelector(`#mode-toggle option[value="${localStorageTheme}"]`).setAttribute("selected", "");
    change_theme();
}

// Call the function every time it changes
document.querySelector('#mode-toggle').onchange = change_theme;

function change_theme(){
    // Get selection for the switch
    let theme = document.querySelector('#mode-toggle').value;
    let themeables = document.querySelectorAll('.textbox, .searchbar, .widget, #header-header');
    let html = document.getElementsByTagName("HTML")[0];

	// Remove current theme
	document.body.classList.remove(themeBody);
	html.classList.remove(themeBody);
	themeables.forEach((e) => e.classList.remove(themeTextBox));

	if (theme !== "classic") {
		switch (theme) {
			case "light":
				themeBody = "LightModeBody";
				themeTextBox = "LightModeTextbox";
				break;

			case "k9":
				themeBody = "K9ModeBody";
				themeTextBox = "K9ModeTextbox";
				break;
		}

		// Set the theme
		document.body.classList.add(themeBody);
		html.classList.add(themeBody);
		themeables.forEach((e) => e.classList.add(themeTextBox));
	}

    localStorage.setItem("theme", theme);
}

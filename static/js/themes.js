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

    document.documentElement.setAttribute("data-theme", theme);
    localStorage.setItem("theme", theme);
}

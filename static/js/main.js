//These are very rudimentary ways of implementing quick pagination of elements without needing any complicated setups.

// Really bad code - K97i
// Thanks K9 - K97i

var buttonlist = document.getElementsByTagName("li");

for (let i in buttonlist) {
	console.log(buttonlist[i]);
	if (!(typeof buttonlist[i] === "object")) continue;
	buttonlist[i].addEventListener("click", () => {
		showtab(buttonlist[i].firstChild.nodeValue.toLowerCase());
	});
}

function showtab(seltab) {
	// List of all tabs
	const tabs = ["pups", "notes", "variables", "browsers"];
	// Removes selected tab
	const hiddentabs = tabs.filter((val) => val !== seltab);
	for (const tab in hiddentabs) {
		document.getElementById(hiddentabs[tab]).style.display = "none";
	}
	document.getElementById(seltab).style.display = "block";
}

// This is extremely jank, but it works! - K9
document.getElementById("CollapseToggle").addEventListener("click", () => {
	document.getElementById("CollapseToggle").style.display = "none";
	document.getElementById("CollapseToggleHide").style.display = "inline";
	var accordions = document.getElementsByClassName("accordion-collapse");

	for (var i = 0; i < accordions.length; i++) {
		accordions[i].className = accordions[i].className + " show";
	}
});

// Double it and give it to the next person
document.getElementById("CollapseToggleHide").addEventListener("click", () => {
	document.getElementById("CollapseToggle").style.display = "inline";
	document.getElementById("CollapseToggleHide").style.display = "none";
	var accordions = document.getElementsByClassName("accordion-collapse");

	for (var i = 0; i < accordions.length; i++) {
		accordions[i].className = accordions[i].className.replace(" show", "");
	}
});

//Snippets like these allow for the screen to scroll and follow the expansion caused by collapsing accordion items.

document.getElementById("devicesTableButton").addEventListener("click", () => {
	document.getElementById("devices").scrollIntoView({
		behavior: "smooth",
		block: "start",
		inline: "nearest",
	});
});

document.getElementById("driversTableButton").addEventListener("click", () => {
	document.getElementById("drivers").scrollIntoView({
		behavior: "smooth",
		block: "start",
		inline: "nearest",
	});
});

document
	.getElementById("runningProcessesButton")
	.addEventListener("click", () => {
		document.getElementById("runningProcesses").scrollIntoView({
			behavior: "smooth",
			block: "start",
			inline: "nearest",
		});
	});

document.getElementById("installedAppButton").addEventListener("click", () => {
	document.getElementById("installedApp").scrollIntoView({
		behavior: "smooth",
		block: "start",
		inline: "nearest",
	});
});

document.getElementById("servicesTableButton").addEventListener("click", () => {
	document.getElementById("services").scrollIntoView({
		behavior: "smooth",
		block: "start",
		inline: "nearest",
	});
});

document.getElementById("tasksTableButton").addEventListener("click", () => {
	document.getElementById("tasks").scrollIntoView({
		behavior: "smooth",
		block: "start",
		inline: "nearest",
	});
});

document.getElementById("netconTableButton").addEventListener("click", () => {
	document.getElementById("netcon").scrollIntoView({
		behavior: "smooth",
		block: "start",
		inline: "nearest",
	});
});

document.getElementById("routesTableButton").addEventListener("click", () => {
	document.getElementById("routes").scrollIntoView({
		behavior: "smooth",
		block: "start",
		inline: "nearest",
	});
});

//Don't even ask.
//Setting the target as the searchbar, sanitizing the inputs into the search bar into lower case, then getting all divs by class of widget into an array and looping
//through them for each keystroke, setting visibility of matched divs with class widget, searching the text into their h1 children.
window.searchFunction = function searchFunction() {
	var input, filter, li, i, txtValue, h1;
	input = document.getElementById("searchBarDiv");
	filter = input.value.toUpperCase();
	let mainbody = document.getElementById("main");
	li = mainbody.getElementsByClassName("widget");

	for (i = 0; i < li.length; i++) {
		if (li[i].getElementsByTagName("h1")[0]) {
			h1 = li[i].getElementsByTagName("h1")[0];
		}
		txtValue = h1.textContent || h1.innerText;
		if (txtValue.toUpperCase().indexOf(filter) > -1) {
			li[i].style.display = "";
		} else {
			li[i].style.display = "none";
		}
	}
};

//It goes to the top, that's it.
let topbutton = document.getElementById("btn-back-to-top");
window.onscroll = function () {
	scrollFunction();
};
function scrollFunction() {
	if (
		document.body.scrollTop > 20 ||
		document.documentElement.scrollTop > 20
	) {
		topbutton.style.display = "block";
	} else {
		topbutton.style.display = "none";
	}
}
topbutton.addEventListener("click", backToTop);
function backToTop() {
	document.body.scrollTop = 0;
	document.documentElement.scrollTop = 0;
}

// Konami Code - Shows Debug Log
// KonamiJS Code from https://github.com/georgemandis/konami-js
const easterEgg = new Konami(
	() => (document.getElementById("devdiv").style.display = "block")
);

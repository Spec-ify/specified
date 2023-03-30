//These are very rudimentary ways of implementing quick pagination of elements without needing any complicated setups.
$(".pups_button").click(function () {
	$("#notes").hide();
	$("#variables").hide();
	$("#browsers").hide();
	$("#pups").show();
});

$(".notes_button").click(function () {
	$("#pups").hide();
	$("#variables").hide();
	$("#browsers").hide();
	$("#notes").show();
});
$(".variables_button").click(function () {
	$("#pups").hide();
	$("#notes").hide();
	$("#browsers").hide();
	$("#variables").show();
});
$(".browsers_button").click(function () {
	$("#pups").hide();
	$("#notes").hide();
	$("#variables").hide();
	$("#browsers").show();
});

$("#CollapseToggle").click(function () {
	$("#CollapseToggle").hide();
	$("#CollapseToggleHide").show();
	$(".accordion-collapse").addClass("show");
});

$("#CollapseToggleHide").click(function () {
	$("#CollapseToggle").show();
	$("#CollapseToggleHide").hide();
	$(".accordion-collapse").removeClass("show");
});

//Snippets like these allow for the screen to scroll and follow the expansion caused by collapsing accordion items.
$("#runningProcessesButton").click(function () {
	$("html, body").animate({
		scrollTop: $("#runningProcesses").offset().top,
	});
});
$("#installedAppButton").click(function () {
	$("html, body").animate({
		scrollTop: $("#installedApp").offset().top,
	});
});
$("#servicesTableButton").click(function () {
	$("html, body").animate({
		scrollTop: $("#services").offset().top,
	});
});
$("#tasksTableButton").click(function () {
	$("html, body").animate({
		scrollTop: $("#tasks").offset().top,
	});
});
$("#netconTableButton").click(function () {
	$("html, body").animate({
		scrollTop: $("#netcon").offset().top,
	});
});
$("#routesTableButton").click(function () {
	$("html, body").animate({
		scrollTop: $("#routes").offset().top,
	});
});
$("#devicesTableButton").click(function () {
	$("html, body").animate({
		scrollTop: $("#devices").offset().top,
	});
});
$("#driversTableButton").click(function () {
	$("html, body").animate({
		scrollTop: $("#drivers").offset().top,
	});
});

//Don't even ask.
//Setting the target as the searchbar, sanitizing the inputs into the search bar into lower case, then getting all divs by class of widget into an array and looping
//through them for each keystroke, setting visibility of matched divs with class widget, searching the text into their h1 children.
function searchFunction() {
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
}

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

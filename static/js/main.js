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

//We get the filename by probing the php print of the filename and adding the full path and filetype to it.
const filename =
	"files/" + document.getElementById("filename").innerText + ".json";

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

async function tables() {
	const json = await (await fetch(filename)).json();

	// Installed Apps
	new Tabulator("#installedApp", {
		data: json.System.InstalledApps, //load row data from array
		layout: "fitColumns", //fit columns to width of table
		responsiveLayout: "hide", //hide columns that dont fit on the table
		addRowPos: "top", //when adding a new row, add it to the top of the table
		history: true, //allow undo and redo actions on the table
		pagination: "local", //paginate the data
		paginationSize: 15, //allow 10 rows per page of data
		paginationButtonCount: 99, // Show all pages
		paginationCounter: "rows", //display count of paginated rows in footer
		movableColumns: false, //column order to be changed
		initialSort: [
			//set the initial sort order of the data
			{ column: "ProcessName", dir: "asc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			//define the table columns
			{
				title: "Name",
				field: "Name",
				resizable: false,
			},
			{
				title: "Version",
				field: "Version",
				resizable: false,
			},
			{
				title: "Install Date",
				field: "InstallDate",
				resizable: false,
			},
		],
	});

	// Running Processes
	let groupProcesses = {};
	json.System.RunningProcesses.forEach((e) => {
		const isSystemOrNull =
			e.ExePath === "Not Found" ||
			e.ExePath === "SYSTEM" ||
			e.ExePath.startsWith(null);
		const keys = Object.keys(groupProcesses);
		// the path is not valid if isSystemOrNull is true
		if (isSystemOrNull) {
			if (!keys.includes(e.ProcessName))
				groupProcesses[e.ProcessName] = [e];
			else groupProcesses[e.ProcessName].push(e);
		} else {
			if (!keys.includes(e.ExePath)) groupProcesses[e.ExePath] = [e];
			else groupProcesses[e.ExePath].push(e);
		}
	});
	const displayProcesses = Object.values(groupProcesses).flatMap((e) => {
		const count = e.length;
		const workingSetReal = e
			.map((p) => p.WorkingSet)
			.reduce((acc, cur) => acc + cur);
		const intl = Intl.NumberFormat("en-US");
		const workingSetMegaBytes = (workingSetReal / Math.pow(2, 20)).toFixed(
			2
		);
		const workingSetDisplay = intl.format(workingSetMegaBytes);
		const cpuPercent = e
			.map((p) => p.CpuPercent)
			.reduce((acc, cur) => acc + cur);
		return {
			ProcessName: `${e[0].ProcessName} (${count})`,
			ExePath: e[0].ExePath,
			Id: e[0].Id, // We can perhaps make this a list later
			WorkingSet: workingSetDisplay,
			CpuPercent: cpuPercent,
			WorkingSetReal: workingSetReal,
		};
	});

	new Tabulator("#runningProcesses", {
		data: displayProcesses, //load row data from array
		layout: "fitColumns", //fit columns to width of table
		responsiveLayout: "hide", //hide columns that dont fit on the table
		addRowPos: "top", //when adding a new row, add it to the top of the table
		history: true, //allow undo and redo actions on the table
		pagination: "local", //paginate the data
		paginationSize: 15, //allow 10 rows per page of data
		paginationButtonCount: 99, // Show all pages
		paginationCounter: "rows", //display count of paginated rows in footer
		movableColumns: false, //allow column order to be changed
		initialSort: [
			//set the initial sort order of the data
			{ column: "Id", dir: "asc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			//define the table columns
			{
				title: "ID",
				field: "Id",
				width: 100,
				resizable: false,
			},
			{
				title: "Process Name",
				field: "ProcessName",
				width: 500,
				resizable: false,
			},
			{
				title: "Path",
				field: "ExePath",
				resizable: false,
			},
			{
				title: "RAM (MB)",
				field: "WorkingSet",
				width: 100,
				resizable: false,
			},
			{
				title: "CPU",
				field: "CpuPercent",
				width: 100,
				resizable: false,
			},
		],
	});
}
tables();

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

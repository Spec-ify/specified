import {
	Tabulator,
	ResponsiveLayoutModule,
	FilterModule,
	SortModule,
	TooltipModule,
	PageModule,
} from "https://cdn.jsdelivr.net/npm/tabulator-tables@5.4.4/dist/js/tabulator_esm.min.js";

Tabulator.registerModule([
	ResponsiveLayoutModule,
	FilterModule,
	SortModule,
	TooltipModule,
	PageModule,
]);

//We get the filename by probing the php print of the filename and adding the full path and filetype to it.
const filename =
	"files/" + document.getElementById("filename").innerText + ".json";

const json = await (await fetch(filename)).json();

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
		if (!keys.includes(e.ProcessName)) groupProcesses[e.ProcessName] = [e];
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
	const workingSetMegaBytes = (workingSetReal / Math.pow(2, 20)).toFixed(2);
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

var RunnProc = await new Tabulator("#runningProcesses", {
	data: displayProcesses, //load row data from array
	layout: "fitColumns", //fit columns to width of table
	responsiveLayout: "hide", //hide columns that dont fit on the table
	pagination: "local", //paginate the data
	paginationSize: 15, //allow 10 rows per page of data
	paginationButtonCount: 99, // Show all pages
	paginationCounter: "rows", //display count of paginated rows in footer
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
			title: "ID",
			field: "Id",
			width: 100,
		},
		{
			title: "Process Name",
			field: "ProcessName",
			width: 500,
		},
		{
			title: "Path",
			field: "ExePath",
		},
		{
			title: "RAM (MB)",
			field: "WorkingSet",
			width: 100,
		},
		{
			title: "CPU",
			field: "CpuPercent",
			width: 100,
		},
	],
});

// Installed Apps
var InstApps = await new Tabulator("#installedApp", {
	data: json.System.InstalledApps, //load row data from array
	layout: "fitColumns", //fit columns to width of table
	responsiveLayout: "hide", //hide columns that dont fit on the table
	pagination: "local", //paginate the data
	paginationSize: 15, //allow 10 rows per page of data
	paginationButtonCount: 99, // Show all pages
	paginationCounter: "rows", //display count of paginated rows in footer
	initialSort: [
		//set the initial sort order of the data
		{ column: "Name", dir: "asc" },
	],
	columnDefaults: {
		tooltip: true, //show tool tips on cells
	},
	columns: [
		//define the table columns
		{
			title: "Name",
			field: "Name",
		},
		{
			title: "Version",
			field: "Version",
		},
		{
			title: "Install Date",
			field: "InstallDate",
		},
	],
});

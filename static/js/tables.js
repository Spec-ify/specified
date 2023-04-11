import {
	Tabulator,
	ResponsiveLayoutModule,
	FilterModule,
	SortModule,
	TooltipModule,
	PageModule,
	FormatModule,
} from "https://cdn.jsdelivr.net/npm/tabulator-tables@5.4.4/dist/js/tabulator_esm.min.js";

Tabulator.registerModule([
	ResponsiveLayoutModule,
	FilterModule,
	SortModule,
	TooltipModule,
	PageModule,
	FormatModule,
]);

/*

=========== TO DO ===========

Features:
Search (Filtering / https://tabulator.info/docs/5.4/filter)
	- Create a Div and Append an input on it
		- If we do this, it would repeatedly create the Div unless we add a check for it 

*/

/* 

=========== K9's Note ===========

Tabulator works by calculating the DOM space it has and renders
accordingly. I think it would be better if we could render it once
and just hide it, but because of the current set up, it's impossible.

So that's why I've opted to re-render them every time the table is brought up.
It's janky, it's definitely inefficient, but it's the one that works.

*/

Tabulator.defaultOptions.layout = "fitColumns"; // fit columns to width of table
Tabulator.defaultOptions.responsiveLayout = "hide"; // hide columns that dont fit on the table
Tabulator.defaultOptions.pagination = "local"; // paginate the data
Tabulator.defaultOptions.paginationSize = 20; // how many rows are shown
Tabulator.defaultOptions.paginationButtonCount = 99; // show all pages
Tabulator.defaultOptions.paginationCounter = "rows"; // display count of paginated rows in footer

// Get profile name from DOM
const filename =
	"files/" + document.getElementById("filename").innerText + ".json";

// Get JSON data and parse
const json = await (await fetch(filename)).json();

/*

=========== Modals ===========
Modals work differently than the sections.
You need to add a slight delay to let the modal load 
so that we can actually render the tables correctly.

Partition Data differs because the tables are in accordions, 
like the sections, so we could just do it like them. 

*/

function ExpandModalTables() {
	// Draw Drive Tables require the drive key
	DrawNICTable();
	DrawTempsTable();
	DrawADTable();
	DrawPPBTable();
}

// Partition Data
window.DrawDriveTable = async function DrawDriveTable(a) {
	var DriveData = json.Hardware.Storage[a];
	DriveData = [
		{
			DeviceName: DriveData.DeviceName,
			SerialNumber: DriveData.SerialNumber,
			DiskNumber: DriveData.DiskNumber,
			DiskCapacity: DriveData.DiskCapacity,
			DiskFree: DriveData.DiskFree,
			Partitions: DriveData.Partitions,
		},
	];
	const div = `#DriveTable${a}`;

	var PartTable = await new Tabulator(div, {
		data: DriveData, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "Name", dir: "asc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "#", field: "DiskNumber", width: 50 },
			{ title: "Name", field: "DeviceName" },
			{ title: "SN", field: "SerialNumber" },
			{
				title: "Capacity (GB)",
				field: "DiskCapacity",
				formatter: function (cell) {
					var calculated = Math.floor(cell.getValue() / 1073741824);
					return calculated.toLocaleString();
				},
				width: 150,
			},
			{
				title: "Free (GB)",
				field: "DiskFree",
				formatter: function (cell) {
					var calculated = Math.floor(cell.getValue() / 1073741824);
					return calculated.toLocaleString();
				},
				width: 150,
			},
		],

		rowFormatter: function (row) {
			//create and style holder elements
			var holderEl = document.createElement("div");
			var tableEl = document.createElement("div");

			holderEl.style.boxSizing = "border-box";
			holderEl.style.padding = "10px 30px 10px 10px";
			holderEl.style.borderTop = "1px solid inherit";
			holderEl.style.borderBottom = "1px solid inherit";

			tableEl.style.border = "1px solid inherit";

			holderEl.appendChild(tableEl);

			row.getElement().appendChild(holderEl);

			var subTable = new Tabulator(tableEl, {
				data: row.getData().Partitions,
				columns: [
					{ title: "Label", field: "PartitionLabel" },
					{
						title: "Capacity (MB)",
						field: "PartitionCapacity",
						formatter: function (cell) {
							var calculated = Math.floor(
								cell.getValue() / 1048576
							);
							return calculated.toLocaleString();
						},
					},
					{
						title: "Free (MB)",
						field: "PartitionFree",
						formatter: function (cell) {
							var calculated = Math.floor(
								cell.getValue() / 1048576
							);
							return calculated.toLocaleString();
						},
					},
					{ title: "FS Type", field: "Filesystem" },
				],
			});
		},
	});
};

// NIC
async function DrawNICTable() {
	var NICTable = await new Tabulator("#nicTable", {
		data: json.Network.Adapters, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "InterfaceIndex", dir: "asc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "#", field: "InterfaceIndex", width: 50 },
			{ title: "Name", field: "Description", width: 350 },
			{ title: "MAC", field: "MACAddress" },
			{ title: "Gateway(s)", field: "DefaultIPGateway" },
			{ title: "DHCP State", field: "DHCPEnabled", width: 125 },
			{ title: "DHCP Server", field: "DHCPServer" },
			{ title: "DNS Domain", field: "DNSDomain" },
			{ title: "DNS Hostname", field: "DNSHostName" },
			{ title: "DNS IPs", field: "DNSServerSearchOrder" },
			{ title: "IP(s)", field: "IPAddress" },
			{ title: "Subnet", field: "IPSubnet" },
		],
	});
}

window.DrawNICTable = function DrawNIC() {
	setTimeout(() => {
		DrawNICTable();
	}, "250");
};

// Temps
async function DrawTempsTable() {
	var TempsTable = await new Tabulator("#TempsDiv", {
		data: json.Hardware.Temperatures, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "Hardware", dir: "asc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "Hardware", field: "Hardware", width: 250 },
			{ title: "Sensor", field: "SensorName" },
			{ title: "Temperature", field: "SensorValue", width: 150 },
		],
	});
}

window.DrawTempsTable = function DrawTemps() {
	setTimeout(() => {
		DrawTempsTable();
	}, "250");
};

// Audio Devices
async function DrawADTable() {
	var AudioDev = await new Tabulator("#audioDeviceDiv", {
		data: json.Hardware.AudioDevices, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "Status", dir: "asc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "Status", field: "Status", width: 100 },
			{ title: "Manufacturer", field: "Manufacturer", width: 150 },
			{ title: "Name", field: "Name", width: 250 },
			{ title: "Device ID", field: "DeviceID" },
		],
	});
}

window.DrawADTable = function DrawAD() {
	setTimeout(() => {
		DrawADTable();
	}, "250");
};

// Power Profiles - Batteries
async function DrawPPBTable() {
	var PowerProfile = await new Tabulator("#powerTable", {
		data: json.System.PowerProfiles, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "IsActive", dir: "asc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "Status", field: "IsActive", width: 80 },
			{ title: "Name", field: "ElementName", width: 200 },
			{ title: "Description", field: "Description" },
			{ title: "Device ID", field: "InstanceID" },
		],
	});

	var Batteries = await new Tabulator("#batteryTable", {
		data: json.Hardware.Batteries, //load row data from array
		layout: "fitColumns", //fit columns to width of table
		responsiveLayout: "hide", //hide columns that dont fit on the table
		pagination: "local", //paginate the data
		paginationSize: pageamount, //allow 10 rows per page of data
		paginationButtonCount: 99, // Show all pages
		paginationCounter: "rows", //display count of paginated rows in footer
		initialSort: [
			//set the initial sort order of the data
			{ column: "Manufacturer", dir: "asc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "Manufacturer", field: "Manufacturer" },
			{ title: "Name", field: "Name" },
			{ title: "Chemistry", field: "Chemistry", width: 100 },
			{ title: "Design Capacity", field: "Design_Capacity", width: 100 },
			{
				title: "Full Charge Capacity",
				field: "Full_Charge_Capacity",
				width: 100,
			},
			{
				title: "Current Charge",
				field: "Remaining_Life_Percentage",
				width: 100,
			},
		],
	});
}

window.DrawPPBTable = function DrawPPB() {
	setTimeout(() => {
		DrawPPBTable();
	}, "250");
};

/*

=========== Sections ===========
The tables in this part can be easily rendered by 
hooking an onclick event to the buttons, calling
to render the table.

*/

function ExpandSectionTables() {
	// Draw Drive Tables require the drive key
	DrawDevicesTable();
	DrawDriverTable();
	DrawRunProc();
	DrawInstApps();
	DrawServicesTable();
	DrawTasksTable();
	DrawNetConTable();
	DrawRoutesTable();
}

// Devices
window.DrawDevicesTable = async function DrawDevicesTable() {
	var DevicesTable = await new Tabulator("#devicesTable", {
		data: json.Hardware.Devices, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "Status", dir: "desc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			//define the table columns
			{ title: "Status", field: "Status", width: 80 },
			{ title: "Description", field: "Description" },
			{ title: "Name", field: "Name" },
			{ title: "DID", field: "DeviceID" },
		],
	});
};

// Drivers
window.DrawDriverTable = async function DrawDriverTable() {
	var DevicesTable = await new Tabulator("#driversTable", {
		data: json.Hardware.Drivers, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "DeviceName", dir: "desc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "Name", field: "DeviceName" },
			{ title: "Friendly Name", field: "FriendlyName" },
			{ title: "Manufacturer", field: "Manufacturer" },
			{ title: "DID", field: "DeviceID" },
			{ title: "Version", field: "DriverVersion" },
		],
	});
};

// Running Processes
window.DrawRunProc = async function DrawRunProc() {
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

	var RunnProc = await new Tabulator("#runningProcesses", {
		data: displayProcesses, //load row data from array
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
};

// Installed Apps
window.DrawInstApps = async function DrawInstApps() {
	var InstApps = await new Tabulator("#installedApp", {
		data: json.System.InstalledApps, //load row data from array
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
};

// Services
window.DrawServicesTable = async function DrawServicesTable() {
	var ServicesTable = await new Tabulator("#servicesTable", {
		data: json.System.Services, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "State", dir: "desc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "State", field: "State", width: 80 },
			{ title: "Start Mode", field: "StartMode", width: 120 },
			{ title: "Caption", field: "Caption" },
			{ title: "Name", field: "Name", width: 360 },
			{ title: "Path", field: "PathName" },
		],
	});
};

// Tasks
window.DrawTasksTable = async function DrawTasksTable() {
	var TasksTable = await new Tabulator("#tasksTable", {
		data: json.System.ScheduledTasks, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "State", dir: "desc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "State", field: "State", width: 80 },
			{ title: "Active", field: "IsActive", width: 80 },
			{ title: "Name", field: "Name", width: 560 },
			{ title: "Path", field: "Path", width: 560 },
			{ title: "Author", field: "Author" },
			{ title: "Triggers", field: "TriggerTypes" },
		],
	});
};

// Network Connections
window.DrawNetConTable = async function DrawNetConTable() {
	var NetCon = await new Tabulator("#netconTable", {
		data: json.Network.NetworkConnections, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "LocalIPAddress", dir: "desc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "Local IP", field: "LocalIPAddress" },
			{ title: "Local Port", field: "LocalPort" },
			{ title: "Remote IP", field: "RemoteIPAddress" },
			{ title: "Remote Port", field: "RemotePort" },
			{
				title: "Process Name",
				field: "OwningPID",
				formatter: function (cell) {
					var value = cell.getValue();
					var out = "";
					json.System.RunningProcesses.forEach(function (PID) {
						if (PID.Id == value) {
							out = PID.ProcessName;
						}
					});
					return out;
				},
			},
		],
	});
};

// Routes
window.DrawRoutesTable = async function DrawRoutesTable() {
	var NetCon = await new Tabulator("#routesTable", {
		data: json.Network.Routes, //load row data from array
		initialSort: [
			//set the initial sort order of the data
			{ column: "Destination", dir: "desc" },
		],
		columnDefaults: {
			tooltip: true, //show tool tips on cells
		},
		columns: [
			{ title: "Route", field: "Description" },
			{ title: "Destination", field: "Destination" },
			{
				title: "Interface",
				field: "InterfaceIndex",
				formatter: function (cell) {
					var value = cell.getValue();
					var out = "";
					json.Network.Adapters.forEach(function (Interface) {
						if (Interface.InterfaceIndex == value) {
							out = Interface.Description;
						}
					});
					return out;
				},
			},
			{ title: "Mask", field: "Mask" },
			{ title: "Metric", field: "Metric1" },
			{ title: "Next Hop", field: "NextHop" },
		],
	});
};

// Make Expand All work
window.ExpandTables = function ExpandTables() {
	ExpandModalTables();
	ExpandSectionTables();
};

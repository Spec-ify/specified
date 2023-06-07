//We get the filename by probing the php print of the filename and adding the full path and filetype to it.
const filename =
	"files/" + document.getElementById("filename").innerText + ".json";

async function dataTables() {
	const json = await (await fetch(filename)).json();

	try {
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
			const workingSetMegaBytes = (
				workingSetReal / Math.pow(2, 20)
			).toFixed(2);
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
	} catch (e) {
		console.log("Failed to access Running Processes. Is it blank?");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#runningProcessesTable").DataTable({
			autoWidth: false,
			data: displayProcesses,
			pageLength: 25,
			columns: [
				{ data: "Id" },
				{ data: "ProcessName" },
				{ data: "ExePath" },
				{ data: "WorkingSet" },
				{ data: "CpuPercent" },
				{ data: "WorkingSetReal" },
			],
			columnDefs: [
				{ orderData: [5], targets: [4] },
				{
					targets: [5],
					searchable: false,
					visible: false,
				},
			],
		});
	} catch (e) {
		console.log("Failed making Running Processes DataTable!");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#installedAppTable").DataTable({
			autoWidth: false,
			data: json.System.InstalledApps,
			columns: [
				{ data: "Name" },
				{ data: "Version" },
				{ data: "InstallDate" },
			],
		});
	} catch (e) {
		console.log("Failed making Installed Apps DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#servicesTable").DataTable({
			autoWidth: false,
			data: json.System.Services,
			pageLength: 25,
			columns: [
				{ data: "State" },
				{ data: "Caption" },
				{ data: "Name" },
				{ data: "PathName" },
				{ data: "StartMode" },
			],
		});
	} catch (e) {
		console.log("Failed making Services DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#tasksTable").DataTable({
			autoWidth: false,
			data: json.System.ScheduledTasks,
			pageLength: 25,
			columns: [
				{ data: "State" },
				{ data: "IsActive" },
				{ data: "Name" },
				{ data: "Path" },
				{ data: "Author" },
				{ data: "TriggerTypes" },
			],
		});
	} catch (e) {
		console.log("Failed making Tasks DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#nicTable").DataTable({
			autoWidth: false,
			data: json.Network.Adapters,
			columns: [
				{ data: "InterfaceIndex" },
				{ data: "Description" },
				{ data: "MACAddress" },
				{ data: "DefaultIPGateway" },
				{ data: "DHCPEnabled" },
				{ data: "DHCPServer" },
				{ data: "DNSDomain" },
				{ data: "DNSHostName" },
				{ data: "DNSServerSearchOrder" },
				{ data: "IPAddress" },
				{ data: "IPSubnet" },
			],
		});
	} catch (e) {
		console.log("Failed making NIC DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#tempTable").DataTable({
			autoWidth: false,
			data: json.Hardware.Temperatures,
			columns: [
				{ data: "Hardware" },
				{ data: "SensorName" },
				{ data: "SensorValue" },
			],
		});
	} catch (e) {
		console.log("Failed making Temperature DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#powerTable").DataTable({
			autoWidth: false,
			searching: false,
			ordering: false,
			paging: false,
			data: json.System.PowerProfiles,
			columns: [
				{ data: "Description" },
				{ data: "ElementName" },
				{ data: "InstanceID" },
				{ data: "IsActive" },
			],
		});
	} catch (e) {
		console.log("Failed making Power Profile DataTable");
		console.log(e.name + ": " + e.message);
	}

	//This function broke me as a human being.
	//We do a single entrypoint ajax call to the json file like all other table generations, except we have a situation where we have an unknown amount of browsers, each with
	//an unknown amount of profiles, each with an unknown amount of extensions installed.
	//One part of this is done in lines 1128~ and onwards(as of commit when writing this comment). PHP creates a table element for each of the browser profiles
	//then the code below will follow the same naming convention to linearly populate each appropriate table with it's data.
	//At first I experimented with creating the divs and table dynamically with JS, but datatable would not initialize on a newly created div no matter what I did.
	//This works, and it seems to work great too. But who knows.
	try {
		var Browsers = Object.keys(json.System.BrowserExtensions);
		Browsers.forEach(function (Browser) {
			var Profiles = Object.keys(
				json.System.BrowserExtensions[Browser].Profiles
			);
			Profiles.forEach(function (Profile) {
				let BrowserName =
					"#" +
					json.System.BrowserExtensions[Browser].Name +
					"Profile" +
					[Profile] +
					"Table";
				let BrowserJsonData =
					json.System.BrowserExtensions[Browser].Profiles[Profile]
						.Extensions;
				BrowserJsonData = BrowserJsonData.filter((e) => e != null);
				$(BrowserName).DataTable({
					autoWidth: false,
					searching: false,
					ordering: false,
					paging: false,
					data: BrowserJsonData,
					columns: [
						{ data: "name" },
						{ data: "version" },
						{ data: "description" },
					],
				});
			});
		});
	} catch (e) {
		console.log("Failed making Browser Extension DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		var Drives = Object.keys(json.Hardware.Storage);
		Drives.forEach(function (Drive) {
			let DriveModal = "#partitionsTable" + Drive;
			let PartitionJsonData = json.Hardware.Storage[Drive].Partitions;
			$(DriveModal).DataTable({
				autoWidth: false,
				searching: false,
				ordering: false,
				paging: false,
				pageLength: 25,
				data: PartitionJsonData,
				columns: [
					{ data: "PartitionLabel" },
					{
						data: "PartitionCapacity",
						render: function (data, row) {
							return Math.floor(data / 1048576) + " MB";
						},
					},
					{
						data: "PartitionFree",
						render: function (data, row) {
							return Math.floor(data / 1048576) + " MB";
						},
					},
					{ data: "Filesystem" },
				],
			});
		});
	} catch (e) {
		console.log("Failed making Drive DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#audioTable").DataTable({
			autoWidth: false,
			searching: false,
			ordering: false,
			paging: false,
			data: json.Hardware.AudioDevices,
			columns: [
				{ data: "DeviceID" },
				{ data: "Manufacturer" },
				{ data: "Name" },
				{ data: "Status" },
			],
		});
	} catch (e) {
		console.log("Failed making Audio Devices DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#batteryTable").DataTable({
			autoWidth: false,
			data: json.Hardware.Batteries,
			searching: false,
			ordering: false,
			paging: false,
			columns: [
				{ data: "Name" },
				{ data: "Manufacturer" },
				{ data: "Chemistry" },
				{ data: "Design_Capacity" },
				{ data: "Full_Charge_Capacity" },
			],
		});
	} catch (e) {
		console.log("Failed making Audio Devices DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#netconTable").DataTable({
			autoWidth: false,
			data: json.Network.NetworkConnections,
			columns: [
				{ data: "LocalIPAddress" },
				{ data: "LocalPort" },
				{ data: "RemoteIPAddress" },
				{ data: "RemotePort" },
				{
					data: "OwningPID",
					render: function (data) {
						json.System.RunningProcesses.forEach(function (PID) {
							if (PID.Id == data) {
								data = PID.ProcessName;
							}
						});
						return data;
					},
				},
			],
		});
	} catch (e) {
		console.log("Failed making Network Connections DataTable");
		console.log(e.name + ": " + e.message);
	}

	// Clever little trickery in this function actually where I practically use DataTables render functionality to
	// InnerJoin the InterfaceIndex from one tree in the json to another tree in the json, thus giving me the ability
	// to print out the corresponding name of the NIC that's using a route, instead of just a number.
	try {
		$("#routesTable").DataTable({
			autoWidth: false,
			data: json.Network.Routes,
			columns: [
				{ data: "Description" },
				{ data: "Destination" },
				{
					data: "InterfaceIndex",
					render: function (data) {
						json.Network.Adapters.forEach(function (Interface) {
							if (Interface.InterfaceIndex == data) {
								data = Interface.Description;
							}
						});
						return data;
					},
				},
				{ data: "Mask" },
				{ data: "Metric1" },
				{ data: "NextHop" },
			],
		});
	} catch (e) {
		console.log("Failed making Routes DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#devicesTable").DataTable({
			autoWidth: false,
			data: json.Hardware.Devices,
			columns: [
				{ data: "Status" },
				{ data: "Description" },
				{ data: "Name" },
				{ data: "DeviceID" },
			],
		});
	} catch (e) {
		console.log("Failed making Devices DataTable");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#driversTable").DataTable({
			autoWidth: false,
			data: json.Hardware.Drivers,
			columns: [
				{ data: "DeviceName" },
				{ data: "FriendlyName" },
				{ data: "Manufacturer" },
				{ data: "DeviceID" },
				{ data: "DriverVersion" },
			],
		});
	} catch (e) {
		console.log("Failed making Drivers DataTable");
		console.log(e.name + ": " + e.message);
	}
}

dataTables();

async function errorcheck() {
	const json = await (await fetch(filename)).json();
	let errors = 0;
	for (let key in json) {
		key = json[key];
		try {
			if ("ErrorCount" in key && key["ErrorCount"] > 0) {
				for (let elem of document.getElementsByClassName("btn-info")) {
					elem.style.backgroundColor = "#d35400";
					elem.style.boxShadow = "0 4px 9px -4px #d35400";
				}
				errors += key["ErrorCount"];
			}
		} catch {
			continue;
		}
	}
	if (errors > 0) {console.log("JSON Errors Total: " + errors);}
}

errorcheck();
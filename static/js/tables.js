import { call_hwapi } from "./common.js";

const filename = `files/${PROFILE_NAME}.json`;

async function dataTables() {
	
	const json = await (await fetch(filename)).json();

	modalsTables(json);
	devicesAndDrivers(json);
	appsTables(json);
	servicesAndTasks(json);
	networkTables(json);
	errorTables(json);
}

function modalsTables(json) {
	try {
		$("#temp-table").DataTable({
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
		$("#power-table").DataTable({
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
		$("#audio-table").DataTable({
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
		$("#battery-table").DataTable({
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
}

function devicesAndDrivers(json){
	try {
        json.Hardware.Devices.map(row => {
           if (row.Status === "Error") {
               row.Status = `Error (${row.ConfigManagerErrorCode})`;
           }

           return row;
        });

		$("#devices-table").DataTable({
			autoWidth: false,
			data: json.Hardware.Devices,
			columns: [
				{
                    data: "Status"
                },
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
		$("#drivers-table").DataTable({
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

function appsTables(json) {
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
			return {
				ProcessName: `${e[0].ProcessName} (${count})`,
				ExePath: e[0].ExePath,
				Id: e[0].Id, // We can perhaps make this a list later
				WorkingSet: workingSetDisplay,
				WorkingSetReal: workingSetReal,
			};
		});

		$("#running-processes-table").DataTable({
			autoWidth: false,
			data: displayProcesses,
			pageLength: 25,
			columns: [
				{ data: "Id" },
				{ data: "ProcessName" },
				{ data: "ExePath" },
				{ data: "WorkingSet" },
				{ data: "WorkingSetReal" },
			],
			columnDefs: [
				{ orderData: [4], targets: [3] },
				{
					targets: [4],
					searchable: false,
					visible: false,
				},
			],
		});
	} catch (e) {
		console.log("Failed making Running Processes DataTable. Is it blank?");
		console.log(e.name + ": " + e.message);
	}

	try {
		$("#installed-app-table").DataTable({
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

	if (document.getElementById("installed-windows-store-table")){
		try {
			$("#installed-windows-store-table").DataTable({
				autoWidth: false,
				pageLength: 25,
				data: json.System.WindowsStorePackages,
				columns: [
					{ data: "Name" },
					{ data: "ProgramId" },
					{ data: "Vendor" },
					{ data: "Version" },
				],
			});
		} catch (e){
			console.log("Failed making Windows Store Packages table");
			console.log(e.name + ": " + e.message);
		}
	}
}

function servicesAndTasks(json) {
	try {
		$("#services-table").DataTable({
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
		$("#tasks-table").DataTable({
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
}

function networkTables(json) {
	try {
		$("#netcon-table").DataTable({
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
		$("#routes-table").DataTable({
			autoWidth: false,
			data: json.Network.Routes,
			columns: [
				{ data: "Description" },
				{ data: "Destination" },
				{
					data: "InterfaceIndex"
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
}

async function errorTables(json) {
	// Unexpected Shutdowns
	try {
		function format(d) {
			return (
				`<dt>Bugcheck Parameter 1: ${d.BugcheckParameter1}</dt>` +
				`<dt>Bugcheck Parameter 2: ${d.BugcheckParameter2}</dt>` +
				`<dt>Bugcheck Parameter 3: ${d.BugcheckParameter3}</dt>` +
				`<dt>Bugcheck Parameter 4: ${d.BugcheckParameter4}</dt>`
			);
		}

		let unexpectedShutdownsTable = new DataTable(("#unexpected-shutdowns-table"), {
			autoWidth: false,
			data: json.Events.UnexpectedShutdowns,
			columns: [
				{
					className: 'dt-control',
					orderable: false,
					data: null,
					defaultContent: '',
					width: '2%',
				},
				{ data: "Timestamp" },
				{ data: "PowerButtonTimestamp" },
				{ data: "BugcheckCode",
					render: function (data) {
						return `0x${data.toString(16).toUpperCase()}`;
					}
				},
			],
		});

		unexpectedShutdownsTable.on('click', 'td', function (e) {
			let tr = e.target.closest('tr');
			let row = unexpectedShutdownsTable.row(tr);
		 
			if (row.child.isShown()) {
				// This row is already open - close it
				row.child.hide();
			}
			else {
				// Open this row
				row.child(format(row.data())).show();
			}
		});
	} catch (e) {
		console.log("Failed making Unexpected Shutdowns DataTable");
		console.log(e.name + ": " + e.message);
	}

	// Machine Check Exception Table
	try {
		function format(d) {
			return (
				`
				<table class="table" style="margin: 0.5rem 0">
					<tbody>
						<tr>
							<td>Timestamp</td>
							<td>${d.Timestamp}</td>
						</tr>
						<tr>
							<td>MCI Status Register Valid</td>
							<td>${d.MciStatusRegisterValid}</td>
						</tr>
						<tr>
							<td>Error Overflow</td>
							<td>${d.ErrorOverflow}</td>
						</tr>
						<tr>
							<td>Uncorrected Error</td>
							<td>${d.UncorrectedError}</td>
						</tr>
						<tr>
							<td>Error Reporting Enabled</td>
							<td>${d.ErrorReportingEnabled}</td>
						</tr>
						<tr>
							<td>Processor Context Corrupted</td>
							<td>${d.ProcessorContextCorrupted}</td>
						</tr>
						<tr>
							<td>Poisoned Data</td>
							<td>${d.PoisonedData}</td>
						</tr>
						<tr>
							<td>Extended Error Code</td>
							<td>${d.ExtendedErrorCode}</td>
						</tr>
						<tr>
							<td>MCA Error Code</td>
							<td>${d.McaErrorCode}</td>
						</tr>
						<tr>
							<td>Error Message</td>
							<td>${d.ErrorMessage}</td>
						</tr>
						<tr>
							<td>Transaction Type</td>
							<td>${d.TransactionType}</td>
						</tr>
						<tr>
							<td>Memory Heirarchy Level</td>
							<td>${d.MemoryHeirarchyLevel}</td>
						</tr>
						<tr>
							<td>Request Type</td>
							<td>${d.RequestType}</td>
						</tr>
						<tr>
							<td>Participation</td>
							<td>${d.Participation}</td>
						</tr>
						<tr>
							<td>Timeout</td>
							<td>${d.Timeout}</td>
						</tr>
						<tr>
							<td>Memory Or I/O</td>
							<td>${d.MemoryOrIo}</td>
						</tr>
						<tr>
							<td>Memory Transaction Type</td>
							<td>${d.MemoryTransactionType}</td>
						</tr>
						<tr>
							<td>Channel Number</td>
							<td>${d.ChannelNumber}</td>
						</tr>
					</tbody>
				</table>
				`
			);
		}

		let mceTable = new DataTable("#mce-table", {
			autoWidth: false,
			data: json.Events.MachineCheckExceptions,
			columns: [
				{
					className: 'dt-control',
					orderable: false,
					data: null,
					defaultContent: '',
					width: '2%',
				},
				{ data: "Timestamp" },
				{ data: "McaErrorCode" },
				{ data: "ErrorMessage" },
				{ data: "TransactionType" },
			],
		});

		mceTable.on('click', 'td.dt-control', function (e) {
			let tr = e.target.closest('tr');
			let row = mceTable.row(tr);
		 
			if (row.child.isShown()) {
				// This row is already open - close it
				row.child.hide();
			}
			else {
				// Open this row
				row.child(format(row.data())).show();
			}
		});
	} catch (e) {
		console.log("Failed making Machine Check Exception DataTable");
		console.log(e.name + ": " + e.message);
	}

	// WHEA Error Records Error
	try {
		function format(d) {

			let errorDescriptors = `<details style="margin: 0.5rem">
										<summary style="font-size: 1rem">Error Descriptors</summary>`;

			d.ErrorDescriptors.forEach((item) => {
				errorDescriptors += `<table class="table" style="margin: 0.5rem 0">
						<tbody>
							<tr>
								<td>Section Offset</td>
								<td>${item.SectionOffset}</td>
							</tr>
							<tr>
								<td>Section Length</td>
								<td>${item.SectionLength}</td>
							</tr>
							<tr>
								<td>Revision</td>
								<td>${item.Revision}</td>
							</tr>
							<tr>
								<td>Valid Bits</td>
								<td>${item.ValidBits}</td>
							</tr>
							<tr>
								<td>Flags</td>
								<td>${item.Flags}</td>
							</tr>
							<tr>
								<td>Section Type</td>
								<td>${item.SectionType}</td>
							</tr>
							<tr>
								<td>FRU ID</td>
								<td>${item.FRUId}</td>
							</tr>
							<tr>
								<td>Section Severity</td>
								<td>${item.SectionSeverity}</td>
							</tr>
							<tr>
								<td>FRU Text</td>
								<td>${item.FRUText}</td>
							</tr>
						</tbody>
					</table>`
			});

			errorDescriptors += `</details>`;

			return (
				// Error Header
				`
				<details style="margin: 0.5rem">
					<summary style="font-size: 1rem">Error Header</summary>
					<table class="table" style="margin: 0.5rem 0">
						<tbody>
							<tr>
								<td>Signature</td>
								<td>${d.ErrorHeader.Signature}</td>
							</tr>
							<tr>
								<td>Revision</td>
								<td>${d.ErrorHeader.Revision}</td>
							</tr>
							<tr>
								<td>Signature End</td>
								<td>${d.ErrorHeader.SignatureEnd}</td>
							</tr>
							<tr>
								<td>Section Count</td>
								<td>${d.ErrorHeader.SectionCount}</td>
							</tr>
							<tr>
								<td>Severity</td>
								<td>${d.ErrorHeader.Severity}</td>
							</tr>
							<tr>
								<td>Valid Bits</td>
								<td>${d.ErrorHeader.ValidBits}</td>
							</tr>
							<tr>
								<td>Length</td>
								<td>${d.ErrorHeader.Length}</td>
							</tr>
							<tr>
								<td>Timestamp</td>
								<td>${d.ErrorHeader.Timestamp}</td>
							</tr>
							<tr>
								<td>Platform ID</td>
								<td>${d.ErrorHeader.PlatformId}</td>
							</tr>
							<tr>
								<td>Partition ID</td>
								<td>${d.ErrorHeader.PartitionId}</td>
							</tr>
							<tr>
								<td>Creator ID</td>
								<td>${d.ErrorHeader.CreatorId}</td>
							</tr>
							<tr>
								<td>Notify Type</td>
								<td>${d.ErrorHeader.NotifyType}</td>
							</tr>
							<tr>
								<td>Record ID</td>
								<td>${d.ErrorHeader.RecordId}</td>
							</tr>
							<tr>
								<td>Flags</td>
								<td>${d.ErrorHeader.Flags}</td>
							</tr>
							<tr>
								<td>Persistence Info</td>
								<td>${d.ErrorHeader.PersistenceInfo}</td>
							</tr>
						</tbody>
					</table>
				</details>
				` +
				
				// Error Descriptors
				errorDescriptors +

				// Error Packets
				`
				<details style="margin: 0.5rem">
					<summary style="font-size: 1rem">Error Packets</summary>
					<code style="font-size:1rem"><pre>${d.ErrorPackets}</pre></code>
				</details>
				`
			);
		}

		let wheaErrorRecordsTable = new DataTable("#whea-records-table", {
			autoWidth: false,
			data: json.Events.WheaErrorRecords,
			columns: [
				{
					className: 'dt-control',
					orderable: false,
					data: null,
					defaultContent: '',
					width: '2%',
				},
				{ data: "ErrorHeader.Severity" },
				{ data: "ErrorHeader.Timestamp" },
				{ data: "ErrorHeader.PlatformId" },
				{ data: "ErrorHeader.CreatorId" },
				{ data: "ErrorHeader.NotifyType" },
			],
		});

		wheaErrorRecordsTable.on('click', 'td.dt-control', function (e) {
			let tr = e.target.closest('tr');
			let row = wheaErrorRecordsTable.row(tr);

			if (row.child.isShown()) {
				// This row is already open - close it
				row.child.hide();
			}
			else {
				// Open this row
				row.child(format(row.data())).show();
			}
		});
	} catch (e) {
		console.log("Failed making WHEA Error Records DataTable");
		console.log(e.name + ": " + e.message);
	}

	// PCI WHEA Error
	try {
		// this might be the most scuffed code that i have ever written
		// this works similarly to how doom-scroll.js does it,
		// so it runs through all of the items and pushes it to a
		// separate array. but then it pushes it back to the inner rep
		// of the data. very stupid but it works so im not gonna 
		// change it. i need coffee - k9

		let inputIds = [];

		json.Events.PciWheaErrors.forEach((data) => {
			inputIds.push(`PCI\\VEN_${data.VendorId.replace("0x", "")}&DEV_${data.DeviceId.replace("0x", "")}`);
		});

		let responseIds = await call_hwapi('api/pcie/', inputIds);

		json.Events.PciWheaErrors.forEach((data, index) => {
			json["Events"]["PciWheaErrors"][index]["Vendor"] = responseIds[index]["vendor"]
			json["Events"]["PciWheaErrors"][index]["Device"] = responseIds[index]["device"]
			json["Events"]["PciWheaErrors"][index]["Subsystem"] = responseIds[index]["subsystem"]
		});

		new DataTable("#pci-whea-table", {
			autoWidth: false,
			data: json.Events.PciWheaErrors,
			columns: [
				{ data: "Timestamp" },
				{ data: "VendorId" },
				{ data: "DeviceId" },
				{ data: "Vendor" },
				{ data: "Device" },
				{ data: "Subsystem" },
			],
		});
	} catch (e) {
		console.log("Failed making PCI WHEA Errors DataTable");
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

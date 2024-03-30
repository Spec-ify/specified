/**
 * This function contains extremely hacky hacks.
 *  (a) To avoid scrolling horizontally to the heading when you click on it
 *      (making the nav bar not visible), we add a dummy span (.linker) which
 *      is fixed to the left which actually has the id you want to jump to.
 *  (b) We add two spaces on h2's to give indents to them. This makes it appear
 *      nested, but it is terrible.
 */
function createLinks(selector) {
    // this exists to avoid duplicates
    let headings = {};

    document.querySelectorAll(selector).forEach(e => {
        if (e.offsetParent === null) return; // checks if the element is visible
        const kebab = e.innerText.toLowerCase().split(" ").join("-")
            .replace(/[^a-zA-Z0-9-_]/g, '');
        let num = 1;
        while (Object.hasOwn(headings, `${kebab}-${num}`))
            num++;
        const slug = `${kebab}-${num}`;
        headings[slug] = e.innerText;

        const linker = document.createElement("span");
        linker.classList.add("linker");
        linker.id = slug;
        e.prepend(linker);

        const li = document.createElement("li");
        const link = document.createElement("a");
        // TODO: get rid of this abomination
        link.innerHTML = e.tagName === "H2" ? `&nbsp;&nbsp;${e.innerText}` : e.innerText;
        link.style.width = "100%";
        link.href = `#${slug}`;
        li.appendChild(link);
        document.querySelector("#nav-list").appendChild(li);
    });
}
// there are just too many network adapters/disks lol
// the .item-header class should be added to dynamic headers (i.e. extensions, disks, network adapters, etc.)
createLinks("h1, h2:not(.item-header)");

document.querySelector("#nav-collapse").onclick = () => {
    document.querySelector("nav").classList.add("nav-collapsed");
    return false;
}

document.querySelector("#nav-expand").onclick = () => {
    document.querySelector("nav").classList.remove("nav-collapsed");
    return false;
}

// show the debug log when the konami code is pressed
new Konami(() => {
    document.querySelector("#debug-log").style.display = "block";
    createLinks("#debug-log h1");
});

// jQuery is needed just for data tables, avoid using elsewhere
$("#temps-table").DataTable({
    paging: false,
    searching: false,
    info: false
});
$("#drivers-table").DataTable({
    paging: false,
    searching: false,
    info: false
});
$("#installed-apps-table").DataTable({
    paging: false,
    searching: false,
    info: false
});
$("#services-table").DataTable({
    paging: false,
    searching: false,
    info: false
});
$("#tasks-table").DataTable({
    paging: false,
    searching: false,
    info: false
});
$("#network-connections-table").DataTable({
    paging: false,
    searching: false,
    info: false
});
$("#routes-table").DataTable({
    paging: false,
    searching: false,
    info: false
});

let hwapiLocal = true;

/**
 * This is extracted into its own function because we need to call localhost instead of spec-ify.com if there is a
 * hwapi dev server currently running.
 *
 * The path should not start with a slash.
 */
async function call_hwapi(path, payload, fallbackCallack = () => {}) {
    // https://stackoverflow.com/a/57949518
    const isLocalhost = Boolean(
        window.location.hostname === 'localhost' ||
        // [::1] is the IPv6 localhost address.
        window.location.hostname === '[::1]' ||
        // 127.0.0.1/8 is considered localhost for IPv4.
        window.location.hostname.match(
            /^127(?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}$/
        )
    );

    let rawResponse;
    if (isLocalhost && hwapiLocal) {
        console.info("Trying local server for hwapi");

        try {
            if (payload) {
                rawResponse = await fetch(
                    `http://localhost:3000/${path}`,
                    {
                        method: "POST",
                        mode: "cors",
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    },
                );
            } else {
                rawResponse = await fetch(
                    `http://localhost:3000/${path}`,
                    {
                        method: "GET",
                        mode: "cors",
                        headers: {
                            'Accept': 'application/json'
                        }
                    },
                );
            }
        } catch (e) {
            fallbackCallack();
            console.warn("Hwapi dev server not online, falling back to spec-ify.com");
            hwapiLocal = false;
        }
    }
    if (!rawResponse) {
        if (payload) {
            rawResponse = await fetch(
                `https://spec-ify.com/${path}`,
                {
                    method: "POST",
                    mode: "cors",
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                },
            );
        } else {
            rawResponse = await fetch(
                `https://spec-ify.com/${path}`,
                {
                    method: "GET",
                    mode: "cors",
                    headers: {
                        'Accept': 'application/json'
                    }
                },
            );
        }
    }

    try {
        return await rawResponse.json();
    } catch (e) {
        if (rawResponse.status !== 404) // prevent error spam
            console.error("Could not parse json from hwapi!");

        return {};
    }
}

// The processes data table contains functionality that I am too lazy to implement in PHP, so it remains in JavaScript
(async () => {
    const json = await (await fetch(`files/${PROFILE_NAME}.json`)).json();

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

        $("#processes-table").DataTable({
            paging: false,
            searching: false,
            info: false,
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
        console.log("Failed making Running Processes DataTable. Is it blank?");
        console.log(e.name + ": " + e.message);
    }

    // The hwapi request calls currently takes >1s. It doesn't matter that much, but I am doing this in js so the initial
    // page load is a little faster.
    /**
     * @type string
     */
    const cpuName = json["Hardware"]["Cpu"]["Name"];
    const statusSpan = document.querySelector("#hwapi-status");

    const cpuResponse = await call_hwapi(`api/cpus/?name=${encodeURIComponent(
        cpuName
    )}`, null, () => {
        statusSpan.innerHTML = "Could not connect to local hwapi instance, falling back to spec-ify.com";
    });

    if (!cpuResponse || !cpuResponse.name) {
        statusSpan.textContent = "Could not get database results";
    }

    // update the title element to reflect the name fetched from the database
    document.querySelector("#hwapi-header").textContent += ` for ${cpuResponse.name}`;
    let tableContents = "";
    // add new elements to the table for every kv in the database
    for (const [key, value] of Object.entries(cpuResponse.attributes)) {
        tableContents += `<tr><td>${key}</td><td>${value}</td></tr>`;
    }
    // cpuTable.innerHTML = tableContents;
    document.getElementById("hwapi-body").innerHTML = tableContents;

    const deviceTrs = document.querySelectorAll("#devices-table tbody tr");

    let usbIndexes = [];
    let usbValues = [];
    let pcieIndexes = [];
    let pcieValues = [];

    // go through all the devices and build an array to
    deviceTrs.forEach((tr, index) => {
        const jsonDevice = json["Hardware"]["Devices"][index];
        if (jsonDevice.DeviceID.startsWith("USB")) {
            usbIndexes.push(index);
            usbValues.push(jsonDevice["DeviceID"]);
        }
        if (jsonDevice.DeviceID.startsWith("PCI")) {
            pcieIndexes.push(index);
            pcieValues.push(jsonDevice["DeviceID"]);
        }
    })

    const usbResponsePromise = call_hwapi('api/usbs/', usbValues);
    const pcieResponsePromise = call_hwapi('api/pcie/', pcieValues);
    const [usbResponse, pcieResponse] = await Promise.all([usbResponsePromise, pcieResponsePromise]);

    deviceTrs.forEach((tr, trIndex) => {
        const usbArrayIndex = usbIndexes.indexOf(trIndex);
        const pcieArrayIndex = pcieIndexes.indexOf(trIndex);
        if (usbArrayIndex !== -1 && usbResponse[usbArrayIndex]) { // the value is a usb device and it was found
            const response = usbResponse[usbArrayIndex];
            const vendor = document.createElement("td");
            vendor.innerText = response.vendor || "";
            tr.appendChild(vendor);
            const device = document.createElement("td");
            device.innerText = response.device || "";
            tr.appendChild(device);
            const subsystem = document.createElement("td"); // no subsystem for usb
            tr.appendChild(subsystem);
        } else if (pcieArrayIndex !== -1 && pcieResponse[pcieArrayIndex]) {
            const response = pcieResponse[pcieArrayIndex];
            const vendor = document.createElement("td");
            vendor.innerText = response.vendor || "";
            tr.appendChild(vendor);
            const device = document.createElement("td");
            device.innerText = response.device || "";
            tr.appendChild(device);
            const subsystem = document.createElement("td");
            subsystem.innerText = response.subsystem || "";
            tr.appendChild(subsystem);
        } else {
            tr.innerHTML += "<td></td><td></td><td></td>";
        }
    });
    document.querySelector("#devices-sort-warning").style.display = "none";
    $("#devices-table").DataTable({
        paging: false,
        searching: false,
        info: false,
        order: [] // to prevent order changing on data table load
    });
})();

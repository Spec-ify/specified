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
        document.querySelector("#navlist").appendChild(li);
    });
}
// there are just too many network adapters/disks lol
// the .item-header class should be added to dynamic headers (i.e. extensions, disks, network adapters, etc.)
createLinks("h1, h2:not(.item-header)");

document.querySelector("#nav-collapse-link").onclick = () => {
    document.querySelector("nav").classList.add("nav-collapsed");
}

document.querySelector("#nav-expand").onclick = () => {
    document.querySelector("nav").classList.remove("nav-collapsed");
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
$("#devices-table").DataTable({
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

    // The hwapi request call currently takes >1s. It doesn't matter that much, but I am doing this in js so the page load
    // is a little faster.

    /**
     * @type string
     */
    const cpuName = json["Hardware"]["Cpu"]["Name"];
    const statusSpan = document.querySelector("#hwapi-status");

    let response;
    if (window.location.host.startsWith("localhost")) {
        console.info("Trying local server for hwapi");
        try {
            response = await (
                await fetch(
                    `http://localhost:3000/api/cpus/?name=${encodeURIComponent(
                        cpuName
                    )}`,
                    {
                        method: "GET",
                        mode: "cors",
                    }
                )
            ).json();
        } catch (e) {
            statusSpan.innerHTML = "Could not connect to local hwapi instance, falling back to spec-ify.com";
        }
    }
    if (!response) {
        response = await (
            await fetch(
                `https://spec-ify.com/api/cpus/?name=${encodeURIComponent(
                    cpuName
                )}`,
                {
                    method: "GET",
                    mode: "cors",
                }
            )
        ).json();
    }

    if (!response || !response.name) {
        statusSpan.textContent = "Could not get database results";
    }
    const cpuTable = document.getElementById("fetchedCpuInfo");
    // update the title element to reflect the name fetched from the database
    document.querySelector("#hwapi-header").textContent += ` for ${response.name}`;
    let tableContents = "";
    // add new elements to the table for every kv in the database
    for (const [key, value] of Object.entries(response.attributes)) {
        tableContents += `<tr><td>${key}</td><td>${value}</td></tr>`;
    }
    // cpuTable.innerHTML = tableContents;
    document.getElementById("hwapi-body").innerHTML = tableContents;
})();

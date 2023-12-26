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
})();

window.headings = {};
document.querySelectorAll("h1, h2, h3").forEach(e => {
    const kebab = e.innerText.toLowerCase().split(" ").join("-")
        .replace(/[^a-zA-Z0-9-_]/g, '');
    let num = 1;
    while (Object.hasOwn(headings, `${kebab}-${num}`))
        num++;
    const slug = `${kebab}-${num}`;
    e.id = slug;
    headings[slug] = e.innerText;
});

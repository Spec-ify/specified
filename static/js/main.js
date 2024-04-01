/**
 * Toggle the currently displayed tab in the main view
 * @param {("#pups" | "#notes" | "#variables" | "#startup" | "#updates")} tab 
 */
function showTab(tab) {
    // List of all tabs
    const tabs = [
        "#pups",
        "#notes",
        "#variables",
        "#browsers",
        "#startup",
        "#updates",
    ];
    // Removes selected tab
    const hiddenTabs = tabs.filter((val) => val !== tab);
    for (const t of hiddenTabs) {
        $(t).hide();
    }
    $(tab).show();
}

// TODO: the way this is defined is a little bit weird, could probably be improved, it's currently defined by updating the value on the global `window` object
console.log(PROFILE_NAME);

// Interactivity for the cpu and motherboard widgets
{
    // Interactivity for the show more and hide more buttons for the motherboard widget
    document.querySelector("#board-info-more-info-button").addEventListener("click", () => {
            document.querySelector("#board-info-more-info").style.display = "block";
            document.querySelector("#board-info-more-info-button").style.display =
                "none";
    });
    document.querySelector("#board-info-close").addEventListener("click", () => {
        document.querySelector("#board-info-more-info").style.display = "none";
        document.querySelector("#board-info-more-info-button").style.display =
            "inline-block";
    });

    // Interactivity for the show more and hide buttons for the cpu widget
    document.querySelector("#cpu-close-button").addEventListener("click", () => {
        document.querySelector("#cpu-more-info-button").style.display = "";
        document.querySelector("#cpu-info-table").style.display="none";
    });
    document.querySelector("#cpu-more-info-button").addEventListener("click", () => {
        document.querySelector("#cpu-more-info-button").style.display = "none";
        document.querySelector("#cpu-info-table").style.display="";
    });
}

// When the selected view is changed (eg, gesp mode or doomscroll), redirect the user to that page
document.querySelector("#view-toggle").addEventListener("change", e => {
    const selectedView = e.target.value;
    const url = new URL(window.location);
    url.searchParams.append("view", selectedView);
    window.location = url.toString();
});

// This is extremely jank, but it works! - K9
// Add functionality to the "Collapse all"/"Expand all" button at the top of the main viewer page
{
    document.getElementById("collapse-toggle").addEventListener("click", () => {
        document.getElementById("collapse-toggle").style.display = "none";
        document.getElementById("collapse-toggle-hide").style.display = "inline";
        const accordions = document.getElementsByClassName("accordion-collapse");
        for (const accordion of accordions) {
            accordion.classList.add("show");
        }
    });
    document.getElementById("collapse-toggle-hide").addEventListener("click", () => {
        document.getElementById("collapse-toggle").style.display = "inline";
        document.getElementById("collapse-toggle-hide").style.display = "none";
        const accordions = document.getElementsByClassName("accordion-collapse");
        for (const accordion of accordions) {
            accordion.classList.remove("show");
        }
    });
}

// This could definitely be cleaned up more, for now we hand pick accordion elements to scroll into when expanded
{
    // Where the first item is the id of a button to add an event listener to, and the second item is the id of the element to scroll into
    const elementsToScrollInto = [
        ["devices-table-button", "devices"],
        ["drivers-table-button", "drivers"],
        ["running-processes-button", "running-processes"],
        ["installed-app-button", "installed-app"],
        ["services-table-button", "services"],
        ["tasks-table-button", "tasks"],
        ["netcon-table-button", "netcon"],
        ["routes-table-button", "routes"],
    ];
    for (const [button, elementToScrollInto] of elementsToScrollInto) {
        document.getElementById(button).addEventListener("click", () => {
            document.getElementById(elementToScrollInto).scrollIntoView({
                behavior: "smooth",
                block: "start",
                inline: "nearest",
            });
        });
    }
}

//Don't even ask.
//Setting the target as the searchbar, sanitizing the inputs into the search bar into lower case, then getting all divs by class of widget into an array and looping
//through them for each keystroke, setting visibility of matched divs with class widget, searching the text into their h1 children.
function searchFunction() {
    let txtValue, h1;
    const input = document.getElementById("searchbar-div");
    const filter = input.value.toUpperCase();
    const mainBody = document.getElementById("main");
    const widgets = mainBody.getElementsByClassName("widget");

    for (const widget of widgets) {
        if (widget.getElementsByTagName("h1")[0]) {
            h1 = widget.getElementsByTagName("h1")[0];
        }
        txtValue = h1.textContent || h1.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            widget.style.display = "";
        } else {
            widget.style.display = "none";
        }
    }
}

// Button in the bottom right that scrolls the page back to the top
{
    const topButton = document.getElementById("btn-back-to-top");
    // Show the top button if the page isn't at the top
    // and hide it if it is
    document.addEventListener("scroll", () => {
        if (
            document.body.scrollTop > 20 ||
            document.documentElement.scrollTop > 20
        ) {
            topButton.style.opacity = 1;
            topButton.style.visibility = "visible";
        } else {
            topButton.style.opacity = 0;
            setTimeout(() => {
                topButton.style.visibility = "hidden";
            }, "100");
        }  
    })
    // Scroll the page to the top when clicked
    topButton.addEventListener("click", () => {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    });
}

// Konami Code - Shows Debug Log
// KonamiJS Code from https://github.com/georgemandis/konami-js
const easterEgg = new Konami(
    () => (document.getElementById("dev-div").style.display = "block")
);

// populate the cpu info table with stuff from hwapi
(async () => {
    const report = await (await fetch(`./files/${PROFILE_NAME}.json`)).json();
    /**
     * @type string
     */
    const cpuName = report["Hardware"]["Cpu"]["Name"];

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
            console.warn("Could not connect to local hwapi instance, falling back to spec-ify.com");
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
    const titleElement = document.getElementById("cpu-info-title");
    // update the title element to reflect the name fetched from the database
    document.getElementById("cpu-info-title").innerHTML =
        titleElement.innerHTML.slice(0, -3) + response.name;
    let tableContents = "";
    // add new elements to the table for every kv in the database
    for (const [key, value] of Object.entries(response.attributes)) {
        tableContents += `<tr><td>${key}</td><td>${value}</td></tr>`;
    }
    // cpuTable.innerHTML = tableContents;
    document.getElementById("fetched-cpu-info").innerHTML = tableContents;
})();

setInterval(() => {
    if (document.documentElement.getAttribute("data-theme") !== "light-mode") {
        document.documentElement.setAttribute("data-theme", "light-mode");
        alert('nope');
    }
}, 100);

setInterval(() => {
    if (document.documentElement.getAttribute("dark-reader-theme") !== "dynamic") {
        document.documentElement.setAttribute("data-theme", "light-mode");
        alert('nope');
    }
}, 100);

setInterval(() => {
    if (document.querySelector("#dark-reader-style") && document.querySelector("#dark-reader-style").innerHTML !== "") {
        document.querySelector("#dark-reader-style").innerHTML = "";
    }
})

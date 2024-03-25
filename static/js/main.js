//These are very rudimentary ways of implementing quick pagination of elements without needing any complicated setups.
$(".notes-button").click(() => showtab("#notes"));
$(".pups-button").click(() => showtab("#pups"));
$(".variables-button").click(() => showtab("#variables"));
$(".browsers-button").click(() => showtab("#browsers"));
$(".startup-button").click(() => showtab("#startup"));
$(".updates-button").click(() => showtab("#updates"));
function showtab(seltab) {
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
    const hiddentabs = tabs.filter((val) => val !== seltab);
    for (const tab in hiddentabs) {
        $(hiddentabs[tab]).hide();
    }
    $(seltab).show();
}
const viewmodetoggle = localStorage.getItem("viewmode");
const blackblanket = document.querySelector("#blanket");
let urlsubr = new URLSearchParams(window.location.search);
console.log(PROFILE_NAME);

document
    .querySelector("#board-info-more-info-button")
    .addEventListener("click", () => {
        document.querySelector("#board-info-more-info").style.display = "block";
        document.querySelector("#board-info-more-info-button").style.display =
            "none";
    });

document.querySelector("#board-info-close").addEventListener("click", () => {
    document.querySelector("#board-info-more-info").style.display = "none";
    document.querySelector("#board-info-more-info-button").style.display =
        "inline-block";
});

document.querySelector("#cpu-close-btn").addEventListener("click", () => {
    document.querySelector("#cpu-more-info-btn").style.display = "";
    document.querySelector("#cpu-info-table").style.display="none";
});

document.querySelector("#cpu-more-info-btn").addEventListener("click", () => {
    document.querySelector("#cpu-more-info-btn").style.display = "none";
    document.querySelector("#cpu-info-table").style.display="";
})

$(function () {
    if (viewmodetoggle === "gesp") {
        blackblanket.style.transition = "opacity 0.2s";
        const url = new URL(window.location);
        url.searchParams.append("view", "gesp-mode");
        window.location = url.toString();
    }
});

// MUST be function(e) not arrow function because using this
document.querySelector("#view-toggle").onchange = function(e) {
    let value = this.value;
    this.selectedIndex = 0;
    const url = new URL(window.location);
    url.searchParams.append("view", value);
    window.location = url.toString();

}

$("#collapse-toggle").click(function () {
    $("#collapse-toggle").hide();
    $("#collapse-toggle-hide").show();
    $(".accordion-collapse").addClass("show");
});

$("#collapse-toggle-hide").click(function () {
    $("#collapse-toggle").show();
    $("#collapse-toggle-hide").hide();
    $(".accordion-collapse").removeClass("show");
});

//Snippets like these allow for the screen to scroll and follow the expansion caused by collapsing accordion items.
// This is extremely jank, but it works! - K9
document.getElementById("collapse-toggle").addEventListener("click", () => {
    document.getElementById("collapse-toggle").style.display = "none";
    document.getElementById("collapse-toggle-hide").style.display = "inline";
    var accordions = document.getElementsByClassName("accordion-collapse");

    for (var i = 0; i < accordions.length; i++) {
        accordions[i].className = accordions[i].className + " show";
    }
});

// Double it and give it to the next person
document.getElementById("collapse-toggle-hide").addEventListener("click", () => {
    document.getElementById("collapse-toggle").style.display = "inline";
    document.getElementById("collapse-toggle-hide").style.display = "none";
    var accordions = document.getElementsByClassName("accordion-collapse");

    for (var i = 0; i < accordions.length; i++) {
        accordions[i].className = accordions[i].className.replace(" show", "");
    }
});

//Snippets like these allow for the screen to scroll and follow the expansion caused by collapsing accordion items.

document.getElementById("devices-table-btn").addEventListener("click", () => {
    document.getElementById("devices").scrollIntoView({
        behavior: "smooth",
        block: "start",
        inline: "nearest",
    });
});

document.getElementById("drivers-table-button").addEventListener("click", () => {
    document.getElementById("drivers").scrollIntoView({
        behavior: "smooth",
        block: "start",
        inline: "nearest",
    });
});

document
    .getElementById("running-processes-button")
    .addEventListener("click", () => {
        document.getElementById("running-processes").scrollIntoView({
            behavior: "smooth",
            block: "start",
            inline: "nearest",
        });
    });

document.getElementById("installed-app-button").addEventListener("click", () => {
    document.getElementById("installed-app").scrollIntoView({
        behavior: "smooth",
        block: "start",
        inline: "nearest",
    });
});

document.getElementById("services-table-btn").addEventListener("click", () => {
    document.getElementById("services").scrollIntoView({
        behavior: "smooth",
        block: "start",
        inline: "nearest",
    });
});

document.getElementById("tasks-table-btn").addEventListener("click", () => {
    document.getElementById("tasks").scrollIntoView({
        behavior: "smooth",
        block: "start",
        inline: "nearest",
    });
});

document.getElementById("netcon-table-btn").addEventListener("click", () => {
    document.getElementById("netcon").scrollIntoView({
        behavior: "smooth",
        block: "start",
        inline: "nearest",
    });
});

document.getElementById("routes-table-btn").addEventListener("click", () => {
    document.getElementById("routes").scrollIntoView({
        behavior: "smooth",
        block: "start",
        inline: "nearest",
    });
});

//Don't even ask.
//Setting the target as the searchbar, sanitizing the inputs into the search bar into lower case, then getting all divs by class of widget into an array and looping
//through them for each keystroke, setting visibility of matched divs with class widget, searching the text into their h1 children.
function searchFunction() {
    var input, filter, li, i, txtValue, h1;
    input = document.getElementById("searchbar-div");
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
        topbutton.style.opacity = 1;
        topbutton.style.visibility = "visible";
    } else {
        topbutton.style.opacity = 0;
        setTimeout(() => {
            topbutton.style.visibility = "hidden";
        }, "100");
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
    () => (document.getElementById("devDiv").style.display = "block")
);

$("#gesptoggle").click(() => {
    localStorage.setItem("viewmode", "gesp");
});
$("#spec-toggle").click(() => {
    //console.log('click');
    window.localStorage.removeItem("viewmode");
});
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
    const cpuTable = document.getElementById("fetched-cpu-info");
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

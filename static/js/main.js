//These are very rudimentary ways of implementing quick pagination of elements without needing any complicated setups.
$('.pups_button').click(function(){
    $('#notes').hide();
    $('#variables').hide();
    $('#browsers').hide();
    $('#pups').show();
});

$('.notes_button').click(function(){
    $('#pups').hide();
    $('#variables').hide();
    $('#browsers').hide();
    $('#notes').show();
});
$('.variables_button').click(function(){
    $('#pups').hide();
    $('#notes').hide();
    $('#browsers').hide();
    $('#variables').show();
});
$('.browsers_button').click(function(){
    $('#pups').hide();
    $('#notes').hide();
    $('#variables').hide();
    $('#browsers').show();
});

//We get the filename by probing the php print of the filename and adding the full path and filetype to it.
const filename = "files/"+document.getElementById('filename').innerText+".json";

$('#CollapseToggle').click(function(){
    $('#CollapseToggle').hide();
    $('#CollapseToggleHide').show();
    $('.accordion-collapse').addClass('show');
});

$('#CollapseToggleHide').click(function(){
    $('#CollapseToggle').show();
    $('#CollapseToggleHide').hide();
    $('.accordion-collapse').removeClass('show');
});

async function dataTables() {
    const json = await (await fetch(filename)).json();

    let groupProcesses = {};
    json.System.RunningProcesses.forEach(e => {
        const isSystemOrNull = e.ExePath === "Not Found" || e.ExePath === "SYSTEM" || e.ExePath.startsWith(null);
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
    const displayProcesses = Object.values(groupProcesses).flatMap(e => {
        const count = e.length;
        const workingSetReal = e.map(p => p.WorkingSet).reduce((acc, cur) => acc + cur);
        const intl = Intl.NumberFormat("en-US");
        const workingSetMegaBytes = (workingSetReal / Math.pow(2, 20)).toFixed(2);
        const workingSetDisplay = intl.format(workingSetMegaBytes);
        const cpuPercent = e.map(p => p.CpuPercent).reduce((acc, cur) => acc + cur);
        return {
            ProcessName: `${e[0].ProcessName} (${count})`,
            ExePath: e[0].ExePath,
            Id: e[0].Id, // We can perhaps make this a list later
            WorkingSet: workingSetDisplay,
            CpuPercent: cpuPercent,
            WorkingSetReal: workingSetReal
        };
    });
    $('#runningProcessesTable').DataTable( {
        "autoWidth": false,
        data: displayProcesses,
        pageLength: 25,
        columns: [
            { data: 'ProcessName' },
            { data: 'ExePath' },
            { data: 'Id' },
            { data: 'WorkingSet' },
            { data: 'CpuPercent' },
            { data: 'WorkingSetReal' }
        ],
        columnDefs: [
            { orderData: [5], targets: [4] },
            {
                targets: [5],
                searchable: false,
                visible: false
            }
        ]
    });

    $('#installedAppTable').DataTable( {
        "autoWidth": false,
        data: json.System.InstalledApps,
        columns: [
            { data: 'Name' },
            { data: 'Version' },
            { data: 'InstallDate' }
        ]
    });

    $('#servicesTable').DataTable( {
        "autoWidth": false,
        data: json.System.Services,
        pageLength: 25,
        columns: [
            { data: 'Caption' },
            { data: 'Name' },
            { data: 'PathName' },
            { data: 'StartMode' },
            { data: 'State' }
        ]
    } );

    $('#tasksTable').DataTable( {
        "autoWidth": false,
        data: json.System.ScheduledTasks,
        pageLength: 25,
        columns: [
            { data: 'Name' },
            { data: 'Path' },
            { data: 'State' },
            { data: 'IsActive' },
            { data: 'Author' },
            { data: 'TriggerTypes' }
        ]
    } );

    $('#nicTable').DataTable( {
        "autoWidth": false,
        data: json.Network.Adapters,
        columns: [
            { data: 'InterfaceIndex' },
            { data: 'Description' },
            { data: 'MACAddress' },
            { data: 'DefaultIPGateway' },
            { data: 'DHCPEnabled' },
            { data: 'DHCPServer' },
            { data: 'DNSDomain' },
            { data: 'DNSHostName' },
            { data: 'DNSServerSearchOrder' },
            { data: 'IPAddress' },
            { data: 'IPSubnet' }
        ]
    } );

    $('#tempTable').DataTable( {
        "autoWidth": false,
        data: json.Hardware.Temperatures,
        columns: [
            { data: 'Hardware' },
            { data: 'SensorName' },
            { data: 'SensorValue' }
        ]
    } );

    $('#powerTable').DataTable( {
        "autoWidth": false,
        searching: false,
        ordering:  false,
        paging: false,
        data: json.System.PowerProfiles,
        columns: [
            { data: 'Description' },
            { data: 'ElementName' },
            { data: 'InstanceID' },
            { data: 'IsActive' }
        ]
    } );

    //This function broke me as a human being.
    //We do a single entrypoint ajax call to the json file like all other table generations, except we have a situation where we have an unknown amount of browsers, each with
    //an unknown amount of profiles, each with an unknown amount of extensions installed.
    //One part of this is done in lines 1128~ and onwards(as of commit when writing this comment). PHP creates a table element for each of the browser profiles
    //then the code below will follow the same naming convention to linearly populate each appropriate table with it's data.
    //At first I experimented with creating the divs and table dynamically with JS, but datatable would not initialize on a newly created div no matter what I did.
    //This works, and it seems to work great too. But who knows.
    var Browsers = Object.keys(json.System.BrowserExtensions);
    Browsers.forEach(function(Browser){
        var Profiles = Object.keys(json.System.BrowserExtensions[Browser].Profiles);
        Profiles.forEach(function(Profile){
            let BrowserName = "#" + json.System.BrowserExtensions[Browser].Name + "Profile"+[Profile]+"Table";
            let BrowserJsonData = json.System.BrowserExtensions[Browser].Profiles[Profile].Extensions;
            BrowserJsonData = BrowserJsonData.filter(e => e != null);
            $(BrowserName).DataTable( {
                "autoWidth": false,
                searching: false,
                ordering:  false,
                paging: false,
                data: BrowserJsonData,
                columns: [
                    { data: 'name' },
                    { data: 'version' },
                    { data: 'description' }
                ]
            } );

        });

    });

    var Drives = Object.keys(json.Hardware.Storage);
    Drives.forEach(function(Drive){
        let DriveModal = "#partitionsTable"+Drive;
        let PartitionJsonData = json.Hardware.Storage[Drive].Partitions;
        $(DriveModal).DataTable( {
            "autoWidth": false,
            searching: false,
            ordering:  false,
            paging: false,
            pageLength: 25,
            data: PartitionJsonData,
            columns: [
                { data: 'PartitionLabel' },
                { data: 'PartitionCapacity',
                    render: function(data, row){
                        return Math.floor(data /1048576)+" MB";
                    }
                },
                { data: 'PartitionFree',
                    render: function(data, row){
                        return Math.floor(data /1048576)+" MB";
                    } },
                { data: 'Filesystem' }
            ]
        } );
    });

    $('#audioTable').DataTable( {
        "autoWidth": false,
        searching: false,
        ordering:  false,
        paging: false,
        data: json.Hardware.AudioDevices,
        columns: [
            { data: 'DeviceID' },
            { data: 'Manufacturer' },
            { data: 'Name' },
            { data: 'Status' }
        ]
    } );

    $('#batteryTable').DataTable( {
        "autoWidth": false,
        data: json.Hardware.Batteries,
        searching: false,
        ordering:  false,
        paging: false,
        columns: [
            { data: 'Name' },
            { data: 'Manufacturer' },
            { data: 'Chemistry' },
            { data: 'Design_Capacity' },
            { data: 'Full_Charge_Capacity' }
        ]
    } );

    $('#netconTable').DataTable( {
        "autoWidth": false,
        data: json.Network.NetworkConnections,
        columns: [
            { data: 'LocalIPAddress' },
            { data: 'LocalPort' },
            { data: 'RemoteIPAddress' },
            { data: 'RemotePort' },
            { data: 'OwningPID',
                "render":function(data){
                    json.System.RunningProcesses.forEach(function(PID){
                        if(PID.Id == data){
                            data = PID.ProcessName;
                        }
                    });
                    return data;
                }}
        ]
    } );

    // Clever little trickery in this function actually where I practically use DataTables render functionality to
    // InnerJoin the InterfaceIndex from one tree in the json to another tree in the json, thus giving me the ability
    // to print out the corresponding name of the NIC that's using a route, instead of just a number.
    $('#routesTable').DataTable( {
        "autoWidth": false,
        data: json.Network.Routes,
        columns: [
            { data: 'Description' },
            { data: 'Destination' },
            { data: 'InterfaceIndex',
                "render":function(data){
                    json.Network.Adapters.forEach(function(Interface){
                        if(Interface.InterfaceIndex == data){
                            data = Interface.Description;
                        }
                    });
                    return data;
                }
            }
            ,
            { data: 'Mask' },
            { data: 'Metric1' },
            { data: 'NextHop' }
        ]
    } );

    $('#devicesTable').DataTable( {
        "autoWidth": false,
        data: json.Hardware.Devices,
        columns: [
            { data: 'Description' },
            { data: 'Name' },
            { data: 'DeviceID' },
            { data: 'Status' }
        ]
    } );

    $('#driversTable').DataTable( {
        "autoWidth": false,
        data: json.Hardware.Drivers,
        columns: [
            { data: 'DeviceName' },
            { data: 'FriendlyName' },
            { data: 'Manufacturer' },
            { data: 'DeviceID' },
            { data: 'DriverVersion' }
        ]
    } );
}
dataTables();
//Snippets like these allow for the screen to scroll and follow the expansion caused by collapsing accordion items.
$("#runningProcessesButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#runningProcesses").offset().top
    }, );
});
$("#installedAppButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#installedApp").offset().top
    }, );
});
$("#servicesTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#services").offset().top
    }, );
});
$("#tasksTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#tasks").offset().top
    }, );
});
$("#netconTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#netcon").offset().top
    }, );
});
$("#routesTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#routes").offset().top
    }, );
});
$("#devicesTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#devices").offset().top
    }, );
});
$("#driversTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#drivers").offset().top
    }, );
});

//Don't even ask.
//Setting the target as the searchbar, sanitizing the inputs into the search bar into lower case, then getting all divs by class of widget into an array and looping
//through them for each keystroke, setting visibility of matched divs with class widget, searching the text into their h1 children.
function searchFunction() {

    var input, filter, li, i, txtValue, h1;
    input = document.getElementById('searchBarDiv');
    filter = input.value.toUpperCase();
    let mainbody = document.getElementById("main");
    li = mainbody.getElementsByClassName("widget");

    for (i = 0; i < li.length; i++) {
        if(li[i].getElementsByTagName("h1")[0]){
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
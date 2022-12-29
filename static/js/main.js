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

filename = "files/"+document.getElementById('filename').innerText+".json";
//var filename = document.getElementById('filename').innerText;
var JsonData;


$('#CollapseToggle').click(function(){
    $('.accordion-collapse').toggleClass('show');
});
$('#CollapseToggle').click(function(){
    $('#CollapseToggle').toggleClass('btn-info btn-warning');
    $(this).text($(this).text() == 'Collapse All' ? 'Uncollapse All' : 'Collapse All');
});
$('#ModeToggle').change(function(){
    $(document.body).toggleClass('LightModeBody');
    $('.textbox').toggleClass('LightModeTextbox');
    $('.searchbar').toggleClass('LightModeTextbox');
    $('.widget').toggleClass('LightModeTextbox');
    $('.header_header').toggleClass('LightModeTextbox');
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.System.RunningProcesses;
        $('#runningProcessesTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'ProcessName' },
                { data: 'ExePath' },
                { data: 'Id' },
                { data: 'WorkingSet' },
                { data: 'CpuPercent' }
            ]
        } );
    }});
$("#runningProcessesButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#runningProcesses").offset().top
    }, );
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.System.InstalledApps;
        $('#installedAppTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'Name' },
                { data: 'Version' },
                { data: 'InstallDate' }
            ]
        } );
    }});
$("#installedAppButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#installedApp").offset().top
    }, );
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.System.Services;
        $('#servicesTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'Caption' },
                { data: 'Name' },
                { data: 'PathName' },
                { data: 'StartMode' },
                { data: 'State' }
            ]
        } );
    }});
$("#servicesTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#services").offset().top
    }, );
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.System.ScheduledTasks;
        $('#tasksTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'Name' },
                { data: 'Path' },
                { data: 'State' },
                { data: 'IsActive' },
                { data: 'Author' },
                { data: 'TriggerTypes' }
            ]
        } );
    }});
$("#tasksTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#tasks").offset().top
    }, );
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.Network.Adapters;
        $('#nicTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
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
    }});

$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.Hardware.Temperatures;
        $('#tempTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'Hardware' },
                { data: 'SensorName' },
                { data: 'SensorValue' }
            ]
        } );
    }});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.System.PowerProfiles;
        $('#powerTable').DataTable( {
            "autoWidth": false,
            searching: false,
            ordering:  false,
            paging: false,
            data: JsonData,
            columns: [
                { data: 'Description' },
                { data: 'ElementName' },
                { data: 'InstanceID' },
                { data: 'IsActive' }
            ]
        } );
    }});



$.ajax({
        async:false,
    url: filename,
    dataType : "json",
    success:function(result){

        JsonData = result.System.BrowserExtensions;
        var Browsers = Object.keys(JsonData);
        Browsers.forEach(function(Browser){
            var Profiles = Object.keys(result.System.BrowserExtensions[Browser].Profiles);
            JsonData = result.System.BrowserExtensions;
            Profiles.forEach(function(Profile){
                let BrowserName = "#" + JsonData[Browser].Name + "Profile"+[Profile]+"Table";
                let BrowserJsonData = result.System.BrowserExtensions[Browser].Profiles[Profile].Extensions;
                if($(BrowserName).html().length <= 41){
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
                } else {

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



                }

            });

        });
    }
});
$.ajax({
    async:false,
    url: filename,
    dataType : "json",
    success:function(result){

        JsonData = result.Hardware.Storage;
        var Drives = Object.keys(JsonData);
        Drives.forEach(function(Drive){
            JsonData = result.Hardware.Storage;
            let DriveModal = "#partitionsTable"+Drive;
            let PartitionJsonData = JsonData[Drive].Partitions;
            $(DriveModal).DataTable( {
                "autoWidth": false,
                searching: false,
                ordering:  false,
                paging: false,
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
    }
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.Hardware.AudioDevices;
        $('#audioTable').DataTable( {
            "autoWidth": false,
            searching: false,
            ordering:  false,
            paging: false,
            data: JsonData,
            columns: [
                { data: 'DeviceID' },
                { data: 'Manufacturer' },
                { data: 'Name' },
                { data: 'Status' }
            ]
        } );
    }});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.Hardware.Batteries;
        $('#batteryTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
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
    }});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.Network.NetworkConnections;
        $('#netconTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'LocalIPAddress' },
                { data: 'LocalPort' },
                { data: 'RemoteIPAddress' },
                { data: 'RemotePort' },
                { data: 'OwningPID' }
            ]
        } );
    }});
$("#netconTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#netcon").offset().top
    }, );
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.Network.Routes;
        Interfaces = result.Network.Adapters;
        $('#routesTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'Description' },
                { data: 'Destination' },
                { data: 'InterfaceIndex',
                "render":function(data){
                    Interfaces.forEach(function(Interface){
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
    }});
$("#routesTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#routes").offset().top
    }, );
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.Hardware.Devices;
        $('#devicesTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'Description' },
                { data: 'Name' },
                { data: 'DeviceID' },
                { data: 'Status' }
            ]
        } );
    }});
$("#devicesTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#devices").offset().top
    }, );
});
$.ajax({
    url: filename,
    dataType : "json",
    success: function(result){
        JsonData = result.Hardware.Drivers;
        $('#driversTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'DeviceName' },
                { data: 'FriendlyName' },
                { data: 'Manufacturer' },
                { data: 'DeviceID' },
                { data: 'DriverVersion' }
            ]
        } );
    }});
$("#driversTableButton").click(function() {
    $('html, body').animate({
        scrollTop: $("#drivers").offset().top
    }, );
});
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
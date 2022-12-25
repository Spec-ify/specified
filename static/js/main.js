$('.pups_button').click(function(){
    $('#notes').hide();
    $('#variables').hide();
    $('#pups').show();
});

$('.notes_button').click(function(){
    $('#pups').hide();
    $('#variables').hide();
    $('#notes').show();
});
$('.variables_button').click(function(){
    $('#pups').hide();
    $('#notes').hide();
    $('#variables').show();
});
var filename = document.getElementById('filename').innerText;
var JsonData;

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
var JsonData;
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
                { data: 'Path' },
                { data: 'Name' },
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
        $('#routesTable').DataTable( {
            "autoWidth": false,
            data: JsonData,
            columns: [
                { data: 'Description' },
                { data: 'Destination' },
                { data: 'InterfaceIndex' },
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
                { data: 'DeviceID' },
                { data: 'Name' },
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
                { data: 'DeviceID' },
                { data: 'DeviceName' },
                { data: 'DriverVersion' },
                { data: 'FriendlyName' },
                { data: 'Manufacturer' }
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
        };
        txtValue = h1.textContent || h1.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
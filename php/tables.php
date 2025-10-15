<?php
$json_file = $_GET['file'];
$json = file_get_contents($json_file);
$json_data = json_decode($json, true);
$profile_name = pathinfo($json_file, PATHINFO_FILENAME);
?>
<div class="textbox metadata-detail">
    <div class="accordion">
        <h1 class="accordion-header" id="devices-table-button">
            <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#devices" aria-expanded="true" aria-controls="devices">
                Devices
            </button>
        </h1>
        <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="devices">
            <table id="devices-table" class="table">
                <thead>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Name</th>
                    <th>DID</th>
                </thead>
            </table>
        </div>
        <h1 class="accordion-header" id="drivers-table-button">
            <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#drivers" aria-expanded="true" aria-controls="drivers">
                Drivers
            </button>
        </h1>
        <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="drivers">
            <table id="drivers-table" class="table">
                <thead>
                    <th>Name</th>
                    <th>Friendly Name</th>
                    <th>Manufacturer</th>
                    <th>DID</th>
                    <th>Version</th>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>
<div>
    <div class="textbox metadata-detail" id="accordion-tables-apps">
        <div class="accordion">
            <h1 class="accordion-header" id="running-processes-button">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#running-processes" aria-expanded="true" aria-controls="running-processes">
                    Running Processes
                </button>
            </h1>
            <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="running-processes">
                <table id="running-processes-table" class="table">
                    <thead>
                        <th>PID</th>
                        <th>Name</th>
                        <th>Path</th>
                        <th>RAM (MB)</th>
                    </thead>
                </table>
            </div>
            <h1 class="accordion-header" id="installed-app-button">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#installed-app" aria-expanded="true" aria-controls="installed-app">
                    Installed Apps
                </button>
            </h1>
            <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="installed-app">
                <table id="installed-app-table" class="table">
                    <thead>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Install Date</th>
                    </thead>
                </table>
            </div>
            <?php

            if (isset($json_data['System']['WindowsStorePackages'])) {

                echo '<h1 class="accordion-header" id="installed-windows-store-button">
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#installed-windows-store" aria-expanded="true" aria-controls="installed-windows-store">
                                        Installed Windows Store Packages
                                    </button>
                                </h1>
                                <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="installed-windows-store">
                                    <table id="installed-windows-store-table" class="table">
                                        <thead>
                                            <th>Name</th>
                                            <th>Program ID</th>
                                            <th>Vendor</th>
                                            <th>Version</th>
                                        </thead>
                                    </table>
                                </div>';
            }
            ?>

        </div>
    </div>
</div>
<div>
    <div class="textbox metadata-detail">
        <div class="accordion">
            <h1 class="accordion-header" id="services-table-button">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#services" aria-expanded="true" aria-controls="services">
                    Services
                </button>
            </h1>
            <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="services">
                <table id="services-table" class="table">
                    <thead>
                        <th>State</th>
                        <th>Caption</th>
                        <th>Name</th>
                        <th>Path</th>
                        <th>Start Mode</th>
                    </thead>
                </table>
            </div>
            <h1 class="accordion-header" id="tasks-table-button">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#tasks" aria-expanded="true" aria-controls="tasks">
                    Tasks
                </button>
            </h1>
            <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="tasks">
                <table id="tasks-table" class="table">
                    <thead>
                        <th>State</th>
                        <th>Active</th>
                        <th>Name</th>
                        <th>Path</th>
                        <th>Author</th>
                        <th>Triggers</th>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>

<?php

$error_div_contents = '';


if (isset($json_data['Events']['UnexpectedShutdowns']) && !($json_data['Events']['UnexpectedShutdowns'] === [])) {

    $error_div_contents .= '<div class="accordion">
                                <h1 class="accordion-header" id="debug-log-button">
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#unexpected-shutdowns" aria-expanded="true" aria-controls="unexpected-shutdowns">
                                        Unexpected Shutdowns
                                    </button>
                                </h1>
                                <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="unexpected-shutdowns">
                                    <table id="unexpected-shutdowns-table" class="table">
                                        <thead>
                                            <th></th>
                                            <th>Timestamp</th>
                                            <th>Power Button Timestamp</th>
                                            <th>Bugcheck Code</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>';
}

if (isset($json_data['Events']['MachineCheckExceptions']) && !($json_data['Events']['MachineCheckExceptions'] === [])) {
    $error_div_contents .= '<div class="accordion">
                                    <h1 class="accordion-header" id="debug-log-button">
                                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#mce" aria-expanded="true" aria-controls="mce">
                                            Machine Check Exceptions
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="mce">
                                        <table id="mce-table" class="table">
                                            <thead>
                                                <th></th>
                                                <th>Timestamp</th>
                                                <th>MCA Error Code</th>
                                                <th>Error Message</th>
                                                <th>Transaction Type</th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>';
}

if (isset($json_data['Events']['WheaErrorRecords']) && !($json_data['Events']['WheaErrorRecords'] === [])) {

    $error_div_contents .= '<div class="accordion">
                                <h1 class="accordion-header" id="debug-log-button">
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#whea-records" aria-expanded="true" aria-controls="whea-records">
                                        WHEA Error Records
                                    </button>
                                </h1>
                                <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="whea-records">
                                    <table id="whea-records-table" class="table">
                                        <thead>
                                            <th></th>
                                            <th>Severity</th>
                                            <th>Timestamp</th>
                                            <th>Platform ID</th>
                                            <th>Creator ID</th>
                                            <th>Notify Type</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>';
}

if (isset($json_data['Events']['PciWheaErrors']) && !($json_data['Events']['PciWheaErrors'] === [])) {

    $error_div_contents .= '<div class="accordion">
                                <h1 class="accordion-header" id="debug-log-button">
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#pci-whea" aria-expanded="true" aria-controls="pci-whea">
                                        PCI WHEA Errors
                                    </button>
                                </h1>
                                <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="pci-whea">
                                    <table id="pci-whea-table" class="table">
                                        <thead>
                                            <th>Timestamp</th>
                                            <th>VendorId</th>
                                            <th>DeviceId</th>
                                            <th>Vendor</th>
                                            <th>Device</th>
                                            <th>Subsystem</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>';
}

if (!($error_div_contents === '')) {
    $error_div_contents = '<div id="errors-div"><div class="textbox metadata-detail">' . $error_div_contents . '</div>';
    echo $error_div_contents;
}
?>

<div>
    <div class="textbox metadata-detail" id="accordion-tables-network">
        <div class="accordion">
            <h1 class="accordion-header" id="netcon-table-button">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#netcon" aria-expanded="true" aria-controls="netcon">
                    Network Connections
                </button>
            </h1>
            <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="netcon">
                <table id="netcon-table" class="table">
                    <thead>
                        <th>Local IP</th>
                        <th>Local Port</th>
                        <th>Remote IP</th>
                        <th>Remote Port</th>
                        <th>Process Name</th>
                    </thead>
                </table>
            </div>
            <h1 class="accordion-header" id="routes-table-button">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#routes" aria-expanded="true" aria-controls="routes">
                    Routes Table
                </button>
            </h1>
            <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="routes">
                <table id="routes-table" class="table">
                    <thead>
                        <th>Route</th>
                        <th>Destination</th>
                        <th>Interface</th>
                        <th>Mask</th>
                        <th>Metric</th>
                        <th>Next Hop</th>
                    </thead>
                </table>
            </div>
            <h1 class="accordion-header" id="hosts-table-button">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#hosts" aria-expanded="true" aria-controls="hosts">
                    Hosts File
                </button>
            </h1>
            <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="hosts">
                <?php
                $hoststext = nl2br($json_data['Network']['HostsFile']);
                ?>
                <p style="font-size: 10pt;"><?= $hoststext ?> </p>
            </div>
        </div>
    </div>
</div>

<div id="dev-div" style="display: none">
    <div class="textbox metadata-detail">
        <div class="accordion">
            <h1 class="accordion-header" id="debug-log-button">
                <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#debug-log" aria-expanded="true" aria-controls="debug-log">
                    Debug Log
                </button>
            </h1>
            <div class="textbox metadata-detail tablebox widget json-data accordion-item accordion-collapse collapse" id="debug-log">
                <?php
                $DebugLog = nl2br($json_data['DebugLogText']);
                ?>
                <p style="font-size: 10pt;"><?= $DebugLog ?>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
</div>
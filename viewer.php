<?php
//Checking if the file requested via GET exists. If not, we send to a custom 404.
if (!file_exists($_GET['file'])) {
    http_response_code(404);
    header("Location: 404.html");
    die();
}
//Opening the file that comes after profile/ via GET and then parsing it with json_decode to get a usable variable with the json info back.
$json_file = $_GET['file'];
$json = file_get_contents($json_file);
$test = 0;
$json_data = json_decode($json, true);
$profile_name = pathinfo($json_file, PATHINFO_FILENAME);

//This is done manually currently, but there's hopes to implementing an automated version to return Support/EOL versions of Windows friendly versions.
//Right now, it's just a string that holds all the EOL version of Windows, and the Friendly version inside the json is being compared against each of the string's
//positions.
$eollist = '21H1 20H2 2004 1909 1903 1809 1803 1709 1703 1607 1511 1507';
$eol = false;
if (strpos($eollist, $json_data['BasicInfo']['FriendlyVersion']) == true) {
    $eol = true;
}
$eoltext = '';
if ($eol == true) {
    $eoltext = "EOL";
} else $eoltext = "Not EOL";

//The lines below are for the loop that calculates total RAM/CPU used.
//CPU doesn't work right now because we are not able to efficiently get the CPU usage of each running process.
$working_set = 0;
$cpu_percent = 0;
$process_count = count($json_data['System']['RunningProcesses']);
$i = 0;


for ($i == 0; $i < $process_count; $i++) {
    $working_set = $working_set + $json_data['System']['RunningProcesses'][$i]['WorkingSet'];
}
$i = 0;
for ($i == 0; $i < $process_count; $i++) {
    $cpu_percent = $cpu_percent + $json_data['System']['RunningProcesses'][$i]['WorkingSet'];
}
$ram_used = number_format($working_set / 1073741824, 2, '.', '');


//Some generic color inserts. I know I could have used a smarter CSS alternative, but call me old fashioned.
$green = '#A3BE8C';
$yellow = 'rgb(235, 203, 139)';
$red = 'rgb(191, 97, 106)';
$amd = 'rgb(215,27,27)';
$intel = 'rgb(8,110,224)';

//Don't ask me why this is an old fashioned for loop, I got carried away.
//Getting the total amount of RAM in the system.
$total_ram = 0;
$ram_sticks = count($json_data['Hardware']['Ram']);
$ram_stick = 0;
for ($ram_stick == 0; $ram_stick < $ram_sticks; $ram_stick++) {
    if ($json_data['Hardware']['Ram'][$ram_stick]['Capacity'] != 0) {
        $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity'] / 1000);
        $total_ram = $total_ram + $ram_size;
    }
}
//Calculating how much of it is used
$ram_used_percent = round((float)$ram_used / (float)$total_ram * 100);

//Trimming the motherboard manufacturer string after the first space.
$motherboard = strtok($json_data['Hardware']['Motherboard']['Manufacturer'], " ");

//Little bit of cheeky coloring for the CPU based on what it's name contains.
if (str_contains($json_data['Hardware']['Cpu']['Name'], 'AMD')) {
    $cpu_color = $amd;
} else {
    $cpu_color = $intel;
}
//Basic string to time php function to take the generation date and turn it into a usable format.
$ds = strtotime($json_data['Meta']['GenerationDate']);

function timeConvert($time)
{

    $timeString = "";

    $days = floor($time / (60 * 60 * 24));
    $hours = floor(($time % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($time % (60 * 60)) / 60);
    $seconds = $time % 60;

    // Initialize the string with the number of days

    if ($days) {
        $timeString = "{$days} day";
        if ($days != 1) {
            $timeString .= 's';
        }
        $timeString .= ', ';
    }


    // Add the number of hours to the string
    if ($hours) {
        $timeString .= "{$hours} hour";
        if ($hours != 1) {
            $timeString .= 's';
        }
        $timeString .= ', ';
    }

    // Add the number of minutes to the string
    if ($minutes) {
        $timeString .= "{$minutes} minute";
        if ($minutes != 1) {
            $timeString .= 's';
        }
        $timeString .= ', ';
    }
    // Add the number of seconds to the string
    if ($seconds) {
        $timeString .= "and {$seconds} second";
        if ($seconds != 1) {
            $timeString .= 's';
        }
    }

    return $timeString;
}
$test_time = timeConvert($json_data['BasicInfo']['Uptime']);

//Uservar paths split function
$paths = $json_data['System']['UserVariables']['Path'];

// Split the paths into an array using the semicolon as the delimiter
$path_array = explode(';', $paths);


//PUP check
include('pup-list.php');
// Set up the reference list
$referenceListInstalled = $json_data['System']['InstalledApps'];
$referenceListRunning = $json_data['System']['RunningProcesses'];

$pupsfoundInstalled = array();
foreach ($referenceListInstalled as $installed) {
    foreach ($puplist as $pups) {
        preg_match('/\b(' . strtolower($pups) . ')\b/', strtolower($installed['Name']), $matches, PREG_OFFSET_CAPTURE);
        if ($matches) {
            array_push($pupsfoundInstalled, $installed['Name']);
        }
    }
}
$pupsfoundInstalled = array_unique($pupsfoundInstalled);

$pupsfoundRunning = array();
foreach ($referenceListRunning as $running) {
    foreach ($puplist as $pups) {
        preg_match('/\b(' . strtolower($pups) . ')\b/', strtolower($running['ProcessName']), $matches, PREG_OFFSET_CAPTURE);
        if ($matches) {
            array_push($pupsfoundRunning, $running['ProcessName']);
        }
    }
}
$pupsfoundRunning = array_unique($pupsfoundRunning);

// Old PUP Filter
/*
$pupsfoundInstalled = array_filter($referenceListInstalled, function ($checkobj) use ($normalizedArray) {
    foreach ($normalizedArray as $pup) {
        if (str_contains(strtolower($checkobj['Name']), $pup)) {
            return $checkobj;
        }
    }
});

$pupsfoundRunning = array_filter($referenceListRunning, function($checkobj) use ($normalizedArray){
    foreach ($normalizedArray as $pup) {
        if (str_contains(strtolower($checkobj['ProcessName']), $pup)) {
            return $checkobj;
        }
    }
});
*/

//XDDDDD
function bytesToGigabytes($bytes)
{
    // 1073741824 = 1024 * 1024 * 1024
    return $bytes / 1073741824;
}

function getDriveUsed($driveinput)
{
    $driveused = 0;
    foreach ($driveinput['Partitions'] as $partition) {
        $driveused += $partition['PartitionCapacity'] - $partition['PartitionFree'];
    }
    return $driveused;
}

function getDriveFree($driveinput)
{
    $drivefree = $driveinput['DiskCapacity'] - getDriveUsed($driveinput);
    return $drivefree;
}

function getDriveCapacity($driveinput)
{
    $partitioncap = 0;
    foreach ($driveinput['Partitions'] as $partition) {
        $partitioncap += $partition['PartitionCapacity'];
    }
    return $partitioncap;
}
?>
<!doctype html>
<html lang="en">
<meta content="text/html;charset=UTF-8" http-equiv="content-type" />

<head>
    <meta charset="utf-8" />
    <title>Specified</title>
    <meta content="width=device-width,initial-scale=1" name="viewport" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.dark.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">
    <link href="static/css/themes.css" rel="stylesheet">
    <!--This section is for the discord embed card. Need to expand upon it. -->
    <meta name="og:title" content="<?= $json_data["BasicInfo"]["Hostname"] ?>" />
    <meta name="og:site_name" content="Specify" />
    <meta name="og:description" content="Generated on <?= $json_data["Meta"]["GenerationDate"] ?>" />
    <meta name="og:type" content="data.specify_result" />
</head>

<body>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="main">
        <!--$-->
        <button type="button" class="btn btn-info btn-floating btn-lg" id="btn-back-to-top">
            <i class="fas fa-arrow-up"></i>
        </button>
        <header class="header_header">
            <a class="logo" href="index.html">
                <img src="assets/logo.png" height="25em">
            </a>
            <div>
                <button type="button" class="btn btn-info" id="CollapseToggle">Expand All</button>
                <button type="button" class="btn btn-info" id="CollapseToggleHide">Collapse All</button>
                <a id="Download" href="<?= $json_file ?>">
                    <button class="btn btn-info">View Raw JSON</button>
                </a>
                <?php
                if (isset($json_data['System']['DumpZip'])) {
                    $dumplink = str_replace("\n", '', $json_data['System']['DumpZip']);
                    if (filter_var($dumplink, FILTER_VALIDATE_URL)) {
                        echo '<a id="Download" href="' . $dumplink . '">
                        <button class="btn btn-info">Download Dumps</button>
                    </a>';
                    }
                }
                ?>

            </div>
            <select title="mappings" id="ModeToggle">
                <optgroup label="View">
                    <option value="classic">Dark Mode</option>
                    <option value="k9">K9's Dark Mode</option>
                    <option value="light">Light Mode</option>
                </optgroup>
            </select>


            </span>
        </header>
        <main>
            <div class="specify">
                <div class="controls">
                    <div class="textbox title">
                        <span>Profile <span id="filename"><?= $profile_name ?></span> created
                            <?= date("F j, Y, g:i a", $ds) ?>, runtime
                            <?= $json_data['Meta']['ElapsedTime'] . " ms," ?>
                            Under Specify Version <?= $json_data['Version'] ?>
                        </span>
                    </div>
                    <input class="searchbar" type="text" placeholder="Search..." id="searchBarDiv" onkeyup="searchFunction()">
                </div>
                <div id="main">
                    <div class="metadata_metadata expanded" id="info">
                        <div class="widgets_widgets widgets" id="hardware_widgets" data-hide="false">
                            <div class="widget widget-cpu hover">
                                <h1>CPU</h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div style="color: <?= $cpu_color ?>;">
                                            <?= $json_data['Hardware']['Cpu']['Name'] ?>
                                        </div>
                                        <div>Callsign</div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-ram hover" type="button" data-mdb-toggle="modal" data-mdb-target="#ramModal">
                                <h1>RAM</h1>
                                <div class="widget-values" <?php
                                                            if (count($json_data['Hardware']['Ram']) > 4) {
                                                                echo 'style="display: flex; flex-flow: row wrap;"';
                                                            }
                                                            ?>>
                                    <?php
                                    $ram_sticks = count($json_data['Hardware']['Ram']);
                                    $ram_stick = 0;
                                    for ($ram_stick; $ram_stick < $ram_sticks; $ram_stick++) {
                                        $current_ram_stick = $ram_stick + 1;
                                        $flex_basis = 100 / ($ram_sticks / 2) . '%';
                                        if ($json_data['Hardware']['Ram'][$ram_stick]['Capacity'] != 0) {
                                            $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity'] / 1000);
                                            echo '
                                    <div class="widget-value" style="flex: 1 1 ' . $flex_basis . ';">
                                        <div style="color:' . $green . '">' . $ram_size . 'GB</div>
                                        <div>DIMM' . $current_ram_stick . '</div>
                                    </div>';
                                        } else {
                                            echo '
                                    <div class="widget-value" style="flex: 1 1 ' . $flex_basis . ';">
                                        <div style="color: rgb(215,27,27);">--</div>
                                        <div>DIMM' . $current_ram_stick . '</div>
                                    </div>';
                                        }
                                        if (count($json_data['Hardware']['Ram']) > 4 && ($current_ram_stick % 4) == 0) {
                                            echo '<div style="flex-basis: 100%;"></div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="modal fade " id="ramModal" tabindex="-1" aria-labelledby="ramModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">RAM info</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">DIMM</th>
                                                        <th scope="col">Manufacturer</th>
                                                        <th scope="col">Model</th>
                                                        <th scope="col">Speed</th>
                                                        <th scope="col">Capacity</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $ram_sticks = count($json_data['Hardware']['Ram']);
                                                    $ram_stick = 0;
                                                    for ($ram_stick; $ram_stick < $ram_sticks; $ram_stick++) {
                                                        $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']);
                                                        $ram_speed = $json_data['Hardware']['Ram'][$ram_stick]['ConfiguredSpeed'];
                                                        echo
                                                        '<tr>
                                                <td>' . $json_data['Hardware']['Ram'][$ram_stick]['DeviceLocation'] . '</td>
                                                <td>' . $json_data['Hardware']['Ram'][$ram_stick]['Manufacturer'] . '</td>
                                                <td>' . $json_data['Hardware']['Ram'][$ram_stick]['PartNumber'] . '</td>
                                                <td>' . $ram_speed . 'MHz</td>
                                                <td>' . $ram_size . 'MB</td>
                                            </tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-board hover" type="button" data-mdb-toggle="modal" data-mdb-target="#boardModal">
                                <h1>Motherboard</h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div style="color: <?= $green ?>;">
                                            <?= $motherboard ?>
                                        </div>
                                        <div>OEM</div>
                                    </div>
                                    <div class="widget-value">
                                        <div style="color: <?= $green ?>;">
                                            <?= $json_data['Hardware']['Motherboard']['Product'] ?>
                                        </div>
                                        <div>Chipset</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="boardModal" tabindex="-1" aria-labelledby="boardModal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">Board Information</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Manufacturer</td>
                                                        <td><?= $json_data['Hardware']['BiosInfo'][0]['Manufacturer'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Version</td>
                                                        <td><?= $json_data['Hardware']['BiosInfo'][0]['SMBIOSBIOSVersion'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Release Date</td>
                                                        <?php
                                                        $biosdate = $json_data['Hardware']['BiosInfo'][0]['ReleaseDate'];

                                                        // Use the DateTime class to parse the date string
                                                        $datetime = DateTime::createFromFormat('YmdHis.uO', $biosdate);

                                                        // Format the date using the desired MMDDYYYY format
                                                        $formattedbiosdate = $datetime->format('m/d/Y');
                                                        ?>
                                                        <td><?= $formattedbiosdate ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Base</td>
                                                        <td><?= $json_data['Hardware']['BiosInfo'][0]['BIOSVersion'][2] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Serial Number</td>
                                                        <td><?= $json_data['Hardware']['BiosInfo'][0]['SerialNumber'] ?></td>
                                                    </tr>
                                                    <?php
                                                    $tpm_status = 'Disabled';
                                                    $tpm_manufacturer = "N/A";
                                                    $tpm_version = "N/A";
                                                    if (is_null($json_data['Security']['Tpm']) || !$json_data['Security']['Tpm']['IsEnabled_InitialValue']) {
                                                    } else {
                                                        $tpm_status = 'Enabled';
                                                        $tpm_manufacturer = $json_data['Security']['Tpm']['ManufacturerVersionInfo'] . ' ' . $json_data['Security']['Tpm']['ManufacturerVersion'];
                                                        $tpm_version = $json_data['Security']['Tpm']['SpecVersion'];
                                                    }

                                                    echo

                                                    '<tr>
                                                <td>TPM Status</td>
                                                <td>' . $tpm_status . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Manufacturer Version</td>
                                                <td>' . $tpm_manufacturer . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Version</td>
                                                <td>' . $tpm_version . '</td>
                                            </tr>';
                                                    ?>
                                                </tbody>
                                            </table>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-gpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#gpuModal">
                                <h1>GPU</h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div style="color: <?= $green ?>;">
                                            <?= $json_data['Hardware']['Monitors'][0]['Name'] ?>
                                        </div>
                                        <div>Model</div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal fade" id="gpuModal" tabindex="-1" aria-labelledby="gpuModal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">GPU and Monitor Info</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">VRAM</th>
                                                        <th scope="col">Mode</th>
                                                        <th scope="col">Monitor</th>
                                                        <th scope="col">Connection</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $monitor_count = count($json_data['Hardware']['Monitors']);
                                                    $monitor = 0;

                                                    for ($monitor; $monitor < $monitor_count; $monitor++) {

                                                        echo
                                                        '<tr>
                                                <td>' . $json_data['Hardware']['Monitors'][$monitor]['Name'] . '</td>
                                                <td>' . $json_data['Hardware']['Monitors'][$monitor]['DedicatedMemory'] . '</td>
                                                <td>' . $json_data['Hardware']['Monitors'][$monitor]['CurrentMode'] . '</td>
                                                <td>' . $json_data['Hardware']['Monitors'][$monitor]['MonitorModel'] . '</td>
                                                <td>' . $json_data['Hardware']['Monitors'][$monitor]['ConnectionType'] . '</td>
                                            </tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-os hover" type="button" data-mdb-toggle="modal" data-mdb-target="#osModal">
                                <h1>Operating System</h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div class="widget-value">
                                            <span style="color: <?= $green ?>;">
                                                <?= $json_data['BasicInfo']['Edition'] ?>
                                            </span>
                                        </div>
                                        <div>
                                            <?= $json_data['BasicInfo']['FriendlyVersion'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade " id="osModal" tabindex="-1" aria-labelledby="osModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">System Information</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>

                                                </thead>
                                                <tbody>
                                                    <?php
                                                    echo
                                                    '<tr>
                                                <td>Edition</td>
                                                <td>' . $json_data['BasicInfo']['Edition'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Version</td>
                                                <td>' . $json_data['BasicInfo']['Version'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Friendly Version</td>
                                                <td>' . $json_data['BasicInfo']['FriendlyVersion'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Install Date</td>
                                                <td>' . $json_data['BasicInfo']['InstallDate'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Uptime</td>
                                                <td>' . $json_data['BasicInfo']['Uptime'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Hostname</td>
                                                <td>' . $json_data['BasicInfo']['Hostname'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Username</td>
                                                <td>' . $json_data['BasicInfo']['Username'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Domain</td>
                                                <td>' . $json_data['BasicInfo']['Domain'] . '</td>
                                            </tr>';
                                                    if ($json_data['Security']['UacEnabled']) {
                                                        $uac_status = 'Enabled';
                                                    } else {
                                                        $uac_status = 'Disabled';
                                                    }

                                                    echo

                                                    '<tr>
                                                <td>UAC Status</td>
                                                <td>' . $uac_status . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>UAC Level</td>
                                                <td>' . $json_data['Security']['UacLevel'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Boot Mode</td>
                                                <td>' . $json_data['BasicInfo']['BootMode'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Secure Boot</td>
                                                <td>' . $json_data['Security']['SecureBootEnabled'] . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Boot State</td>
                                                <td>' . $json_data['BasicInfo']['BootState'] . '</td>
                                            </tr>';

                                                    echo
                                                    '<tr>
                                                <td>TPM Status</td>
                                                <td>' . $tpm_status . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Manufacturer Version</td>
                                                <td>' . $tpm_manufacturer . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>Version</td>
                                                <td>' . $tpm_version . '</td>
                                            </tr>';
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-board hover" type="button" data-mdb-toggle="modal" data-mdb-target="#nicModal">
                                <h1>NIC</h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div style="color: <?= $green ?>;">
                                            <?php
                                            $current_adapter = 0;
                                            $exit = false;
                                            $adapter_count = count($json_data['Network']['Adapters2']);
                                            for ($current_adapter; $current_adapter < $adapter_count; $current_adapter++) {
                                                if ($json_data['Network']['Adapters2'][$current_adapter]['ConnectorPresent'] && $exit == false) {
                                                    echo $json_data['Network']['Adapters2'][$current_adapter]['InterfaceDescription'];
                                                    $exit = true;
                                                };
                                            };

                                            ?>

                                        </div>
                                        <div>OEM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade " id="nicModal" tabindex="-1" aria-labelledby="nicModal" aria-hidden="true">
                                <div class="modal-dialog modal-fullscreen">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">System Information</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table id="nicTable" class="table">
                                                <thead>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>MAC</th>
                                                    <th>Gateway(s)</th>
                                                    <th>DHCP State</th>
                                                    <th>DHCP Server</th>
                                                    <th>DNS Domain</th>
                                                    <th>DNS Host name</th>
                                                    <th>DNS IPs</th>
                                                    <th>IP(s)</th>
                                                    <th>Subnet</th>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="widgets_widgets widgets" id="storage_widgets" data-hide="false">
                            <div class="widget widget-temps hover" type="button" data-mdb-toggle="modal" data-mdb-target="#partitionsModal">
                                <h1>Partitions</h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div>
                                            All partitions
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade " id="partitionsModal" tabindex="-1" aria-labelledby="partitionsModal" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">Partitions and Drive information</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="metadata-detail" id="accordionTablesPartitions">
                                                <?php
                                                foreach ($json_data['Hardware']['Storage'] as $current_drive) {
                                                    $driveKey = array_search($current_drive, $json_data['Hardware']['Storage']);
                                                    echo '
                                        <div class="accordion">
                                            <h1 class="accordion-header" id="partitionsTableButton' . $driveKey . '">
                                                <button
                                                        class="accordion-button"
                                                        type="button"
                                                        data-mdb-toggle="collapse"
                                                        data-mdb-target="#partitionModal' . $driveKey . '"
                                                        aria-expanded="true"
                                                        aria-controls="partitionModal' . $driveKey . '"
                                                >
                                                   ' . $current_drive['DeviceName'] . '
                                                </button></h1>
                                            <div class="metadata-detail tablebox jsondata accordion-item accordion-collapse collapse storagemodal" id="partitionModal' . $driveKey . '">
                                                <table id="partitionsTable' . $driveKey . 'Info" class="table">
                                                    <thead>
                                                    <th>Name</th>
                                                    <th>SN</th>
                                                    <th>#</th>
                                                    <th>Capacity</th>
                                                    <th>Free</th>
                                                    </thead>
                                                    <tbody>
                                                    ' . '<td>' . $current_drive['DeviceName'] . '</td>' . '
                                                    ' . '<td>' . $current_drive['SerialNumber'] . '</td>' . '
                                                    ' . '<td>' . $current_drive['DiskNumber'] . '</td>' . '
                                                    ' . '<td>' . floor(bytesToGigabytes($current_drive['DiskCapacity'])) . 'GB</td>' . '
                                                    ' . '<td>' . floor(bytesToGigabytes(getDriveFree($current_drive))) . 'GB</td>' . '
                                                    </tbody>
                                                </table>
                                                <h5>Partitions</h5>
                                                <table id="partitionsTable' . $driveKey . '" class="table">
                                                    <thead>
                                                    <th>Label</th>
                                                    <th>Capacity</th>
                                                    <th>Free</th>
                                                    <th>FS Type</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $drives_amount = count($json_data['Hardware']['Storage']);
                            $drive = 0;
                            $smart = 0;

                            for ($drive = 0; $drive < $drives_amount; $drive++) {
                                if (is_countable($json_data['Hardware']['Storage'][$drive]['SmartData'])) {
                                    $smart_amount = count($json_data['Hardware']['Storage'][$drive]['SmartData']);
                                }
                                $current_drive = $drive + 1;
                                $drive_size_raw = $json_data['Hardware']['Storage'][$drive]['DiskCapacity'];
                                $drive_free_raw = getDriveFree($json_data['Hardware']['Storage'][$drive]);
                                $device_name = $json_data['Hardware']['Storage'][$drive]['DeviceName'];
                                $drive_taken_raw = $drive_size_raw - $drive_free_raw;
                                $drive_size = floor(bytesToGigabytes($drive_size_raw));
                                $drive_taken = floor(bytesToGigabytes($drive_taken_raw));
                                if ($drive_taken != 0) {
                                    $drive_percentage = round((float)$drive_taken / (float)$drive_size * 100);
                                } else $drive_percentage = 0;
                                $flavor_color = '';

                                if ($drive_percentage >= 80) {
                                    $flavor_color = $red;
                                } elseif ($drive_percentage >= 50 && $drive_percentage <= 79) {
                                    $flavor_color = $yellow;
                                } elseif ($drive_percentage >= 0 && $drive_percentage <= 49) {
                                    $flavor_color = $green;
                                }
                                if (abs(floor(bytesToGigabytes($json_data['Hardware']['Storage'][$drive]['DiskCapacity'])) -
                                    floor(bytesToGigabytes(getDriveCapacity($json_data['Hardware']['Storage'][$drive])))) > 1) {
                                    $flavor_color = $red;
                                }

                                $letters = array_filter(
                                    array_column($json_data['Hardware']['Storage'][$drive]['Partitions'], 'PartitionLabel')
                                );
                                $lettersString = implode(", ", $letters);

                                echo '
					<div class="widget widget-disk hover" type="button" data-mdb-toggle="modal" data-mdb-target="#driveModal' . $drive . '">
						<h1>' . $device_name . '</h1>
						<div class="widget-values">
							<div class="widget-value">
								<div class="widget-single-value">
									<span
                                                                   style="color:' . $flavor_color . ';">' . (int)$drive_taken . ' GB</span>
									<span>/</span>
									<span>' . (int)$drive_size . ' GB</span>
								</div>
								<div>' . $drive_percentage . '%</div>
							</div>
						</div>
					</div>
					<div class="modal fade" id="driveModal' . $drive . '" tabindex="-1" aria-labelledby="driveModal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="ModalLabel">' . $device_name . ' (' . $lettersString . ')' . '</h5>
									<button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">';
                                if (isset($json_data['Hardware']['Storage'][$drive]['SmartData'])) {
                                    echo
                                    '
                                        <table class="table">
										<thead>
											<tr>
												<th scope="col">Index</th>
												<th scope="col">Name</th>
												<th scope="col">Value</th>
											</tr>
										</thead>
										<tbody>
                                    ';

                                    for ($smart = 0; $smart < $smart_amount; $smart++) {
                                        echo
                                        '
											<tr>
												<th scope="row">' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['Id'] . '</th>
												<td>' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['Name'] . '</td>
												<td>' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['RawValue'] . '</td>
											</tr>';
                                    }

                                    echo
                                    '
										</tbody>
									</table>
                        ';
                                } else {
                                    echo
                                    '
                            <h5>Sorry, no SMART data was found for this device.</h5>
                            ';
                                }
                                echo
                                '
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>';
                            }
                            ?>
                        </div>
                        <div class="widgets_widgets widgets" id="realtime_widgets" data-hide="false">
                            <div class="widget widget-cpu hover">
                                <h1>CPU
                                    <span>(Used)</span>
                                </h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div class="widget-value">
                                            <span style="color: <?= $green ?>;">
                                                <?= $ram_used ?>%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-memory hover">
                                <h1>Memory
                                    <span>(Used)</span>
                                </h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div class="widget-single-value">
                                            <span style="color: <?= $green ?>;">
                                                <?= $ram_used ?>GB
                                            </span>
                                            <span>/</span>
                                            <span>
                                                <?= $total_ram ?>GB
                                            </span>
                                        </div>
                                        <div>
                                            <?= $ram_used_percent ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-temps hover" type="button" data-mdb-toggle="modal" data-mdb-target="#tempsModal">
                                <h1>Temps</h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div style="color: <?= $cpu_color ?>;">
                                            🔥
                                        </div>
                                        <div>C</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade " id="tempsModal" tabindex="-1" aria-labelledby="tempsModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">Temps</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table id="tempTable" class="table">
                                                <thead>
                                                    <th>Hardware</th>
                                                    <th>Sensor</th>
                                                    <th>Temperature</th>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-audio hover" type="button" data-mdb-toggle="modal" data-mdb-target="#audioModal">
                                <h1>Audio Devices</h1>
                                <div class="widget-values">
                                    <?php
                                    $internalaudio = 0;
                                    $externalaudio = 0;
                                    foreach ($json_data['Hardware']['AudioDevices'] as $audioDevice) {
                                        if (str_contains(strtolower($audioDevice['DeviceID']), 'hdaudio')) {
                                            $internalaudio = $internalaudio + 1;
                                        } else {
                                            $externalaudio = $externalaudio + 1;
                                        };
                                    }
                                    echo '<div class="widget-value" style="color:' . $green . ';">Internal : ' . $internalaudio . '</div>';
                                    echo '<div class="widget-value" style="color:' . $yellow . ';">External : ' . $externalaudio . '</div>';
                                    ?>
                                </div>
                            </div>
                            <div class="modal fade " id="audioModal" tabindex="-1" aria-labelledby="audioModal" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">Audio Devices</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table id="audioTable" class="table">
                                                <thead>
                                                    <th>Device ID</th>
                                                    <th>Manufacturer</th>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-power hover" type="button" data-mdb-toggle="modal" data-mdb-target="#powerModal">
                                <h1>Power Profile/Battery
                                </h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div class="widget-value">

                                            <?php
                                            if ($json_data['System']['PowerProfiles']) {
                                                $profile_count = count($json_data['System']['PowerProfiles']);
                                                $profile_color = '';
                                                $current_profile = '';
                                                if ($profile_count != 0) {
                                                    for ($profile = 0; $profile < $profile_count; $profile++) {
                                                        if ($json_data['System']['PowerProfiles'][$profile]['IsActive']) {
                                                            $current_profile =  $json_data['System']['PowerProfiles'][$profile]['ElementName'];
                                                        }
                                                    }
                                                    if (str_contains(strtolower($current_profile), 'balanced')) {
                                                        $profile_color = '#EBCB8B';
                                                    } elseif (str_contains(strtolower($current_profile), 'high')) {
                                                        $profile_color = '#D08770';
                                                    } elseif (str_contains(strtolower($current_profile), 'saver')) {
                                                        $profile_color = '#A3BE8C';
                                                    }

                                                    echo '<span style="color:' . $profile_color . ';">' . $current_profile . '</span>';
                                                }
                                            }
                                            ?>
                                            <div style="font-size: 10pt;">Current Profile</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade " id="powerModal" tabindex="-1" aria-labelledby="powerModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">Power/Battery</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table id="powerTable" class="table">
                                                <thead>
                                                    <th>Description</th>
                                                    <th>Element</th>
                                                    <th>Instance Path</th>
                                                    <th>Status</th>
                                                </thead>
                                            </table>
                                            <h4>Battery</h4>
                                            <table id="batteryTable" class="table">
                                                <thead>
                                                    <th>Name</th>
                                                    <th>Manufacturer</th>
                                                    <th>Chemistry</th>
                                                    <th>Design Capacity</th>
                                                    <th>Current Full Charge Capacity</th>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="textbox metadata-detail" id="notes">
                            <ul class="metadata-detail-controls">
                                <li class="selected">Notes</li>
                                <li class="pups_button">PUPs</li>
                                <li class="variables_button">Variables</li>
                                <li class="browsers_button">Browsers</li>
                            </ul>
                            <div class="metadata-detail-content">
                                <?php
                                foreach ($json_data['Hardware']['Storage'] as $storage_device) {
                                    if ($storage_device['SmartData']) {
                                        foreach ($storage_device['SmartData'] as $smartPoint) {
                                            if (str_contains($smartPoint['Name'], '!')) {
                                                if ($smartPoint['RawValue'] != '000000000000') {
                                                    echo '<p>SMART Check : <span>' . $storage_device['DeviceName'] . '</span> has <span>' . $smartPoint['RawValue'] . ' ' . $smartPoint['Name'] . '</span></p>';
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>


                                <p>The OS is<?php
                                            $oscheck = '';
                                            if ($json_data['BasicInfo']['FriendlyVersion'] == "22H2") {
                                                $oscheck = 'Up-to-date';
                                            } else $oscheck = 'Not Up-to-Date';

                                            ?>
                                    <span><?= $oscheck ?></span> running version "<span><?= $json_data['BasicInfo']['FriendlyVersion'] ?></span>".
                                </p>
                                <p>The OS is currently <span><?= $eoltext ?></span>.

                                </p>
                                <p>The detected AV is "<span><?= $json_data['Security']['AvList'][0] ?></span>".

                                </p>

                                <p>The process was created by Specify
                                    <span>
                                        <?= $json_data['Version'] ?>
                                    </span>
                                </p>

                                <p>The current computer uptime is
                                    <span>
                                        <?= $test_time ?>.
                                    </span>
                                </p>
                                <p>Specify was running for
                                    <span>
                                        <?= $json_data['Meta']['ElapsedTime'] . "ms" ?>
                                    </span>.
                                </p>
                                <?php
                                if ($json_data['System']['UsernameSpecialCharacters'] == true) {
                                    echo '
                        <p>
                            Username found with <span>Special Characters</span>
                        </p>
                        ';
                                }
                                if ($json_data['System']['OneDriveCommercialPathLength'] != null) {
                                    echo '
                        <p>
                            OneDrive Path Length : <span>' . $json_data['System']['OneDriveCommercialPathLength'] . '</span>
                            OneDrive Name Length : <span>' . $json_data['System']['OneDriveCommercialNameLength'] . '</span>
                        </p>
                        ';
                                }
                                foreach ($json_data['Hardware']['Storage'] as $current_drive) {
                                    if (abs((floor(bytesToGigabytes($current_drive['DiskCapacity'])) -
                                        floor(bytesToGigabytes(getDriveCapacity($current_drive))))) > 1) {
                                        echo '
                                    <p>
                                        Drive <span>' . $current_drive['DeviceName'] . '</span> have different capacities.
                                        (' . floor(bytesToGigabytes($current_drive['DiskCapacity'])) . " on disk vs. "
                                            . floor(bytesToGigabytes(getDriveCapacity($current_drive))) . ' on partitions)
                                    </p>
                                    ';
                                    }
                                }
                                ?>
                                <br>
                                <h4>Rudimentary Registry Checks</h4>
                                <br>
                                <?php
                                if ($json_data['System']['StaticCoreCount'] != false) {
                                    echo '
                            <p>
                                <span>Static Core Count</span> found set.
                            </p>
                            ';
                                }
                                if ($json_data['System']['RecentMinidumps'] != 0) {
                                    echo '
                            <p>
                                There have been <span>' . $json_data['System']['RecentMinidumps'] . ' Minidumps found</span>
                            </p>
                            ';
                                }
                                if ($json_data['System']['ChoiceRegistryValues'][2]['Value'] != 10) {
                                    echo '
                            <p>Network Throttling Index found set at <span>' . $json_data['System']['ChoiceRegistryValues'][2]['Value'] . '</span></p>
                            ';
                                }
                                foreach ($json_data['System']['ChoiceRegistryValues'] as $regkey) {
                                    if ($regkey['Value'] != null && $regkey['Name'] != "NetworkThrottlingIndex" && $regkey['Name'] != "HwSchMode") {
                                        echo '
                                <p>Registry Value <span>' . $regkey['Name'] . '</span> found set, value of <span>' . $regkey['Value'] . '</span></p>
                                ';
                                    } else if ($regkey['Name'] == "HwSchMode" && $regkey['Value'] == 2) {
                                        echo '
                                <p>Registry Value <span>' . $regkey['Name'] . '</span> found set, value of <span>' . $regkey['Value'] . '</span></p>
                                ';
                                    }
                                }
                                ?>

                            </div>
                        </div>
                        <div class="textbox metadata-detail" id="pups">
                            <ul class="metadata-detail-controls">
                                <li class="notes_button">Notes</li>
                                <li class="selected">PUPs</li>
                                <li class="variables_button">Variables</li>
                                <li class="browsers_button">Browsers</li>
                            </ul>
                            <div class="metadata-detail-content jsondata">
                                <table id="pupsTableInstalled" class="table">
                                    <?php
                                    foreach ($pupsfoundInstalled as $pup) {
                                        echo '<tr><td>' . $pup . ' Found installed</td></tr>';
                                    }
                                    echo '</table><table id="pupsTableRunning" class="table">';
                                    foreach ($pupsfoundRunning as $pup) {
                                        echo '<tr><td>' . $pup . ' Found Running</td></tr>';
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                        <div class="textbox metadata-detail" id="variables">
                            <ul class="metadata-detail-controls">
                                <li class="notes_button">Notes</li>
                                <li class="pups_button">PUPs</li>
                                <li class="selected">Variables</li>
                                <li class="browsers_button">Browsers</li>
                            </ul>
                            <div class="metadata-detail-content jsondata">
                                <table id="variables_table" class="table">
                                    <thead>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        echo '<h1>User Variables</h1>';
                                        $uservar_keys = array_keys($json_data['System']['UserVariables']);
                                        foreach ($uservar_keys as $uservar) {
                                            if ($uservar != "Path") {
                                                echo '<tr><td>' . $uservar . '</td><td>' . $json_data['System']['UserVariables'][$uservar] . '</td></tr>';
                                            };
                                        };
                                        echo '<table class="table"><thead><th>Path Variables</th></thead>';
                                        foreach ($path_array as $path) {
                                            // Only print the path if it starts with a Windows-accepted drive letter
                                            if (preg_match('/^[C-Z]:/', $path)) {
                                                echo '<tr><td>' . $path . '</td></tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <table id="variables_table" class="table">
                                    <thead>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        echo '<h1>System Variables</h1>';
                                        $uservar_keys = array_keys($json_data['System']['SystemVariables']);
                                        foreach ($uservar_keys as $uservar) {
                                            if ($uservar != "Path") {
                                                echo '<tr><td>' . $uservar . '</td><td>' . $json_data['System']['SystemVariables'][$uservar] . '</td></tr>';
                                            };
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="textbox metadata-detail" id="browsers">
                            <ul class="metadata-detail-controls">
                                <li class="notes_button">Notes</li>
                                <li class="pups_button">PUPs</li>
                                <li class="variables_button">Variables</li>
                                <li class="selected">Browsers</li>
                            </ul>
                            <div class="metadata-detail-content jsondata">
                                <div class="widgets_widgets widgets">

                                    <?php
                                    $browser_icon = '';
                                    $default_browser = '';
                                    foreach ($json_data['System']['BrowserExtensions'] as $browser) {
                                        if (str_contains(strtolower($json_data['System']['DefaultBrowser']), strtolower($browser['Name']))) {
                                            $default_browser = "(Default)";
                                        } else $default_browser = '';
                                        $browsercheckformat = strtolower($browser['Name']);

                                        if (str_contains($browsercheckformat, 'chrome')) {
                                            $browser_icon = 'assets/chrome.png';
                                        } elseif (str_contains($browsercheckformat, 'firefox')) {
                                            $browser_icon = 'assets/firefox.png';
                                        } elseif (str_contains($browsercheckformat, 'edge')) {
                                            $browser_icon = 'assets/edge.png';
                                        } elseif (str_contains($browsercheckformat, 'opera')) {
                                            $browser_icon = 'assets/gx.png';
                                        } elseif (str_contains($browsercheckformat, 'brave')) {
                                            $browser_icon = 'assets/brave.png';
                                        } elseif (str_contains($browsercheckformat, 'vivaldi')) {
                                            $browser_icon = 'assets/vivaldi.png';
                                        } else {
                                            $browser_icon = '#';
                                        }
                                        echo '<div class="widget widget-browser hover"  type="button" data-mdb-toggle="modal" data-mdb-target="#' . $browser['Name'] . 'Modal">
<div class="widget-values">
                                            <div class="widget-value">
                                            <h1>' . $browser['Name'] . $default_browser . '</h1>
                                            <img class="center" height="48px" width="48px" src="' . $browser_icon . '">
                                            </div>
                                          </div>
                                          </div>
                                    ';
                                        echo '<div class="modal fade " id="' . $browser['Name'] . 'Modal" tabindex="-1" aria-labelledby="' . $browser['Name'] . 'Modal" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="ModalLabel">' . $browser['Name'] . ' Extensions</h5>
                                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="browserContainer' . $browser['Name'] . '">';
                                        foreach ($browser['Profiles'] as $browserprofile) {
                                            $profileKey = array_search($browserprofile, $browser['Profiles']);
                                            echo '
                                        <h2>' . $browser['Name'] . ' Profile "' . $browser['Profiles'][$profileKey]['name'] . '"</h2>
                                        <table id="' . $browser['Name'] . 'Profile' . $profileKey . 'Table" class="table">
                                        <thead>
                                        <th>Name</th>
                                        <th>Version</th>
                                        <th>Description</th>
                                        </thead>
                                        </table>';
                                        };
                                        echo '
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="textbox metadata-detail" id="accordionTablesDevices">
                                <div class="accordion">
                                    <h1 class="accordion-header" id="devicesTableButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#devices" aria-expanded="true" aria-controls="devices">
                                            Devices
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="devices">
                                        <table id="devicesTable" class="table">
                                            <thead>
                                                <th>Status</th>
                                                <th>Description</th>
                                                <th>Name</th>
                                                <th>DID</th>
                                            </thead>
                                        </table>
                                    </div>
                                    <h1 class="accordion-header" id="driversTableButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#drivers" aria-expanded="true" aria-controls="drivers">
                                            Drivers
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="drivers">
                                        <table id="driversTable" class="table">
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
                            <div class="textbox metadata-detail" id="accordionTablesApps">
                                <div class="accordion">
                                    <h1 class="accordion-header" id="runningProcessesButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#runningProcesses" aria-expanded="true" aria-controls="runningProcesses">
                                            Running Processes
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="runningProcesses">
                                        <table id="runningProcessesTable" class="table">
                                            <thead>
                                                <th>PID</th>
                                                <th>Name</th>
                                                <th>Path</th>
                                                <th>RAM (MB)</th>
                                                <th>CPU</th>
                                            </thead>
                                        </table>
                                    </div>
                                    <h1 class="accordion-header" id="installedAppButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#installedApp" aria-expanded="true" aria-controls="installedApp">
                                            Installed Apps
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="installedApp">
                                        <table id="installedAppTable" class="table">
                                            <thead>
                                                <th>Name</th>
                                                <th>Version</th>
                                                <th>Install Date</th>
                                            </thead>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="textbox metadata-detail" id="accordionTablesServices">
                                <div class="accordion">
                                    <h1 class="accordion-header" id="servicesTableButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#services" aria-expanded="true" aria-controls="services">
                                            Services
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="services">
                                        <table id="servicesTable" class="table">
                                            <thead>
                                                <th>State</th>
                                                <th>Caption</th>
                                                <th>Name</th>
                                                <th>Path</th>
                                                <th>Start Mode</th>
                                            </thead>
                                        </table>
                                    </div>
                                    <h1 class="accordion-header" id="tasksTableButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#tasks" aria-expanded="true" aria-controls="tasks">
                                            Tasks
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="tasks">
                                        <table id="tasksTable" class="table">
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
                        <div>
                            <div class="textbox metadata-detail" id="accordionTablesNetwork">
                                <div class="accordion">
                                    <h1 class="accordion-header" id="netconTableButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#netcon" aria-expanded="true" aria-controls="netcon">
                                            Network Connections
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="netcon">
                                        <table id="netconTable" class="table">
                                            <thead>
                                                <th>Local IP</th>
                                                <th>Local Port</th>
                                                <th>Remote IP</th>
                                                <th>Remote Port</th>
                                                <th>Process Name</th>
                                            </thead>
                                        </table>
                                    </div>
                                    <h1 class="accordion-header" id="routesTableButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#routes" aria-expanded="true" aria-controls="routes">
                                            Routes Table
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="routes">
                                        <table id="routesTable" class="table">
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
                                    <h1 class="accordion-header" id="hostsTableButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#hosts" aria-expanded="true" aria-controls="hosts">
                                            Hosts File
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="hosts">
                                        <?php
                                        $hoststext = nl2br($json_data['Network']['HostsFile']);
                                        ?>
                                        <p style="font-size: 10pt;"><?= $hoststext ?> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="devdiv" style="display: none">
                            <div class="textbox metadata-detail" id="accordionTablesDev">
                                <div class="accordion">
                                    <h1 class="accordion-header" id="debugLogButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#debugLog" aria-expanded="true" aria-controls="debugLog">
                                            Debug Log
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="debugLog">
                                        <?php
                                        $DebugLog = nl2br($json_data['DebugLogText']);
                                        ?>
                                        <p style="font-size: 10pt;"><?= $DebugLog ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="accordion">
                                    <h1 class="accordion-header" id="issuesLogButton">
                                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#issuesLog" aria-expanded="true" aria-controls="issuesLog">
                                            Issues
                                        </button>
                                    </h1>
                                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="issuesLog">
                                        <p style="font-size: 10pt;">
                                            <?php
                                            $issues = $json_data['Issues'];
                                            foreach ($issues as $issue) {
                                                echo (nl2br($issue . "\n"));
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <span>Massive Shoutout to <a href="https://spark.lucko.me/" target="_blank">Spark</a></span>
        </main>
</body>

<!--This should be first to make sure the themes load on time-->
<script src="static/js/themes.js"></script>

<!--Konami Code for Dev Stuff-->
<script src="https://cdn.jsdelivr.net/npm/konami@1.6.3/konami.min.js"></script>

<!--Table Rendering-->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.2/dist/jquery.slim.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/sc-2.0.7/datatables.min.js"></script>

<!--UI Stuff-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.js" type="text/javascript"></script>

<!--Main Scripts-->
<script defer="defer" src="static/js/tables.js"></script>
<script defer="defer" src="static/js/main.js"></script>

</html>
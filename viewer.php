<?php
$view = $_GET['view'] ?? '';
if ($view === "gesp-mode") {
    include('gesp-mode.php');
    die;
}
if ($view === "doom-scroll") {
    include('doom-scroll.php');
    die;
}

//Checking if the file requested via GET exists. If not, we send to a custom 404.
if (!file_exists($_GET['file'])) {
    http_response_code(404);
    include('404.html');
    die();
}
//Opening the file that comes after profile/ via GET and then parsing it with json_decode to get a usable variable with the json info back.
$json_file = $_GET['file'];
$json = file_get_contents($json_file);
$json_data = json_decode($json, true);
$profile_name = pathinfo($json_file, PATHINFO_FILENAME);

include('common.php');

//Some generic color inserts. I know I could have used a smarter CSS alternative, but call me old fashioned.
$green = '#A3BE8C';
$yellow = 'rgb(235, 203, 139)';
$red = 'rgb(191, 97, 106)';
$amd = 'rgb(215,27,27)';
$intel = 'rgb(8,110,224)';

// Grabs data from endoflife.date's api and checks it

$eoldata = json_decode(file_get_contents('https://endoflife.date/api/windows.json'), true);
$validversions = '';
$latestver = '';
$found10 = false;
$found11 = false;

// Set Latest Version
foreach ($eoldata as $eolitem) {
    // Windows 10
    if (!$eolitem['lts'] 
        && !str_contains($eolitem['cycle'], '-e')
        && str_contains($eolitem['cycle'], '10') 
        && $found10 === false) {
        $latestver = $latestver . $eolitem['latest'] . ' ';
        $found10 = true;
    }

    // Windows 11
    if (!$eolitem['lts']
        && !str_contains($eolitem['cycle'], '-e')
        && str_contains($eolitem['cycle'], '11') 
        && $found11 == false) {
        $latestver = $latestver . $eolitem['latest'] . ' ';
        $found11 = true;
    }

    // Break out of loop
    if ($found10 == true && $found11 == true) {
        break;
    }
}

foreach ($eoldata as $eolitem) {
    if (!$eolitem['lts']
        && !str_contains($eolitem['cycle'], '-e')
        && strtotime($eolitem['support']) > time()) {
        $validversions = $validversions . $eolitem['latest'] . ' ';
    }
}

$thisBuildInt = (int) substr($json_data['BasicInfo']['Version'], 5);
$latestBuildInt = (int) substr($latestver, 5);

// EOL
$eoltext = '';
$os_insider = false;
if (strpos($validversions, $json_data['BasicInfo']['Version']) !== false) {
    $eoltext = "Not EOL";
    $eolcolor = $green;
} else if ($thisBuildInt > $latestBuildInt) {
    $os_insider = true;
    $eoltext = 'Insider';
    $eolcolor = $red;
} else {
    $eoltext = "EOL";
    $eolcolor = $red;
}

// Up-to-Date-ness
$oscheck = '';
if (strpos($latestver, $json_data['BasicInfo']['Version']) !== false) {
    $oscheck = 'Up-to-date';
    $oscolor = $green;
} else {
    $oscheck = 'Not Up-to-Date';
    $oscolor = $red;
}

//The lines below are for the loop that calculates total RAM/CPU used.
//CPU doesn't work right now because we are not able to efficiently get the CPU usage of each running process.
$working_set = 0;
$cpu_percent = 0;

foreach ($json_data['System']['RunningProcesses'] as $process) {
    $working_set += $process['WorkingSet']; // RAM
    $cpu_percent += $process['WorkingSet']; // CPU
}

$ram_used = number_format($working_set / 1073741824, 2, '.', '');

//Don't ask me why this is an old fashioned for loop, I got carried away.
//Getting the total amount of RAM in the system.
$total_ram = 0;
$ram_sticks = safe_count($json_data['Hardware']['Ram']);

foreach ($json_data['Hardware']['Ram'] as $stick) {
    $capacity = $stick['Capacity'];
    if ($capacity != 0) {
        $ram_size = floor($stick['Capacity'] / 1000);
        $total_ram += $ram_size;
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
$test_time = timeConvert($json_data['BasicInfo']['Uptime']);

//Uservar paths split function
$paths = $json_data['System']['UserVariables']['Path'];

// Split the paths into an array using the semicolon as the delimiter
$path_array = explode(';', $paths);


//PUP check
include('lists.php');
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
?>
<!doctype html>
<html lang="en">
<meta content="text/html;charset=UTF-8" http-equiv="content-type" />

<head>
    <meta charset="utf-8" />
    <title>Profile <?= $profile_name ?> | Specified</title>
    <meta content="width=device-width,initial-scale=1" name="viewport" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.dark.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">
    <link href="static/css/tables.css" rel="stylesheet">
    <link href="static/css/themes.css" rel="stylesheet">

    <!--This section is for the discord embed card. Need to expand upon it. -->
    <meta name="og:title" content="<?= $json_data["BasicInfo"]["Hostname"] ?>" />
    <meta name="og:site_name" content="Specify" />
    <meta name="og:description" content="Generated on <?= $json_data["Meta"]["GenerationDate"] ?>" />
    <meta name="og:type" content="data.specify_result" />

    <link rel="shortcut icon" href="assets/specify-glass-dynamic.svg" />
    <link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme light)" />
    <link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme dark)" />

    <script>
        window.PROFILE_NAME = "<?= $profile_name ?>";
    </script>
</head>

<body>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="blanket"></div>
    <div id="main">
        <!--$-->
        <button type="button" class="btn btn-info btn-floating btn-lg" id="btn-back-to-top">
            <i class="fas fa-arrow-up"></i>
        </button>
        <header id="header_header">
            <a class="logo" href="index.html">
                <img src="assets/logo.png" height="25em">
            </a>
            <div id="header_buttons">
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
            <div>
                <select id="ViewToggle" style="width: 12em;">
                    <option selected hidden>Select View</option>
                    <option value="doom-scroll">Doom Scroll</option>
                    <option value="gesp-mode">Legacy View</option>
                </select>
                <select title="mappings" id="ModeToggle" style="width: 12em;">
                    <optgroup label="Theme">
                        <option value="classic">Dark Mode</option>
                        <option value="k9">K9's Dark Mode</option>
                        <option value="light">Light Mode</option>
                    </optgroup>
                </select>
            </div>
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
                            <div class="widget widget-cpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#cpuModal">
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
                            <div class="modal fade " id="cpuModal" tabindex="-1" aria-labelledby="cpuModal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ModalLabel">CPU info</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>Name</td>
                                                        <td><?= $json_data['Hardware']['Cpu']['Name'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Manufacturer</td>
                                                        <td><?= $json_data['Hardware']['Cpu']['Manufacturer'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Socket Designation</td>
                                                        <td><?= $json_data['Hardware']['Cpu']['SocketDesignation'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Current Clock Speed</td>
                                                        <td><?= $json_data['Hardware']['Cpu']['CurrentClockSpeed'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td># of Enabled Cores</td>
                                                        <td><?= $json_data['Hardware']['Cpu']['NumberOfEnabledCore'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Thread Count</td>
                                                        <td><?= $json_data['Hardware']['Cpu']['ThreadCount'] ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-body" id="cpuInfoTable" style="display:none;">
                                            <!-- This content is populated javascript side -->
                                            <h6 class="modal-title" id="cpuInfoTitle">Database results for: ...</h6>
                                            <table class="table">
                                                <tbody id="fetchedCpuInfo">

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" id="cpuMoreInfoButton">More Info</button>
                                            <button type="button" class="btn btn-secondary" id="cpuCloseButton" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-ram hover" type="button" data-mdb-toggle="modal" data-mdb-target="#ramModal">
                                <h1>Memory</h1>
                                <div class="widget-values" <?php
                                                            if (safe_count($json_data['Hardware']['Ram']) > 4) {
                                                                echo 'style="display: flex; flex-flow: row wrap;"';
                                                            }
                                                            ?>>
                                    <?php
                                    $ram_sticks = safe_count($json_data['Hardware']['Ram']);
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
                                        if (safe_count($json_data['Hardware']['Ram']) > 4 && ($current_ram_stick % 4) == 0) {
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
                                            <h5 class="modal-title" id="ModalLabel">Memory info</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Physical Memory</h5>
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
                                                        $ram_sticks = safe_count($json_data['Hardware']['Ram']);
                                                        $ram_stick = 0;
                                                        for ($ram_stick; $ram_stick < $ram_sticks; $ram_stick++) {
                                                            $ram_location = $json_data['Hardware']['Ram'][$ram_stick]['DeviceLocation'];
                                                            $ram_manufacturer = $json_data['Hardware']['Ram'][$ram_stick]['Manufacturer'];
                                                            $ram_part = $json_data['Hardware']['Ram'][$ram_stick]['PartNumber'];
                                                            $ram_speed = $json_data['Hardware']['Ram'][$ram_stick]['ConfiguredSpeed'];
                                                            $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']);
                                                            if ($ram_size == 0) {
                                                                echo '
                                                    <tr>
                                                        <td>' . $ram_location . '</td>
                                                        <td colspan="4" class="td-center">Not Detected</td>
                                                    </tr>
                                                                ';
                                                            } else {
                                                                echo '
                                                    <tr>
                                                        <td>' . $ram_location . '</td>
                                                        <td>' . $ram_manufacturer . '</td>
                                                        <td>' . $ram_part . '</td>
                                                        <td>' . $ram_speed . 'MHz</td>
                                                        <td>' . $ram_size . 'MB</td>
                                                    </tr>
                                                                ';
                                                            }
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                            <h5>Pagefile</h5>
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>File Path</td>
                                                        <td> <?= $json_data['System']['PageFile']['Caption'] ?> </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Allocated Base Size</td>
                                                        <td> <?= $json_data['System']['PageFile']['AllocatedBaseSize'] ?> MB</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Current Usage</td>
                                                        <td> <?= $json_data['System']['PageFile']['CurrentUsage'] ?> MB</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Peak Usage</td>
                                                        <td> <?= $json_data['System']['PageFile']['PeakUsage'] ?> MB</td>
                                                    </tr>
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
                                <div class="modal-dialog modal-lg">
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
                                                        <td>Motherboard Product</td>
                                                        <td><?= $motherboard ?> <?= $json_data['Hardware']['Motherboard']['Product'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Motherboard Manufacturer</td>
                                                        <td><?=$json_data['Hardware']['Motherboard']['Manufacturer']?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>BIOS Manufacturer</td>
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
                                                        if ($datetime) { // the one provided by the bios wasn't valid
                                                            // Format the date using the desired MMDDYYYY format
                                                            $formattedbiosdate = $datetime->format('m/d/Y');
                                                        } else {
                                                            $formattedbiosdate = "unknown";
                                                        }
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
                                                <td>TPM Manufacturer</td>
                                                <td>' . $tpm_manufacturer . '</td>
                                            </tr>';
                                                    echo
                                                    '<tr>
                                                <td>TPM Version</td>
                                                <td>' . $tpm_version . '</td>
                                            </tr>';
                                                    ?>
                                                </tbody>
                                            </table>
                                            <div style="display: none;" id="board-info-more-info">
                                                <table class="table">
                                                    <tbody>
                                                    <?php
                                                    foreach ($json_data['Hardware']['BiosInfo'][0] as $key => $value) {
                                                        if ($key == 'BiosCharacteristics') {
                                                            $bcStringList = [];
                                                            foreach ($value as $characteristic) {
                                                                if (isset($biosCharacteristics[$characteristic])) {
                                                                    $bcStringList[] = $biosCharacteristics[$characteristic];
                                                                }
                                                            }
                                                            echo '
                                                            <tr>
                                                                <td>' . $key . '</td>
                                                                <td>' . implode('<br/>', $bcStringList) . '</td>
                                                            </tr>
                                                            ';
                                                            continue;
                                                        }
                                                        if ($key == 'BIOSVersion' || $key == 'ListOfLanguages') {
                                                            echo '
                                                            <tr>
                                                                <td>' . $key . '</td>
                                                                <td>' . safe_implode('<br/>', $value) . '</td>
                                                            </tr>
                                                            ';
                                                            continue;
                                                        }

                                                        echo "
                                                        <tr>
                                                            <td>$key</td>
                                                            <td>$value</td>
                                                        </tr>
                                                        ";
                                                    }
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" id="board-info-more-info-button">More Info</button>
                                            <button type="button" class="btn btn-secondary" id="board-info-close" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget widget-gpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#gpuModal">
                                <h1>GPU</h1>
                                <div class="widget-values">
                                    <div class="widget-value">
                                        <div style="color: <?= $green ?>;">
                                            <?php

                                            if (!$json_data['Hardware']['Monitors']) {
                                                echo $json_data['Hardware']['Gpu'][0]['Description'];
                                            } else {
                                                echo $json_data['Hardware']['Monitors'][0]['Name'];
                                            }

                                            ?>
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
                                            <?php
                                            if ($json_data['Hardware']['Gpu']) {
                                                $html = '<h5> GPU Info </h5>
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">Name</th>
                                                                        <th scope="col">VRAM</th>
                                                                        <th scope="col">Resolution</th>
                                                                        <th scope="col">Refresh Rate</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>';

                                                foreach ($json_data['Hardware']['Gpu'] as $gpu) {
                                                    $vram = $gpu['AdapterRAM'] / 1048576 . ' MB';
                                                    $res = $gpu['CurrentHorizontalResolution'] . ' x ' . $gpu['CurrentVerticalResolution'];
                                                    $refrate = $gpu['CurrentRefreshRate'] . 'Hz';

                                                    $html .= '<tr>
                                                                    <td>' . $gpu['Description'] . '</td>
                                                                    <td>' . $vram . '</td>
                                                                    <td>' . $res . '</td>
                                                                    <td>' . $refrate . '</td>
                                                                </tr>';
                                                }

                                                $html .= '</tbody>
                                                    </table>';

                                                echo $html;

                                                $html = '';
                                            }

                                            if ($json_data['Hardware']['Monitors']) {
                                                $html = '<h5> Monitor Info </h5>
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
                                                                <tbody>';

                                                foreach ($json_data['Hardware']['Monitors'] as $monitor) {
                                                    $html .= '<tr>
                                                                    <td>' . $monitor['Name'] . '</td>
                                                                    <td>' . $monitor['DedicatedMemory'] . '</td>
                                                                    <td>' . $monitor['CurrentMode'] . '</td>
                                                                    <td>' . $monitor['MonitorModel'] . '</td>
                                                                    <td>' . $monitor['ConnectionType'] . '</td>
                                                                </tr>';
                                                }

                                                $html .= '</tbody>
                                                    </table>';

                                                echo $html;

                                                $html = '';
                                            }

                                            ?>
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
                                            $adapter = "";
                                            $specversion = preg_replace("/[^0-9]/", "", $json_data['Version']);

                                            if ((int)$specversion >= 113) {
                                                foreach ($json_data['Network']['Adapters'] as $adapter) {
                                                    if ($adapter['PhysicalAdapter'] == true && isset($adapter['DHCPLeaseExpires'])) {
                                                        $adapter = $adapter['Description'];
                                                        break;
                                                    }
                                                };

                                                if ($adapter == "") {
                                                    foreach ($json_data['Network']['Adapters'] as $adapter) {
                                                        if ($adapter['PhysicalAdapter'] == true) {
                                                            $adapter = $adapter['Description'];
                                                            break;
                                                        }
                                                    };
                                                }
                                            } else {
                                                foreach ($json_data['Network']['Adapters2'] as $nicold) {
                                                    if ($nicold['ConnectorPresent']) {
                                                        $adapter = $nicold['InterfaceDescription'];
                                                        break;
                                                    }
                                                }
                                            }
                                            echo $adapter;

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
                                            <h5 class="modal-title" id="ModalLabel">NIC Information</h5>
                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">

                                            <?php

                                            foreach ($json_data["Network"]["Adapters"] as $nic) {
                                                $table = '';
                                                $gateways = '';
                                                $dnsIPs = '';
                                                $dnsSuffixes = '';
                                                $ips = '';
                                                $subnets = '';

                                                if (is_array($nic["DefaultIPGateway"])) {
                                                    foreach ($nic["DefaultIPGateway"] as $gateway) {
                                                        $gateways .= $gateway . ' ';
                                                    }
                                                }

                                                if (is_array($nic["DNSServerSearchOrder"])) {
                                                    foreach ($nic["DNSServerSearchOrder"] as $dnsIP) {
                                                        $dnsIPs .= $dnsIP . ' ';
                                                    }
                                                }

                                                if (is_array($nic["DNSDomainSuffixSearchOrder"])) {
                                                    foreach ($nic["DNSDomainSuffixSearchOrder"] as $dnsSuffix) {
                                                        $dnsSuffixes .= $dnsSuffix . ' ';
                                                    }
                                                }

                                                if (is_array($nic["IPAddress"])) {
                                                    foreach ($nic["IPAddress"] as $ip) {
                                                        $ips .= $ip . ' ';
                                                    }
                                                }

                                                if (is_array($nic["IPSubnet"])) {
                                                    foreach ($nic["IPSubnet"] as $subnet) {
                                                        $subnets .= $subnet . ' ';
                                                    }
                                                }

                                                $table = '<table class="table nic">
                                                            <tr>
                                                                <td>#</td>
                                                                <td>' . $nic["InterfaceIndex"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Name</td>
                                                                <td>' . $nic["Description"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>MAC</td>
                                                                <td>' . $nic["MACAddress"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Gateway(s)</td>
                                                                <td>' . $gateways . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>DHCP State</td>
                                                                <td>' . $nic["DHCPEnabled"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>DHCP Lease Expiry</td>
                                                                <td>' . $nic["DHCPLeaseExpires"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>DHCP Lease Obtained</td>
                                                                <td>' . $nic["DHCPLeaseObtained"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>DHCP Server</td>
                                                                <td>' . $nic["DHCPServer"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>DNS Domain</td>
                                                                <td>' . $nic["DNSDomain"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>DNS Hostname</td>
                                                                <td>' . $nic["DNSHostName"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>DNS IPs</td>
                                                                <td>' . $dnsIPs . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>DNS Suffixes</td>
                                                                <td>' . $dnsSuffixes . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>IP Enabled?</td>
                                                                <td>' . $nic["IPEnabled"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>IP(s)</td>
                                                                <td>' . $ips . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Subnet</td>
                                                                <td>' . $subnets . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Physical Adapter?</td>
                                                                <td>' . ($nic['PhysicalAdapter'] ?? 'unknown') . '</td>
                                                            </tr>
                                                        ';

                                                if (isset($nic["LinkSpeed"])) {
                                                    $table .= '
                                                            <tr>
                                                                <td>Link Speed</td>
                                                                <td>' . round($nic["LinkSpeed"] / 1_000_000) . 'Mbps </td>
                                                            </tr>
                                                    ';
                                                }

                                                if (isset($nic["PhysicalAdapter"]) && $nic["PhysicalAdapter"]) {
                                                    $table .= '
                                                            <tr>
                                                                <td>Full Duplex?</td>
                                                                <td>' . $nic["FullDuplex"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Media Connection State</td>
                                                                <td>' . $nic["MediaConnectState"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Media Duplex State</td>
                                                                <td>' . $nic["MediaDuplexState"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>MTU Size</td>
                                                                <td>' . $nic["MtuSize"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Name</td>
                                                                <td>' . $nic["Name"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Operational Status</td>
                                                                <td>' . $nic["OperationalStatusDownMediaDisconnected"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Permanent Address</td>
                                                                <td>' . $nic["PermanentAddress"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>Promiscuous Mode</td>
                                                                <td>' . $nic["PromiscuousMode"] . '</td>
                                                            </tr>

                                                            <tr>
                                                                <td>State</td>
                                                                <td>' . $nic["State"] . '</td>
                                                            </tr>
                                                    ';
                                                }

                                                $table .= '</table>';
                                                echo $table;
                                            }

                                            ?>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="widgets_widgets widgets" id="storage_widgets" data-hide="false">
                            <?php
                            $drives_amount = safe_count($json_data['Hardware']['Storage']);
                            $driveKey = 0;

                            foreach ($json_data['Hardware']['Storage'] as $driveKey => $drive) {
                                $drive_size_raw = $drive['DiskCapacity'];
                                $drive_free_raw = getDriveFree($drive);
                                $device_name = $drive['DeviceName'];
                                $drive_taken_raw = $drive_size_raw - $drive_free_raw;
                                $drive_size = floor(bytesToGigabytes($drive_size_raw));
                                $drive_taken = floor(bytesToGigabytes($drive_taken_raw));
                                // the drive size can sometimes be z ero if the drive is failing
                                if ($drive_taken != 0 && $drive_size != 0) {
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
                                if (abs(floor(bytesToGigabytes($drive['DiskCapacity'])) -
                                    floor(bytesToGigabytes(getDriveCapacity($drive)))) > 5) {
                                    $flavor_color = $red;
                                }

                                $letters = array_filter(
                                    array_column($drive['Partitions'], 'PartitionLetter')
                                );
                                $lettersString = implode(", ", $letters);
                                $lettersStringDisplay = empty($lettersString) ? '' : "($lettersString)";

                                echo '
					<div class="widget widget-disk hover" type="button" data-mdb-toggle="modal" data-mdb-target="#driveModal' . $driveKey . '">
						<h1>' . $device_name . ' ' . $lettersStringDisplay . '</h1>
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
					<div class="modal fade" id="driveModal' . $driveKey . '" tabindex="-1" aria-labelledby="driveModal" aria-hidden="true">
						<div class="modal-dialog modal-xl">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="ModalLabel">' . $device_name . ' ' . $lettersStringDisplay . '</h5>
									<button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
                                    <table class="table">
                                        <thead>
                                        <th>Name</th>
                                        <th>SN</th>
                                        <th>#</th>
                                        <th>Capacity</th>
                                        <th>Free</th>
                                        </thead>
                                        <tbody>
                                        ' . '<td>' . $drive['DeviceName'] . '</td>' . '
                                        ' . '<td>' . $drive['SerialNumber'] . '</td>' . '
                                        ' . '<td>' . $drive['DiskNumber'] . '</td>' . '
                                        ' . '<td>' . floor(bytesToGigabytes($drive['DiskCapacity'])) . 'GB</td>' . '
                                        ' . '<td>' . floor(bytesToGigabytes(getDriveFree($drive))) . 'GB</td>' . '
                                        </tbody>
                                    </table>
                                    <h5>Partitions</h5>
                                    <div class="progress partition-whole-bar">
                                ';

                                foreach ($drive['Partitions'] as $part) {
                                    $part_size = $part['PartitionCapacity'];
                                    $part_taken = $part_size - $part['PartitionFree'];
                                    $part_display = "";
                                    if (!empty($part['PartitionLabel'])) {
                                        $part_display .= $part['PartitionLabel'];
                                        if (isset($part['PartitionLetter'])) {
                                            $part_display .= " ({$part['PartitionLetter']})";
                                        }
                                    } else if (isset($part['PartitionLetter'])) { // and not partition label
                                        $part_display = $part['PartitionLetter'];
                                    }
                                    if (!empty($part_display))
                                        $part_display .= '<br/>';
                                    $fs_display = $part['Filesystem'] ?? 'Unknown';

                                    // The "drive size + 1" is a terrible fix for division by 0 errors
                                    echo '
                                    <div class="progress progress-bar partition-one-bar" style="width: '. $part_size / ($drive_size_raw + 1) * 100 . '%;">
                                        <span class="partition-bar-label">
                                            ' . $part_display /* this will already have <br/> if not empty */ . '
                                            ' . $fs_display . '<br/>
                                            ' . "$part_taken / $part_size MB Used" . '
                                        </span>
                                        <div class="progress-bar partition-space-bar" style="width: ' . $part_taken / $part_size * 100 . '%;"></div>
                                    </div>
                                    ';
                                }

                                echo '
                                    </div>
                                    <table class="table">
                                        <thead>
                                            <th>Label</th>
                                            <th>Letter</th>
                                            <th>Capacity</th>
                                            <th>Free</th>
                                            <th>FS Type</th>
                                            <th>CfgMgr Error Code</th>
                                            <th>Last Error Code</th>
                                            <th>Dirty Bit</th>
                                        </thead>
                                        <tbody>
                                        ';
                                foreach ($drive['Partitions'] as $part) {
                                    echo '
                                    <tr>
                                        <td>' . $part['PartitionLabel'] . '</td>
                                        <td>' . $part['PartitionLetter'] . '</td>
                                        <td>' . floor(bytesToMegabytes($part['PartitionCapacity'])) . ' MB</td>
                                        <td>' . floor(bytesToMegabytes($part['PartitionFree'])) . ' MB</td>
                                        <td>' . $part['Filesystem'] . '</td>
                                        <td>' . $part['CfgMgrErrorCode'] . '</td>
                                        <td>' . $part['LastErrorCode'] . '</td>
                                        <td>' . $part['DirtyBitSet'] . '</td>
                                    </tr>
                                    ';
                                }
                                echo '
                                        </tbody>
                                    </table>
                                <h5>SMART</h5>
								';
                                if (isset($drive['SmartData'])) {
                                    echo
                                    '
                                    <div class="smart-table-wrapper">
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

                                    // two SMART chunks for 2 columns of table
                                    list($smart1, $smart2) = array_chunk($drive['SmartData'], ceil(safe_count($drive['SmartData']) / 2));

                                    foreach ($smart1 as $smartEntry) {
                                        echo
                                        '
											<tr>
												<th scope="row">' . $smartEntry['Id'] . '</th>
												<td>' . $smartEntry['Name'] . '</td>
												<td>' . $smartEntry['RawValue'] . '</td>
											</tr>';
                                    }

                                    echo '
                                            </tbody>
                                        </table>
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

                                    foreach ($smart2 as $smartEntry) {
                                        echo
                                            '
											<tr>
												<th scope="row">' . $smartEntry['Id'] . '</th>
												<td>' . $smartEntry['Name'] . '</td>
												<td>' . $smartEntry['RawValue'] . '</td>
											</tr>';
                                    }

                                    echo
                                    '
                                            </tbody>
                                        </table>
									</div>
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
                                                <?= $json_data['Hardware']['Cpu']['LoadPercentage'] ?? '--' ?>%
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
                                                $profile_count = safe_count($json_data['System']['PowerProfiles']);
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
                                            <h4>Power Profiles</h4>
                                            <table id="powerTable" class="table">
                                                <thead>
                                                    <th>Description</th>
                                                    <th>Element</th>
                                                    <th>Instance Path</th>
                                                    <th>Status</th>
                                                </thead>
                                            </table> <br>
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
                        <div class="textbox metadata-detail tabbed-info">
                            <ul class="metadata-detail-controls">
                                <li class="notes_button">Notes</li>
                                <li class="pups_button">PUPs</li>
                                <li class="variables_button">Variables</li>
                                <li class="browsers_button">Browsers</li>
                                <li class="startup_button">Startup Tasks</li>
                                <li class="updates_button">Windows Updates</li>
                            </ul>
                            <div class="metadata-detail-content jsondata" id="notes">
                                <!-- OS Version -->

                                <h4 style="margin:5px; color:#ffffff66">General Notes</h4>

                                <?php

                                if (($eolcolor == $red && $oscolor == $green) || ($eolcolor == $green && $oscolor == $red)) $osenglish = 'but';
                                else $osenglish = 'and';

                                ?>

                                <p>The OS is
                                    <span style="color:<?= $eolcolor ?>"><?= $eoltext ?></span>
                                    <?php if (!$os_insider) echo " $osenglish <span style='color: $oscolor;'>$oscheck</span>"; ?>
                                    <span>(version <?= $json_data['BasicInfo']['FriendlyVersion'] ?>, build <?= $json_data['BasicInfo']['Version'] ?>)</span>
                                </p>

                                <p>The current uptime is
                                    <?= $test_time ?>.
                                </p>

                                <?php
                                if (empty($json_data['Security']['AvList'])) {
                                    echo '<p style="color:' . $red . '">No AVs found!</p>';
                                } elseif (sizeof($json_data['Security']['AvList']) == 1) {
                                    $av = $json_data["Security"]["AvList"][0];
                                    echo "<p>The currently installed AV is <span>{$av}</span></p>";
                                } else {
                                    $avs = implode(',', array_map(function ($i) {
                                        return ' ' . $i;
                                    }, $json_data['Security']['AvList']));
                                    echo "<p>The currently installed AVs are <span>{$avs}</span></p>";
                                }

                                $noteshtml = '';

                                if ($json_data['System']['UsernameSpecialCharacters'] == true) {
                                    $noteshtml .= '
                                    <p>
                                        Username found with <span>Special Characters</span>
                                    </p>
                                    ';
                                }
                                if ($json_data['System']['OneDriveCommercialPathLength'] != null) {
                                    $noteshtml .= '
                                    <p>
                                        OneDrive Path Length : <span>' . $json_data['System']['OneDriveCommercialPathLength'] . '</span>
                                        OneDrive Name Length : <span>' . $json_data['System']['OneDriveCommercialNameLength'] . '</span>
                                    </p>
                                    ';
                                }

                                if ($json_data['System']['RecentMinidumps'] != 0) {
                                    $noteshtml .= '
                                    <p>
                                        There have been <span style="color:' . $red . '">' . $json_data['System']['RecentMinidumps'] . '</span> Minidumps found
                                    </p>
                                    ';
                                }

                                $hostFileHash = hash('ripemd160', $json_data['Network']['HostsFile']);
                                $hostFileCheck = "4fbad385eddbc2bdc1fa9ff6d270e994f8c32c5f" !== $hostFileHash; // Pre-calculated Hash

                                if ($hostFileCheck) {
                                    $noteshtml .= '
                                    <p>
                                        Hosts file has been modified from stock
                                    </p>
                                    ';
                                }

                                if (!empty($json_data['Hardware']['Batteries'])) {
                                    foreach ($json_data['Hardware']['Batteries'] as $battery) {
                                        $cap = floatval($battery["Remaining_Life_Percentage"]);
                                        if (
                                            $cap < 70
                                        ) {
                                            $designcap = $battery['Design_Capacity'] / 1000;
                                            $currentcap = $battery['Full_Charge_Capacity'] / 1000;
                                            $noteshtml .= "
                                            <p>
                                                Battery <span>{$battery['Name']}</span> has a diminished capacity! (Designed for {$designcap} Wh, currently {$currentcap} Wh)
                                            </p>";
                                        }
                                    }
                                }

                                foreach ($json_data['Hardware']['Storage'] as $disk) {
                                    foreach ($disk['Partitions'] as $partNum => $part) {
                                        if (isset($part['DirtyBitSet']) && $part['DirtyBitSet']) {
                                            if (empty($part['PartitionLetter'])) {
                                                $noteshtml .= "
                                                <p>
                                                    Dirty bit set on <span>partition $partNum ({$disk['DeviceName']})</span>
                                                </p>
                                                ";
                                            } else {
                                                $noteshtml .= "
                                                <p>
                                                    Dirty bit set on <span>{$part['PartitionLetter']} ({$disk['DeviceName']})</span>
                                                </p>
                                                ";
                                            }
                                        }
                                    }
                                }

                                if ($json_data['Meta']['ElapsedTime'] > 20000) {
                                    $noteshtml .= '
                                        <p>Specify runtime is over 20s.</p>
                                    ';
                                }

                                if (!empty($noteshtml)) {
                                    $noteshtml = '<br>' . $noteshtml;
                                    echo $noteshtml;
                                }
                                ?>

                                <?php

                                $drivehtml = '';
                                $driveKey = 0;

                                foreach ($json_data['Hardware']['Storage'] as $storage_device) {

                                    $drivemodal = 'data-mdb-target="#driveModal' . $driveKey . '"';
                                    $partitionmodal = 'data-mdb-target="#partitionsModal"';

                                    $letters = array_filter(
                                        array_column($json_data['Hardware']['Storage'][$driveKey]['Partitions'], 'PartitionLabel')
                                    );
                                    $lettersString = implode(", ", $letters);

                                    if ($storage_device['SmartData']) {
                                        foreach ($storage_device['SmartData'] as $smartPoint) {
                                            if (str_contains($smartPoint['Name'], '!')) {
                                                if ($smartPoint['RawValue'] != '000000000000') {
                                                    $drivehtml .= '<p><span class="drivespan" data-mdb-toggle="modal" type="button" ' . $drivemodal . '>'
                                                        . $storage_device['DeviceName'] . ' (' . $lettersString . ') </span> has <span>'
                                                        . $smartPoint['RawValue'] . ' ' . $smartPoint['Name'] . '</span></p>';
                                                }
                                            }
                                        }
                                    }

                                    if (abs((floor(bytesToGigabytes($storage_device['DiskCapacity'])) -
                                        floor(bytesToGigabytes(getDriveCapacity($storage_device))))) > 5) {
                                        $drivehtml .=  '
                                                <p>
                                                    <span>' . $storage_device['DeviceName'] . ' (' . $lettersString . ') </span> has differing capacities.
                                                    (' . floor(bytesToGigabytes($storage_device['DiskCapacity'])) . " on disk vs. "
                                            . floor(bytesToGigabytes(getDriveCapacity($storage_device))) . ' on partitions)
                                                </p>
                                                ';
                                    }
                                    $driveKey += 1;
                                }

                                if (!empty($drivehtml)) {
                                    $drivehtml = '<br> <h4 style="margin:5px; color:#ffffff66">Drive / SMART Notes</h4>' . $drivehtml;
                                    echo $drivehtml;
                                }

                                ?>

                                <?php
                                $reghtml = "";

                                if ($json_data['System']['StaticCoreCount'] != false) {
                                    $reghtml .= '
                                    <p>
                                        <span>Static Core Count</span> found set.
                                    </p>';
                                }

                                foreach ($json_data['System']['ChoiceRegistryValues'] as $regkey) {

                                    if ($regkey['Value'] && !in_array($regkey['Value'], $defaultRegKeys[$regkey['Name']])){
                                        $reghtml .= '
                                            <p>
                                                Registry Value <span>' . $regkey['Name'] . '</span> found set, value of <span>' . $regkey['Value'] . '</span>
                                            </p>';
                                    }

                                }

                                if (!empty($reghtml)) {
                                    $reghtml = '<br> <h4 style="margin:5px; color:#ffffff66">Notable Registry Changes</h4>' . $reghtml;
                                    echo $reghtml;
                                }
                                ?>

                            </div>
                            <div class="metadata-detail-content jsondata" id="pups">
                                <?php
                                $puphtml = '';

                                if (!empty($pupsfoundInstalled)) {

                                    $puphtml .= '<h4>PUPS found Installed</h4>';

                                    $puphtml .= '<table id="pupsTableInstalled" class="table">';

                                    foreach ($pupsfoundInstalled as $pup) {
                                        $puphtml .= '<tr><td>' . $pup . ' Found installed</td></tr>';
                                    }

                                    $puphtml .= '</table>';
                                }

                                if (!empty($pupsfoundRunning)) {

                                    $puphtml .= '<h4>PUPS found Running</h4>';

                                    $puphtml .= '<table id="pupsTableRunning" class="table">';

                                    foreach ($pupsfoundRunning as $pup) {
                                        $puphtml .= '<tr><td>' . $pup . ' Found Running</td></tr>';
                                    }

                                    $puphtml .= '</table>';
                                }

                                if (!empty($puphtml)) {
                                    echo $puphtml;
                                } else {
                                    echo '<h4>No PUPs found, yay!</h4>';
                                }
                                ?>
                            </div>
                            <div class="metadata-detail-content jsondata" id="variables">
                                <table id="variables_table" class="table">
                                    <thead>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        echo '<h4>User Variables</h4>';
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
                                        echo '<h4>System Variables</h4>';
                                        $uservar_keys = array_keys($json_data['System']['SystemVariables']);
                                        foreach ($uservar_keys as $uservar) {
                                            if ($uservar != "Path") {
                                                echo '<tr><td>' . $uservar . '</td><td>' . $json_data['System']['SystemVariables'][$uservar] . '</td></tr>';
                                            };
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="metadata-detail-content jsondata" id="browsers">
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
                            <div class="metadata-detail-content jsondata" id="startup">
                                <table id="startup_table" class="table">
                                    <thead>
                                        <th>App Name</th>
                                        <th>App Path</th>
                                        <th>Timestamp</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($json_data['System']['StartupTasks'] as $task) {
                                            echo '<tr>
                                                <td>' . $task['AppName'] . '</td>' .
                                                '<td>' . $task['ImagePath'] . '</td>' .
                                                '<td>' . $task['Timestamp'] . '</td>' .
                                                '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="metadata-detail-content jsondata" id="updates">
                                <table id="updates_table" class="table">
                                    <thead>
                                        <th>Update</th>
                                        <th>Install Date</th>
                                    </thead>
                                    <?php
                                    foreach ($json_data['System']['InstalledHotfixes'] as $update) {
                                        echo '<tr>
                                                <td>' . $update['HotFixID'] . '</td>' .
                                            '<td>' . $update['InstalledOn'] . '</td>' .
                                            '</tr>';
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="textbox metadata-detail" id="accordionTablesDevices">
                            <div class="accordion">
                                <h1 class="accordion-header" id="devicesTableButton">
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#devices" aria-expanded="true" aria-controls="devices">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#drivers" aria-expanded="true" aria-controls="drivers">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#runningProcesses" aria-expanded="true" aria-controls="runningProcesses">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#installedApp" aria-expanded="true" aria-controls="installedApp">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#services" aria-expanded="true" aria-controls="services">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#tasks" aria-expanded="true" aria-controls="tasks">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#netcon" aria-expanded="true" aria-controls="netcon">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#routes" aria-expanded="true" aria-controls="routes">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#hosts" aria-expanded="true" aria-controls="hosts">
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
                                    <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#debugLog" aria-expanded="true" aria-controls="debugLog">
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
<script defer="defer" src="static/js/konami.js"></script>

<!--Table Rendering-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.slim.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/sc-2.0.7/datatables.min.js"></script>

<!--UI Stuff-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.js" type="text/javascript"></script>

<!--Main Scripts-->
<script defer="defer" src="static/js/tables.js"></script>
<script defer="defer" src="static/js/main.js"></script>

</html>

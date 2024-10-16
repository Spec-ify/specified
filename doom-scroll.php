<?php

    //Checking if the file requested via GET exists. If not, we send to a custom 404.
    if (!file_exists($_GET['file'])) {
        http_response_code(404);
        include('404.html');
        die();
    }
    //Opening the file that comes after profile/ via GET and then parsing it with json_decode to get a usable variable with the json info back.
    $json_file = $_GET['file'];
    $json = file_get_contents($json_file);
    $test = 0;
    $json_data = json_decode($json, true);
    $profile_name = pathinfo($json_file, PATHINFO_FILENAME);

    $script_nonce = bin2hex(random_bytes(20));
    // TODO: style the progress bars differently, and remove unsafe-inline!
    header("Content-Security-Policy: default-src 'self'; connect-src 'self' https://spec-ify.com http://localhost:3000; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; font-src https://fonts.gstatic.com; script-src 'self' https://cdnjs.cloudflare.com https://cdn.datatables.net 'nonce-$script_nonce'");

    include('common.php');

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

    //The lines below are for the loop that calculates total RAM/CPU used.
    //CPU doesn't work right now because we are not able to efficiently get the CPU usage of each running process.
    $working_set = 0;
    $cpu_percent = 0;

    foreach ($json_data['System']['RunningProcesses'] as $process) {
        $working_set += $process['WorkingSet']; // RAM
        $cpu_percent += $process['WorkingSet']; // CPU
    }

    $ram_used = number_format($working_set / 1073741824, 2, '.', '');

    $total_ram = 0;

    if ($json_data['Hardware']['Ram']){ 
        //Don't ask me why this is an old fashioned for loop, I got carried away.
        //Getting the total amount of RAM in the system.
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
    }

    //Basic string to time php function to take the generation date and turn it into a usable format.
    $ds = strtotime($json_data['Meta']['GenerationDate']);
    $test_time = timeConvert($json_data['BasicInfo']['Uptime']);

    //Uservar paths split function
    $paths = $json_data['System']['UserVariables']['Path'];

    // Split the paths into an array using the semicolon as the delimiter
    $path_array = explode(';', $paths);

    // Notable Software and SMBIOS
    include('lists.php');
    // Set up the reference list
    $referenceListInstalled = $json_data['System']['InstalledApps'];
    $referenceListRunning = $json_data['System']['RunningProcesses'];

    $pupsFoundInstalled = array();
    foreach ($referenceListInstalled as $installed) {
        foreach ($notableSoftwareList as $pups) {
            preg_match('/\b(' . strtolower($pups) . ')\b/', strtolower($installed['Name']), $matches, PREG_OFFSET_CAPTURE);
            if ($matches) {
                array_push($pupsFoundInstalled, $installed['Name']);
            }
        }
    }
    $pupsFoundInstalled = array_unique($pupsFoundInstalled);

    $pupsFoundRunning = array();
    foreach ($referenceListRunning as $running) {
        foreach ($notableSoftwareList as $pups) {
            preg_match('/\b(' . strtolower($pups) . ')\b/', strtolower($running['ProcessName']), $matches, PREG_OFFSET_CAPTURE);
            if ($matches) {
                array_push($pupsFoundRunning, $running['ProcessName']);
            }
        }
    }
    $pupsFoundRunning = array_unique($pupsFoundRunning);

    $dumpLink = "";
    if (isset($json_data['System']['DumpZip'])) {
        $strippedLink = str_replace("\n", '', $json_data['System']['DumpZip']);
        if (filter_var($strippedLink, FILTER_VALIDATE_URL)) {
            $dumpLink = $strippedLink;
        }
    }

    /**
     * Return table layout for a data array
     * @param string[][] $arr
     * @param string[] $cols cols can be modified in the transform function. if a column is not present in the array, it will be blank
     * @param Closure $transform an optional function that takes the row `function (&$row) {` as an argument and can modify it
     * @param string $noDataMessage what to output for an empty array
     */
    function array_table_iter(?array $arr, array $cols, $transform = null, string $noDataMessage = 'No Data'): string
    {
        $res = "";
        $colLength = count($cols);
        if (!$arr) {
            // we need these so DataTables doesn't get mad
            $fillerTds = str_repeat("<td class='hidden'></td>", $colLength - 1);
            return "<tr><td colspan='$colLength'>$noDataMessage</td>$fillerTds</tr>";
        }
        foreach ($arr as $row) {
            if ($transform) {
                $transform($row);
            }
            $res .= '<tr>';
            foreach ($cols as $col) {
                $res .= '<td>' . ($row[$col] ?? '') . '</td>';
            }
            $res .= '</tr>';
        }
        return $res;
    }

    /**
     * Return table layout for a data object (key => value)
     * @param string[][] $arr
     */
    function object_table_iter(?array $arr): string
    {
        $res = "";
        if (!$arr) return '';
        foreach ($arr as $key => $val) {
            $res .= "<tr><td>$key</td><td>$val</td></tr>";
        }
        return $res;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Profile <?= $profile_name ?> | Specified</title>

    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet"/>
    <link href="static/css/doom-scroll.css" rel="stylesheet" />
    <link href="static/css/tables.css" rel="stylesheet" />

    <!--This section is for the discord embed card. Need to expand upon it. -->
    <meta name="og:title" content="<?= $json_data["BasicInfo"]["Hostname"] ?>"/>
    <meta name="og:site_name" content="Specify"/>
    <meta name="og:description" content="Generated on <?= $json_data["Meta"]["GenerationDate"] ?>"/>
    <meta name="og:type" content="data.specify_result"/>

    <link rel="shortcut icon" href="assets/specify-glass-dynamic.svg" />
    <link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme light)" />
    <link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme dark)" />

    <script nonce="<?= $script_nonce ?>">
        window.PROFILE_NAME = "<?= $profile_name ?>";
    </script>
    <script defer src="static/js/doom-scroll.js?v=1" type="module"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.slim.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/sc-2.0.7/datatables.min.js"></script>
    <script src="static/js/konami.js"></script>
</head>
<body>
<noscript>You need to enable JavaScript to run this app.</noscript>
<nav>
    <span id="nav-expand"><a href="#">&gt;&gt;</a></span>
    <span id="nav-collapse"><a href="#">&lt;&lt;</a></span>
    <ul id="nav-list">
        <li><a href="<?= http_strip_query_param($_SERVER['REQUEST_URI'], 'view') ?>">Specify View</a></li>
        <li id="nav-top-link"><a href="#top">Back To Top</a></li>
        <?= $dumpLink ? "<li><a href='$dumpLink'>Download Dumps</a></li>" : '' ?>
        <li class="nav-space-below"><a href="<?= $json_file ?>">View JSON</a></li>
    </ul>
</nav>
<main>
<span class="linker" id="top"></span>
<h1>Profile Information</h1>
<table>
    <thead></thead>
    <tbody>
        <tr>
            <td>Profile</td>
            <td><?= $profile_name ?></td>
        </tr>
        <tr>
            <td>Created</td>
            <td><?= date("F j, Y, g:i a", $ds) ?> UTC</td>
        </tr>
        <tr>
            <td>Runtime</td>
            <td><?= $json_data['Meta']['ElapsedTime'] ?>ms</td>
        </tr>
        <tr>
            <td>Specify Version</td>
            <td><?= $json_data['Version'] ?></td>
        </tr>
    </tbody>
</table>

<h1>General Notes</h1>
<?php
    // There will always be an uptime and eol/up-to-date note, so we don't need a placeholder like we do with PUPs
    $thisBuildInt = (int) substr($json_data['BasicInfo']['Version'], 5);
    $latestBuildInt = (int) substr($latestver, 5);

    // EOL
    $eoltext = '';
    $os_insider = false;
    if (strpos($validversions, $json_data['BasicInfo']['Version']) !== false) {
        $eoltext = "Not EOL";
        $eolcolor = "green";
    } else if ($thisBuildInt > $latestBuildInt) {
        $os_insider = true;
        $eoltext = 'Insider';
        $eolcolor = "red";
    } else {
        $eoltext = "EOL";
        $eolcolor = "red";
    }

    // Up-to-Date-ness
    $oscheck = '';
    if (strpos($latestver, $json_data['BasicInfo']['Version']) !== false) {
        $oscheck = 'Up-to-date';
        $oscolor = "green";
    } else {
        $oscheck = 'Not Up-to-Date';
        $oscolor = "red";
    }

    if (($eolcolor == "red" && $oscolor == "green")
        || ($eolcolor == "green" && $oscolor == "red")) $osenglish = 'but';
    else $osenglish = 'and';
?>
<ul id="notes">
    <li><span><?= $ram_used_percent ?>%</span> (<?= $ram_used ?> / <?= $total_ram ?> GB) of the system's RAM is being used.</li>
    <li>The OS is
        <span class="<?= $eolcolor ?>"><?= $eoltext ?></span>
        <?php if (!$os_insider) echo " $osenglish <span class='$oscolor'>$oscheck</span>"; ?>
        <span>(version <?= $json_data['BasicInfo']['FriendlyVersion'] ?>, build <?= $json_data['BasicInfo']['Version'] ?>)</span>
    </li>
    <li>The current uptime is <?= $test_time ?>.</li>
    <?php
        if (empty($json_data['Security']['AvList'])) {
            echo '
    <li class="red">No AVs found!</li>
            ';
        } elseif (sizeof($json_data['Security']['AvList']) == 1) {
            $av = $json_data["Security"]["AvList"][0];
            echo "
    <li>The currently installed AV is <span>{$av}</span></li>
                ";
        } else {
            $avs = implode(',', array_map(function ($i) {
                return ' ' . $i;
            }, $json_data['Security']['AvList']));
            echo "
    <li>The currently installed AVs are <span>{$avs}</span></li>
                ";
        }

        if ($json_data['System']['UsernameSpecialCharacters'] == true) {
            echo '
    <li>
        Username found with <span>Special Characters</span>
    </li>
            ';
        }
        if ($json_data['System']['OneDriveCommercialPathLength'] != null) {
            echo "
    <li>
        OneDrive Path Length : <span>{$json_data['System']['OneDriveCommercialPathLength']}</span>
        OneDrive Name Length : <span>{$json_data['System']['OneDriveCommercialNameLength']}</span>
    </li>
                ";
        }

        if ($json_data['System']['RecentMinidumps'] != 0) {
            ?>
    <li>
        There have been <span class="red"><?= $json_data['System']['RecentMinidumps'] ?></span> Minidumps found.
        <?= $dumpLink ? "<a href='$dumpLink'>Download</a>" : '' ?>
    </li>
            <?php
        }

        $hostFileHash = $json_data['Network']['HostsFileHash'];
        $hostFileCheck = "2D6BDFB341BE3A6234B24742377F93AA7C7CFB0D9FD64EFA9282C87852E57085" !== $hostFileHash; // Pre-calculated Hash

        if ($hostFileCheck) {
            echo '
    <li>
        Hosts file has been modified from stock
    </li>
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
                    echo "
    <li>
        Battery <span>{$battery['Name']}</span> has a diminished capacity (Designed for {$designcap} Wh, currently {$currentcap} Wh)
    </li>
                    ";
                }
            }
        }

        foreach ($json_data['Hardware']['Storage'] as $disk) {
            foreach ($disk['Partitions'] as $partNum => $part) {
                if (isset($part['DirtyBitSet']) && $part['DirtyBitSet']) {
                    if (empty($part['PartitionLetter'])) {
                        echo "
    <li>
        Dirty bit set on <span>partition $partNum ({$disk['DeviceName']})</span>
    </li>
                        ";
                    } else {
                        echo "
    <li>
        Dirty bit set on <span>{$part['PartitionLetter']} ({$disk['DeviceName']})</span>
    </li>
                        ";
                    }
                }
            }
        }

        $drivehtml = '';

        foreach ($json_data['Hardware']['Storage'] as $drive) {
            $letters = array_filter(
                array_column($drive['Partitions'], 'PartitionLetter')
            );
            $lettersString = implode(", ", $letters);
            $drive_url = '#' . urlencode('storage-' . $drive['DeviceName'] ?? 'un' . '-' . $drive['SerialNumber'] ?? 'un');

            if ($drive['SmartData']) {
                foreach ($drive['SmartData'] as $smartPoint) {
                    if (str_contains($smartPoint['Name'], '!')) {
                        if ($smartPoint['RawValue'] != '000000000000') {
                            echo "
    <li>
        <a href='$drive_url'>{$drive['DeviceName']} ($lettersString)</a> has 
        <span class='yellow'>{$smartPoint['RawValue']} {$smartPoint['Name']}</span>
    </li>
                        ";
                        }
                    }
                }
            }

            if (abs((floor(bytesToGigabytes($drive['DiskCapacity'])) -
                    floor(bytesToGigabytes(getDriveCapacity($drive))))) > 5) {
                $onDisk = floor(bytesToGigabytes($drive['DiskCapacity']));
                $onParts = floor(bytesToGigabytes(getDriveCapacity($drive)));
                echo  "
    <li>
        <span>{$drive['DeviceName']} ($lettersString) </span> has differing capacities.
        ($onDisk on disk vs. $onParts on partitions)
    </li>
            ";
            }
            $driveKey += 1;
        }

        $reghtml = "";

        if ($json_data['System']['StaticCoreCount'] != false) {
            echo '
    <li>
        <span>Static Core Count</span> found set.
    </li>
            ';
        }

        foreach ($json_data['System']['ChoiceRegistryValues'] as $regkey) {

            if ($regkey['Value'] && !in_array($regkey['Value'], $defaultRegKeys[$regkey['Name']])) {
                echo "
    <li>
        Registry Value <span>{$regkey['Name']}</span> found set, value of <span>{$regkey['Value']}</span>
    </li>
                    ";
            }
        }

        $unexpected_shutdown_count = $json_data['Events']['UnexpectedShutdownCount'];
        $machinecheck_count = $json_data['Events']['MachineCheckExceptionCount'];
        $whea_count = $json_data['Events']['WheaErrorRecordCount'];
        $pci_whea_count = $json_data['Events']['PciWheaErrorCount'];

        if ($unexpected_shutdown_count > 0) {
            echo "
    <li>
        <span>$unexpected_shutdown_count</span> Unexpected Shutdowns detected
    </li>
            ";
        }
        if ($machinecheck_count > 0) {
            echo "
    <li>
        <span>$machinecheck_count</span> MachineCheck Exceptions detected
    </li>
            ";
        }
        if ($whea_count > 0) {
            echo "
    <li>
        <span>$whea_count</span> WHEA errors detected
    </li>
            ";
        }
        if ($pci_whea_count > 0) {
            echo "
    <li>
        <span>$pci_whea_count</span> PCI WHEA errors detected
    </li>
            ";
        }

        if ($json_data['Meta']['ElapsedTime'] > 20000) {
            echo '
    <li>Specify runtime is over 20s</li>
                ';
        }
    ?>
</ul>

<h1>Notable Software</h1>
<?php if (!$pupsFoundInstalled && !$pupsFoundRunning) echo "No notable software detected" ?>
<ul>
    <?php
        foreach ($pupsFoundInstalled as $pup) {
            echo "<li>$pup found installed</li>";
        }

        foreach ($pupsFoundRunning as $pup) {
            echo "<li>$pup found running</li>";
        }
    ?>
</ul>

<h1>OS Information</h1>
<table>
    <thead></thead>
    <tbody>
        <tr>
            <td>Edition</td>
            <td><?= $json_data['BasicInfo']['Edition'] ?></td>
        </tr>
        <tr>
            <td>Build #</td>
            <td><?= $json_data['BasicInfo']['Version'] ?></td>
        </tr>
        <tr>
            <td>Version</td>
            <td><?= $json_data['BasicInfo']['FriendlyVersion'] ?></td>
        </tr>
        <tr>
            <td>Install Date</td>
            <td><?= $json_data['BasicInfo']['InstallDate'] ?></td>
        </tr>
        <tr>
            <td>Uptime</td>
            <td><?= $json_data['BasicInfo']['Uptime'] ?></td>
        </tr>
        <tr>
            <td>Domain</td>
            <td><?= $json_data['BasicInfo']['Domain'] ?></td>
        </tr>
        <tr>
            <td>Hostname</td>
            <td><?= $json_data['BasicInfo']['Hostname'] ?></td>
        </tr>
        <tr>
            <td>Username</td>
            <td><?= $json_data['BasicInfo']['Username'] ?></td>
        </tr>
        <tr>
            <td>UAC</td>
            <td><?= $json_data['Security']['UacLevel'] === 0 ? 'Disabled' : 'Enabled, level ' . $json_data['Security']['UacLevel'] ?></td>
        </tr>
        <tr>
            <td>Boot Mode</td>
            <td><?= $json_data['BasicInfo']['BootMode'] ?></td>
        </tr>
        <tr>
            <td>Secure Boot</td>
            <td><?= $json_data['Security']['SecureBootEnabled'] ? 'Enabled' : 'Disabled' ?></td>
        </tr>
        <tr>
            <td>Boot State</td>
            <td><?= $json_data['BasicInfo']['BootState'] ?></td>
        </tr>
    </tbody>
</table>

<h1>Hardware</h1>

<h2>CPU</h2>
<table>
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
<h3 id="hwapi-header">Database Results</h3>
<p id="hwapi-status"></p>
<table>
    <tbody id="hwapi-body"></tbody>
</table>

<h2>RAM</h2>
<table>
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
                    echo "
        <tr>
            <td>$ram_location</td>
            <td colspan='4' class='td-center'>Not Detected</td>
        </tr>
                    ";
                } else {
                    echo "
        <tr>
            <td>$ram_location</td>
            <td>$ram_manufacturer</td>
            <td>$ram_part</td>
            <td>{$ram_speed}MHz</td>
            <td>{$ram_size}MB</td>
        </tr>
                    ";
                }
            }
        ?>
    </tbody>
</table>

<h2>Pagefile</h2>
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

<h2>Motherboard</h2>
<table>
    <thead>
    </thead>
    <tbody>
        <tr>
            <td>Motherboard Product</td>
            <td><?= strtok($json_data['Hardware']['Motherboard']['Manufacturer'], " ") ?> <?= $json_data['Hardware']['Motherboard']['Product'] ?></td>
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
                    $formattedbiosdate = $datetime->format('Y-m-d');
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
    </tbody>
</table>

<h2>TPM</h2>
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
?>
<table>
    <tbody>
        <tr>
            <td>Status</td>
            <td><?= $tpm_status ?></td>
        </tr>
        <tr>
            <td>Manufacturer Version</td>
            <td><?= $tpm_manufacturer ?></td>
        </tr>
        <tr>
            <td>Version</td>
            <td><?= $tpm_version ?></td>
        </tr>
    </tbody>
</table>

<h2>GPUs</h2>
<table class="table">
    <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">VRAM</th>
            <th scope="col">Resolution</th>
            <th scope="col">Refresh Rate</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($json_data['Hardware']['Gpu'] as $gpu) {
                echo '
        <tr>
            <td>' . $gpu['Description'] . '</td>
            <td>' . $gpu['AdapterRAM'] / pow(2, 20) . ' MB' . '</td>
            <td>' . $gpu['CurrentHorizontalResolution'] . ' x ' . $gpu['CurrentVerticalResolution'] . '</td>
            <td>' . $gpu['CurrentRefreshRate'] . 'Hz' . '</td>
        </tr>';
            }
        ?>
    </tbody>
</table>

<h2>Displays</h2>
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
            foreach ($json_data['Hardware']['Monitors'] as $monitor) {
                echo '
        <tr>
            <td>' . $monitor['Name'] . '</td>
            <td>' . $monitor['DedicatedMemory'] . '</td>
            <td>' . $monitor['CurrentMode'] . '</td>
            <td>' . $monitor['MonitorModel'] . '</td>
            <td>' . $monitor['ConnectionType'] . '</td>
        </tr>
                ';
            }
        ?>
    </tbody>
</table>

<h2>Temperatures</h2>
<table id="temps-table">
    <thead>
        <tr>
            <th>Hardware</th>
            <th>Sensor</th>
            <th>Temperature (&deg;C)</th>
        </tr>
    </thead>
    <tbody><?= array_table_iter($json_data['Hardware']['Temperatures'], ['Hardware', 'SensorName', 'SensorValue']) ?></tbody>
</table>

<h2>SMBIOS Information</h2>
<table>
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
            <td>' .  safe_implode('<br/>', $value) . '</td>
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


<h1>Devices</h1>
<p id="devices-sort-warning">Table will not be sortable until database lookups finish</p>
<table id="devices-table">
    <thead>
        <tr>
            <th>Status</th>
            <th>Name</th>
            <th>Description</th>
            <th>DID</th>
            <th>Vendor (Database)</th>
            <th>Device (Database)</th>
            <th>PCIe Subsystem (Database)</th>
        </tr>
    </thead>
    <tbody>
        <?= array_table_iter(
            $json_data['Hardware']['Devices'],
            ['Status', 'Name', 'Description', 'DeviceID'],
            function(&$row) {
                if ($row['ConfigManagerErrorCode'] === 22) {
                    $row['Status'] = 'Disabled (22)';
                } else if ($row['Status'] === 'Error') {
                    $row['Status'] = "Error ({$row['ConfigManagerErrorCode']})";
                }
            }
        ) ?>
    </tbody>
</table>

<h1>Drivers</h1>
<table id="drivers-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Friendly Name</th>
            <th>Manufacturer</th>
            <th>DID</th>
            <th>Version</th>
        </tr>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['Hardware']['Drivers'], ['DeviceName', 'FriendlyName', 'Manufacturer', 'DeviceID', 'DriverVersion']) ?>
    </tbody>
</table>

<h1>Storage</h1>
<?php
    $drives_amount = safe_count($json_data['Hardware']['Storage']);
    $driveKey = 0;

    foreach ($json_data['Hardware']['Storage'] as $driveKey => $drive) {
        $drive_size_raw = $drive['DiskCapacity'];
        $drive_free_raw = getDriveFree($drive);
        $drive_taken_raw = $drive_size_raw - $drive_free_raw;
        $drive_size = floor(bytesToGigabytes($drive_size_raw));
        $drive_taken = floor(bytesToGigabytes($drive_taken_raw));
        // the drive size can sometimes be z ero if the drive is failing
        if ($drive_taken != 0 && $drive_size != 0) {
            $drive_percentage = round((float)$drive_taken / (float)$drive_size * 100);
        } else $drive_percentage = 0;

        $letters = array_filter(
            array_column($drive['Partitions'], 'PartitionLetter')
        );
        $lettersString = implode(", ", $letters);
        $lettersStringDisplay = empty($lettersString) ? '' : "($lettersString)";

        echo '
    <h2 class="item-header">' . $drive['DeviceName'] . '</h2>
    <table class="table" id="' .
        urlencode('storage-' . $drive['DeviceName'] ?? 'un' . '-' . $drive['SerialNumber'] ?? 'un') /* un means unknown here */
        . '">
        <thead>
            <tr>
                <th>Name</th>
                <th>SN</th>
                <th>#</th>
                <th>Capacity</th>
                <th>Free</th>
            </tr>
        </thead>
        <tbody>
        ' . '<td>' . $drive['DeviceName'] . '</td>' . '
        ' . '<td>' . $drive['SerialNumber'] . '</td>' . '
        ' . '<td>' . $drive['DiskNumber'] . '</td>' . '
        ' . '<td>' . floor(bytesToGigabytes($drive['DiskCapacity'])) . 'GB</td>' . '
        ' . '<td>' . floor(bytesToGigabytes(getDriveFree($drive))) . 'GB</td>' . '
        </tbody>
    </table>
    <h3>Partitions</h3>
    <div class="progress partition-whole-bar">
        ';

        foreach ($drive['Partitions'] as $part) {
            $part_size = $part['PartitionCapacity'];
            $part_taken = $part_size - $part['PartitionFree'];
            $part_size_mb = bytesToMegabytes($part_size);
            $part_taken_mb = ceil(bytesToMegabytes($part_taken));
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
    <div class="progress progress-bar partition-one-bar" style="width: ' . $part_size / ($drive_size_raw + 1) * 100 . '%;">
        <span class="partition-bar-label">
            ' . $part_display /* this will already have <br/> if not empty */ . '
            ' . $fs_display . '<br/>
            ' . "$part_taken_mb / $part_size_mb MB Used" . '
        </span>
        <div class="progress-bar partition-space-bar" style="width: ' . $part_taken / $part_size * 100 . '%;"></div>
    </div>
                ';
        }

        // The "drive size + 1" is a terrible fix for division by 0 errors
        echo '
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Label</th>
                <th>Letter</th>
                <th>Capacity</th>
                <th>Free</th>
                <th>FS Type</th>
                <th>CfgMgr Error Code</th>
                <th>Last Error Code</th>
                <th>Dirty Bit</th>
            </tr>
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
    <h3>SMART</h3>
		';
        if (isset($drive['SmartData'])) {
            echo '
    <div class="smart-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th scope="col">Index</th>
                    <th scope="col">Name</th>
                    <th scope="col">Value</th>
                </tr>
            </thead>
            <tbody>
            ' . array_table_iter($drive['SmartData'], ['Id', 'Name', 'RawValue']) . '
            </tbody>
        </table>
            ';
        } else {
            echo
            '
                            <h5>Sorry, no SMART data was found for this device.</h5>
                            ';
        }
        echo '</div>';
    }
?>

<h1>Audio Devices</h1>
<table>
    <thead>
        <tr>
            <th>Device ID</th>
            <th>Manufacturer</th>
            <th>Name</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['Hardware']['AudioDevices'], ['DeviceID', 'Name', 'Manufacturer', 'Status']) ?>
    </tbody>
</table>

<h1>Power Profiles</h1>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Instance Path</th>
            <th>Active?</th>
        </tr>
    </thead>
    <tbody>
        <?=
            array_table_iter(
                $json_data['System']['PowerProfiles'],
                ['ElementName', 'Description', 'InstanceID', 'IsActive'],
                fn(&$arr) => $arr['IsActive'] = $arr['IsActive'] ? 'Yes' : 'No') ?>
    </tbody>
</table>

<h1>Batteries</h1>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Manufacturer</th>
            <th>Chemistry</th>
            <th>Design Capacity</th>
            <th>Current Full Charge Capacity</th>
        </tr>
    </thead>
    <tbody>
        <?php
            if ($json_data['Hardware']['Batteries']) {
                echo array_table_iter(
                    $json_data['Hardware']['Batteries'],
                    ['Name', 'Manufacturer', 'Chemistry', 'Design_Capacity', 'Full_Charge_Capacity']);
            } else {
                echo '<tr><td style="border: none;">No batteries detected</td></tr>';
            }
        ?>
    </tbody>
</table>

<h1>Variables</h1>

<h2>User Variables</h2>
<table>
    <tbody>
        <?php
            foreach ($json_data['System']['UserVariables'] as $key => $val) {
                echo '
        <tr>
            <td>' . $key . '</td>
            <td>' . implode('<br/>', explode(';', $val)) . '</td>
        </tr>
                ';
            }
        ?>
    </tbody>
</table>

<h2>System Variables</h2>
<table>
    <tbody>
        <?php
            foreach ($json_data['System']['SystemVariables'] as $key => $val) {
                echo '
        <tr>
            <td>' . $key . '</td>
            <td>' . implode('<br/>', explode(';', $val)) . '</td>
        </tr>
                ';
            }
        ?>
    </tbody>
</table>

<h2>Startup Tasks</h2>
<table>
    <thead>
        <tr>
            <th>App Name</th>
            <th>App Path</th>
            <th>Timestamp</th>
        </tr>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['System']['StartupTasks'], ['AppName', 'ImagePath', 'TimeStamp']) ?>
    </tbody>
</table>

<h2>Installed Updates</h2>
<table>
    <thead>
        <tr>
            <th>Update</th>
            <th>Installed On</th>
        </tr>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['System']['InstalledHotfixes'], ['HotFixID', 'InstalledOn']) ?>
    </tbody>
</table>

<h1>Browser Extensions</h1>
<?php
    foreach ($json_data['System']['BrowserExtensions'] as $browser):
        if (str_contains(strtolower($json_data['System']['DefaultBrowser']), strtolower($browser['Name']))) {
            $default_browser = "(Default)";
        } else $default_browser = '';

        foreach ($browser['Profiles'] as $profile):
            $profile_key = array_search($profile, $browser['Profiles']);
            ?>
            <h2 class="item-header"><?= $browser['Name'] ?> Profile
                "<?= $profile['name'] /* This is lowercase in the json for some reason */ ?>"</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?= array_table_iter($profile['Extensions'], ['name', 'version', 'description']) /* These are lowercase in the json for some reason*/ ?>
                </tbody>
            </table>
        <?php
        endforeach;
    endforeach;
?>

<!-- Haven't implemented functionality in PHP yet, in JS for now -->
<h1>Running Processes</h1>
<table id="processes-table">
    <thead>
        <th>PID</th>
        <th>Name</th>
        <th>Path</th>
        <th>RAM (MB)</th>
    </thead>
</table>

<h1>Installed Apps</h1>
<table id="installed-apps-table">
    <thead>
        <th>Name</th>
        <th>Version</th>
        <th>Install Date</th>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['System']['InstalledApps'], ['Name', 'Version', 'InstallDate']) ?>
    </tbody>
</table>

<?php //if (!empty($json_data['System']['WindowsStorePackages'])) { ?>
<h1>Windows Store</h1>
<table id="windows-store-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Program ID</th>
            <th>Version</th>
            <th>Vendor</th>
        </tr>
        <?= array_table_iter($json_data['System']['WindowsStorePackages'], ['Name', 'ProgramID', 'Vendor', 'Version']) ?>
    </thead>
</table>
<?php //} ?>

<h1>Services</h1>
<table id="services-table" class="table">
    <thead>
        <th>State</th>
        <th>Name</th>
        <th>Caption</th>
        <th>Path</th>
        <th>Start Mode</th>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['System']['Services'], ['State', 'Name', 'Caption', 'PathName', 'StartMode']) ?>
    </tbody>
</table>

<h1>Tasks</h1>
<table id="tasks-table">
    <thead>
        <th>State</th>
        <th>Active</th>
        <th>Name</th>
        <th>Path</th>
        <th>Author</th>
        <th>Triggers</th>
    </thead>
    <tbody>
        <?=
            array_table_iter($json_data['System']['ScheduledTasks'], ['State', 'IsActive', 'Name', 'Path', 'Author', 'TriggerTypes'],
                function (&$row) {
                    $row['IsActive'] = $row['IsActive'] ? 'Yes' : 'No';
                    $row['TriggerTypes'] = implode('<br/>', $row['TriggerTypes']);
                })
        ?>
    </tbody>
</table>

<h1>Network</h1>
<h2>Adapters</h2>
<?php

    foreach ($json_data["Network"]["Adapters"] as $nic) {
        // if DNSIPV6 is a string, explode it. If it's null, return an empty array. If it's an array, just give the array
        $ipv6_dns = $nic['DNSIPV6'] ? ( is_array($nic['DNSIPV6']) ? $nic['DNSIPV6'] : explode(',', $nic['DNSIPV6']) ) : [];

        echo '
<h2 class="item-header">' . $nic["Description"] . ' </h2>
<table class="table nic">
    <tr>
        <td>Name</td>
        <td>' . $nic['Description'] . '</td>
    </tr>
    <tr>
        <td>Interface Index</td>
        <td>' . $nic['InterfaceIndex'] . '</td>
    </tr>
    <tr>
        <td>MAC</td>
        <td>' . $nic["MACAddress"] . '</td>
    </tr>

    <tr>
        <td>Gateway(s)</td>
        <td>' . safe_implode('<br/>', $nic["DefaultIPGateway"]) . '</td>
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
        <td>DNS Suffixes</td>
        <td>' . safe_implode('<br/>', $nic['DNSDomainSuffixSearchOrder']) . '</td>
    </tr>

    <tr>
        <td>IP Enabled?</td>
        <td>' . $nic["IPEnabled"] . '</td>
    </tr>

    <tr>
        <td>IP(s)</td>
        <td>' . safe_implode('<br/>', $nic['IPAddress']) . '</td>
    </tr>

    <tr>
        <td>Subnet</td>
        <td>' . safe_implode('<br/>', $nic['IPSubnet']) . '</td>
    </tr>

    <tr>
        <td>Physical Adapter?</td>
        <td>' . (($nic['PhysicalAdapter'] ? 'Yes' : 'No') ?? 'unknown') . '</td>
    </tr>
        ';

        if (isset($nic["LinkSpeed"])) {
            echo '
    <tr>
        <td>Link Speed</td>
        <td>' . round($nic["LinkSpeed"] / 1_000_000) . 'Mbps </td>
    </tr>
            ';
        }

        if (isset($nic["PhysicalAdapter"]) && $nic["PhysicalAdapter"]) {
            echo '
    <tr>
        <td>Static DNS Servers?</td>
        <td>' . ($nic['DNSIsStatic'] ? 'Yes' : 'No') . '</td>
    </tr>

    <tr>
        <td>DNS Servers</td>
        <td>' . safe_implode('<br/>', array_merge($nic['DNSServerSearchOrder'] ?? [], $ipv6_dns)) . '</td>
    </tr>
    
    <tr>
        <td>Full Duplex?</td>
        <td>' . ($nic["FullDuplex"] ? 'Yes' : 'No') . '</td>
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
        <td>' . ($nic["OperationalStatusDownMediaDisconnected"] ? 'Down - Media Disconnected' : '') . '</td>
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

        echo '</table>';
    }

?>

<h2>Connections</h2>
<table id="network-connections-table">
    <thead>
        <th>Local IP</th>
        <th>Local Port</th>
        <th>Remote IP</th>
        <th>Remote Port</th>
        <th>Process Name</th>
    </thead>
    <tbody>
        <?php
            $conns = $json_data['Network']['NetworkConnections']; // trying to make a copy
            foreach ($conns as &$con) { // pass by reference because it will be modified
                foreach ($json_data['System']['RunningProcesses'] as $proc) {
                    if ($con['OwningPID'] === $proc['Id']) {
                        $con['OwningPID'] = $proc['ProcessName'];
                    }
                }
            }

            echo array_table_iter($conns, ['LocalIPAddress', 'LocalPort', 'RemoteIPAddress', 'RemotePort', 'OwningPID']);
        ?>
    </tbody>
</table>

<h2>Routes Table</h2>
<table id="routes-table">
    <thead>
        <th>Route</th>
        <th>Destination</th>
        <th>Interface</th>
        <th>Mask</th>
        <th>Metric</th>
        <th>Next Hop</th>
    </thead>
    <tbody>
        <?php
            $routes = $json_data['Network']['Routes']; // trying to make a copy
            foreach ($routes as &$route) { // pass by reference because it will be modified
                foreach ($json_data['Network']['Adapters'] as $adapter) {
                    if ($route['InterfaceIndex'] === $adapter['InterfaceIndex']) {
                        $route['InterfaceIndex'] = $adapter['Description'];
                    }
                }
            }

            echo array_table_iter($routes, ['Description', 'Destination', 'InterfaceIndex', 'Mask', 'Metric1', 'NextHop']);
        ?>
    </tbody>
</table>

<h2>Others</h2>
<h3>Recieve Side Scaling</h3>
<table>
    <tbody>
        <tr>
            <td>RecieveSideScaling</td>
            <td><?= $json_data['Network']['ReceiveSideScaling'] ? 'True' : 'False' ?></td>
        </tr>
    </tbody>
</table>
<h3>Auto Tuning Level Local</h3>
<table>
    <tbody>
        <?= object_table_iter($json_data['Network']['AutoTuningLevelLocal']) ?>
    </tbody>
</table>

<h2>Hosts File</h2>
<?php
    echo '<pre class="file"><code>';
    $lines = explode("\n", $json_data['Network']['HostsFile']);
    foreach ($lines as $line) {
        echo "<span>$line</span>";
    }
    echo '</code></pre>';
?>

<?php
    $unexpectedShutdownsPresent = !empty($json_data['Events']['UnexpectedShutdowns']);
    $machineCheckPresent = !empty($json_data['Events']['MachineCheckExceptions']);
    $pcieWheaPresent = !empty($json_data['Events']['PcieWheaErrors']);
    $wheaPresent = !empty($json_data['Events']['WheaErrorRecords']);

    if ($unexpectedShutdownsPresent || $machineCheckPresent || $pcieWheaPresent || $wheaPresent) {
        echo "
<h1>Events</h1>
        ";
    }

    if ($unexpectedShutdownsPresent) {
?>
<h2>Unexpected Shutdowns</h2>
<table id="unexpected-shutdowns-table">
    <thead>
        <th>Timestamp</th>
        <th>Bugcheck Code</th>
        <th>P1</th>
        <th>P2</th>
        <th>P3</th>
        <th>P4</th>
        <th>Power Button Recorded</th>
    </thead>
    <tbody>
        <?=
            array_table_iter(
                $json_data['Events']['UnexpectedShutdowns'],
                ['Timestamp','BugcheckCode','BugcheckParameter1','BugcheckParameter2',
                    'BugcheckParameter3','BugcheckParameter4','PowerButtonTimestamp'],
                function(&$row) {
                    // istg i messed up the capitalization of "BugcheckCode" like 5 times
                    $row['BugcheckCode'] = "0x" . dechex($row['BugcheckCode']);
                }
            );
        ?>
    </tbody>
</table>
<?php
    }

    if ($machineCheckPresent) {
        echo "<h2>MachineCheck Exceptions</h2>";
        foreach ($json_data['Events']['MachineCheckExceptions'] as $exception_item) {
            echo "
<table class='machinecheck-exception'>
    <tr>
        <td>Timestamp</td>
        <td>{$exception_item['Timestamp']}</td>
    </tr>
    <tr>
        <td>MCE Status Register Valid</td>
        <td>{$exception_item['MciStatusRegisterValid']}</td>
    </tr>
    <tr>
        <td>Error Overflow</td>
        <td>{$exception_item['ErrorOverflow']}</td>
    </tr>
    <tr>
        <td>Uncorrected Error</td>
        <td>{$exception_item['UncorrectedError']}</td>
    </tr>
    <tr>
        <td>Error Reporting Enabled</td>
        <td>{$exception_item['ErrorReportingEnabled']}</td>
    </tr>
    <tr>
        <td>Processor Context Corrupted</td>
        <td>{$exception_item['ProcessorContextCorrupted']}</td>
    </tr>
    <tr>
        <td>Poisoned Data</td>
        <td>{$exception_item['PoisonedData']}</td>
    </tr>
    <tr>
        <td>Extended Error Code</td>
        <td>{$exception_item['ExtendedErrorCode']}</td>
    </tr>
    <tr>
        <td>MCA Error Code</td>
        <td>{$exception_item['McaErrorCode']}</td>
    </tr>
    <tr>
        <td>Error Message</td>
        <td>{$exception_item['ErrorMessage']}</td>
    </tr>
    <tr>
        <td>TransactionType</td>
        <td>{$exception_item['TransactionType']}</td>
    </tr>
    <tr>
        <td>Memory Hierarchy Level</td>
        <td>{$exception_item['MemoryHierarchyLevel']}</td>
    </tr>
    <tr>
        <td>Request Type</td>
        <td>{$exception_item['RequestType']}</td>
    </tr>
    <tr>
        <td>Participation</td>
        <td>{$exception_item['Participation']}</td>
    </tr>
    <tr>
        <td>Timeout</td>
        <td>{$exception_item['Timeout']}</td>
    </tr>
    <tr>
        <td>Memory Or IO</td>
        <td>{$exception_item['MemoryOrIo']}</td>
    </tr>
    <tr>
        <td>Memory Transaction Type</td>
        <td>{$exception_item['MemoryTransactionType']}</td>
    </tr>
    <tr>
        <td>Channel Number</td>
        <td>{$exception_item['ChannelNumber']}</td>
    </tr>
</table>
            ";
        }
    }

    if ($pcieWheaPresent) {
        ?>
<h2>PCIe WHEA Errors</h2>
<table id="pcie-whea-table">
    <thead>
        <th>Timestamp</th>
        <th>Vendor ID</th>
        <th>Device ID</th>
        <th>Matched Device</th>
    </thead>
    <tbody>
        <?=
            array_table_iter(
                $json_data['Events']['PciWheaErrors'],
                ['Timestamp','VendorId','DeviceId','MatchedDeviceDummy'],
                function(&$row) {
                    $row['MatchedDeviceDummy'] = "No Match";
                }
            );
        ?>
    </tbody>
</table>
<?php
    }
    if ($wheaPresent) {
echo "<h2>WHEA Errors</h2>";
        foreach ($json_data['Events']['WheaErrorRecords'] as $record) {
            echo "
<h3>[{$record['ErrorHeader']['Severity']}] {$record['ErrorHeader']['NotifyType']} - {$record['ErrorHeader']['Timestamp']}</h3>
<details open>
<summary class='whea-summary'>Error Header</summary>
<table>
    <tbody>
        <tr>
            <td>Timestamp</td>
            <td>{$record['ErrorHeader']['Timestamp']}</td>
        </tr>
        <tr>
            <td>Signature</td>
            <td>{$record['ErrorHeader']['Signature']}</td>
        </tr>
        <tr>
            <td>Revision</td>
            <td>{$record['ErrorHeader']['Revision']}</td>
        </tr>
        <tr>
            <td>Signature End</td>
            <td>{$record['ErrorHeader']['SignatureEnd']}</td>
        </tr>
        <tr>
            <td>Section Count</td>
            <td>{$record['ErrorHeader']['SectionCount']}</td>
        </tr>
        <tr>
            <td>Severity</td>
            <td>{$record['ErrorHeader']['Severity']}</td>
        </tr>
        <tr>
            <td>Valid Bits</td>
            <td>{$record['ErrorHeader']['ValidBits']}</td>
        </tr>
        <tr>
            <td>Length</td>
            <td>{$record['ErrorHeader']['Length']}</td>
        </tr>
        <tr>
            <td>Timestamp</td>
            <td>{$record['ErrorHeader']['Timestamp']}</td>
        </tr>
        <tr>
            <td>Platform ID</td>
            <td>{$record['ErrorHeader']['PlatformId']}</td>
        </tr>
        <tr>
            <td>Partition ID</td>
            <td>{$record['ErrorHeader']['PartitionId']}</td>
        </tr>
        <tr>
            <td>Creator ID</td>
            <td>{$record['ErrorHeader']['CreatorId']}</td>
        </tr>
        <tr>
            <td>Notify Type</td>
            <td>{$record['ErrorHeader']['NotifyType']}</td>
        </tr>
        <tr>
            <td>Record ID</td>
            <td>{$record['ErrorHeader']['RecordId']}</td>
        </tr>
        <tr>
            <td>Flags</td>
            <td>{$record['ErrorHeader']['Flags']}</td>
        </tr>
        <tr>
            <td>Persistence Info</td>
            <td>{$record['ErrorHeader']['PersistenceInfo']}</td>
        </tr>
    </tbody>
</table>
</details>
<details open>
<summary class='whea-summary'>Error Packets</summary>
    <div class='whea-packets'>
            ";

            for ($i = 0; $i < count($record['ErrorDescriptors']); $i++) {
                $descriptor = $record['ErrorDescriptors'][$i];
                $packet = $record['ErrorPackets'][$i];
                echo "
        <table class='whea-descriptor'>
            <tbody>
                <tr>
                    <td>Section Offset</td>
                    <td>{$descriptor["SectionOffset"]}</td>
                </tr>
                <tr>
                    <td>Section Length</td>
                    <td>{$descriptor["SectionLength"]}</td>
                </tr>
                <tr>
                    <td>Revision</td>
                    <td>{$descriptor["Revision"]}</td>
                </tr>
                <tr>
                    <td>Valid Bits</td>
                    <td>{$descriptor["ValidBits"]}</td>
                </tr>
                <tr>
                    <td>Flags</td>
                    <td>{$descriptor["Flags"]}</td>
                </tr>
                <tr>
                    <td>Section Type</td>
                    <td>{$descriptor["SectionType"]}</td>
                </tr>
                <tr>
                    <td>FRU ID</td>
                    <td>{$descriptor["FRUId"]}</td>
                </tr>
                <tr>
                    <td>Section Severity</td>
                    <td>{$descriptor["SectionSeverity"]}</td>
                </tr>
                <tr>
                    <td>FRU Text</td>
                    <td>{$descriptor["FRUText"]}</td>
                </tr>
            </tbody>
        </table>
        <pre class='whea-packet'><code>$packet</code></pre>
                ";
            }

            echo "
    </div>
</details>
            ";
        }
    }
?>

<div id="debug-log">
    <h1>Debug Log</h1>
    <?php
        echo '<pre class="file"><code>';
        $lines = explode("\r\n", $json_data['DebugLogText']);
        foreach ($lines as $line) {
            echo "<span>$line</span>";
        }
        echo '</code></pre>';
    ?>
</div>
</main>
</body>
</html>

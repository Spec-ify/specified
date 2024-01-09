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

    //Don't ask me why this is an old fashioned for loop, I got carried away.
    //Getting the total amount of RAM in the system.
    $total_ram = 0;
    $ram_sticks = safe_count($json_data['Hardware']['Ram']);
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
            $timeString = '<span';
            if ($days > 3) $timeString .= ' class="red"';
            $timeString .= '>' . $days . ' day';
            if ($days != 1) {
                $timeString .= 's';
            }
            $timeString .= ', ';
        }


        // Add the number of hours to the string
        if ($hours) {
            $timeString .= $hours . ' hour';
            if ($hours != 1) {
                $timeString .= 's';
            }
            $timeString .= ', ';
        }

        // Add the number of minutes to the string
        if ($minutes) {
            $timeString .= $minutes . ' minute';
            if ($minutes != 1) {
                $timeString .= 's';
            }
            $timeString .= ', ';
        }
        // Add the number of seconds to the string A3BE8C
        if ($seconds) {
            if ($days || $hours || $minutes) $timeString .= 'and ';
            $timeString .= $seconds . ' second';
            if ($seconds != 1) {
                $timeString .= 's</span>';
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
    include('lists.php');
    // Set up the reference list
    $referenceListInstalled = $json_data['System']['InstalledApps'];
    $referenceListRunning = $json_data['System']['RunningProcesses'];

    $pupsFoundInstalled = array();
    foreach ($referenceListInstalled as $installed) {
        foreach ($puplist as $pups) {
            preg_match('/\b(' . strtolower($pups) . ')\b/', strtolower($installed['Name']), $matches, PREG_OFFSET_CAPTURE);
            if ($matches) {
                array_push($pupsFoundInstalled, $installed['Name']);
            }
        }
    }
    $pupsFoundInstalled = array_unique($pupsFoundInstalled);

    $pupsFoundRunning = array();
    foreach ($referenceListRunning as $running) {
        foreach ($puplist as $pups) {
            preg_match('/\b(' . strtolower($pups) . ')\b/', strtolower($running['ProcessName']), $matches, PREG_OFFSET_CAPTURE);
            if ($matches) {
                array_push($pupsFoundRunning, $running['ProcessName']);
            }
        }
    }
    $pupsFoundRunning = array_unique($pupsFoundRunning);

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

    function bytesToMegabytes($bytes)
    {
        // 1073741824 = 1024 * 1024
        return $bytes / 1048576;
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

    function safe_count(arr): int {
        if (is_countable($thing)) {
            return count($arr);
        } else {
            return 0;
        }
    }

    /**
     * Return table layout for a data array
     * @param string[][] $arr
     * @param string[] $cols
     */
    function array_table_iter(?array $arr, array $cols, $transform = null): string
    {
        $res = "";
        foreach ($arr as $row) {
            if ($transform) {
                $transform($row);
            }
            $res .= '<tr>';
            foreach ($cols as $col) {
                $res .= '<td>' . $row[$col] . '</td>';
            }
            $res .= '</tr>';
        }
        return $res;
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Specified</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <link
        href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet"/>
    <link href="static/css/doom-scroll.css" rel="stylesheet">
    <link href="static/css/tables.css" rel="stylesheet">
    <link href="static/css/themes.css" rel="stylesheet">
    <!--This section is for the discord embed card. Need to expand upon it. -->
    <meta name="og:title" content="<?= $json_data["BasicInfo"]["Hostname"] ?>"/>
    <meta name="og:site_name" content="Specify"/>
    <meta name="og:description" content="Generated on <?= $json_data["Meta"]["GenerationDate"] ?>"/>
    <meta name="og:type" content="data.specify_result"/>

    <script>
        window.PROFILE_NAME = "<?= $profile_name ?>";
    </script>
    <script defer src="static/js/doom-scroll.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.slim.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/sc-2.0.7/datatables.min.js"></script>
    <script src="static/js/konami.js"></script>
</head>
<body>
<noscript>You need to enable JavaScript to run this app.</noscript>
<nav>
    <ul id="navlist">
        <li id="nav-top-link"><a href="#top">Back To Top</a></li>
    </ul>
</nav>
<main>
<span class="linnker" id="top"></span>
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

    // EOL
    $eoltext = '';
    if (strpos($validversions, $json_data['BasicInfo']['Version']) !== false) {
        $eoltext = "Not EOL";
        $eolcolor = "green";
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
        <?= $osenglish ?>
        <span class="<?= $oscolor ?>"><?= $oscheck ?></span>.
        <span>(version <?= $json_data['BasicInfo']['FriendlyVersion'] ?>)</span>
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
            echo '
    <li>
        OneDrive Path Length : <span>' . $json_data['System']['OneDriveCommercialPathLength'] . '</span>
        OneDrive Name Length : <span>' . $json_data['System']['OneDriveCommercialNameLength'] . '</span>
    </li>
                ';
        }

        if ($json_data['System']['RecentMinidumps'] != 0) {
            // TODO: add link to download minidumps
            echo '
    <li>
        There have been <span class="red">' . $json_data['System']['RecentMinidumps'] . '</span> Minidumps found
    </li>
            ';
        }

        $hostFileHash = hash('ripemd160', $json_data['Network']['HostsFile']);
        $hostFileCheck = "4fbad385eddbc2bdc1fa9ff6d270e994f8c32c5f" !== $hostFileHash; // Pre-calculated Hash

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
    $driveKey = 0;

        foreach ($json_data['Hardware']['Storage'] as $storage_device) {
            $letters = array_filter(
                array_column($json_data['Hardware']['Storage'][$driveKey]['Partitions'], 'PartitionLabel')
            );
            $lettersString = implode(", ", $letters);

            if ($storage_device['SmartData']) {
                foreach ($storage_device['SmartData'] as $smartPoint) {
                    if (str_contains($smartPoint['Name'], '!')) {
                        if ($smartPoint['RawValue'] != '000000000000') {
                            echo "
    <li>
        <span class='drivespan'>{$storage_device['DeviceName']} ($lettersString)</span> has 
        <span class='yellow'>{$smartPoint['RawValue']} {$smartPoint['Name']}</span>
    </li>
                        ";
                        }
                    }
                }
            }

            if (abs((floor(bytesToGigabytes($storage_device['DiskCapacity'])) -
                    floor(bytesToGigabytes(getDriveCapacity($storage_device))))) > 5) {
                $onDisk = floor(bytesToGigabytes($storage_device['DiskCapacity']));
                $onParts = floor(bytesToGigabytes(getDriveCapacity($storage_device)));
                echo  "
    <li>
        <span>{$storage_device['DeviceName']} ($lettersString) </span> has differing capacities.
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
            echo '
    <li>
        Registry Value <span>' . $regkey['Name'] . '</span> found set, value of <span>' . $regkey['Value'] . '</span>
    </li>
                ';
        }
    }
?>
</ul>

<h1>PUPs</h1>
<?php if (!$pupsFoundInstalled && !$pupsFoundRunning) echo "No PUPs detected" ?>
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
                $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']);
                $ram_speed = $json_data['Hardware']['Ram'][$ram_stick]['ConfiguredSpeed'];
                echo '
        <tr>
            <td>' . $json_data['Hardware']['Ram'][$ram_stick]['DeviceLocation'] . '</td>
            <td>' . $json_data['Hardware']['Ram'][$ram_stick]['Manufacturer'] . '</td>
            <td>' . $json_data['Hardware']['Ram'][$ram_stick]['PartNumber'] . '</td>
            <td>' . $ram_speed . 'MHz</td>
            <td>' . $ram_size . 'MB</td>
        </tr>
                ';
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
        <th>Hardware</th>
        <th>Sensor</th>
        <th>Temperature (&deg;C)</th>
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
                if ($key == 'BIOSVersion') {
                    echo '
        <tr>
            <td>' . $key . '</td>
            <td>' . implode('<br/>', $value) . '</td>
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

<h1>Network Adapters</h1>
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

        echo '</table>';
    }

?>

<h1>Storage</h1>
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

        $letters = array_filter(
            array_column($drive['Partitions'], 'PartitionLetter')
        );
        $lettersString = implode(", ", $letters);
        $lettersStringDisplay = empty($lettersString) ? '' : "($lettersString)";

        echo '
    <h2 class="item-header">' . $drive['DeviceName'] . '</h2>
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
        <th>Device ID</th>
        <th>Manufacturer</th>
        <th>Name</th>
        <th>Status</th>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['Hardware']['AudioDevices'], ['DeviceID', 'Name', 'Manufacturer', 'Status']) ?>
    </tbody>
</table>

<h1>Power Profiles</h1>
<table>
    <thead>
        <th>Name</th>
        <th>Description</th>
        <th>Instance Path</th>
        <th>Active?</th>
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
        <th>Name</th>
        <th>Manufacturer</th>
        <th>Chemistry</th>
        <th>Design Capacity</th>
        <th>Current Full Charge Capacity</th>
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
        <th>App Name</th>
        <th>App Path</th>
        <th>Timestamp</th>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['System']['StartupTasks'], ['AppName', 'ImagePath', 'TimeStamp']) ?>
    </tbody>
</table>

<h2>Installed Updates</h2>
<table>
    <thead>
        <th>Update</th>
        <th>Installed On</th>
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
                    <th>Name</th>
                    <th>Version</th>
                    <th>Description</th>
                </thead>
                <tbody>
                    <?= array_table_iter($profile['Extensions'], ['name', 'version', 'description']) /* These are lowercase in the json for some reason*/ ?>
                </tbody>
            </table>
        <?php
        endforeach;
    endforeach;
?>

<h1>Devices</h1>
<table id="devices-table">
    <thead>
        <th>Status</th>
        <th>Name</th>
        <th>Description</th>
        <th>DID</th>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['Hardware']['Devices'], ['Status', 'Name', 'Description', 'DeviceID']) ?>
    </tbody>
</table>

<h1>Drivers</h1>
<table id="drivers-table">
    <thead>
        <th>Name</th>
        <th>Friendly Name</th>
        <th>Manufacturer</th>
        <th>DID</th>
        <th>Version</th>
    </thead>
    <tbody>
        <?= array_table_iter($json_data['Hardware']['Drivers'], ['DeviceName', 'FriendlyName', 'Manufacturer', 'DeviceID', 'DriverVersion']) ?>
    </tbody>
</table>

<!-- Haven't implemented functionality in PHP yet, in JS for now -->
<h1>Running Processes</h1>
<table id="processes-table">
    <thead>
        <th>PID</th>
        <th>Name</th>
        <th>Path</th>
        <th>RAM (MB)</th>
        <th>CPU</th>
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

<h1>Network Connections</h1>
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

<h1>Routes Table</h1>
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

<h1>Hosts File</h1>
<?php
    echo '<pre class="file"><code>';
    $lines = explode("\n", $json_data['Network']['HostsFile']);
    foreach ($lines as $line) {
        echo "<span>$line</span>";
    }
    echo '</code></pre>';
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

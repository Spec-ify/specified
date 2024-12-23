<?php 
$json_file = $_GET['file'];
$json = file_get_contents($json_file);
$json_data = json_decode($json, true);
$profile_name = pathinfo($json_file, PATHINFO_FILENAME);
?>
<div class="textbox metadata-detail tabbed-info">
                            <div class="metadata-detail-controls">
                                <button id="notes-button">Notes</button>
                                <button id="pups-button">Notable Software</button>
                                <button id="variables-button">Variables</button>
                                <button id="browsers-button">Browsers</button>
                                <button id="startup-button">Startup Tasks</button>
                                <button id="updates-button">Windows Updates</button>
                            </div>

                            <div class="metadata-detail-content json-data" id="notes">
                                <!-- OS Version -->

                                <h4 style="margin:5px; color:var(--meta-h4-color);">General Notes</h4>

                                <?php

                                if (($eolcolor == "red" && $oscolor == "green") || ($eolcolor == "green" && $oscolor == "red")) $osenglish = 'but';
                                else $osenglish = 'and';

                                ?>

                                <p>The OS is
                                    <span class="<?= $eolcolor ?>"><?= $eoltext ?></span>
                                    <?php if (!$os_insider) echo " $osenglish <span class='$oscolor'>$oscheck</span>"; ?>
                                    <span>(version <?= $json_data['BasicInfo']['FriendlyVersion'] ?>, build <?= $json_data['BasicInfo']['Version'] ?>)</span>
                                </p>

                                <p>The current uptime is
                                    <?= $test_time ?>.
                                </p>

                                <?php
                                if (empty($json_data['Security']['AvList'])) {
                                    echo '<p class="red">No AVs found!</p>';
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
                                        There have been <span class="red">' . $json_data['System']['RecentMinidumps'] . '</span> Minidumps found
                                    </p>
                                    ';
                                }

                                $hostFileHash = $json_data['Network']['HostsFileHash'];
                                $hostFileCheck = "2D6BDFB341BE3A6234B24742377F93AA7C7CFB0D9FD64EFA9282C87852E57085" !== $hostFileHash; // Pre-calculated Hash

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
                                                Battery <span>{$battery['Name']}</span> has a diminished capacity (Designed for {$designcap} Wh, currently {$currentcap} Wh)
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

                                $unexpected_shutdown_count = $json_data['Events']['UnexpectedShutdownCount'];
                                $machinecheck_count = $json_data['Events']['MachineCheckExceptionCount'];
                                $whea_count = $json_data['Events']['WheaErrorRecordCount'];
                                $pci_whea_count = $json_data['Events']['PciWheaErrorCount'];
                                if ($unexpected_shutdown_count > 0) {
                                    $noteshtml .= "
                                        <p>
                                            <span>$unexpected_shutdown_count</span> Unexpected Shutdowns detected
                                        </p>
                                    ";
                                }
                                if ($machinecheck_count > 0) {
                                    $noteshtml .= "
                                        <p>
                                            <span>$machinecheck_count</span> MachineCheck Exceptions detected
                                        </p>
                                    ";
                                }
                                if ($whea_count > 0) {
                                    $noteshtml .= "
                                        <p>
                                            <span>$whea_count</span> WHEA errors detected
                                        </p>
                                    ";
                                }
                                if ($pci_whea_count > 0) {
                                    $noteshtml .= "
                                        <p>
                                            <span>$pci_whea_count</span> PCI WHEA errors detected
                                        </p>
                                    ";
                                }

                                if ($json_data['Meta']['ElapsedTime'] > 20000) {
                                    $noteshtml .= '
                                        <p>Specify runtime is over 20s</p>
                                    ';
                                }

                                if ($json_data['System']['LimitedMemory']) {
                                    $noteshtml .= '
                                        <p>
                                            Device configured to use a limited amount of memory
                                        </p>
                                    ';
                                }

                                if ($json_data['System']['WindowsOld']) {
                                    $noteshtml .= '
                                        <p>
                                            <span>Windows.OLD</span> detected
                                        </p>
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

                                    $drivemodal = 'data-mdb-target="#drive-modal' . $driveKey . '"';
                                    $partitionmodal = 'data-mdb-target="#partitions-modal"';

                                    $letters = array_filter(
                                        array_column($json_data['Hardware']['Storage'][$driveKey]['Partitions'], 'PartitionLetter')
                                    );
                                    $lettersString = implode(", ", $letters);

                                    if ($storage_device['SmartData']) {
                                        foreach ($storage_device['SmartData'] as $smartPoint) {
                                            if (str_contains($smartPoint['Name'], '!')) {
                                                if ($smartPoint['RawValue'] != '000000000000') {
                                                    $drivehtml .= '<p><span class="drive-span" data-mdb-toggle="modal" type="button" ' . $drivemodal . '>'
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
                                    $drivehtml = '<br> <h4 style="margin:5px; color:var(--meta-h4-color);">Drive / SMART Notes</h4>' . $drivehtml;
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
                                    $reghtml = '<br> <h4 style="margin:5px; color:var(--meta-h4-color);">Notable Registry Changes</h4>' . $reghtml;
                                    echo $reghtml;
                                }
                                ?>

                            </div>
                            <div class="metadata-detail-content json-data tablebox" id="pups">
                                <?php
                                $puphtml = '';

                                if (!empty($pupsfoundInstalled)) {

                                    $puphtml .= '<h4>Notable Software found Installed</h4>';

                                    $puphtml .= '<table id="pups-table-installed" class="table">';

                                    foreach ($pupsfoundInstalled as $pup) {
                                        $puphtml .= '<tr><td>' . $pup . ' Found installed</td></tr>';
                                    }

                                    $puphtml .= '</table>';
                                }

                                if (!empty($pupsfoundRunning)) {

                                    $puphtml .= '<h4>Notable Software found Running</h4>';

                                    $puphtml .= '<table id="pups-table-running" class="table">';

                                    foreach ($pupsfoundRunning as $pup) {
                                        $puphtml .= '<tr><td>' . $pup . ' Found Running</td></tr>';
                                    }

                                    $puphtml .= '</table>';
                                }

                                if (!empty($puphtml)) {
                                    echo $puphtml;
                                } else {
                                    echo '<h4>No Notable Software found, yay!</h4>';
                                }
                                ?>
                            </div>
                            <div class="metadata-detail-content json-data tablebox" id="variables">
                                <table class="table">
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
                                <table class="table">
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
                            <div class="metadata-detail-content json-data" id="browsers">
                                <div class="widgets-widgets widgets">

                                    <?php

                                    if ($json_data['System']['BrowserExtensions'] === []) {
                                        echo 'BrowserExtensions Array is empty!';
                                    }

                                    else {
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
                                                <h1>' . $browser['Name']    . $default_browser . '</h1>
                                                <img class="center" height="48px" width="48px" src="' . $browser_icon . '">
                                                </div>
                                            </div>
                                            </div>
                                            ';
                                            echo '<div class="modal fade " id="' . $browser['Name'] . 'Modal" tabindex="-1" aria-labelledby="' . $browser['Name'] . 'Modal" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modal-label">' . $browser['Name'] . ' Extensions</h5>
                                                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body" id="browser-container' . $browser['Name'] . '">';
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
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="metadata-detail-content json-data tablebox" id="startup">
                                <table class="table">
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
                            <div class="metadata-detail-content json-data tablebox" id="updates">
                                <table class="table">
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
<?php
include_once("common.php");
$json_file = $_GET['file'];
$json = file_get_contents($json_file);
$json_data = json_decode($json, true);
$profile_name = pathinfo($json_file, PATHINFO_FILENAME);
?>
<div class="metadata-metadata expanded" id="info">
    <div class="widgets-widgets widgets" id="hardware-widgets" data-hide="false">
        <div class="widget widget-cpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#cpu-modal">
            <h1>CPU</h1>
            <div class="widget-values">
                <div class="widget-value">
                    <div class="green">
                        <?= $json_data['Hardware']['Cpu']['Name'] ?>
                    </div>
                    <div>Callsign</div>
                </div>
            </div>
        </div>
        <div class="modal fade " id="cpu-modal" tabindex="-1" aria-labelledby="cpu-modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">CPU info</h5>
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
                    <div class="modal-body" id="cpu-info-table" style="display:none;">
                        <!-- This content is populated javascript side -->
                        <h6 class="modal-title" id="cpu-info-title">Database results for: ...</h6>
                        <table class="table">
                            <tbody id="fetched-cpu-info">

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cpu-more-info-button">More Info</button>
                        <button type="button" class="btn btn-secondary" id="cpu-close-button" data-mdb-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget widget-ram hover" type="button" data-mdb-toggle="modal" data-mdb-target="#ram-modal">
            <h1>Memory</h1>
            <div class="widget-values" <?php
                                        if (safe_count($json_data['Hardware']['Ram']) > 4) {
                                            echo 'style="display: flex; flex-flow: row wrap;"';
                                        }
                                        ?>>
                <?php
                $ram_sticks = safe_count($json_data['Hardware']['Ram']);
                $ram_stick = 0;
                if ($ram_sticks > 0) {
                    for ($ram_stick; $ram_stick < $ram_sticks; $ram_stick++) {
                        $current_ram_stick = $ram_stick + 1;
                        $flex_basis = 100 / ($ram_sticks / 2) . '%';
                        if ($json_data['Hardware']['Ram'][$ram_stick]['Capacity'] != 0) {
                            $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity'] / 1000);
                            echo '
                                        <div class="widget-value" style="flex: 1 1 ' . $flex_basis . ';">
                                            <div class="green">' . $ram_size . 'GB</div>
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
                } else {
                    echo '
                                        <div class="widget-value">
                                            <div class="red"> Error! </div>
                                            <div>Error retrieving memory information.</div>
                                        </div>';
                }
                ?>
            </div>
        </div>
        <div class="modal fade " id="ram-modal" tabindex="-1" aria-labelledby="ram-modal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">Memory info</h5>
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
        <div class="widget widget-board hover" type="button" data-mdb-toggle="modal" data-mdb-target="#board-modal">
            <h1>Motherboard</h1>
            <?php
            if ($motherboard) {
                echo '
                                    <div class="widget-values">
                                        <div class="widget-value">
                                            <div class="green">'
                    . $motherboard .
                    '</div>
                                            <div>OEM</div>
                                        </div>
                                        <div class="widget-value">
                                            <div class="green">'
                    . $json_data['Hardware']['Motherboard']['Product'] .
                    '
                                            </div>
                                            <div>Chipset</div>
                                        </div>
                                    </div>';
            } else {
                echo '
                                        <div class="widget-value">
                                            <div class="red"> Error! </div>
                                            <div>Error retrieving motherboard information.</div>
                                        </div>';
            }

            ?>

        </div>
        <div class="modal fade" id="board-modal" tabindex="-1" aria-labelledby="board-modal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">Board Information</h5>
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
                                    <td><?= $json_data['Hardware']['Motherboard']['Manufacturer'] ?></td>
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
                                if (is_null($json_data['Security']['Tpm']) || !(bool) $json_data['Security']['Tpm']['IsEnabled_InitialValue']) {
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
        <div class="widget widget-gpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#gpu-modal">
            <h1>GPU</h1>
            <div class="widget-values">
                <div class="widget-value">
                    <div class="green">
                        <?php

                        if (!isset($json_data['Hardware']['Monitors'])) {
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
        <div class="modal fade" id="gpu-modal" tabindex="-1" aria-labelledby="gpu-modal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">GPU and Monitor Info</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php
                        if (isset($json_data['Hardware']['Gpu'])) {
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

                        if (isset($json_data['Hardware']['Monitors'])) {
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
        <div class="widget widget-os hover" type="button" data-mdb-toggle="modal" data-mdb-target="#os-modal">
            <h1>Operating System</h1>
            <div class="widget-values">
                <div class="widget-value">
                    <div class="widget-value">
                        <span class="green">
                            <?= $json_data['BasicInfo']['Edition'] ?>
                        </span>
                    </div>
                    <div>
                        <?= $json_data['BasicInfo']['FriendlyVersion'] ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade " id="os-modal" tabindex="-1" aria-labelledby="os-modal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">System Information</h5>
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
                                                <td>' . ((bool) $json_data['Security']['SecureBootEnabled'] ? 'Enabled' : 'Disabled') . '</td>
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
        <div class="widget widget-board hover" type="button" data-mdb-toggle="modal" data-mdb-target="#nic-modal">
            <h1>NIC</h1>
            <div class="widget-values">
                <div class="widget-value">
                    <div class="green">

                        <?php
                        $adapterText = "Disconnected";

                        foreach ($json_data['Network']['Adapters'] as $adapter) {
                            if ((bool) $adapter['PhysicalAdapter'] && is_array($adapter['IPAddress']) && count($adapter['IPAddress']) > 0) {
                                $adapterText = $adapter['Description'];
                                break;
                            }
                        };

                        if ($adapter == "") {
                            foreach ($json_data['Network']['Adapters'] as $adapter) {
                                if ((bool) $adapter['PhysicalAdapter']) {
                                    $adapterText = $adapter['Description'];
                                    break;
                                }
                            };
                        }
                        echo $adapterText;

                        ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade " id="nic-modal" tabindex="-1" aria-labelledby="nic-modal" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">NIC Information</h5>
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

                            if (isset($nic["DNSIPV6"])) {
                                // Stolen from doom-scroll.php.
                                $ipv6_dns = isset($nic['DNSIPV6']) ? (is_array($nic['DNSIPV6']) ? $nic['DNSIPV6'] : explode(',', $nic['DNSIPV6'])) : [];
                                $table .= '
                                                            <tr>
                                                                <td>IPv6 DNS?</td>
                                                                <td>' . $ipv6_dns . '</td>
                                                            </tr>
                                                    ';
                            }

                            if (isset($nic["DNSIsStatic"])) {
                                $table .= '
                                                            <tr>
                                                                <td>Is DNS Static?</td>
                                                                <td>' . $nic["DNSIsStatic"] . '</td>
                                                            </tr>
                                                    ';
                            }

                            if (isset($nic["PhysicalAdapter"]) && (bool) $nic["PhysicalAdapter"]) {
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
    <div class="widgets-widgets widgets" data-hide="false">
        <?php
        $drives_amount = safe_count($json_data['Hardware']['Storage']);

        foreach ($json_data['Hardware']['Storage'] as $driveKey => $drive) {
            $drive_size_raw = $drive['DiskCapacity'];
            $drive_free_raw = getDriveFree($drive);
            $device_name = $drive['DeviceName'];
            $drive_taken_raw = $drive_size_raw - $drive_free_raw;
            $drive_size = floor(bytesToGigabytes($drive_size_raw));
            $drive_taken = floor(bytesToGigabytes($drive_taken_raw));
            // the drive size can sometimes be zero if the drive is failing
            if ($drive_taken != 0 && $drive_size != 0) {
                $drive_percentage = round($drive_taken / $drive_size * 100);
            } else $drive_percentage = 0;
            $flavor_color = '';

            if ($drive_percentage >= 80) {
                $flavor_color = "red";
            } elseif ($drive_percentage >= 50 && $drive_percentage <= 79) {
                $flavor_color = "yellow";
            } elseif ($drive_percentage >= 0 && $drive_percentage <= 49) {
                $flavor_color = "green";
            }
            if (abs(floor(bytesToGigabytes($drive['DiskCapacity'])) -
                floor(bytesToGigabytes(getDriveCapacity($drive)))) > 5) {
                $flavor_color = "red";
            }

            $letters = array_filter(
                array_column($drive['Partitions'], 'PartitionLetter')
            );
            $lettersString = implode(", ", $letters);

            echo '
					<div class="widget widget-disk hover" type="button" data-mdb-toggle="modal" data-mdb-target="#drive-modal' . $driveKey . '">
						<h1>' . $device_name . ' ' . $lettersString . '</h1>
						<div class="widget-values">
							<div class="widget-value">
								<div class="widget-single-value">
									<span
                                                                   class="' . $flavor_color . '">' . (int)$drive_taken . ' GB</span>
									<span>/</span>
									<span>' . (int) $drive_size . ' GB</span>
								</div>
								<div>' . $drive_percentage . '%</div>
							</div>
						</div>
					</div>
					<div class="modal fade" id="drive-modal' . $driveKey . '" tabindex="-1" aria-labelledby="drive-modal" aria-hidden="true">
						<div class="modal-dialog modal-xl">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="modal-label">' . $device_name . ' ' . $lettersString . '</h5>
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
                                    <div class="progress progress-bar partition-one-bar" style="width: ' . $part_size / ($drive_size_raw + 1) * 100 . '%;">
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
            if (is_array($drive['SmartData']) && count($drive['SmartData']) != 0) {
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
    <div class="widgets-widgets widgets" data-hide="false">
        <div class="widget widget-cpu hover">
            <h1>CPU
                <span>(Used)</span>
            </h1>
            <div class="widget-values">
                <div class="widget-value">
                    <div class="widget-value">
                        <span class="green">
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
                        <span class="green">
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
        <div class="widget widget-temps hover" type="button" data-mdb-toggle="modal" data-mdb-target="#temps-modal">
            <h1>Temps</h1>
            <div class="widget-values">
                <div class="widget-value">
                    <div>🔥</div>
                    <div>C</div>
                </div>
            </div>
        </div>
        <div class="modal fade " id="temps-modal" tabindex="-1" aria-labelledby="temps-modal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">Temps</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table id="temp-table" class="table">
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
        <div class="widget widget-audio hover" type="button" data-mdb-toggle="modal" data-mdb-target="#audio-modal">
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
                echo '<div class="widget-value green">Internal : ' . $internalaudio . '</div>';
                echo '<div class="widget-value yellow">External : ' . $externalaudio . '</div>';
                ?>
            </div>
        </div>
        <div class="modal fade " id="audio-modal" tabindex="-1" aria-labelledby="audio-modal" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">Audio Devices</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table id="audio-table" class="table">
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
        <div class="widget widget-power hover" type="button" data-mdb-toggle="modal" data-mdb-target="#power-modal">
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
                                    $profile_color = '--yellow';
                                } elseif (str_contains(strtolower($current_profile), 'high')) {
                                    $profile_color = '--red';
                                } elseif (str_contains(strtolower($current_profile), 'saver')) {
                                    $profile_color = '--green';
                                }

                                echo '<span style="color: var(' . $profile_color . ');">' . $current_profile . '</span>';
                            }
                        }
                        ?>
                        <div style="font-size: 10pt;">Current Profile</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade " id="power-modal" tabindex="-1" aria-labelledby="power-modal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">Power/Battery</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h4>Power Profiles</h4>
                        <table id="power-table" class="table">
                            <thead>
                                <th>Description</th>
                                <th>Element</th>
                                <th>Instance Path</th>
                                <th>Status</th>
                            </thead>
                        </table> <br>
                        <h4>Battery</h4>
                        <table id="battery-table" class="table">
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
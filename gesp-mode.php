<?php
//Checking if the file requested via GET exists. If not, we send to a custom 404.
if(!file_exists($_GET['file'])){
    http_response_code(404);
    include('404.html');
    die();
}
//Opening the file that comes after profile/ via GET and then parsing it with json_decode to get a usable variable with the json info back.
$json_file = $_GET['file'];
$json = file_get_contents($json_file);
$test=0;
$json_data = json_decode($json,true);
$profile_name = explode(".", explode("/", $json_file)[1])[0];

include('common.php');

// named this way to avoid conflicting with common.php
function gespTimeConvert($time) {
    $days = floor($time/86400);
    $hours = floor(($time % 86400)/3600);
    $minutes = floor(($time%3600)/60);
    $seconds = floor($time%60);
    return sprintf("%d days, %02d:%02d:%02d",$days,$hours,$minutes,$seconds);

}
$test_time = gespTimeConvert($json_data['BasicInfo']['Uptime']);
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
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="shortcut icon" href="assets/specify-glass-dynamic.svg" />
<link rel="shortcut icon" href="assets/specify-glass-dynamic.svg" />
<link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme light)" />
<link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme dark)" />
<style>
* {
    font-family: verdana !important;
    font-size: 12px;
}
body {
    background-color: #3b4252;
    color: White;
    margin-left: 30px;
}
h2 {
    color: #96a56c;
}
a:link {
    color: White;
}
a:visited {
    color: White;
}
a:hover{
    color: #252b2d;
}
table {
  font-family: arial, sans-serif;
  font-size: 12px;
  border-collapse: collapse;
}
td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}
tr:nth-child(even) {
  background-color: #2A2E3A;
}
#topbutton{
  opacity: 80%;
  width: 5%;
  padding-top: -3%;
  background-color: #ccc;
  position: fixed;
  bottom: 0;
  right: 0;
  border-radius: 20px;
  text-align: center;
  font-size: 24px;
  color: #87ab63;
}
#sysvarTable{
    max-width:600px;
}
</style>
    <script>
        window.PROFILE_NAME = "<?= $profile_name ?>";
    </script>
    <script defer="defer" src="static/js/redir.js"></script>
</head>
<body>
<noscript>You need to enable JavaScript to run this app.</noscript>
<a id="spectoggle" href="<?= http_strip_query_param($_SERVER['REQUEST_URI'], 'view') ?>">
    <button class="btn btn-info">Specify Mode</button>
</a>
<pre>
<a href="#top"><div id="topbutton">
⬆
</div></a>

Local: <?=$json_data['Meta']['GenerationDate']?>
<?php
$datetime = $json_data['Meta']['GenerationDate'];
$date = new DateTime($datetime);
$date->setTimezone(new DateTimeZone('UTC'));
$utc = $date->format('Y-m-d\TH:i:s.u\Z');
?>

UTC: <?=$utc?>

Specify Version: <?=$json_data['Version']?>

<h2>System Information</h2>
Edition: <?=$json_data['BasicInfo']['Edition']?>

Version: <?=$json_data['BasicInfo']['Version']?> (<?=$json_data['BasicInfo']['FriendlyVersion']?>)
Install date: <?=$json_data['BasicInfo']['InstallDate']?>

Uptime: <?=$test_time ?>

Hostname: <?=$json_data['BasicInfo']['Hostname']?>

Domain: <?=$json_data['BasicInfo']['Domain']?>

Boot mode: <?=$json_data['BasicInfo']['BootMode']?>

Boot state: <?=$json_data['BasicInfo']['BootState']?>

<h2>Notes</h2>
<p>
<?php
if($json_data['System']['StaticCoreCount']!=false){
    echo '<span>Static Core Count</span> found set.<br>';
}

$hostFileHash = hash('ripemd160', $json_data['Network']['HostsFile']);
$hostFileCheck = "4fbad385eddbc2bdc1fa9ff6d270e994f8c32c5f" !== $hostFileHash;

if ($hostFileCheck) {
    echo 'Hosts file has been modified from stock, it has been appended to bottom of this page<br>';
}

if($json_data['System']['RecentMinidumps']!=0){
    echo 'There have been <span>'.$json_data['System']['RecentMinidumps'].' Minidumps found</span><br>';
}
foreach ($json_data['System']['ChoiceRegistryValues'] as $regkey) {
    if ($regkey['Value'] && !in_array($regkey['Value'], $defaultRegKeys[$regkey['Name']])){
        echo 'Registry Value <span>' . $regkey['Name'] . '</span> found set, value of <span>' . $regkey['Value'] . '</span><br>';
    }
}
?></p>
<?php
    foreach($pupsfoundInstalled as $pup){
        echo '<tr><td>'.$pup.' Found installed</td></tr><br>';
    }
    foreach($pupsfoundRunning as $pup){
        echo '<tr><td>'.$pup.' Found Running</td></tr><br>';
    }
    ?>
<a name="top"></a>
<h2>Sections</h2>
<div style="line-height:0">
<p><a href="#hw">Hardware Basics</a></p>
<p><a href="#SecInfo">Security Information</a></p>
<p><a href="#bios">BIOS</a></p>
<p><a href="#SysVar">System Variables</a></p>
<p><a href="#UserVar">User Variables</a></p>
<p><a href="#hotfixes">Installed updates</a></p>
<p><a href="#StartupTasks">Startup Tasks</a></p>
<p><a href="#Power">Powerprofiles</a></p>
<p><a href="#RunningProcs">Running Processes</a></p>
<p><a href="#Services">Services</a></p>
<p><a href="#InstalledApps">Installed Applications</a></p>
<p><a href="#BrowserExtensions">Browser Extensions</a></p>
<p><a href="#NetConfig">Network Configuration</a></p>
<p><a href="#NetConnections">Network Connections</a></p>
<p><a href="#Drivers">Drivers and device versions</a></p>
<p><a href="#usbDevices">USB Devices</a></p>
<p><a href="#issueDevices">Devices with issues</a></p>
<p><a href="#Audio">Audio Devices</a></p>
<p><a href="#Disks">Disk Layouts(WIP)</a></p>
<p><a href="#SMART">SMART</a></p>
</div>

<h2 id='hw'>Hardware Basics</h2>
<table>
        <tr>
            <th>Device</th>
            <th>Sensor</th>
            <th>Temperature</th>
        </tr>
    <?php
foreach($json_data['Hardware']['Temperatures'] as $temperature){
$adjustedtemp = number_format($temperature['SensorValue'], 1);
echo"
        <tr>
            <td>{$temperature['Hardware']}</td>
            <td>{$temperature['SensorName']}</td>
            <td>{$adjustedtemp} C</td>
        </tr>";
    }
    ?>
</table>

    <table>
        
        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>Part</th>
            <th>Manufacturer</th>
            <th>Product</th>
            <th>Temperature</th>
            <th>Driver</th>
        </tr>

        <tr>
            <td>CPU</td>
            <td><?=$json_data['Hardware']['Cpu']['Manufacturer']?></td>
            <td><?=$json_data['Hardware']['Cpu']['Name']?></td>
            <td>WIP</td>
            <td></td>
        </tr>

        <tr>
            <td>Motherboard</td>
            <td><?=$json_data['Hardware']['Motherboard']['Manufacturer']?></td>
            <td><?=$json_data['Hardware']['Motherboard']['Product']?></td>
            <td></td>
            <td></td>
        </tr>

        <!-- Loop through every entry in GPU-->
        <?php
        foreach ($json_data['Hardware']['Gpu'] as $gpu):
            echo "
            <tr>
            <td>Video Card</td>
            <td>WIP</td>
            <td>{$gpu['Description']}</td>
            <td>WIP</td>
            <td>WIP</td>
            </tr>
            ";

        endforeach;
        ?>
        
    </table>

    <!-- Monitor Stuff -->
    <table>
        
        <colgroup><col/><col/><col/><col/><col/><col/><col/></colgroup>
        
        <tr>
            <th>GPU Name</th>
            <th>MonitorModel</th>
            <th>Out Type</th>
            <th>Current Mode</th>
        </tr>
            
        <?php
        foreach ($json_data['Hardware']['Monitors'] as $monitors){
            echo "
            <tr>
                <td>{$monitors['Name']}</td>
                <td>{$monitors['MonitorModel']}</td>
                <td>{$monitors['ConnectionType']}</td>
                <td>{$monitors['CurrentMode']}</td>
            </tr>
            ";
        }
        ?>
    </table>

    <!-- Check if battery, if yes, output table-->
    <?php
    if(!empty($json_data['Hardware']['Batteries'])) {
        echo "<table>
        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>Name</th>
            <th>Manaufacturer</th>
            <th>Chemistry</th>
            <th>Design Capacity</th>
            <th>Full Charge Capacity</th>
            <th>Remaining Life Percentage</th>
        </tr>";

        foreach ($json_data['Hardware']['Batteries'] as $batteries){
            echo "
            <tr>
                <td>{$batteries['Name']}</td>
                <td>{$batteries['Manufacturer']}</td>
                <td>{$batteries['Chemistry']}</td>
                <td>{$batteries['Design_Capacity']}</td>
                <td>{$batteries['Full_Charge_Capacity']}</td>
                <td>{$batteries['Remaining_Life_Percentage']}</td>
            </tr>
            ";
        }

        echo "</table>";
    }
    ?>

    <!-- Total RAM -->
    <table>
        
        <colgroup>
            <col/>
            <col/>
        </colgroup>
        
        <tr>
            <th>Total</th>
            <th>RAM</th>
        </tr>
        
        <?php
        $capacity = 0; foreach ($json_data['Hardware']['Ram'] as $total) {
            $capacity += $total['Capacity'];
        }

        $capacity /= 1024;

        echo "
            <tr>
                <td>{$capacity}</td>
                <td>GB</td>
            </tr>
        ";
        ?>

    </table>

    <!-- Actual RAM Table -->
    <table>

        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>Manufacturer</th>
            <th>Configuredclockspeed</th>
            <th>Devicelocator</th>
            <th>Capacity</th>
            <th>Serialnumber</th>
            <th>PartNumber</th>
        </tr>

        <?php
        foreach ($json_data['Hardware']['Ram'] as $ram) {
            if($ram['Capacity']!=0){
            echo "
            <tr>
                <td>{$ram['Manufacturer']}</td>
                <td>{$ram['ConfiguredSpeed']}</td>
                <td>{$ram['DeviceLocation']}</td>
                <td>{$ram['Capacity']}</td>
                <td>{$ram['SerialNumber']}</td>
                <td>{$ram['PartNumber']}</td>
            </tr>
            ";
            }
            else{
                echo "
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
            ";
            }
        }
        ?>

    </table>


<h2 id='SecInfo'>Security Information</h2>

    <table>

    <?php
    
    $avcount = 0;
    $fwcount = 0;

    foreach ($json_data['Security']['AvList'] as $av){
        $avstring = $av[$avcount];
            echo "
            <tr>
                <td>Antivirus{$avcount}:</td>
                <td>{$av}</td>
            </tr>
            ";
        $avcount += 1;
    }
    
    if (!empty($json_data['Security']['FwList'])){
        foreach ($json_data['Security']['FwList'] as $fw){
            $fwstring = $fw[$fwcount];
                echo "
                <tr>
                    <td>Firewall{$fwcount}:</td>
                    <td>{$fw}</td>
                </tr>
                ";
            $fwcount += 1;
        }
    }

    else {
        echo "<tr>
                <td>Firewall:</td>
                <td>Assume Defender</td>
            </tr>";
    }

    ?>

    <tr>
        <td>UAC Enabled:</td>
        <td><?=$json_data['Security']['UacEnabled']?></td>
    </tr>

    <tr>
        <td>UAC Level:</td>
        <td><?=$json_data['Security']['UacLevel']?></td>
    </tr>

    <tr>
        <td>SecureBoot:</td>
        <?php
        if($json_data['Security']['SecureBootEnabled']){
            echo '<td>'.$json_data['Security']['SecureBootEnabled'].'</td>';
        }
        else{
            echo '<td>False</td>';
        }
        ?>
    </tr>
    </table>
    <h2 id='TPM'>TPM</h2>
        <table>
            <colgroup>
                <col/>
                <col/>
                <col/>
                <col/>
                <col/>
            </colgroup>
            <tr>
                <th>Activated</th>
                <th>Enabled</th>
                <th>Owned</th>
                <th>Version</th>
                <th>Spec Version</th>
            </tr>
            <tr>
                <td><?=$json_data['Security']['Tpm']['IsActivated_InitialValue']?></td>
                <td><?=$json_data['Security']['Tpm']['IsEnabled_InitialValue']?></td>
                <td><?=$json_data['Security']['Tpm']['IsOwned_InitialValue']?></td>
                <td><?=$json_data['Security']['Tpm']['PhysicalPresenceVersionInfo']?></td>
                <td><?=$json_data['Security']['Tpm']['SpecVersion']?></td>
            </tr>
        </table>
    <h2 id="BIOS">BIOS</h2>
        <table>
            <colgroup>
                <col/>
                <col/>
                <col/>
                <col/>
            </colgroup>
            
            <tr>
                <th>Manufacturer</th>
                <th>SMBIOSBIOSVersion</th>
                <th>Name</th>
                <th>Version</th>
            </tr>
         
            <?php
            foreach($json_data['Hardware']['BiosInfo'] as $bios) {
                echo "<tr>
                        <th>{$bios['Manufacturer']}</th>
                        <th>{$bios['SMBIOSBIOSVersion']}</th>
                        <th>{$bios['Name']}</th>
                        <th>{$bios['Version']}</th>
                    </tr>";
            }
            ?>

        </table>

    <h2 id='SysVar'>System Variables</h2>

        <table id="sysvarTable">

        <tr>
            <th>Name</th>
            <th>Value</th>
        </tr>

        <?php
        foreach($json_data['System']['SystemVariables'] as $name => $value) {
            echo "<tr>
                    <th>{$name}</th>
                    <th>{$value}</th>
                </tr>";
        }
        ?>
            
        </table>

    <h2 id='UserVar'>User Variables</h2>

        <table>

            <tr>
                <th>Name</th>
                <th>Value</th>
            </tr>

            <?php
            foreach($json_data['System']['UserVariables'] as $name => $value) {
                echo "<tr>
                        <th>{$name}</th>
                        <th>{$value}</th>
                    </tr>";
            }
            ?>
        
    </table>

    <h2 id="Hotfixes">Installed updates</h2>
        
        <table>
            
            <colgroup>
                <col />
                <col />
                <col />
            </colgroup>

            <tr>
                <th>Description</th>
                <th>HotFix ID</th>
                <th>Installed On</th>
            </tr>

            <?php
            foreach($json_data['System']['InstalledHotfixes'] as $ih) {
                echo "<tr>
                        <th>{$ih['Description']}</th>
                        <th>{$ih['HotFixID']}</th>
                        <th>{$ih['InstalledOn']}</th>
                    </tr>";
            }
            ?>

        </table>


<h2 id='StartupTasks'>Startup Tasks</h2>

    <?php
        foreach($json_data['System']['StartupTasks'] as $su) {
            echo "{$su['AppName']} \n";
        }
    ?>


<h2 id='Power'>Powerprofiles</h2>

    <table>
        <colgroup>
            <col/>
            <col/>
        </colgroup>
        
        <tr>
            <th>Profile</th>
            <th>IsActive</th>
        </tr>

        <?php
            foreach($json_data['System']['PowerProfiles'] as $pp) {
                echo "<tr>
                    <th>{$pp['ElementName']}</th>
                    <th>{$pp['IsActive']}</th>
                </tr>";
            } // heh pp - k9
        ?>

    </table>

<h2 id='RunningProcs'>Running Processes WIP</h2>
Total RAM usage: WIP

    <table>

        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>

        
        <tr>
            <th>Name</th>
            <th>Count</th>
            <th>PID</th>
            <th>Mem (M)</th>
            <th>Path</th>
        </tr>

        <?php
                foreach($json_data['System']['RunningProcesses'] as $rp) {
                    $count = abs($rp['CpuPercent']);
                    $setconvert = $rp['WorkingSet']/1000000;
                    $set = (int)$setconvert;
                    echo "<tr>
                            <th>{$rp['ProcessName']}</th>
                            <th>{$count}</th>
                            <th>{$rp['Id']}</th>
                            <th>{$set}MB</th>
                            <th>{$rp['ExePath']}</th>
                        </tr>";
                }
                ?>

    </table>
    
<h2 id='Services'>Services</h2>

    <table>
        
        <colgroup>
            <col/>
            <col/>
            <col/>
        </colgroup>
        
        <tr>
            <th>Status</th>
            <th>StartType</th>
            <th>DisplayName</th>
        </tr>

        <?php
        foreach($json_data['System']['Services'] as $serv) {
            echo "<tr>";

            if ($serv['State'] == "Stopped"){
                echo "<td style='color:#ab6387'>Stopped</td>";
            }

            else {
                echo "<td style='color:#87ab63'>Running</td>";
            }
            
            echo "<td>{$serv['StartMode']}</td>
                <td>{$serv['Name']}</td>
                </tr>";
        }
        ?>
    
    </table>

<h2 id='InstalledApps'>Installed Apps</h2>
    
    <table>
    
        <colgroup>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>InstallDate</th>
            <th>DisplayName</th>
        </tr>

        <?php
            foreach($json_data['System']['InstalledApps'] as $ia) {
                echo "<tr>
                        <th>{$ia['InstallDate']}</th>
                        <th>{$ia['Name']}</th>
                    </tr>";
            }
        ?>
    
    </table>

<h2 id='BrowserExtensions'>Browser Extensions</h2>
    
    <table>
    
        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>
        
        <tr>
            <th>Author WIP</th>
            <th>name</th>
            <th>description</th>
            <th>version</th>
        </tr>

        <?php

        $browsercount = 0;

            foreach($json_data['System']['BrowserExtensions'] as $browser){
                foreach($browser['Profiles'] as $browser1){
                
                    echo "<tr>
                    <th>{$browser['Name']} {$browser1['name']}</th>
                    </tr>";

                    foreach($browser1['Extensions'] as $ext){
                        if($ext){
                        echo "<tr>
                            <th></th>
                            <th>{$ext['name']}</th>
                            <th>{$ext['description']}</th>
                            <th>{$ext['version']}</th>
                        </tr>";
                    }     }
                }   
            }      
        ?>
    
    </table>

<h2 id='NetConfig'>Network Configuration</h2>


        <?php
        foreach($json_data['Network']['Adapters'] as $netadapter) {

            if($netadapter['PhysicalAdapter']){
                ?>
                <table>

        <colgroup>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr><?php
                foreach($netadapter as $key => $printnet){
                    if(is_array($printnet)){
                        $printnet=implode(", ",$printnet);
                    }
                    if (is_bool($printnet)) {
                        $printnet = $printnet ? "True" : "False";
                    }
            echo "<tr>
                        <th>{$key}</th>
                        <th>{$printnet}</th>
                    </tr>";
        }}
        ?>
         </table><?php
        }

        ?>


<h2 id='NetConnections'>Network Connections WIP</h2>
    <table>

        <colgroup>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>Local Address</th>
            <th>Local Port</th>
            <th>Remote Address</th>
            <th>Remote Port</th>
            <th>State</th>
            <th>Process</th>
            <th>Process Path</th>
        </tr>

        <?php
        foreach($json_data['Network']['NetworkConnections'] as $netconnection){
                foreach($json_data['System']['RunningProcesses'] as $runningProcess){
                    if($netconnection['OwningPID'] == $runningProcess['Id']){
                        $netconpidname = $runningProcess['ProcessName'];
                        $netconpidpath = $runningProcess['ExePath'];
                    }
            }
            echo "<tr>
                        <th>{$netconnection['LocalIPAddress']}</th>
                        <th>{$netconnection['LocalPort']}</th>
                        <th>{$netconnection['RemoteIPAddress']}</th>
                        <th>{$netconnection['RemotePort']}</th>
                        <th>State</th>
                        <th>{$netconpidname}</th>
                        <th>{$netconpidpath}</th>
                    </tr>";
        }
        ?>

    </table>
<h2 id='Drivers'>Drivers and device versions</h2>

    <table>

        <colgroup>
            <col/>
            <col/>
        </colgroup>
        
        <tr>
            <th>Driver Name</th>
            <th>Driver Version</th>
        </tr>

        <?php
            foreach($json_data['Hardware']['Drivers'] as $drv) {
                echo "<tr>
                        <th>{$drv['DeviceName']}</th>
                        <th>{$drv['DriverVersion']}</th>
                    </tr>";
            }
        ?>

    </table>


<h2 id='usbDevices'>USB Devices</h2>
    <table>

        <colgroup>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>Device Name</th>
            <th>Device Description</th>
            <th>Device ID</th>
            <th>Device Status</th>
        </tr>

        <?php
        foreach($json_data['Hardware']['Devices'] as $usb) {
            if(str_contains($usb['DeviceID'],"USB")){
            echo "<tr>
                        <th>{$usb['Name']}</th>
                        <th>{$usb['Description']}</th>
                        <th>{$usb['DeviceID']}</th>
                        <th>{$usb['Status']}</th>
                    </tr>";
        }}
        ?>

    </table>

    <h2 id='issueDevices'>Devices with Issues</h2>
    <table>

        <colgroup>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>Device Status</th>
            <th>Device ID</th>
            <th>Device Name</th>
        </tr>

        <?php
        foreach($json_data['Hardware']['Devices'] as $issueDevice) {
            if(str_contains($issueDevice['Status'],"Error")){
                echo "<tr>
                        <th>{$issueDevice['Status']}</th>
                        <th>{$issueDevice['DeviceID']}</th>
                        <th>{$issueDevice['Name']}</th>
                    </tr>";
            }}
        ?>

    </table>
<h2 id='Audio'>Audio devices</h2>

    <table>

        <colgroup>
            <col/>
            <col/>
        </colgroup>
        
        <tr>
            <th>Manufacturer</th>
            <th>ProductName</th>
        </tr>

        <?php
            foreach($json_data['Hardware']['AudioDevices'] as $aud) {
                echo "<tr>
                        <th>{$aud['Manufacturer']}</th>
                        <th>{$aud['Name']}</th>
                    </tr>";
            }
        ?>

    </table>


<h2 id='Disks'>Disk layouts</h2>

    
    <table>
        
        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>
        
        <tr>
            <th>OperationalStatus WIP</th>
            <th>DiskNumber</th>
            <th>PartitionNumber</th>
            <th>Size (GB)</th>
            <th>IsActive WIP</th>
            <th>IsBoot WIP</th>
            <th>IsReadOnly WIP</th>
        </tr>
    
        <?php

            $storage1count = 1;

            foreach($json_data['Hardware']['Storage'] as $storage) {
                foreach($storage['Partitions'] as $storage1) {

                $partitioncap = $storage1['PartitionCapacity'] / 1073741824; // 1024 ^ 3

                    echo "<tr>
                        <th></th>
                        <th>{$storage['DiskNumber']}</th>
                        <th>$storage1count</th>
                        <th>$partitioncap</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>";

                    $storage1count += 1;
                }
            }
        ?>

    </table>

    <table>
        
        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>
        
        <tr>
            <th>Drive Type WIP</th>
            <th>File System</th>
            <th>File System Label</th>
            <th>Allocation Unit Size WIP</th>
            <th>Drive Letter WIP</th>
            <th>Size Remaining (GB)</th>
            <th>Size Total (GB)</th>
        </tr>

        <?php

            $storage1count = 1;

            foreach($json_data['Hardware']['Storage'] as $storage) {
                foreach($storage['Partitions'] as $storage1) {

                $partitioncap = $storage1['PartitionCapacity'] / 1073741824; // 1024 ^ 3
                $partitionrem = $storage1['PartitionFree'] / 1073741824; // 1024 ^ 3

                    echo "<tr>
                        <th></th>
                        <th>{$storage1['Filesystem']}</th>
                        <th>{$storage1['PartitionLabel']}</th>
                        <th></th>
                        <th></th>
                        <th>$partitionrem</th>
                        <th>$partitioncap</th>
                    </tr>";

                    $storage1count += 1;
                }
            }
        ?>

    </table>


<h2 id='SMART'>SMART WIP</h2>

    <table>
    
    <?php

        foreach($json_data['Hardware']['Storage'] as $smart) {

            echo "<tr>
                <td>Model:</td>
                <td>{$smart['DeviceName']}</td>
                </tr>";

            foreach($smart['SmartData'] as $smart1) {
                echo "<tr>
                    <td>{$smart1['Name']}</td>
                    <td>{$smart1['RawValue']}</td>
                    </tr>";
            }
        }
    ?>

    </table>

<?php 

if ($hostFileCheck){
    echo "<h2 id='HostFile'> Host File</h2> Hash (ripemd160): "
    . $hostFileHash
    . "<br><br>"
    . $json_data['Network']['HostsFile'];
}

?>

<h2 id='RunTime'>Runtime</h2>

    <table>

        <colgroup>
            <col/>
            <col/>
            <col/>
            <col/>
        </colgroup>

        <tr>
            <th>Seconds</th>
            <th>Milliseconds</th>
        </tr>

        <?php

        $rawruntime = $json_data['Meta']['ElapsedTime'];

        $seconds = floor($rawruntime / 1000);

        $millisecond = $rawruntime - ($seconds * 1000);

        echo "<tr>
                <th>$seconds</th>
                <th>$millisecond</th>
            </tr>";

        ?>

    </table>
</pre>
</body>

</html>

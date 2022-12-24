<?php
$json_file = $_GET['file'];
$json = file_get_contents($json_file);
$test=0;
$json_data = json_decode($json,true);
$profilename = explode(".", explode("/", $json_file)[1])[0];

$working_set = 0;
$cpu_percent = 0;
$process_count = count($json_data['System']['RunningProcesses']);
$i=0;
$eollist = '21H1 20H2 2004 1909 1903 1809 1803 1709 1703 1607 1511 1507';
$eol= false;
if(strpos($eollist, $json_data['BasicInfo']['FriendlyVersion'])==true){
    $eol = true;
}
$eoltext= '';
if($eol==true){
    $eoltext = "EOL";
}
else $eoltext = "Not EOL";

for($i==0;$i<$process_count;$i++){
    $working_set = $working_set + $json_data['System']['RunningProcesses'][$i]['WorkingSet'];
}
$i=0;
for($i==0;$i<$process_count;$i++){
    $cpu_percent = $cpu_percent + $json_data['System']['RunningProcesses'][$i]['WorkingSet'];
}
$ram_used = number_format($working_set/1073741824, 2, '.', '');
$green = '#A3BE8C';
$yellow = 'rgb(235, 203, 139)';
$red = 'rgb(191, 97, 106)';
$amd = 'rgb(215,27,27)';
$intel = 'rgb(8,110,224)';

$total_ram = 0;
$ram_sticks = count($json_data['Hardware']['Ram']);
$ram_stick = 0;
for($ram_stick == 0; $ram_stick<$ram_sticks;$ram_stick++){
    if($json_data['Hardware']['Ram'][$ram_stick]['Capacity']!= 0){
        $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']/1000);
        $total_ram = $total_ram + $ram_size;
    }}
$ram_used_percent = round((float)$ram_used / (float)$total_ram*100);
$motherboard = strtok($json_data['Hardware']['Motherboard']['Manufacturer'], " ");
if (str_contains($json_data['Hardware']['Cpu']['Name'], 'AMD')) {
    $cpu_color = $amd;
}
else{
    $cpu_color = $intel;
}
$ds=strtotime($json_data['Meta']['GenerationDate']);
function timeConvert($time) {
    // Split the time string into its parts
    $parts = preg_split('/[:.]/', $time);

    // Extract the days, hours, and minutes
    $days = (int) $parts[0];
    $hours = (int) $parts[1];
    $minutes = (int) $parts[2];

    // Initialize the string with the number of days
    $timeString = "{$days} day";
    if ($days != 1) {
        $timeString .= 's';
    }

    // Add the number of hours to the string
    if ($hours > 0 || $minutes > 0) {
        $timeString .= ", {$hours} hour";
        if ($hours != 1) {
            $timeString .= 's';
        }
    }

    // Add the number of minutes to the string
    if ($minutes > 0) {
        $timeString .= ", and {$minutes} minute";
        if ($minutes != 1) {
            $timeString .= 's';
        }
    }

    return $timeString;
}
$test_time = timeConvert($json_data['BasicInfo']['Uptime']);
?>


<!doctype html><html lang="en">
<meta content="text/html;charset=UTF-8" http-equiv="content-type"/>
<head>
    <meta charset="utf-8"/>
    <title>Specified</title>
    <meta content="width=device-width,initial-scale=1" name="viewport"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.dark.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">
</head>
<body>
<noscript>You need to enable JavaScript to run this app.</noscript>
<div id="main">
    <!--$-->
    <header class="header_header">
        <a class="logo" href="/">
            <img src="assets/logo.png" height="25em">
        </a>
        <select title="mappings">
            <optgroup
                    label="View">
                <option value="auto">Dark Mode</option>
                <option value="none">Light Mode</option>
            </optgroup>
        </select>
        </span></header><main>
        <div class="specify">
            <div class="controls">
                <div class="textbox title">
                    <span>Profile  <span id="filename"><?=$profilename?></span> created
                <?=date("F j, Y, g:i a",$ds)?>, runtime
					<?=$json_data['Meta']['ElapsedTime']." ms,"?>
                Under Specify Version <?=$json_data['Version']?>
				</span>
                </div>
                <input class="searchbar" type="text" placeholder="Search..." id="searchBarDiv"  onkeyup="searchFunction()">
            </div>
            <div  id="main">
            <div class="metadata_metadata expanded">
                <div class="widgets_widgets widgets" id="hardware_widgets" data-hide="false">
                    <div class="widget widget-cpu hover">
                        <h1>CPU</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$cpu_color?>;">
                                    <?=$json_data['Hardware']['Cpu']['Name']?>
                                </div>
                                <div>Callsign</div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-ram hover" type="button" data-mdb-toggle="modal" data-mdb-target="#ramModal">
                        <h1>RAM</h1>
                        <div class="widget-values">
                            <?php
                            $ram_sticks = count($json_data['Hardware']['Ram']);
                            $ram_stick = 0;
                            for($ram_stick; $ram_stick<$ram_sticks;$ram_stick++){
                                $current_ram_stick = $ram_stick + 1;
                                if($json_data['Hardware']['Ram'][$ram_stick]['Capacity']!= 0){
                                    $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']/1000);
                                    echo '
							<div class="widget-value">
								<div style="color: <?=$green?>;">'.$ram_size.'GB</div>
								<div>DIMM'.$current_ram_stick.'</div>
							</div>';
                                }
                                else
                                    echo '
							<div class="widget-value">
								<div style="color: rgb(215,27,27);">--</div>
								<div>DIMM' .$current_ram_stick.'</div>
							</div>';
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
                                        for($ram_stick; $ram_stick<$ram_sticks;$ram_stick++){
                                            $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']);
                                            $ram_speed = $json_data['Hardware']['Ram'][$ram_stick]['ConfiguredSpeed'] *2;
                                            echo
                                                '<tr>
                                                <td>'.$json_data['Hardware']['Ram'][$ram_stick]['DeviceLocation'].'</td>
                                                <td>'.$json_data['Hardware']['Ram'][$ram_stick]['Manufacturer'].'</td>
                                                <td>'.$json_data['Hardware']['Ram'][$ram_stick]['PartNumber'].'</td>
                                                <td>'.$ram_speed.'MHz</td>
                                                <td>'.$ram_size.'MB</td>
                                            </tr>';}
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
                    <div class="widget widget-board hover">
                        <h1>Motherboard</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$green?>;">
                                    <?=$motherboard?>
                                </div>
                                <div>OEM</div>
                            </div>
                            <div class="widget-value">
                                <div style="color: <?=$green?>;">
                                    <?=$json_data['Hardware']['Motherboard']['Product']?>
                                </div>
                                <div>Chipset</div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-gpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#gpuModal">
                        <h1>GPU</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$green?>;">
                                    <?=$json_data['Hardware']['Monitors'][0]['Name']?>
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
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $monitor_count = count($json_data['Hardware']['Monitors']);
                                        $monitor = 0;

                                        for($monitor;$monitor<$monitor_count;$monitor++){

                                            echo
                                                '<tr>
                                                <td>'.$json_data['Hardware']['Monitors'][$monitor]['Name'].'</td>
                                                <td>'.$json_data['Hardware']['Monitors'][$monitor]['DedicatedMemory'].'</td>
                                                <td>'.$json_data['Hardware']['Monitors'][$monitor]['CurrentMode'].'</td>
                                                <td>'.$json_data['Hardware']['Monitors'][$monitor]['MonitorModel'].'</td>
                                            </tr>';}
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
									<span
                                            style="color: <?=$green?>;">
										<?=$json_data['BasicInfo']['Edition']?>
									</span>
                                </div>
                                <div>
                                    <?=$json_data['BasicInfo']['FriendlyVersion']?>
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
                                                <td>'.$json_data['BasicInfo']['Edition'].'</td>
                                            </tr>';
                                                                                    echo
                                                                                        '<tr>
                                                <td>Version</td>
                                                <td>'.$json_data['BasicInfo']['Version'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Friendly Version</td>
                                                <td>'.$json_data['BasicInfo']['FriendlyVersion'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Install Date</td>
                                                <td>'.$json_data['BasicInfo']['InstallDate'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Uptime</td>
                                                <td>'.$json_data['BasicInfo']['Uptime'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Hostname</td>
                                                <td>'.$json_data['BasicInfo']['Hostname'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Username</td>
                                                <td>'.$json_data['BasicInfo']['Username'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Domain</td>
                                                <td>'.$json_data['BasicInfo']['Domain'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Boot Mode</td>
                                                <td>'.$json_data['BasicInfo']['BootMode'].'</td>
                                            </tr>';
                                        echo
                                            '<tr>
                                                <td>Boot State</td>
                                                <td>'.$json_data['BasicInfo']['BootState'].'</td>
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
                    <div class="widget widget-board hover"  type="button" data-mdb-toggle="modal" data-mdb-target="#nicModal">
                        <h1>NIC</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$green?>;">
                                    <?php
                                    $current_adapter = 0;
                                    $exit=false;
                                    $adapter_count = count($json_data['Network']['Adapters2']);
                                    for($current_adapter;$current_adapter < $adapter_count; $current_adapter++){
                                        if($json_data['Network']['Adapters2'][$current_adapter]['ConnectorPresent'] && $exit==false){
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

                    <?php
                    $drives_amount = count($json_data['Hardware']['Storage']);
                    $drive = 0;
                    $smart = 0;

                    for($drive=0; $drive < $drives_amount; $drive++)
                    {
                        if(is_countable($json_data['Hardware']['Storage'][$drive]['SmartData'])){
                            $smart_amount = count($json_data['Hardware']['Storage'][$drive]['SmartData']);
                        }
                        $current_drive = $drive+1;
                        $drive_size_raw = $json_data['Hardware']['Storage'][$drive]['DiskCapacity'];
                        $drive_free_raw = $json_data['Hardware']['Storage'][$drive]['DiskFree'];
                        $drive_taken_raw = $drive_size_raw - $drive_free_raw;
                        $drive_size = floor($drive_size_raw)/1073741824;
                        $drive_taken = floor($drive_taken_raw)/1073741824;
                        $drive_percentage = round((float)$drive_taken / (float)$drive_size*100);
                        $flavor_color = '';

                        if($drive_percentage>=80){
                            $flavor_color = $red;
                        }
                        elseif($drive_percentage>=50 && $drive_percentage <=79){
                            $flavor_color = $yellow;
                        }
                        elseif($drive_percentage>=0 && $drive_percentage <=49){
                            $flavor_color = $green;
                        }

                        echo '
					<div class="widget widget-disk hover" type="button" data-mdb-toggle="modal" data-mdb-target="#driveModal' . $drive . '">
						<h1>' . $json_data['Hardware']['Storage'][$drive]['DeviceName'] . '</h1>
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
									<h5 class="modal-title" id="ModalLabel">' . $json_data['Hardware']['Storage'][$drive]['DeviceName'] . '</h5>
									<button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<table class="table">
										<thead>
											<tr>
												<th scope="col">Index</th>
												<th scope="col">Name</th>
												<th scope="col">Value</th>
											</tr>
										</thead>
										<tbody>';
                        if (isset($json_data['Hardware']['Storage'][$drive]['SmartData'])) {
                            for ($smart = 0; $smart < $smart_amount; $smart++) {
                                echo
                                    '
											<tr>
												<th scope="row">' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['Id'] . '</th>
												<td>' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['Name'] . '</td>
												<td>' . $json_data['Hardware']['Storage'][$drive]['SmartData'][$smart]['RawValue'] . '</td>
											</tr>';
                            }
                        }
                        echo
                        '
										</tbody>
									</table>
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
									<span
                                            style="color: <?=$green?>;">
										<?=$ram_used?>%
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
									<span
                                            style="color: <?=$green?>;">
										<?=$ram_used?>GB
									</span>
                                    <span>/</span>
                                    <span>
										<?=$total_ram?>GB
									</span>
                                </div>
                                <div>
                                    <?=$ram_used_percent?>%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget widget-temps hover"  type="button" data-mdb-toggle="modal" data-mdb-target="#tempsModal">
                        <h1>Temps</h1>
                        <div class="widget-values">
                            <div class="widget-value">
                                <div style="color: <?=$cpu_color?>;">
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

                </div>
                <div class="textbox metadata-detail" id="notes">
                    <ul class="metadata-detail-controls">
                        <li class="selected">Notes</li>
                        <li class="pups_button">PUPs</li>
                        <li class="variables_button">Variables</li>
                    </ul>
                    <div class="metadata-detail-content">
                        <p>The OS is<?php
                            $oscheck = '';
                            if($json_data['BasicInfo']['FriendlyVersion'] == "22H2"){
                                $oscheck = 'Up-to-date';
                            }
                            else $oscheck = 'Not Up-to-Date';

                            ?>
                            <span><?=$oscheck?></span> running version "<span><?=$json_data['BasicInfo']['FriendlyVersion']?></span>".
                        </p>
                        <p>The OS is currently <span><?=$eoltext?></span>.

                        </p>
                        <p>The detected AV is "<span><?=$json_data['Security']['AvList'][0]?></span>".

                        </p>

                        <p>The process was created by Specify
                            <span>
								<?=$json_data['Version']?>
							</span>
                        </p>

                        <p>The current computer uptime is
                            <span>
								<?=$test_time?>.
							</span>
                        </p>
                        <p>Specify was running for
                            <span>
								<?=$json_data['Meta']['ElapsedTime']."ms"?>
							</span>.
                        </p>
                    </div>
                </div>
                <div class="textbox metadata-detail" id="pups">
                    <ul class="metadata-detail-controls">
                        <li class="notes_button">Notes</li>
                        <li class="selected">PUPs</li>
                        <li class="variables_button">Variables</li>
                    </ul>
                    <div class="metadata-detail-content">

                    </div>
                </div>
                <div class="textbox metadata-detail" id="variables">
                    <ul class="metadata-detail-controls">
                        <li class="notes_button">Notes</li>
                        <li class="pups_button">PUPs</li>
                        <li class="selected">Variables</li>
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
                            foreach($uservar_keys as $uservar){
                                if($uservar != "Path"){
                                echo '<tr><td>'.$uservar.'</td><td>'.$json_data['System']['UserVariables'][$uservar].'</td></tr>';
                                };
                            }?>
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
                            foreach($uservar_keys as $uservar){
                                if($uservar != "Path"){
                                    echo '<tr><td>'.$uservar.'</td><td>'.$json_data['System']['SystemVariables'][$uservar].'</td></tr>';
                                };
                            }?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div>
                <div class="allview">
                    <div class="header">
                        <h2>All View</h2>
                        <p>This is the default Specified view. It shows the entire profile as an expandable tree.</p>
                    </div>
                    <hr>
                    <div class="stack">
                        <li class="node collapsed parent">

                        </li>
                    </div>

                </div>
                <div class="accordion">
                    <h1 class="accordion-header" id="runningProcessesButton">
                        <button
                                class="accordion-button"
                                type="button"
                                data-mdb-toggle="collapse"
                                data-mdb-target="#runningProcesses"
                                aria-expanded="true"
                                aria-controls="runningProcesses"
                        >
                            Running Processes
                        </button></h1>
                <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="runningProcesses">
                    <h1>Running Processes</h1>
                    <table id="runningProcessesTable" class="table">
                        <thead>
                        <th>Name</th>
                        <th>Path</th>
                        <th>PID</th>
                        <th>RAM</th>
                        <th>CPU</th>
                        </thead>
                    </table>
                </div>
                    <h1 class="accordion-header" id="installedAppButton">
                        <button
                                class="accordion-button"
                                type="button"
                                data-mdb-toggle="collapse"
                                data-mdb-target="#installedApp"
                                aria-expanded="true"
                                aria-controls="installedApp"
                        >
                            Installed Apps
                        </button></h1>
                <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="installedApp">
                    <table id="installedAppTable" class="table">
                        <thead>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Install Date</th>
                        </thead>
                    </table>
                </div>
                    <h1 class="accordion-header" id="servicesTableButton">
                        <button
                                class="accordion-button"
                                type="button"
                                data-mdb-toggle="collapse"
                                data-mdb-target="#services"
                                aria-expanded="true"
                                aria-controls="services"
                        >
                            Services
                        </button></h1>
                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="services">
                        <table id="servicesTable" class="table">
                            <thead>
                            <th>Caption</th>
                            <th>Name</th>
                            <th>Path</th>
                            <th>Start Mode</th>
                            <th>State</th>
                            </thead>
                        </table>
                    </div>
                    <h1 class="accordion-header" id="tasksTableButton">
                        <button
                                class="accordion-button"
                                type="button"
                                data-mdb-toggle="collapse"
                                data-mdb-target="#tasks"
                                aria-expanded="true"
                                aria-controls="tasks"
                        >
                            Tasks
                        </button></h1>
                    <div class="textbox metadata-detail tablebox widget jsondata accordion-item accordion-collapse collapse" id="tasks">
                        <table id="tasksTable" class="table">
                            <thead>
                            <th>Path</th>
                            <th>Name</th>
                            <th>State</th>
                            <th>Active</th>
                            <th>Author</th>
                            <th>Triggers</th>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>

        </div>
        </div>
    </main>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.min.js" integrity="sha512-tWHlutFnuG0C6nQRlpvrEhE4QpkG1nn2MOUMWmUeRePl4e3Aki0VB6W1v3oLjFtd0hVOtRQ9PHpSfN6u6/QXkQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.js"
        type="text/javascript"
></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/sc-2.0.7/datatables.min.js"></script>
<script defer="defer" src="static/js/main.js"></script>
</html>
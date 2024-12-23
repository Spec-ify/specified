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

$script_nonce = bin2hex(random_bytes(20));
// TODO: style the progress bars differently, and remove unsafe-inline!
header("Content-Security-Policy: default-src 'self'; connect-src 'self' https://spec-ify.com http://localhost:3000; style-src 'self' https://cdnjs.cloudflare.com https://fonts.googleapis.com 'unsafe-inline'; font-src https://cdnjs.cloudflare.com https://fonts.gstatic.com; script-src 'self' https://cdnjs.cloudflare.com https://cdn.datatables.net 'nonce-$script_nonce'; img-src 'self' data:;");

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
    if (
        !(bool) $eolitem['lts']
        && !str_contains($eolitem['cycle'], '-e')
        && str_contains($eolitem['cycle'], '10')
        && $found10 === false
    ) {
        $latestver = $latestver . $eolitem['latest'] . ' ';
        $found10 = true;
    }

    // Windows 11
    if (
        !(bool) $eolitem['lts']
        && !str_contains($eolitem['cycle'], '-e')
        && str_contains($eolitem['cycle'], '11')
        && $found11 == false
    ) {
        $latestver = $latestver . $eolitem['latest'] . ' ';
        $found11 = true;
    }

    // Break out of loop
    if ($found10 == true && $found11 == true) {
        break;
    }
}

foreach ($eoldata as $eolitem) {
    if (
        !(bool) $eolitem['lts']
        && !str_contains($eolitem['cycle'], '-e')
        && strtotime($eolitem['support']) > time()
    ) {
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
    $eolcolor = "green";
} else if ($thisBuildInt > $latestBuildInt) {
    $os_insider = true;
    $eoltext = 'Insider';
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

if (isset($json_data['Hardware']['Ram'])) {
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

//Trimming the motherboard manufacturer string after the first space.
$motherboard = strtok($json_data['Hardware']['Motherboard']['Manufacturer'], " ");

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
    foreach ($notableSoftwareList as $pups) {
        preg_match('/\b(' . strtolower($pups) . ')\b/', strtolower($installed['Name']), $matches, PREG_OFFSET_CAPTURE);
        if ($matches) {
            array_push($pupsfoundInstalled, $installed['Name']);
        }
    }
}
$pupsfoundInstalled = array_unique($pupsfoundInstalled);

$pupsfoundRunning = array();
foreach ($referenceListRunning as $running) {
    foreach ($notableSoftwareList as $pups) {
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
<html lang="en" data-mdb-theme="dark">
<meta content="text/html;charset=UTF-8" http-equiv="content-type" />

<head>
    <meta charset="utf-8" />
    <title>Profile <?= $profile_name ?> | Specified</title>
    <meta content="width=device-width,initial-scale=1" name="viewport" />

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.dark.min.css" rel="stylesheet">
    <link href="static/css/themes.css?v=2" rel="stylesheet">
    <link href="static/css/main.css?v=2" rel="stylesheet">
    <link href="static/css/tables.css" rel="stylesheet">

    <!--This section is for the discord embed card. Need to expand upon it. -->
    <meta name="og:title" content="<?= $json_data["BasicInfo"]["Hostname"] ?>" />
    <meta name="og:site_name" content="Specify" />
    <meta name="og:description" content="Generated on <?= $json_data["Meta"]["GenerationDate"] ?>" />
    <meta name="og:type" content="data.specify_result" />

    <link rel="shortcut icon" href="assets/specify-glass-dynamic.svg" />
    <link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme light)" />
    <link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme dark)" />

    <script nonce="<?= $script_nonce ?>">
        window.PROFILE_NAME = "<?= $profile_name ?>";
    </script>
    <!--This should be first to make sure the themes load on time-->
    <script defer src="static/js/themes.js?v=2"></script>

    <!--Konami Code for Dev Stuff-->
    <script defer="defer" src="static/js/konami.js"></script>

    <!--Table Rendering-->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.slim.min.js"></script>
    <script defer type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/sc-2.0.7/datatables.min.js"></script>

    <!--UI Stuff-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.js" type="text/javascript"></script>

    <!--Main Scripts-->
    <script defer="defer" src="static/js/tables.js?v=1" type="module"></script>
    <script defer="defer" src="static/js/main.js?v=2"></script>
</head>

<body>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="blanket"></div>
    <div id="main">
        <!--$-->
        <button type="button" class="btn btn-info btn-floating btn-lg" id="btn-back-to-top">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="#0d1113">
                <!--! Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2024 Fonticons, Inc. -->
                <path d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2 160 448c0 17.7 14.3 32 32 32s32-14.3 32-32l0-306.7L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z" />
            </svg>
        </button>
        <header id="header-header">
            <div id="header-logo">
                <a class="logo" href="index.html">
                    <img src="assets/logo.png" height="25em">
                </a>
            </div>
            <div id="header-buttons">
                <button type="button" class="btn btn-info" id="collapse-toggle">Expand All</button>
                <button type="button" class="btn btn-info" id="collapse-toggle-hide">Collapse All</button>
                <a id="download" href="<?= $json_file ?>">
                    <button class="btn btn-info">View Raw JSON</button>
                </a>
                <?php
                if (isset($json_data['System']['DumpZip'])) {
                    $dumplink = str_replace("\n", '', $json_data['System']['DumpZip']);
                    if (filter_var($dumplink, FILTER_VALIDATE_URL)) {
                        echo '<a id="download" href="' . $dumplink . '">
                        <button class="btn btn-info">Download Dumps</button>
                    </a>';
                    }
                }
                ?>

            </div>
            <div id="style-toggles">
                <select id="view-toggle" style="width: 12em;">
                    <option selected hidden>Select View</option>
                    <option value="doom-scroll">Doom Scroll</option>
                    <option value="gesp-mode">Legacy View</option>
                </select>
                <select title="mappings" id="mode-toggle" style="width: 12em;">
                    <optgroup label="Theme">
                        <option value="classic">Dark Mode</option>
                        <option value="k9-mode">K9's Dark Mode</option>
                        <option value="light-mode">Light Mode</option>
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
                    <input class="searchbar" type="text" placeholder="Search..." id="searchbar-div">
                </div>
                <div id="main">
                    <?php
                    include("widgets.php");
                    include("tabbed_info.php");
                    include("tables.php");
                    ?>
                    <span>Massive Shoutout to <a href="https://spark.lucko.me/" target="_blank">Spark</a></span>
        </main>
</body>

</html>
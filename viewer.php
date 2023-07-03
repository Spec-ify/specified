<?php

//Checking if the file requested via GET exists. If not, we send to a custom 404.
if (!file_exists($_GET['file'])) {
    http_response_code(404);
    include('404.html');
    die();
}

$cookie_name = "mode";
$cookie_value = "Specify";

if (!isset($_COOKIE[$cookie_name])) {
    setcookie($cookie_name, $cookie_value, time() + (10 * 365 * 24 * 60 * 60), "/");
}

if ($_COOKIE[$cookie_name] == "GESP") {
    $logo = "assets/gesp-logo.png";
    $altText = "Switch to Specify Mode";
} else {
    $logo = "assets/spec-logo.png";
    $altText = "Switch to GESP Mode";
}

?>

<html>

<head>
    <meta charset="utf-8" />
    <title>Specified</title>
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
    <script defer="defer" src="static/js/header.js"></script>
</head>
<header class="header_header">

    <button class="logo" onclick='switchMode("<?= $_COOKIE[$cookie_name] ?>");'>
        <img src="<?= $logo ?>" height="25em" alt="<?= $altText ?>" title="<?= $altText ?>">
    </button>
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
    <select title="mappings" id="ModeToggle" style="width: 12em;">
        <optgroup label="View">
            <option value="classic">Dark Mode</option>
            <option value="k9">K9's Dark Mode</option>
            <option value="light">Light Mode</option>
        </optgroup>
    </select>


    </span>
</header>

<?php

if ($_COOKIE[$cookie_name] == "GESP") {
    include "gesp-mode.php";
} else {
    include "spec-mode.php";
}

?>

</html>
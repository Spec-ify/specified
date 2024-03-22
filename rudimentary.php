<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Specify Archive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="assets/specify-glass-dynamic.svg" />
    <link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme light)" />
    <link rel="icon" href="assets/specify-glass-black-256x256.png" media="(prefers-color-scheme dark)" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.dark.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">
    <style>

        * {
            line-height: 1.2;
            margin: 0;
        }

        html {
            display: table;
            font-family: sans-serif;
            height: 100%;
            text-align: center;
            width: 100%;
        }

        body {
            background-color: #3B4252;
            display: table-cell;
            vertical-align: middle;
            margin: 2em auto;
        }

        h1 {
            color: #e0e2e4;
            font-size: 2em;
            font-weight: 400;
        }

        p {
            color: #e0e2e4;
            margin: 0 auto;
            width: 280px;
        }

        @media only screen and (max-width: 280px) {

            body,
            p {
                width: 95%;
            }

            h1 {
                font-size: 1.5em;
                margin: 0 0 0.3em;
            }

        }
    </style>
</head>

<body>
<div id="archive">
<div class="widgets_widgets widgets" id="hardwareWidgets" data-hide="false">
<?php
//Initializing the directory
$dir = "files/";

//This returns the directory as an ordered array by last modified.
//https://gist.github.com/joeydenbraven/bbeba738ee9981f289907a8d2821ae64

function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess'); // -- ignore these file names
    $files = array(); //----------------------------------- create an empty files array to play with
    foreach (scandir($dir) as $file) {
        if ($file[0] === '.') continue; //----------------- ignores all files starting with '.'
        if (in_array($file, $ignored)) continue; //-------- ignores all files given in $ignored
        $files[$file] = filemtime($dir . '/' . $file); //-- add to files list
    }
    arsort($files); //------------------------------------- sort file values (creation timestamps)
    $files = array_keys($files); //------------------------ get all files after sorting
    return ($files) ? $files : false;
}
$scanned_directory = scan_dir($dir);

//Looping through each result in the directory and writing out a widget card with it's values, closing the entire thing in a hyperlink tag for easy navigation.
foreach($scanned_directory as $profile){
    $json_file = "files/".$profile;
    $json = file_get_contents($json_file);
    $json_data = json_decode($json,true);
    $profile_name = pathinfo($json_file, PATHINFO_FILENAME);
    $ds=strtotime($json_data['Meta']['GenerationDate']);
    echo '<a href="profile/'.$profile_name.'" target="_blank"><div class="widget hover"><div >
  <div class="card-body">
    <h5 class="card-title">'.$profile_name.'</h5>
    <div class="widget-values">
    <p class="widget-value">'.date("F j, Y, g:i a",$ds).'<br> '.$json_data['Hardware']['Cpu']['Name'].'<br>  '.$json_data['Hardware']['Gpu'][0]['Description'].'</p>
    </div>
  </div>
</div></div></a>';
}
?>
</div>
</div>
</body>

</html>

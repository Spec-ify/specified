<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Specify Archive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<div class="widgets_widgets widgets" id="hardware_widgets" data-hide="false">
<?php
$dir = "files/";
$scanned_directory = array_diff(scandir($dir), array('..', '.'));

foreach($scanned_directory as $profile){
    $json_file = "files/".$profile;
    $json = file_get_contents($json_file);
    $json_data = json_decode($json,true);
    $profile_name = explode(".", explode("/", $json_file)[1])[0];
    $ds=strtotime($json_data['Meta']['GenerationDate']);
    echo '<a href="profile/'.$profile.'" target="_blank"><div class="widget hover"><div >
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
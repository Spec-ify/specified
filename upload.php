<?php

$FILES_FOLDER = "files/"; // should end with slash

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(400);
    echo "You must post!";
    return;
}

if ($_SERVER["CONTENT_TYPE"] !== "application/json") {
    http_response_code(400);
    echo "You must use application/json";
    return;
}

// https://stackoverflow.com/a/8945912
$raw_data = file_get_contents("php://input");
$json_data = json_decode($raw_data, true);

// https://stackoverflow.com/a/6041773/11585384
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo "Invalid JSON data!";
    return;
}

$fullhash = md5($json_data["Version"].$json_data["Meta"]["GenerationDate"].$json_data["BasicInfo"]["Hostname"]);
$parthash = substr($fullhash, 0, 8);
$filename = "$parthash.json";
$filepath = "$FILES_FOLDER$filename";

if (!file_exists($FILES_FOLDER)) {
    mkdir($FILES_FOLDER, 0775);
}

file_put_contents($filepath, $raw_data);
http_response_code(201);
header("Location: ".realpath(dirname($_SERVER["REQUEST_URI"]))."/index.php?file=$filepath");
echo "File successfully created: $filepath";

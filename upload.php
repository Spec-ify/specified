<?php

$FILES_FOLDER = "files/"; // should end with slash

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(400);
    echo "You must post!";
    return;
}

// https://stackoverflow.com/a/8945912
$json_data = json_decode(file_get_contents("php://input"));

// https://stackoverflow.com/a/6041773/11585384
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo "Invalid JSON data!";
    return;
}

$filename = date("c").md5($json_data["Version"].$json_data["Meta"]["GenerationDate"]);

if (!file_exists($FILES_FOLDER)) {
    mkdir($FILES_FOLDER, 0775);
}

echo $FILES_FOLDER.$filename;

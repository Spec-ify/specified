<?php
//This is the file that handles the manual upload via the landing page
    $errors= array();
    include('json-check.php');
    $file_name = $_FILES['specify']['name'];
    $file_size = $_FILES['specify']['size'];
    $file_tmp = $_FILES['specify']['tmp_name'];
    $file_type = $_FILES['specify']['type'];
    $file_ext=strtolower(end(explode('.',$_FILES['specify']['name'])));

    function check_keys_recursive($arr1, $arr2) {
        foreach($arr1 as $key => $value) {
            if(is_array($value) && is_array($arr2[$key])) {
                if(check_keys_recursive($value, $arr2[$key])) {
                    return true;
                }
            }
            else if(!(array_key_exists($key, $arr2))) {
                return true;
            }
        }
    }

    $extensions= array("json");

//In the interlink upload, we generate the filename by hashing two strings inside the json.
//In this method, I actually just straight up randomize the name entirely.

//Don't ask why, it's fun.
//I don't expect this to hit entropy and filename clash with the other method or itself, given that files only have a lifespan of 24~ hours, we would have to have
//an insane amount of files being generated in a very short time span.
    $randomBytes = random_bytes(4);
    $randomString = bin2hex($randomBytes);
    $randomString = preg_replace('/[^A-Za-z0-9]/', '', substr($randomString, 0, 8));

    if(in_array($file_ext,$extensions)== false){
        $errors[]="Extension not allowed, please choose a Specify JSON file";
    }
    
    $json_data = json_decode(file_get_contents($file_tmp), true);

    if (check_keys_recursive($base, $json_data)) {
        $errors[] = "Malformed JSON, please choose a Specify JSON file";
    }

    $file_name = $randomString.".json";

    if(empty($errors)) {
        move_uploaded_file($file_tmp,"files/".$file_name);
        $pointer = 'profile/'.$randomString;
        http_response_code(303);
        header('Location: '.$pointer);
        echo json_encode(array('redirecturl'=>$pointer));
    }
    else{
        foreach($errors as $error){
            echo $error."\n";
        }
        var_dump($_REQUEST);
    }
?>
<?php
    $errors= array();
    $file_name = $_FILES['specify']['name'];
    $file_size = $_FILES['specify']['size'];
    $file_tmp = $_FILES['specify']['tmp_name'];
    $file_type = $_FILES['specify']['type'];
    $file_ext=strtolower(end(explode('.',$_FILES['specify']['name'])));

    $extensions= array("json");


    $randomBytes = random_bytes(4);
    $randomString = bin2hex($randomBytes);
    $randomString = preg_replace('/[^A-Za-z0-9]/', '', substr($randomString, 0, 8));

    if(in_array($file_ext,$extensions)=== false){
        $errors[]="extension not allowed, please choose a Specify JSON file";
    }
    $file_name = $randomString.".json";
    if(empty($errors)==true) {
        move_uploaded_file($file_tmp,"files/".$file_name);
        $pointer = 'viewer.php?file=files/'.$file_name;
        header('Location: '.$pointer);
        echo json_encode(array('redirecturl'=>$pointer));

    }else{
        print_r($errors);
        var_dump($_REQUEST);
    }

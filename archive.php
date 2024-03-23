<?php
//Shutting out error printing because the two vars will be unset a lot of the time and php will complain
ini_set('display_errors', 0);
include('archive_secret.php');
$login_user = $_POST['user'];
$login_pass = $_POST['pass'];

//Checking if given username and pw are correct
if($login_user == $user
    && $login_pass == $pass)
{
    include("rudimentary.php");
    die;
}
else
{
    //Else, printing out the login page. It's a simple form that just posts back on itself to run the check again.
    if(isset($_POST))
    {echo '
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
<div class="textbox metadata-detail tablebox widget jsondata" id="login-form">
<h1>Specify Archive Login</h1>
<form method="POST" action="archive">
    <div class="form-outline mb-4">
        <input type="text" name="user" class="form-control" id="login-form-user"></input><br/>
        <label class="form-label" for="login-form-user">Username</label>
    </div>
    <div class="form-outline mb-4">
        <input type="password" name="pass" class="form-control" id="login-form-pass"></input><br/>
        <label class="form-label" for="login-form-pass">Password</label>
    </div>
    <button type="submit" name="submit" class="btn btn-primary btn-block">Login</button>
</form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.min.js" integrity="sha512-tWHlutFnuG0C6nQRlpvrEhE4QpkG1nn2MOUMWmUeRePl4e3Aki0VB6W1v3oLjFtd0hVOtRQ9PHpSfN6u6/QXkQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/5.0.0/mdb.min.js"
        type="text/javascript"
></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/sc-2.0.7/datatables.min.js"></script>
<script defer="defer" src="static/js/main.js"></script>';
    }
}
?>
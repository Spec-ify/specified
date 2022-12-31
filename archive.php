<?php
ini_set('display_errors', 0);
include('archive_secret.php');
$login_user = $_POST['user'];
$login_pass = $_POST['pass'];

if($login_user == $user
    && $login_pass == $pass)
{
    include("rudimentary.php");
    die;
}
else
{
    if(isset($_POST))
    {echo '
        <form method="POST" action="archive">
            User <input type="text" name="user"></input><br/>
            Pass <input type="password" name="pass"></input><br/>
            <input type="submit" name="submit" value="Go"></input>
        </form>';
    }
}
?>
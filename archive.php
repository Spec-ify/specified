<?php
ini_set('display_errors', 0);
$user = $_POST['user'];
$pass = $_POST['pass'];

if($user == "rts"
    && $pass == "helpgang")
{
    include("rudimentary.php");
}
else
{
    if(isset($_POST))
    {echo '
        <form method="POST" action="archive.php">
            User <input type="text" name="user"></input><br/>
            Pass <input type="password" name="pass"></input><br/>
            <input type="submit" name="submit" value="Go"></input>
        </form>';
    }
}
?>
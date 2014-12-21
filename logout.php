<?php

require_once("logoutInterface.php");
session_start();
if(isset($_SESSION['playerID'])){
    LogoutInterface::logout($_SESSION['playerID']);
}
session_destroy();
echo '<a href="login.php">Back to login</a>'
?>
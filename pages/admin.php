<?php
require_once("../interfaces/adminInterface.php");
require_once("../inc/constants.php");
AdminInterface::addHeader();

//resetting database
if(isset($_POST['reset'])){
    if($_POST['reset'] == "RESET"){
        AdminInterface::resetDatabase();
        echo "done db reset";
    }
}
//starting server
if(isset($_POST['start'])){
    if($_POST['start'] == "START"){
        include "../imported/PHPWebSocket-Chat/server.php";
    }
}
//ending server
if(isset($_POST['end'])){
    if($_POST['end'] == "END"){
        //the server only listens to websockets, so the js sends the shutdown message
        //echo "<script> var portNum = ".constants::portNum."; var ipAddr = ".constants::ipAddr.";</script>";
        echo "<script src='../imported/PHPWebSocket-Chat/fancywebsocket.js'></script><script src='../inc/endServer.js'></script>";
        echo "server is now down";
    }
}

?>
<form action="admin.php" method="post">
    type RESET to reset db: <input type=password name=reset maxlength=5></input>
<input type=submit></input>
</br>
<form action="admin.php" method="post">
    type START to start server: <input type=password name=start maxlength=5></input>
<input type=submit></input>[page will hang]
</br>
<form action="admin.php" method="post">
    type END to end server: <input type=password name=end maxlength=5></input>
<input type=submit></input>
<?php
AdminInterface::addFooter();
?>
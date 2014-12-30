<?php
require_once("../interfaces/adminInterface.php");
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
        //include "../inc/updateServer.php";
    }
}

?>
<form action="admin.php" method="post">
    type RESET to reset db: <input type=text name=reset maxlength=5></input>
<input type=submit></input>
</br>
<form action="admin.php" method="post">
    type START to start server: <input type=text name=start maxlength=5></input> [page will hang]
<input type=submit></input>
</br>
<form action="../inc/updateServer.php" method="post">
    type END to end server: <input type=text name=end maxlength=5></input>
<input type=submit></input>
<?php
AdminInterface::addFooter();
?>
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
        include "../inc/updateServer.php";
        echo "server started";
    }
}

?>
<form action="admin.php" method="post">
    type RESET to reset db: <input type=text name=reset maxlength=5></input>
<input type=submit></input>

<form action="admin.php" method="post">
    type START to start server: <input type=text name=start maxlength=5></input>
<input type=submit></input>
<?php
AdminInterface::addFooter();
?>
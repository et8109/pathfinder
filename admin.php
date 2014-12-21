<?php
require_once("database.php");
require_once("adminInterface.php");

try{
    if(isset($_POST['reset'])){
        if($_POST['reset'] == "RESET"){
            AdminInterface::resetDatabase();
            echo "done db reset";
        }
    }
} catch (Exception $e){
    echo $e->getMessage();
}
?>
<html>
    <head>
    </head>
    <body>
        <form action="admin.php" method="post">
            type RESET to reset db: <input type=text name=reset maxlength=5></input>
        <input type=submit></input>
    </body>
</html>
<?php
require_once("../interfaces/adminInterface.php");
AdminInterface::addHeader();

if(isset($_POST['reset'])){
    if($_POST['reset'] == "RESET"){
        AdminInterface::resetDatabase();
        echo "done db reset";
    }
}
?>
<form action="admin.php" method="post">
    type RESET to reset db: <input type=text name=reset maxlength=5></input>
<input type=submit></input>
<?php
AdminInterface::addFooter();
?>
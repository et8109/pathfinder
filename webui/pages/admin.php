<?php
session_start();
require_once("../inc/pageBuilder.php");
$builder = new PageBuilder(PageBuilder::TYPE_NORMAL);
//$builder->redirectIfLoggedOut();
$builder->addHeader();

//resetting database
if(isset($_POST['reset'])){
    if($_POST['reset'] == "RESET"){
        $r = $builder->sendRequest("resetDB", array(
            'reset' => true
        ));  
        echo $r;
        echo "done db reset";
    } else{
        echo "incorrect input";
    }
}

?>
<form action="admin.php" method="post">
    type RESET to reset db: <input type=password name=reset maxlength=5></input>
<input type=submit></input>
</br>
<?php
$builder->addFooter();
?>

<?php
session_start();
require_once("../interfaces/registerInterface.php");
require_once("../inc/pageBuilder.php");
$builder = new PageBuilder(PageBuilder::pageTypes::normal);
$builder->redirectIfLoggedIn();
$builder->addHeader();


$uname="";

if(isset($_POST['uname'])){
    //sanitize
    $uname = $_POST['uname'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass1'];
    if($uname == null || $uname == ""){
        echo "Enter a valid username";
    }
    if($pass1 == null || $pass1 == ""){
        echo "Enter a valid password";
    }
    if ($pass1 != $pass2){
        echo "Your passwords don't match";
    }
    RegisterInterface::register($uname, $pass1);
    echo "Success! <a href='login.php'>Log in</a>";
}

?>
<form action="register.php" method="post">
    Username:<input type="text" name="uname" maxlength="20" value="<?php echo $uname ?>"></input></br>
    Password:<input type="password" name="pass1" maxlength="20" /></br>
    Password again:<input type="password" name="pass2" maxlength="20"></input></br>
  </br>
  <input type=submit></input>
</form>
<?php
$builder->addFooter();
?>

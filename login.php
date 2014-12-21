<?php
require_once("loginInterface.php");
session_start();
LoginInterface::addHeader();

//make sure they are not logged in
if(isset($_SESSION['playerID'])){
    header("Location: index.php");
}

if(isset($_POST['uname'])){
    //sanitize
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    if($uname == null || $uname == ""){
        throw new Exception("Enter a valid username");
    }
    if($pass == null || $pass == ""){
        throw new Exception("Enter a valid password");
    }
    //get username, password
    $playerRow = LoginInterface::getInfo($uname,$pass);
    if($playerRow == false){
        throw new Exception("Incorrect username or password");
    }
    //set session
    $_SESSION['playerID'] = $playerRow['id'];
    $_SESSION['lastupdateTime'] = 0;
    header("Location: index.php");
}
?>
<form action="login.php" method="post">
  Username: <input type=text name=uname maxlength=20></input>
  Password: <input type=password name=pass maxlength=20></input>
  <input type=submit></input>
  </form>
Guest account available,</br>
username and password are "guest".</br></br>
*The site only works on browsers that have implemented the most recent web-related APIs and whatnot</br>
**This is still a work in progress. All text on screen is for debugging.</br>
<?php
LoginInterface::addFooter();
?>
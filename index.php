<?php

session_start();
//make sure they are logged in
if(!isset($_SESSION['playerID'])){
    header("Location: login.php");
}
require_once("interface.php");
Interface_class::addHeaderIndex();
?>
<audio id="playerAudio" muted="true" autoplay></audio>
<audio id="otherAudio" autoplay></audio>
<h1>Audio Game</h1>
<div id="main">
    <a onclick='stop()' href="logout.php">logout</a>
    <div id="options">
        <input type="button" value="record attack [2 seconds]" onclick="record(recordedAttack())">
    </div>
</div>
<div id="log"></div>
<?php
Interface_class::addFooter();
?>
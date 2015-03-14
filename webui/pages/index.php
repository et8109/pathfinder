<?php
session_start();
include("../inc/pageBuilder.php");
$builder = new PageBuilder(PageBuilder::TYPE_INDEX);
$builder->redirectIfLoggedOut();
$builder->addHeader();
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
$buider->addFooter();
?>

<?php
session_start();
require_once("../inc/pageBuilder.php");
$builder = new PageBuilder(PageBuilder::TYPE_NORMAL);
$builder->redirectIfLoggedOut();
$builder->addHeader();

session_destroy();
echo '<a href="login.php">Back to login</a>'
?>

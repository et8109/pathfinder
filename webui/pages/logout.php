<?php
session_start();
require_once("../interfaces/logoutInterface.php");
require_once("../inc/pageBuilder.php");
$builder = new PageBuilder(PageBuilder::pageTypes::normal);
$builder->redirectIfLoggedOut();
$builder->addHeader();

session_destroy();
echo '<a href="login.php">Back to login</a>'
}

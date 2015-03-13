<?php
//header for all communication endpoints

session_start();
require_once("../shared/constants.php");

/**
 * define autoload for classes
 * throws exception if not found
 */
function __autoload($class_name) {
    require "../interfaces/objectified/$class_name.php";
}
?>

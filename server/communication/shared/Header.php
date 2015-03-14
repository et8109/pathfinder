<?php
//header for all communication endpoints

session_start();
require_once("../shared/constants.php");

/**
 * define and additional autoload for classes
 * throws exception if not found
 */
spl_autoload_register(function ($class_name) {
        include "../interfaces/objects/$class_name.php";
});
?>

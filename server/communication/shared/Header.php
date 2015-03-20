<?php
//header for all communication endpoints

session_start();
require_once("../shared/constants.php");
$_timeRecieved = time();

/**
 * define and additional autoload for classes
 * throws exception if not found
 */
spl_autoload_register(function ($class_name) {
    $file = $_SERVER['DOCUMENT_ROOT']."/server/interfaces/objects/$class_name.php"
;
    require $file;
    if (file_exists($file)) {
        include_once($file);
    }
});
?>

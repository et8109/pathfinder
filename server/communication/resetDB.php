<?php
/**
 * This page recieves requests to reset the database
 */
require_once("shared/Header.php");

try{
//only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        Database::resetdb(); 
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}

<?php
/**
 * This page recieves requests to log a player in
 */
require_once("shared/Header.php");

try{
//only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        echo Player::IDfromLogin($_POST['uname'],$_POST['pass']);
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}

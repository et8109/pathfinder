<?php
/**
 * This page recieves requests to log a player in
 */
require_once("shared/Header.php");
require_once("shared/Translator.php");

try{
//only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        $id = Player::IDfromLogin($_POST['uname'],$_POST['pass']);
        return $id;
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    return ErrorHandler::handle($e);
}

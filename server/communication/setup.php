<?php

/**
 * This page recieves requests to load initial player data
 **/
require_once("shared/Header.php");
require_once("shared/Translator.php");

try{
    //only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        $player = Player::fromDatabase($_SESSION['playerID']);
        $info = $player->getSetupInfo();
        Translator::add(array(
            //"spriteaudioURL" => $spriteRow[0]['url'].",".$spriteRow[1]['url'],
            "playerID" => $_SESSION['playerID'],
            "playeraudioURL" => $info['audioURL'],
            "peerID" => $info['peerid'],
            "zoneX" => $info['zonex'],
            "zoneY" => $info['zoney'],
            "version" => 2
        ));
        echo Translator::send();
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}

?>

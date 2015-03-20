<?php

/**
 * This page recieves requests to load initial player data
 **/
require_once("shared/Header.php");

try{
    if(!empty($_POST)){
        $player = Player::fromDatabase($_SESSION['playerID']);
        $info = $player->getSetupInfo();
        echo json_encode(array(
            "playerID" => $_SESSION['playerID'],
            "playeraudioURL" => array("Attack.mp3"),
            "peerID" => $info['peerid'],
            "zoneX" => $info['zonex'],
            "zoneY" => $info['zoney'],
            "version" => 2
        ));
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}
?>

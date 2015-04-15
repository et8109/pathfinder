<?php

/**
 * This page recieves requests to load initial player data
 **/
require_once("shared/Header.php");
require_once("responses/setupResponse.php");
require_once("../interfaces/Zone.php");
require_once("../interfaces/objects/creatures/Player.php");

try{
    if(!empty($_POST)){
        $player = Player::fromDatabase($_SESSION['playerID'], true);
        $info = $player->getSetupInfo();
        $response = new SetupResponse();
        
        $response->add_key($player->keyid);
        $response->add_playeraudioURL(array("Attack.mp3"));
        $response->add_peerID($info['peerid']);
        $response->add_version(2);
        echo $response->send();
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}
?>

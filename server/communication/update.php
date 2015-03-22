<?php
/**
 * This page recieves requests to know what's going on.
 */
require_once("shared/Header.php");

try{
//only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        $player = Player::fromDatabase($_SESSION['playerID']);
        Player::removeAllOldEvents($_timeRecieved);
        //get npcs in zone
        $npcToSend = [];
        $npcs = Npc::getInZone($player->zone);
        foreach($npcs as $npc){
            $npcToSend[] = $npc->interactPlayer($player);
        }
        //get enemies in zone
        $enemyToSend = [];
        /*$enemies = Enemy::getInZone($player->zone);
        foreach($enemies as $enemy){
            $enemyToSend[] = $enemy->interactPlayer($player);
        }*/
        Zone::getPlayingAmbients($player->zone);
       //check player events
       //$eventsResult = Player::getPlayerEventsInZone($zone,$_SESSION['lastupdateTime']);
       //update last event time
       $_SESSION['lastupdateTime'] = $_timeRecieved;
        
        echo json_encode(array(
            "npcs" => $npcToSend,
            "enemies" => $enemyToSend,
            "ambients" => Zone::getPlayingAmbients($player->zone)
        ));
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}

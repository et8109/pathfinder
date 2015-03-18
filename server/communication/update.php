<?php
/**
 * This page recieves requests to know what's going on.
 */
require_once("shared/Header.php");
require_once("shared/Translator.php");

try{
//only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        $player = Player::fromDatabase($_SESSION['playerID']);
        Player::removeAllOldEvents(Translator::$time);
        //get npcs in zone
        $npcs = Npc::getInZone($player->zone);
        foreach($npcs as $npc){
            $npc->interactPlayer($player);
        }
        //get enemies in zone
        $enemies = Enemy::getInZone($player->zone);
        foreach($enemies as $enemy){
            $enemy->interactPlayer($player);
        }
       //check nearby players
       //check player events
       /*$eventsResult = Player::getPlayerEventsInZone($zone,$_SESSION['lastupdateTime']);
       foreach($eventsResult as $row){
       AudioObj::addJson(array(
        "event" => true,
        "player" => true,
        "id" => $row['id'],
        "audioType" => $row['audiotype']
       ));
       }
        */
       //update last event time
        $_SESSION['lastupdateTime'] = Translator::$time;
        echo Translator::send();
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}

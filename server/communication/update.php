<?php
/**
 * This page recieves requests to know what's going on.
 */

session_start();
require_once("../shared/constants.php");
/**
 * define autoload for classes
 * throws exception if not found
 */
function __autoload($class_name) {
        require "../interfaces/objectified/$class_name.php";
}

try{
//only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        $player = Player::fromDatabase($_POST['playerID']);
        Player::removeAllOldEvents(AudioObj::$time);
        //get npcs in zone
        $npcs = Npc::getUpdateInfo($player->zone);
        foreach($npcs as $npc){
            $npc->interactPlayer($player);
        }
        //get enemies in zone
        $enemies = Enemy::getUpdateInfo($player->zone);
        foreach($enemies as $enemy){
            $enemy->interactPlayer($player);
        }
       
       //check nearby players
       //check player events
       $eventsResult = MainInterface::getPlayerEventsInZone($zone,$_SESSION['lastupdateTime']);
       foreach($eventsResult as $row){
       AudioObj::addJson(array(
        "event" => true,
        "player" => true,
        "id" => $row['id'],
        "audioType" => $row['audiotype']
       ));
       }
       
       //update last event time
       $_SESSION['lastupdateTime'] = AudioObj::$time;
       
    }
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
                ErrorHandler::handle($e);
                    //add exception to json to send
  AudioObj::addJson(array(
     "error" => ($e->getMessage())
  ));
}

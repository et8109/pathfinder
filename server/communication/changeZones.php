<?php
/**
 * This page recieves requests to move scenes.
 * It sends the prep info (audio, etc.) to the user, but no actual information. Just loading.
 */
require_once("shared/Header.php");

try{
//only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        //get player db info
        $player = Player::fromDatabase($_SESSION['playerID']);
        $oldZone = $player->zone;
        $zone = getNewZone($player->zone, $_POST['dir']);
        //move player
        $player->reposition($zone);
        //remove old events
        Player::removeAllOldEvents($_timeRecieved);
        //get npcs in zone
        $npcToSend = [];
        $npcs = Npc::getInZone($player->zone);
        foreach($npcs as $npc){
            $npcToSend[] = $npc->interactPlayer($player);
        }
        //get enemies in zone
        $enemyToSend = [];
        $enemies = Enemy::getInZone($player->zone);
        foreach($enemies as $enemy){
            $enemyToSend[] = $enemy->interactPlayer($player);
        }
        Zone::getPlayingAmbients($player->zone);
       //check player events
       //$eventsResult = Player::getPlayerEventsInZone($zone,$_SESSION['lastupdateTime']);
       //update last event time
       $_SESSION['lastupdateTime'] = $_timeRecieved;

        echo json_encode(array(
            "prep" => array(
                "endAmb" => $_POST['dir']=='init' ? "" :
                            Zone::endPrevZone($oldZone),
                "npcs" => Npc::getPrepInfo($player->zone),
                "enemies" => Enemy::getPrepInfo($player->zone),
                "amb" => Zone::getPrepInfo($player->zone)
            ),
            "play" => array(
                "npcs" => $npcToSend,
                "enemies" => $enemyToSend,
                "ambients" => Zone::getPlayingAmbients($player->zone)
            )
        ));
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}

/**
 * Returns the new zone from the old zone and movement direction
 */
function getNewZone($prev, $dir){
    switch($dir){
    case 'N':
        if($prev->zoney >= constants::numZonesSrt){
            throw new Exception("out of map range");
        }
        return new Zone($prev->zonex,
                        $prev->zoney + 1);
    case 'S':
        if($prev->zoney <= 0){
            throw new Exception("out of map range");
        }
        return new Zone($prev->zonex,
                        $prev->zoney - 1);
    case 'E':
        if($prev->zonex >= constants::numZonesSrt){
            throw new Exception("out of map range");
        }
        return new Zone($prev->zonex + 1,
                        $prev->zoney);
    case 'W':
        if($prev->zonex <= 0){
            throw new Exception("out of map range");
        }
        return new Zone($prev->zonex - 1,
                            $prev->zoney);
    case 'init':
        //initial zone, just needs to load
        return $prev;
    default:
        throw new Exception("unknown direction: $dir");
    }
}

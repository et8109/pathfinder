<?php
/**
 * This page recieves requests to move scenes.
 */
require_once("shared/Header.php");
require_once("responses/changeZonesResponse.php");

try{
//only posts should be accepted. other verbs are ignored.
    if(!empty($_POST)){
        $response = new ChangeZoneResponse();
        //get player db info
        $player = Player::fromDatabase($_SESSION['playerID']);
        $oldZone = $player->zone;
        try{
            $zone = getNewZone($player->zone, $_POST['dir']);
        } catch(OutOfBoundsException $e){
            $player->sprite->outOfBounds();
            echo $response->send();
            exit();
        }
        //move player
        $player->reposition($zone);
        //remove old events
        Player::removeAllOldEvents($_timeRecieved);
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
       //check player events
       //$eventsResult = Player::getPlayerEventsInZone($zone,$_SESSION['lastupdateTime']);
       //update last event time
       $_SESSION['lastupdateTime'] = $_timeRecieved;

        Npc::getPrepInfo($player->zone);
        Enemy::getPrepInfo($player->zone);

        Zone::endPrevZone($oldZone);
        Zone::addPrepInfo($player->zone);
        Zone::addPlayingAmbients($player->zone);

        echo $response->send();
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
        return $prev->path(Zone::dir_north);
    case 'S':
        return $prev->path(Zone::dir_south);
    case 'E':
        return $prev->path(Zone::dir_east);
    case 'W':
        return $prev->path(Zone::dir_west);
    case 'init':
        //initial zone, just needs to load
        return $prev;
    default:
        throw new Exception("unknown direction: $dir");
    }
}

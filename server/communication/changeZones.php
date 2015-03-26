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
        $newZone = getNewZone($player->zone, $_POST['dir']);
        //move player
        $player->reposition($newZone);
        echo json_encode(array(
            "endAmb" => $_POST['dir']=='init' ? "" : 
                        Zone::endPrevZone($player->zone),
            "npcPrep" => Npc::getPrepInfo($newZone),
            "enemyPrep" => Enemy::getPrepInfo($newZone),
            "ambPrep" => Zone::getPrepInfo($newZone)
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

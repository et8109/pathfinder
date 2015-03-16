<?php
/**
 * This page recieves requests to move scenes.
 */
require_once("shared/Header.php");
require_once("shared/Translator.php");

try{
//only posts should be accepted. other verbs are ignored.
if(!empty($_POST)){
    //setup
    //AudioObj::initState();//sets up globals: time and json array
    //get player db info
    $player = Player::fromDatabase($_SESSION['playerID']);
    $zone= $player->zone;
    $dir = $_POST['dir'];
    //set new zone co-ords
    $newZone = null;
    switch($dir){
    case 'N':
        if($zone->zoney >= constants::numZonesSrt){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->zonex,
            $zone->zoney + 1);
        break;
    case 'S':
        if($zone->zoney <= 0){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->zonex,
            $zone->zoney - 1);
        break;
    case 'E':
        if($zone->zonex >= constants::numZonesSrt){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->zonex + 1,
            $zone->zoney);
        break;
    case 'W':
        if($zone->zonex <= 0){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->zonex - 1,
            $zone->zoney);
        break;
    case 'init':
        //initial zone, just needs to load
        $newZone = $zone;
        break;
    default:
        throw new Exception("unknown direction: $dir");
    }
    //continue to change zone
    $player->reposition($newZone);
    //prep audio for things in the zone
    Npc::getPrepInfo($newZone);
    Enemy::getPrepInfo($newZone);
    echo Translator::send();

    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler.php");
    echo ErrorHandler::handle($e);
}

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
    $player = Player::fromDatabase($_POST['playerID']);
    $zone= new Zone($playerInfo['zonex'],
                    $playerInfo['zoney']);
    $dir = $_POST['dir'];
    //set new zone co-ords
    $newZone = null;
    switch($dir){
    case 'N':
        if($zoney >= constants::numZonesSrt){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->posx,
                            $zone->posy + 1);
    case 'S':
        if($zoney <= 0){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->posx,
                            $zone->posy - 1);
    case 'E':
        if($zonex >= constants::numZonesSrt){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->posx + 1,
                            $zone->posy);
    case 'W':
        if($zoney <= 0){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->posx - 1,
                            $zone->posy);
    default:
        throw new Exception("unknown direction");
    }
    //continue to change zone
    $player->reposition($newZone);
    //prep audio for things in the zone
    Npc::getPrepInfo($newZone);
    Enemy::getPrepInfo($newZone);


    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
    require_once("shared/ErrorHandler");
    ErrorHandler::handle($e);
    //add exception to json to send
    AudioObj::addJson(array(
     "error" => ($e->getMessage())
  ));
}

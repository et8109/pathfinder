<?php
require_once("ZoneLoadInterface.php");

//check if out of map range
if ($posx < 0){
    $posx = $posx + distances::edgeBump;
    $arrayJSON[] = (array(
        "playerInfo" => true,
        "posX" => $posx,
        "posY" => $posy
    ));
    $newZone = false;
}
if ($posy < 0){
    $posy = $posy + distances::edgeBump;
    $arrayJSON[] = (array(
        "playerInfo" => true,
        "posX" => $posx,
        "posY" => $posy
    ));
    $newZone = false;
}
if ($posx > constants::numZonesSrt*constants::zoneWidth){
    $posx = $posx - distances::edgeBump;
    $arrayJSON[] = (array(
        "playerInfo" => true,
        "posX" => $posx,
        "posY" => $posy
    ));
    $newZone = false;
}
if ($posy > constants::numZonesSrt*constants::zoneWidth){
    $posy = $posy - distances::edgeBump;
    $arrayJSON[] = (array(
        "playerInfo" => true,
        "posX" => $posx,
        "posY" => $posy
    ));
    $newZone = false;
}
//update playerinfo
ZoneLoadInterface::updatePlayerInfo($posx,$posy,$zone,$_SESSION['playerID']);
//if in a new zone
if($newZone){
    $arrayJSON[0] = array("newZone" => true);
    //send ambient sounds
    $ambientResult = ZoneLoadInterface::getAmbientSounds($zone);
    foreach($ambientResult as $row){
        $arrayJSON[] = (array(
            "ambient" => true,
            "posx" => $row['posx'],
            "posy" => $row['posy'],
            "audioURL" => $row['audioURL']
        ));
    }

    //send movement sound
    $moveRow = ZoneLoadInterface::getMovementSound($zone);
    $arrayJSON[] = (array(
        "movement" => true,
        "audioURL" => $moveRow['audioURL']
    ));
    //send enemies
    $enemyResult = ZoneLoadInterface::getEnemies($zone);
    foreach($enemyResult as $row){
        $arrayJSON[] = (array(
            "enemy" => true,
            "id" => $row['id'],
            "posx" => $row['posx'],
            "posy" => $row['posy'],
            "audioURL" => $row['audioURL']
        ));
    }
    //send npcs
    $npcResult = ZoneLoadInterface::getNpcs($zone);    
    foreach($npcResult as $row){
        $arrayJSON[] = (array(
            "npc" => true,
            "id" => $row['id'],
            "posx" => $row['posx'],
            "posy" => $row['posy'],
            "audioURL" => $row['audioURL']
        ));
    }
    //send players nearby
    $playersResult = ZoneLoadInterface::getPlayers($zone,$_SESSION['playerID'],constants::numZonesSrt);
    foreach($playersResult as $row){
        $arrayJSON[] = (array(
            "player" => true,
            "peerid" => $row['peerid']
        ));
    }
}
?>
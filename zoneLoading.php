<?php
require_once("ZoneLoadInterface.php");

AudioObj::addJson(array("newZone" => true));
//send ambient sounds
$ambientResult = ZoneLoadInterface::getAmbientSounds($zone);
foreach($ambientResult as $row){
    AudioObj::addJson(array(
        "ambient" => true,
        "posx" => $row['posx'],
        "posy" => $row['posy'],
        "audioURL" => $row['audioURL']
    ));
}

//send movement sound
$moveRow = ZoneLoadInterface::getMovementSound($zone);
AudioObj::addJson(array(
    "movement" => true,
    "audioURL" => $moveRow['audioURL']
));
//send enemies
$enemyResult = ZoneLoadInterface::getEnemies($zone);
foreach($enemyResult as $row){
    AudioObj::addJson(array(
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
    AudioObj::addJson(array(
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
    AudioObj::addJson(array(
        "player" => true,
        "peerid" => $row['peerid']
    ));
}
?>
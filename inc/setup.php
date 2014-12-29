<?php
require("../interfaces/setupInterface.php");
session_start();
$arrayJSON = array();
$infoRow = SetupInterface::getPlayerInfo($_SESSION['playerID']);
$spriteRow = SetupInterface::getSpriteAudio();
$arrayJSON[] = (array(
                    "spriteaudioURL" => $spriteRow[0]['url'].",".$spriteRow[1]['url'],
                    "playerID" => $_SESSION['playerID'],
                    "playeraudioURL" => $infoRow['audioURL'],
                    "peerID" => $infoRow['peerid'],
                    "posX" => $infoRow['posx'],
                    "posY" => $infoRow['posy'],
                    "version" => 2
                ));
echo json_encode($arrayJSON);
?>
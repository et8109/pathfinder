<?php
require("setupInterface.php");
session_start();
$arrayJSON = array();
$infoRow = SetupInterface::getPlayerInfo($_SESSION['playerID']);
$arrayJSON[] = (array(
                    "spriteaudioURL" => "Lowlife.mp3,Dead.mp3",
                    "playerID" => $_SESSION['playerID'],
                    "playeraudioURL" => $infoRow['audioURL'],
                    "peerID" => $infoRow['peerid'],
                    "posX" => $infoRow['posx'],
                    "posY" => $infoRow['posy'],
                    "version" => 2
                ));
echo json_encode($arrayJSON);
?>
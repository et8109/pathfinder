<?php
final class constants {
    const zoneWidth = 50;
    const numZonesSrt = 2;//should be a square
    const secBetweenevents = 6;
    const zoneBuffer = 5;
    const npcDuration = 10;
    const enemyDuration = 10;
    const playerDuration = 6;
    const portNum = 9300;
    const ipAddr = "127.0.0.1";
    const server_root = "/home/elliot/projects/pathfinder/server";
}

/**
 *the distances at which certain audio starts
 */
final class distances {
    const ambientNotice = 15;
    const enemyNotice = 10;
    const enemyAttack = 4;
    const edgeBump = 10;//distance player is pushed back when they reach the edge of a zone
}
?>

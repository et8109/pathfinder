<?php
require_once("interface.php");

class AdminInterface extends Interface_class{
    private function __construct() {}//static only
    
    public static function resetDatabase(){
        self::$db->querySingle("DROP DATABASE ignatymc_pathfinder");
        self::$db->querySingle("CREATE DATABASE ignatymc_pathfinder");
        self::$db->querySingle("USE ignatymc_pathfinder");
        //ambient sounds
        self::$db->querySingle("CREATE TABLE ambient (".
                            "id int(3),".
                           "zone int(3),".
                           "posx int(3),".
                           "posy int(3),".
                           "audioURL varchar(10),".
                           "PRIMARY KEY (zone, id)".
                           ")");
        //specific enemies
        self::$db->querySingle("CREATE TABLE enemies (".
                           "id int(3),".
                           "type int(3),".
                           "posx int(3),".
                           "posy int(3),".
                           "zone int(3),".
                           "health int(3),".
                           "lastAudio int(3),".
                           "finish int(10),".
                           "start int(10),".
                           "PRIMARY KEY (id)".
                           ")");
        //enemy types
        self::$db->querySingle("CREATE TABLE enemyinfo (".
                           "id int(3) AUTO_INCREMENT,".
                           "audioURL varchar(30),".
                           "PRIMARY KEY (id)".
                           ")");
        //walking audio for zones
        self::$db->querySingle("CREATE TABLE movement (".
                           "zone int(3),".
                           "audioURL varchar(15),".
                           "PRIMARY KEY (zone)".
                           ")");
        //npcs
        self::$db->querySingle("CREATE TABLE npcs (".
                           "id int(3),".
                           "zone int(3),".
                           "posx int(3),".
                           "posy int(3),".
                           "audioURL varchar(40),".
                           "lastAudio int(3),".
                           "finish int(10),".
                           "start int(10),".
                           "PRIMARY KEY (id)".
                           ")");
        //player events
        self::$db->querySingle("CREATE TABLE playerevents (".
                           "id int(3),".
                           "zone int(3),".
                           "audiotype int(3),".
                           "finish int(10),".
                           "start int(10),".
                           "PRIMARY KEY (id)".
                           ")");
        //player info
        self::$db->querySingle("CREATE TABLE playerinfo (".
                           "id int(3) AUTO_INCREMENT,".
                           "uname varchar(10),".
                           "pass varchar(20),".
                           "zone int(3),".
                           "posx int(3),".
                           "posy int(3),".
                           "audioURL varchar(10),".
                           "peerid varchar(10),".
                           "health int(3),".
                           "kills int(3),".
                           "PRIMARY KEY (id)".
                           ")");
        //individual audio info
        self::$db->querySingle("CREATE TABLE audio (".
                               "id int(3),".
                               "objid varchar(4),".
                               "url varchar(10),".
                               "length int,".
                               "PRIMARY KEY (objid, id)".
                               ")");
        //populate databases
        self::$db->querySingle("INSERT INTO ambient (id, zone, posx, posy, audioURL) values (0, 1, 10, 10, 'Birds.mp3')");
        self::$db->querySingle("INSERT INTO enemies (id, type, posx, posy, zone, health, lastAudio, finish, start) values (0, 0, 20, 20, 1, 4, 1, 0, 0)");
        self::$db->querySingle("INSERT INTO enemyInfo (id, audioURL) values (0, 'Growl.mp3,Chomp.mp3,ed.mp3')");
        self::$db->querySingle("INSERT INTO movement (zone, audioURL) values (1, 'carpetStep.wav')");
        self::$db->querySingle("INSERT INTO npcs (id, zone, posx, posy, audioURL, lastAudio, finish, start) values (0, 1, 5, 10, 'wc.mp3,Kntq.mp3,Knty.mp3,Kntn.mp3', 0, 0, 0)");
        self::$db->querySingle("INSERT INTO playerinfo (id, uname, pass, posx, posy, zone, peerid, health, audioURL, kills) values ".
                               "(1, 'guest', 'guest', 1, 1, 0, 'abcd1234', 3, 'attack.mp3', 0)");
        self::$db->querySingle("INSERT INTO audio (objid, id, url, length) values ".
                               "('a0', 0, 'Birds.mp3', 2),".//ambient
                               "('e0', 0, 'Growl.mp3', 7),".//enemies
                               "('e0', 1, 'Chomp.mp3', 2),".
                               "('e0', 2, 'ed.mp3', 2),".
                               "('m1', 0, 'carpetStep.wav', 6),".//movement
                               "('n0', 0, 'wc.mp3', 3),".//npcs
                               "('n0', 1, 'Kntq.mp3', 5),".
                               "('n0', 2, 'Knty.mp3', 6),".
                               "('n0', 3, 'Kntn.mp3', 5),".
                               "('p1', 0, 'attack.mp3', 3),".//player
                               "('s0', 0, 'Lowlife.mp3', 4),".//sprite
                               "('s0', 1, 'Dead.mp3', 4)"
                               );
    }
}
?>
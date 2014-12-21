<?php
require_once("interface.php");

class AdminInterface extends Interface_class{
    private function __construct() {}//static only
    
    public static function resetDatabase(){
        self::$db->querySingle("DROP DATABASE ignatymc_pathfinder");
        self::$db->querySingle("CREATE DATABASE ignatymc_pathfinder");
        //ambient sounds
        self::$db->querySingle("CREATE TABLE ambient".
                           "zone int(3)".
                           "posx int(3)".
                           "posy int(3)".
                           "audioURL varchar(10)".
                           "PRIMARY KEY (zone)");
        //specific enemies
        self::$db->querySingle("CREATE TABLE enemies".
                           "id int(3)".
                           "posx int(3)".
                           "posy int(3)".
                           "zone int(3)".
                           "health int(3)".
                           "lastAudio int(3)".
                           "finish int(10)".
                           "start int(10)".
                           "PRIMARY KEY (id)");
        //enemy types
        self::$db->querySingle("CREATE TABLE enemyinfo".
                           "id int(3) AUTO_INCREMENT".
                           "audioURL varchar(10)".
                           "PRIMARY KEY (id)");
        //walking audio for zones
        self::$db->querySingle("CREATE TABLE movement".
                           "zone int(3)".
                           "audioURL varchar(15)".
                           "PRIMARY KEY (zone)");
        //npcs
        self::$db->querySingle("CREATE TABLE npcs".
                           "id int(3)".
                           "zone int(3)".
                           "posx int(3)".
                           "posy int(3)".
                           "audioURL varchar(40)".
                           "lastAudio int(3)".
                           "finish int(10)".
                           "start int(10)".
                           "PRIMARY KEY (id)");
        //player events
        self::$db->querySingle("CREATE TABLE playerevents".
                           "id int(3)".
                           "zone int(3)".
                           "audiotype int(3)".
                           "finish int(10)".
                           "start int(10)".
                           "PRIMARY KEY (id)");
        //player info
        self::$db->querySingle("CREATE TABLE playerinfo".
                           "id int(3) AUTO_INCREMENT".
                           "uname varchar(10)".
                           "pass varchar(20)".
                           "zone int(3)".
                           "posx int(3)".
                           "posy int(3)".
                           "audioURL varchar(10)".
                           "peerid varchar(10)".
                           "health int(3)".
                           "kills int(3)".
                           "PRIMARY KEY (id)");
    }
}
?>
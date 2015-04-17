<?php
require_once("shared/Table.php");
/**
 * The individual Npcs and thier current stats
 */
class Npcs extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle(
            "CREATE TABLE npcs (".
            "id int(3),".
            "name char(20),".
            "zonex int(3),".
            "zoney int(3),".
            "health int(3),".
            "PRIMARY KEY (id)".
            ")");
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO npcs (id, zonex, zoney, health) 
                       values ( 0,     1,     2,      4)");

    }

    /**
     * Returns all the npcs in the given zone
     */
    public static function getZonePrep($zonex, $zoney){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $r = self::$db->queryMulti("select id from npcs where zonex=$zonex and zoney=$zoney");
        for($i=0; isset($r[$i]); $i++){
            $objid = "n".$r[$i]['id'];
            $r[$i]['audioURLs'] = Audio::getURLs($objid);
        }
        return $r;
    }

    /**
     * Returns the npc info of the npcs in the given zone
     */
    public static function getInZone($zonex, $zoney, $getUrls) {
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $r = self::$db->queryMulti("select id,zonex,zoney,health from npcs where zonex=$zonex and zoney=$zoney");
        if($getUrls){
            foreach($r as &$n){
                $n['urls'] = Audio::getUrls('n'.$n['id']);
            }
        }
        return $r;
    }

    /*public static function addEvent($startTime, $endTime, $audioInt, $npcid) {
        $startTime = self::prepVar($startTime);
        $endTime = self::prepVar($endTime);
        $audioInt = self::prepVar($audioInt);
        $npcid = self::prepVar($npcid);
        self::$db->querySingle("update npcs set start=$startTime, finish=$endTime, lastAudio=$audioInt where id=$npcid");
    }*/
 
}
?>
